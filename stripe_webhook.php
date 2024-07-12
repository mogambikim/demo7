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
    // Read existing file content
    $lines = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];

    // Add new log entry
    $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;

    // Trim to the maximum number of lines
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }

    // Write the trimmed log back to the file
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

$logFilePath = 'stripe_webhook.log';

$captureLogs = file_get_contents("php://input");
logToFile($logFilePath, "Received raw data: " . $captureLogs);

$event = json_decode($captureLogs, true);

if ($event['type'] == 'payment_intent.succeeded') {
    $paymentIntent = $event['data']['object'];
    $amount_paid = $paymentIntent['amount_received'] / 100; // Convert from cents to GBP
    $phone_number = $paymentIntent['metadata']['phone_number'];
    $username = $phone_number;
    $payment_method = "Stripe Card";
    $gateway = "Stripe";

    // Log the extracted callback data
    logToFile($logFilePath, "Extracted callback data:\nPhone Number: $phone_number\nAmount Paid: $amount_paid\nPayment Method: $payment_method\nGateway: $gateway\n");

    // Check if user exists
    $user = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
    if ($user) {
        // User exists, log this information
        logToFile($logFilePath, "User exists with username: $username");
    } else {
        // User doesn't exist, log and exit
        logToFile($logFilePath, "User not found with username: $username");
        http_response_code(200);
        exit();
    }

    // Retrieve plan_id and router_id from tbl_payment_gateway
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();

    if ($PaymentGatewayRecord) {
        $uname = $PaymentGatewayRecord->username;
        $plan_id = $PaymentGatewayRecord->plan_id;
        $routerId = $PaymentGatewayRecord->routers_id;
        logToFile($logFilePath, "Retrieved plan_id: $plan_id and router_id: $routerId from tbl_payment_gateway");
    } else {
        // Log and exit if no payment gateway record is found
        logToFile($logFilePath, "No payment gateway record found for username: $username");
        http_response_code(200);
        exit();
    }

    // Fetch the plan details
    $plan = ORM::for_table('tbl_plans')->where('id', $plan_id)->find_one();
    $plan_name = $plan->name_plan;
    $plan_type = $plan->type;
    $validity = $plan->validity;
    $units = $plan->validity_unit;

    // Fetch the router details
    $router = ORM::for_table('tbl_routers')->where('id', $routerId)->find_one();
    $router_name = $router->name;

    // Calculate the expiration time
    $now = new DateTime('now', new DateTimeZone('GMT+3'));
    $unit_in_seconds = [
        'Mins' => 60,
        'Hrs' => 3600,
        'Days' => 86400,
        'Months' => 2592000 // Assuming 30 days per month for simplicity
    ];
    $unit_seconds = $unit_in_seconds[$units];
    $expiry_timestamp = $now->getTimestamp() + ($validity * $unit_seconds);
    $expiry_datetime = new DateTime("@$expiry_timestamp");
    $expiry_datetime->setTimezone(new DateTimeZone('GMT+3'));
    $expiry_date = $expiry_datetime->format('Y-m-d');
    $expiry_time = $expiry_datetime->format('H:i:s');

    // Recharge details
    $recharged_on = $now->format('Y-m-d');
    $recharged_time = $now->format('H:i:s');

    // Delete any existing recharge record for the username
    $existing_recharges = ORM::for_table('tbl_user_recharges')
        ->where('username', $username)
        ->find_many();

    foreach ($existing_recharges as $recharge) {
        $recharge->delete();
    }
    logToFile($logFilePath, "Deleted existing recharge records for username: $username");

    // Insert new recharge record
    ORM::for_table('tbl_user_recharges')->create(array(
        'customer_id' => $user->id,
        'username' => $username,
        'plan_id' => $plan_id,
        'namebp' => $plan_name,
        'recharged_on' => $recharged_on,
        'recharged_time' => $recharged_time,
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        'status' => "on",
        'method' => "$gateway-" . uniqid('stripe'),
        'routers' => $router_name,
        'type' => $plan_type
    ))->save();

    logToFile($logFilePath, "Recharge record inserted successfully for username: $username");

    // Insert transaction record
    ORM::for_table('tbl_transactions')->create(array(
        'invoice' => uniqid('stripe'),
        'username' => $username,
        'plan_name' => $plan_name,
        'price' => $amount_paid,
        'recharged_on' => $recharged_on,
        'recharged_time' => $recharged_time,
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        'method' => "$gateway-" . uniqid('stripe'),
        'routers' => $router_name,
        'type' => $plan_type
    ))->save();

    logToFile($logFilePath, "Transaction record inserted successfully for username: $username");

    // Include the external script
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
    $PaymentGatewayRecord->gateway_trx_id = uniqid('stripe');
    $PaymentGatewayRecord->save();

    logToFile($logFilePath, "Payment gateway record updated successfully for username: $username");
}

http_response_code(200);
?>
