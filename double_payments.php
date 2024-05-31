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

function logToFile($filePath, $message, $maxLines = 5000) {
    $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    $lines[] = $message;
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

$now = new DateTime('now', new DateTimeZone('GMT+3'));
$receivedTimestamp = $now->format('Y-m-d H:i:s');
logToFile('doublepayments.log', "Received callback data in double_payments.php at " . $receivedTimestamp . ":\n" . $captureLogs);

// Sleep for 35 seconds
logToFile('doublepayments.log', "Sleeping for 35 seconds before checking for duplicates.");
sleep(35);

// Extract MpesaReceiptNumber
$mpesa_code = ($analizzare->Body->stkCallback->CallbackMetadata->Item['1']->Value);
logToFile('doublepayments.log', "Extracted MpesaReceiptNumber: $mpesa_code");

// Check if a transaction with the same invoice already exists
logToFile('doublepayments.log', "Checking for existing transactions with invoice: $mpesa_code");
$existingTransactions = ORM::for_table('tbl_transactions')
    ->where('invoice', $mpesa_code)
    ->order_by_desc('id')
    ->find_many();

logToFile('doublepayments.log', "Found " . count($existingTransactions) . " transactions with the same invoice.");

if (count($existingTransactions) > 1) {
    logToFile('doublepayments.log', "More than one transaction found, deleting duplicates.");
    $keepTransaction = array_pop($existingTransactions); // Keep the oldest transaction
    foreach ($existingTransactions as $transaction) {
        $transaction->delete();
        logToFile('doublepayments.log', "Deleted duplicate transaction with invoice: $mpesa_code\n");
    }
} else {
    logToFile('doublepayments.log', "No duplicates found or only one transaction found for invoice: $mpesa_code\n");
}
?>
