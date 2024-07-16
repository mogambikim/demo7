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

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

$now = new DateTime('now', new DateTimeZone('GMT+3'));
$receivedTimestamp = $now->format('Y-m-d H:i:s');
logToFile('queryupdate.log', "Received callback data in query_update.php at " . $receivedTimestamp . ":\n" . $captureLogs);

$response_code = $analizzare->ResponseCode;
$resultDesc = $analizzare->ResponseDescription;
$merchant_req_id = $analizzare->MerchantRequestID;
$checkout_req_id = $analizzare->CheckoutRequestID;
$result_code = $analizzare->ResultCode;
$result_desc = $analizzare->ResultDesc;

// Log the extracted callback data
logToFile('queryupdate.log', "Extracted callback data:\nResponse Code: $response_code\nResult Description: $resultDesc\nMerchant Request ID: $merchant_req_id\nCheckout Request ID: $checkout_req_id\nResult Code: $result_code\nResult Description: $result_desc");

if ($response_code == "0" && $result_code == "0") {
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $checkout_req_id)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        logToFile('queryupdate.log', "PaymentGatewayRecord not found for Checkout Request ID: $checkout_req_id");
        exit();
    }

    if (!empty($PaymentGatewayRecord->gateway_trx_id)) {
        logToFile('queryupdate.log', "PaymentGatewayRecord already processed for Checkout Request ID: $checkout_req_id. Aborting.");
        exit();
    }

    $uname = $PaymentGatewayRecord->username;
    $plan_id = $PaymentGatewayRecord->plan_id;
    $routerId = $PaymentGatewayRecord->routers_id;

    $userid = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->order_by_desc('id')
        ->find_one();

    if (!$userid) {
        logToFile('queryupdate.log', "User not found for username: $uname");
        exit();
    }

    $plans = ORM::for_table('tbl_plans')
        ->where('id', $plan_id)
        ->order_by_desc('id')
        ->find_one();

    if (!$plans) {
        logToFile('queryupdate.log', "Plan not found for plan_id: $plan_id");
        exit();
    }

    $plan_type = $plans->type;
    $plan_name = $plans->name_plan;
    $validity = $plans->validity;
    $units = $plans->validity_unit;

    // Convert units to seconds
    $unit_in_seconds = [
        'Mins' => 60,
        'Hrs' => 3600,
        'Days' => 86400,
        'Months' => 2592000 // Assuming 30 days per month for simplicity
    ];

    $unit_seconds = $unit_in_seconds[$units];
    $expiry_timestamp = $now->getTimestamp() + ($validity * $unit_seconds); // Use $now to get the current timestamp

    // Set the timezone explicitly for expiry date and time
    $expiry_datetime = new DateTime("@$expiry_timestamp");
    $expiry_datetime->setTimezone(new DateTimeZone('GMT+3'));
    $expiry_date = $expiry_datetime->format('Y-m-d');
    $expiry_time = $expiry_datetime->format('H:i:s');

    $recharged_on = $now->format('Y-m-d');
    $recharged_time = $now->format('H:i:s');

    // Log the recharge details
    logToFile('queryupdate.log', "Recharge details:\nPlan Type: $plan_type\nPlan Name: $plan_name\nValidity: $validity $units\nExpiry Date: $expiry_date\nExpiry Time: $expiry_time");

    // Include the external script
    $file_path = 'system/adduser.php';
    include_once $file_path;

    // Fetch the router name
    $router = ORM::for_table('tbl_routers')
        ->where('id', $routerId)
        ->find_one();

    $router_name = $router->name;
    logToFile('queryupdate.log', "Fetched router name: $router_name for router ID: $routerId");

    // Check if the status of the username is 'on'
    $existing_recharge = ORM::for_table('tbl_user_recharges')
        ->where('username', $uname)
        ->where('status', 'on')
        ->find_one();

    if ($existing_recharge) {
        logToFile('queryupdate.log', "Username: $uname already has an active recharge. Exiting.");
    } else {
        // Delete existing records for the username
        $deleted_count = ORM::for_table('tbl_user_recharges')
            ->where('username', $uname)
            ->delete_many();

        logToFile('queryupdate.log', "Deleted $deleted_count existing recharge records for username: $uname");

        // Insert new record into tbl_user_recharges
        ORM::for_table('tbl_user_recharges')->create(array(
            'customer_id' => $userid->id,
            'username' => $uname,
            'plan_id' => $plan_id,
            'namebp' => $plan_name,
            'recharged_on' => $recharged_on,
            'recharged_time' => $recharged_time,
            'expiration' => $expiry_date,
            'time' => $expiry_time,
            'status' => "on",
            'method' => $PaymentGatewayRecord->gateway . "-QUERY",
            'routers' => $router_name, // Use the router name instead of the ID
            'type' => $plan_type
        ))->save();

        logToFile('queryupdate.log', "New recharge record inserted successfully for username: $uname");
    }

    // Check if a transaction with the same invoice already exists
    $existingTransactions = ORM::for_table('tbl_transactions')
        ->where('invoice', 'QUERY' . $checkout_req_id)
        ->order_by_desc('id')
        ->find_many();

    if (count($existingTransactions) > 1) {
        // Convert to array for using array_pop
        $existingTransactionsArray = $existingTransactions->as_array();
        $keepTransaction = array_pop($existingTransactionsArray); // Keep the most recent transaction
        logToFile('queryupdate.log', "Keeping transaction with ID: " . $keepTransaction['id']);

        foreach ($existingTransactionsArray as $transaction) {
            logToFile('queryupdate.log', "Attempting to delete transaction with ID: " . $transaction['id']);
            $transactionToDelete = ORM::for_table('tbl_transactions')->find_one($transaction['id']);
            if ($transactionToDelete) {
                $transactionToDelete->delete();
                logToFile('queryupdate.log', "Deleted duplicate transaction with invoice: " . 'QUERY' . $checkout_req_id . " and ID: " . $transaction['id']);
            } else {
                logToFile('queryupdate.log', "Failed to find transaction with ID: " . $transaction['id']);
            }
        }
    } else {
        logToFile('queryupdate.log', "No duplicates found or only one transaction found for invoice: " . 'QUERY' . $checkout_req_id);
    }

    if (count($existingTransactions) == 0) {
        // Insert new record into tbl_transactions
        ORM::for_table('tbl_transactions')->create(array(
            'invoice' => 'QUERY' . (ORM::for_table('tbl_transactions')->max('id') + 1), // Incremental QUERY code
            'username' => $uname,
            'plan_name' => $plan_name,
            'price' => $PaymentGatewayRecord->price,
            'recharged_on' => $recharged_on,
            'recharged_time' => $recharged_time,
            'expiration' => $expiry_date,
            'time' => $expiry_time,
            'method' => $PaymentGatewayRecord->gateway . "-QUERY",
            'routers' => $router_name,
            'type' => $plan_type
        ))->save();

        logToFile('queryupdate.log', "New transaction record inserted successfully for username: $uname");
    } else {
        logToFile('queryupdate.log', "Transaction record already exists for invoice: " . 'QUERY' . $checkout_req_id);
    }

    // Secondary check for duplicate transactions
    $secondaryCheckTransactions = ORM::for_table('tbl_transactions')
        ->where('invoice', 'QUERY' . $checkout_req_id)
        ->order_by_desc('id')
        ->find_many();

    if (count($secondaryCheckTransactions) > 1) {
        $secondaryCheckTransactionsArray = $secondaryCheckTransactions->as_array(); // Convert to array
        $keepTransaction = array_pop($secondaryCheckTransactionsArray); // Keep the most recent transaction
        foreach ($secondaryCheckTransactionsArray as $transaction) {
            $transactionToDelete = ORM::for_table('tbl_transactions')->find_one($transaction['id']);
            if ($transactionToDelete) {
                $transactionToDelete->delete();
                logToFile('queryupdate.log', "Deleted duplicate transaction with invoice in secondary check: " . 'QUERY' . $checkout_req_id);
            } else {
                logToFile('queryupdate.log', "Failed to find transaction with ID: " . $transaction['id']);
            }
        }
    }




    // Fetch the customer details
    $customer = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->find_one();

    if ($customer) {
        // If you have any other actions you want to perform with the customer details,
        // you can add that code here. Otherwise, this block can be left empty or removed.
    }

    if ($PaymentGatewayRecord->status != 2) {
        logToFile('queryupdate.log', "Updating PaymentGatewayRecord...");

        $PaymentGatewayRecord->status = 2;
        $PaymentGatewayRecord->paid_date = $now->format('Y-m-d H:i:s');
        $PaymentGatewayRecord->gateway_trx_id = 'QUERY' . $checkout_req_id;
        $PaymentGatewayRecord->save();

        logToFile('queryupdate.log', "Updated PaymentGatewayRecord status to: 2");
        logToFile('queryupdate.log', "Updated PaymentGatewayRecord paid_date to: " . $PaymentGatewayRecord->paid_date);
        logToFile('queryupdate.log', "Updated PaymentGatewayRecord gateway_trx_id to: " . $PaymentGatewayRecord->gateway_trx_id);
    } else {
        logToFile('queryupdate.log', "PaymentGatewayRecord status is already 2. No update needed.");
    }

    // Log completion
    $completionTimestamp = (new DateTime('now', new DateTimeZone('GMT+3')))->format('Y-m-d H:i:s');
    logToFile('queryupdate.log', "Process completed at $completionTimestamp");
} else {
    logToFile('queryupdate.log', "Response code or result code is not 0. No action taken.");
}

// Fetch the latest 2 transactions for the username, ordered by id in descending order
$transactionsToCheck = ORM::for_table('tbl_transactions')
    ->where('username', $uname)
    ->order_by_desc('id')
    ->limit(2)
    ->find_many();

if (count($transactionsToCheck) == 2) {
    $latestTransaction = $transactionsToCheck[0];
    $previousTransaction = $transactionsToCheck[1];

    $latestRechargedTime = strtotime($latestTransaction->recharged_time);
    $previousRechargedTime = strtotime($previousTransaction->recharged_time);

    // Check if the previous transaction is within 5 minutes of the latest transaction
    $timeDifference = $latestRechargedTime - $previousRechargedTime;

    if ($timeDifference <= 300) { // 5 minutes in seconds
        $previousTransaction->delete();
        logToFile('queryupdate.log', "Deleted previous transaction with ID: " . $previousTransaction->id . " for username: " . $uname . " (Time difference: $timeDifference seconds)");
    }
}

logToFile('queryupdate.log', "Completed check for transactions within 5 minutes for username: " . $uname);


?>
