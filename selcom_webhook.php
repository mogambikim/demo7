<?php
require_once 'config.php';
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';
include "system/autoload/Hookers.php";

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);

// Function to manage log file lines
function logToFile($filePath, $message, $maxLines = 5000) {
    $lines = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];
    $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

$logFilePath = 'selcom_webhook.log';

// Capture and log incoming webhook data
$captureLogs = file_get_contents("php://input");
logToFile($logFilePath, "Received raw data: " . $captureLogs);

// Parse incoming data
$event = json_decode($captureLogs, true);

// Check if payment is successful
if ($event['result'] == 'SUCCESS' && $event['resultcode'] == '000') {
    $amount_paid = $event['data'][0]['amount'] / 100; // Convert from cents to TZS
    $phone_number = $event['data'][0]['msisdn'];
    $username = $phone_number;
    $payment_method = "Selcom Payment";
    $gateway = "Selcom";

    logToFile($logFilePath, "Extracted callback data:\nPhone Number: $phone_number\nAmount Paid: $amount_paid\nPayment Method: $payment_method\nGateway: $gateway\n");

    // Retrieve user from the database
    $user = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
    if (!$user) {
        logToFile($logFilePath, "User not found with username: $username");
        http_response_code(200);
        exit();
    }

    // Fetch payment gateway record
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();
    if (!$PaymentGatewayRecord) {
        logToFile($logFilePath, "No payment gateway record found for username: $username");
        http_response_code(200);
        exit();
    }

    $plan_id = $PaymentGatewayRecord->plan_id;
    $routerId = $PaymentGatewayRecord->routers_id;
    logToFile($logFilePath, "Retrieved plan_id: $plan_id and router_id: $routerId from tbl_payment_gateway");

    // Fetch plan and router details
    $plan = ORM::for_table('tbl_plans')->where('id', $plan_id)->find_one();
    $router = ORM::for_table('tbl_routers')->where('id', $routerId)->find_one();

    // Calculate expiry time
    $now = new DateTime('now', new DateTimeZone('GMT+3'));
    $unit_in_seconds = [
        'Mins' => 60,
        'Hrs' => 3600,
        'Days' => 86400,
        'Months' => 2592000
    ];
    $unit_seconds = $unit_in_seconds[$plan->validity_unit];
    $expiry_timestamp = $now->getTimestamp() + ($plan->validity * $unit_seconds);
    $expiry_datetime = new DateTime("@$expiry_timestamp");
    $expiry_datetime->setTimezone(new DateTimeZone('GMT+3'));
    $expiry_date = $expiry_datetime->format('Y-m-d');
    $expiry_time = $expiry_datetime->format('H:i:s');

    // Update recharge and transaction records
    // Delete existing recharges
    $existing_recharges = ORM::for_table('tbl_user_recharges')->where('username', $username)->find_many();
    foreach ($existing_recharges as $recharge) {
        $recharge->delete();
    }
    logToFile($logFilePath, "Deleted existing recharge records for username: $username");

    // Insert new recharge record
    ORM::for_table('tbl_user_recharges')->create(array(
        'customer_id' => $user->id,
        'username' => $username,
        'plan_id' => $plan_id,
        'namebp' => $plan->name_plan,
        'recharged_on' => $now->format('Y-m-d'),
        'recharged_time' => $now->format('H:i:s'),
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        'status' => "on",
        'method' => "$gateway-" . uniqid('selcom'),
        'routers' => $router->name,
        'type' => $plan->type
    ))->save();

    logToFile($logFilePath, "Recharge record inserted successfully for username: $username");

    // Insert transaction record
    ORM::for_table('tbl_transactions')->create(array(
        'invoice' => uniqid('selcom'),
        'username' => $username,
        'plan_name' => $plan->name_plan,
        'price' => $amount_paid,
        'recharged_on' => $now->format('Y-m-d'),
        'recharged_time' => $now->format('H:i:s'),
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        'method' => "$gateway-" . uniqid('selcom'),
        'routers' => $router->name,
        'type' => $plan->type
    ))->save();

    logToFile($logFilePath, "Transaction record inserted successfully for username: $username");

    // Include external script (if exists)
    $file_path = 'system/adduser.php';
    if (file_exists($file_path)) {
        logToFile($logFilePath, "Including external script: $file_path");
        include_once $file_path;
        logToFile($logFilePath, "External script executed successfully: $file_path");
    } else {
        logToFile($logFilePath, "External script not found: $file_path");
    }

    // Update payment gateway record
    $PaymentGatewayRecord->status = 2;
    $PaymentGatewayRecord->paid_date = $now->format('Y-m-d H:i:s');
    $PaymentGatewayRecord->gateway_trx_id = uniqid('selcom');
    $PaymentGatewayRecord->save();

    logToFile($logFilePath, "Payment gateway record updated successfully for username: $username");
}

http_response_code(200);
?>
