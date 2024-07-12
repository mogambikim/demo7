<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
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

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Log the received data to the error log
$logFilePath = 'add_stripe_user.log';
logToFile($logFilePath, "Received data: " . json_encode($input));

// Extract data from JSON input
$phone = isset($input['phone_number']) ? $input['phone_number'] : '';
$planId = isset($input['plan_id']) ? $input['plan_id'] : '';
$routerId = isset($input['router_id']) ? $input['router_id'] : '';
$username = $phone; // Assuming username is created from phone number

// Log the extracted data
logToFile($logFilePath, "Extracted data: phone: $phone, planId: $planId, routerId: $routerId, username: $username");

// Set variables as required by the external script
$uname = $username;
$plan_id = $planId;

// Log the variables before including the external script
logToFile($logFilePath, "Variables set: uname: $uname, plan_id: $plan_id, routerId: $routerId");

// Include the external script
$file_path = 'system/adduser.php';
include_once $file_path;

// Send a response back to the sender
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Data received and processed']);
?>
