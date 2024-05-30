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

// Capture the logs from input
$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

$now = new DateTime('now', new DateTimeZone('GMT+3'));
$receivedTimestamp = $now->format('Y-m-d H:i:s');
logToFile('paybill.log', "Received callback data in second_update.php at " . $receivedTimestamp . ":\n" . $captureLogs);

$transID = $analizzare->TransID;
$amount = $analizzare->TransAmount;
$billRefNumber = $analizzare->BillRefNumber;

if ($transID !== null && $amount !== null && $billRefNumber !== null) {
    logToFile('paybill.log', "Processing transaction ID: $transID, Amount: $amount, BillRefNumber: $billRefNumber");

    $userData = ORM::for_table('tbl_customers')
        ->where('username', $billRefNumber)
        ->find_one();

    if ($userData) {
        $username = $userData->username;
        $type = $userData->service_type;
        $routerId = $userData->router_id;
        $userId = $userData->id;

        $routerData = ORM::for_table('tbl_routers')
            ->where('id', $routerId)
            ->find_one();
        $routerName = $routerData ? $routerData->name : 'test router';

        $planData = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->order_by_desc('id')
            ->find_one();
        $planId = $planData ? $planData->plan_id : 1;
        $planName = $planData ? $planData->namebp : 'test';

        logToFile('paybill.log', "Router data and plan data retrieved for user: $username");

        $paymentGatewayRecord = ORM::for_table('tbl_payment_gateway')->create();

        $paymentGatewayRecord->username = $username;
        $paymentGatewayRecord->gateway = 'Mpesa Paybill';
        $paymentGatewayRecord->gateway_trx_id = $transID;
        $paymentGatewayRecord->checkout = $transID;
        $paymentGatewayRecord->plan_id = $planId;
        $paymentGatewayRecord->plan_name = $planName;
        $paymentGatewayRecord->routers_id = $routerId;
        $paymentGatewayRecord->routers = $routerName;
        $paymentGatewayRecord->price = $amount;
        $paymentGatewayRecord->pg_url_payment = 'paybill.freeispradius.com';
        $paymentGatewayRecord->payment_method = 'Mpesa Paybill';
        $paymentGatewayRecord->payment_channel = 'Mpesa Paybill';
        $paymentGatewayRecord->pg_request = '';
        $paymentGatewayRecord->pg_paid_response = '';
        $paymentGatewayRecord->expired_date = date("Y-m-d H:i:s");
        $paymentGatewayRecord->created_date = date("Y-m-d H:i:s");
        $paymentGatewayRecord->paid_date = date("Y-m-d H:i:s");
        $paymentGatewayRecord->status = '2';

        $paymentGatewayRecord->save();
        logToFile('paybill.log', "Payment gateway record created for transaction ID: $transID");

        $transaction = ORM::for_table('tbl_transactions')->create();
        $transaction->invoice = $transID;
        $transaction->username = $username;
        $transaction->plan_name = $planName;
        $transaction->price = $amount;
        $transaction->recharged_on = date("Y-m-d");
        $transaction->recharged_time = date("H:i:s");
        $transaction->expiration = date("Y-m-d H:i:s");
        $transaction->time = date("Y-m-d H:i:s");
        $transaction->method = 'Mpesa Paybill Manual';
        $transaction->routers = $routerName;
        $transaction->Type = 'Balance';
        $transaction->save();
        logToFile('paybill.log', "Transaction record created for username: $username, transaction ID: $transID");

        $latestRecharge = ORM::for_table('tbl_user_recharges')
            ->where('customer_id', $userId)
            ->order_by_desc('id')
            ->find_one();

        $planData = ORM::for_table('tbl_plans')
            ->where('id', $planId)
            ->find_one();
        $planPrice = $planData ? $planData->price : 0;

        $userData->balance += $amount;
        $userData->save();
        logToFile('paybill.log', "User balance updated for username: $username");

        if ($userData->balance >= $planPrice && $latestRecharge && $latestRecharge->status == 'off') {
            $deleted_count = ORM::for_table('tbl_user_recharges')
                ->where('username', $username)
                ->delete_many();
            logToFile('paybill.log', "Deleted $deleted_count recharges for username: $username");

            $userData->balance -= $planPrice;
            $userData->save();
            logToFile('paybill.log', "User balance after recharge for username: $username");

            $rechargeResult = Package::rechargeUser($userId, $routerName, $planId, 'Mpesa Paybill Manual', 'Mpesa');
            logToFile('paybill.log', "User recharged: $username, result: " . json_encode($rechargeResult));
        }
    } else {
        logToFile('paybill.log', "User not found for bill reference number: $billRefNumber");

        $transaction = ORM::for_table('tbl_transactions')->create();
        $transaction->invoice = $transID;
        $transaction->username = 'unknown ' . $billRefNumber;
        $transaction->plan_name = 'unknown';
        $transaction->price = $amount;
        $transaction->recharged_on = date("Y-m-d");
        $transaction->recharged_time = date("H:i:s");
        $transaction->expiration = date("Y-m-d H:i:s");
        $transaction->time = date("Y-m-d H:i:s");
        $transaction->method = 'Mpesa Paybill Manual';
        $transaction->routers = 'unknown';
        $transaction->Type = 'Balance';
        $transaction->save();
        logToFile('paybill.log', "Transaction record created for unknown user with bill reference number: $billRefNumber, transaction ID: $transID");
    }
} else {
    logToFile('paybill.log', "Invalid request received: missing transaction ID, amount, or bill reference number");
}
