<?php
require_once 'config.php';
require_once 'system/orm.php';

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);

// Function to manage log file lines
function logToFile($filePath, $message, $maxLines = 5000) {
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

function handleError($errno, $errstr, $errfile, $errline) {
    logToFile('error.log', "Error: [$errno] $errstr - $errfile:$errline");
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

set_error_handler("handleError");

try {
    $captureLogs = file_get_contents("php://input");
    $analizzare = json_decode($captureLogs);

    $now = new DateTime('now', new DateTimeZone('GMT+3'));
    $receivedTimestamp = $now->format('Y-m-d H:i:s');
    logToFile('doublepayments.log', "Received callback data in double_payments.php at " . $receivedTimestamp . ":\n" . $captureLogs);

    // Sleep for 35 seconds
    logToFile('doublepayments.log', "Sleeping for 35 seconds before checking for duplicates.");
    sleep(35);

    // Extract MpesaReceiptNumber
    $mpesa_code = ($analizzare->Body->stkCallback->CallbackMetadata->Item[1]->Value);
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
        $existingTransactionsArray = $existingTransactions->as_array(); // Convert to array
        $keepTransaction = array_pop($existingTransactionsArray); // Keep the most recent transaction
        logToFile('doublepayments.log', "Keeping transaction with ID: " . $keepTransaction['id']);

        foreach ($existingTransactionsArray as $transaction) {
            logToFile('doublepayments.log', "Attempting to delete transaction with ID: " . $transaction['id']);
            try {
                $transactionToDelete = ORM::for_table('tbl_transactions')->find_one($transaction['id']);
                if ($transactionToDelete) {
                    $transactionToDelete->delete();
                    logToFile('doublepayments.log', "Deleted duplicate transaction with invoice: $mpesa_code and ID: " . $transaction['id']);
                } else {
                    logToFile('doublepayments.log', "Failed to find transaction with ID: " . $transaction['id']);
                }
            } catch (Exception $e) {
                logToFile('error.log', "Exception during deletion of transaction ID: " . $transaction['id'] . " - " . $e->getMessage());
            }
        }
    } else {
        logToFile('doublepayments.log', "No duplicates found or only one transaction found for invoice: $mpesa_code");
    }
} catch (Exception $e) {
    logToFile('error.log', "Exception: " . $e->getMessage());
}
?>
