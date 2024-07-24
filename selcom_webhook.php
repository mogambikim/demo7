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

$event = json_decode($captureLogs, true);

if (!isset($event['order_id']) || !isset($event['amount']) || !isset($event['transid']) || $event['result'] !== 'SUCCESS' || $event['resultcode'] !== '000') {
    logToFile($logFilePath, "Invalid data received: " . print_r($event, true));
    http_response_code(400);
    exit();
}

$order_id = $event['order_id'];
$amount_paid = $event['amount'];
$transid = $event['transid'];

logToFile($logFilePath, "Processing payment for order_id: $order_id");

// Retrieve the payment gateway record
$paymentGatewayRecord = ORM::for_table('tbl_payment_gateway')->where('gateway_trx_id', $order_id)->find_one();
if (!$paymentGatewayRecord) {
    logToFile($logFilePath, "Payment gateway record not found for order_id: $order_id");
    http_response_code(200);
    exit();
}

$uname = $paymentGatewayRecord->username;
$plan_id = $paymentGatewayRecord->plan_id;
$router_id = $paymentGatewayRecord->routers_id;

logToFile($logFilePath, "Retrieved plan_id: $plan_id and router_id: $router_id for username: $uname");

// Retrieve user, plan, and router details
$user = ORM::for_table('tbl_customers')->where('username', $uname)->find_one();
if (!$user) {
    logToFile($logFilePath, "User not found with username: $uname");
    http_response_code(200);
    exit();
}

$plan = ORM::for_table('tbl_plans')->where('id', $plan_id)->find_one();
$router = ORM::for_table('tbl_routers')->where('id', $router_id)->find_one();

if (!$plan || !$router) {
    logToFile($logFilePath, "Plan or router not found for plan_id: $plan_id and router_id: $router_id");
    http_response_code(200);
    exit();
}

$plan_name = $plan->name_plan;
$plan_type = $plan->type;
$validity = $plan->validity;
$units = $plan->validity_unit;
$router_name = $router->name;

// Calculate expiry time
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

// Update recharge and transaction records
$existing_recharges = ORM::for_table('tbl_user_recharges')->where('username', $uname)->find_many();
foreach ($existing_recharges as $recharge) {
    $recharge->delete();
}
logToFile($logFilePath, "Deleted existing recharge records for username: $uname");

// Insert new recharge record
ORM::for_table('tbl_user_recharges')->create(array(
    'customer_id' => $user->id,
    'username' => $uname,
    'plan_id' => $plan_id,
    'namebp' => $plan_name,
    'recharged_on' => $recharged_on,
    'recharged_time' => $recharged_time,
    'expiration' => $expiry_date,
    'time' => $expiry_time,
    'status' => "on",
    'method' => "Selcom-" . $transid,
    'routers' => $router_name,
    'type' => $plan_type
))->save();

logToFile($logFilePath, "Recharge record inserted successfully for username: $uname");

// Insert transaction record
ORM::for_table('tbl_transactions')->create(array(
    'invoice' => $transid,
    'username' => $uname,
    'plan_name' => $plan_name,
    'price' => $amount_paid,
    'recharged_on' => $recharged_on,
    'recharged_time' => $recharged_time,
    'expiration' => $expiry_date,
    'time' => $expiry_time,
    'method' => "Selcom-" . $transid,
    'routers' => $router_name,
    'type' => $plan_type
))->save();

logToFile($logFilePath, "Transaction record inserted successfully for username: $uname");

// Include external script for adding user to MikroTik
include 'config.php';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
} catch (PDOException $e) {
    logToFile($logFilePath, "Connection failed: " . $e->getMessage());
    http_response_code(500);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM tbl_routers WHERE id = :routerId");
$stmt->bindParam(':routerId', $router_id);
$stmt->execute();
$routerResult = $stmt->fetch(PDO::FETCH_ASSOC);

if ($routerResult) {
    $username = $routerResult['username'];
    $password = $routerResult['password'];
    $routerIpAddress = $routerResult['ip_address'];
} else {
    logToFile($logFilePath, "Router with ID $router_id not found");
    http_response_code(200);
    exit();
}

require 'system/autoload/PEAR2/Autoload.php';

use PEAR2\Net\RouterOS;

try {
    $client = new RouterOS\Client($routerIpAddress, $username, $password);
    $util = new RouterOS\Util($client);
    $util->setMenu('/ip hotspot user')->add([
        'name' => $uname,
        'password' => '1234',
        'profile' => $plan_name
    ]);
    logToFile($logFilePath, "Hotspot user '$uname' added successfully");

    $userList = $util->setMenu('/ip hotspot active')->getAll();
    $userId = null;
    foreach ($userList as $user) {
        if ($user->getProperty('user') === $uname) {
            $userId = $user->getProperty('.id');
            break;
        }
    }

    if ($userId) {
        $userIP = $util->setMenu('/ip hotspot active')->get($userId)->getProperty('address');
        logToFile($logFilePath, "User '$uname' is logged in with IP address: $userIP");
    } else {
        logToFile($logFilePath, "User '$uname' is not logged in");
    }
} catch (Exception $e) {
    logToFile($logFilePath, 'Exception: ' . $e->getMessage());
}

logToFile($logFilePath, "User creation process completed for username: $uname");

// Update payment gateway record
$paymentGatewayRecord->status = 2;
$paymentGatewayRecord->paid_date = $now->format('Y-m-d H:i:s');
$paymentGatewayRecord->gateway_trx_id = $transid;
$paymentGatewayRecord->save();

logToFile($logFilePath, "Payment gateway record updated successfully for username: $uname");

http_response_code(200);
?>
