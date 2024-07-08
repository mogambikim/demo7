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
function logToFile($filePath, $message, $maxLines = 1000) {
    // Read existing file content
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);

    // Add new log entry
    $lines[] = $message;

    // Trim to the maximum number of lines
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }

    // Write the trimmed log back to the file
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

// Log the received callback data
$now = new DateTime('now', new DateTimeZone('GMT+3'));
$receivedTimestamp = $now->format('Y-m-d H:i:s');
logToFile('transactions.log', "Received callback data at " . $receivedTimestamp . ":\n" . $captureLogs);

sleep(120); // Sleep for 120 seconds

$response_code = $analizzare->Body->stkCallback->ResultCode;
$checkout_req_id = $analizzare->Body->stkCallback->CheckoutRequestID;
$mpesa_code = $analizzare->Body->stkCallback->CallbackMetadata->Item[1]->Value;

// Log the extracted callback data
logToFile('transactions.log', "Extracted callback data:\nResponse Code: $response_code\nCheckout Request ID: $checkout_req_id\nM-PESA Code: $mpesa_code");

// Check if the transaction exists
$existingTransaction = ORM::for_table('tbl_transactions')
    ->where('invoice', $mpesa_code)
    ->order_by_desc('id')
    ->find_one();

if ($existingTransaction) {
    logToFile('transactions.log', "Transaction found for invoice: $mpesa_code");
} else {
    logToFile('transactions.log', "No transaction found for invoice: $mpesa_code");
}

logToFile('transactions.log', "Check completed at " . $now->format('Y-m-d H:i:s'));
?>
