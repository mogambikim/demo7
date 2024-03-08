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


$filename = "webhook_log.txt";
file_put_contents($filename, date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

// ... rest of your code ...

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

// Log the input
file_put_contents('back.log', $captureLogs, FILE_APPEND);
file_put_contents($filename, date('Y-m-d H:i:s') . " - Received data: $captureLogs\n", FILE_APPEND);

// ... rest of your code ...

// Access the data
$transID = $analizzare->TransId;
$amount = $analizzare->Amount;
$businessShortCode = $analizzare->ShortCode;
$msisdn = $analizzare->Msisdn;
$billRefNumber = $analizzare->BillRefNumber;

file_put_contents($filename, date('Y-m-d H:i:s') . " - transID: $transID, amount: $amount, billRefNumber: $billRefNumber\n", FILE_APPEND);

if ($transID !== null && $amount !== null && $billRefNumber !== null) {
    // Use the ORM to query the database
    $userData = ORM::for_table('tbl_customers')
        ->where('username', $billRefNumber)
        ->find_one();

    // Add debug logging
    if ($userData) {
        file_put_contents($filename, date('Y-m-d H:i:s') . " - Found user with username: $billRefNumber\n", FILE_APPEND);
    } else {
        file_put_contents($filename, date('Y-m-d H:i:s') . " - No user found with username: $billRefNumber\n", FILE_APPEND);
    }

    if ($userData) {
        $username = $userData->username;
        $type = $userData->service_type;
        $routerId = $userData->router_id; // Fetch the router_id
        $userId = $userData->id;  // Fetch user id
    
        file_put_contents($filename, date('Y-m-d H:i:s') . " - User $username router ID: $routerId\n", FILE_APPEND);
    
        // Fetch router name
        $routerData = ORM::for_table('tbl_routers')
            ->where('id', $routerId)
            ->find_one();
        $routerName = $routerData ? $routerData->name : 'test router';
    
        // Fetch plan id and name
        $planData = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->order_by_desc('id')
            ->find_one();
        $planId = $planData ? $planData->plan_id : 1;
        $planName = $planData ? $planData->namebp : 'test';
    
        // Create a new record in tbl_payment_gateway
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
        $paymentGatewayRecord->expired_date = date("Y-m-d H:i:s"); // Use current date-time
        $paymentGatewayRecord->created_date = date("Y-m-d H:i:s"); // Use current date-time
        $paymentGatewayRecord->paid_date = date("Y-m-d H:i:s"); // Use current date-time
        $paymentGatewayRecord->status = '2';
    
        $paymentGatewayRecord->save();

     
   // Save transaction data to tbl_transactions
   $transaction = ORM::for_table('tbl_transactions')->create();
   $transaction->invoice = $transID;
   $transaction->username = $username;
   $transaction->plan_name = $planName;
   $transaction->price = $amount;
   $transaction->recharged_on = date("Y-m-d"); // Use current date
   $transaction->recharged_time = date("H:i:s"); // Use current time
   $transaction->expiration = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->time = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->method = 'Mpesa Paybill Manual';
   $transaction->routers = $routerName;
   $transaction->Type = 'Balance';
   $transaction->save();
   // ... rest of your code ...
   
   // Fetch the latest recharge record for the user
   $latestRecharge = ORM::for_table('tbl_user_recharges')
       ->where('customer_id', $userId)
       ->order_by_desc('id')
       ->find_one();
   
   // Fetch the plan price
   $planData = ORM::for_table('tbl_plans')
       ->where('id', $planId)
       ->find_one();
   $planPrice = $planData ? $planData->price : 0;
   
   // Add the amount to the user's balance
   $userData->balance += $amount;
   $userData->save(); // Save the new balance
   
   // Check if the user's balance is enough for the recharge and the latest recharge status is 'off'
   if ($userData->balance >= $planPrice && $latestRecharge && $latestRecharge->status == 'off') {
       // Delete existing records
       $deleted_count = ORM::for_table('tbl_user_recharges')
           ->where('username', $username)
           ->delete_many();
   
       // Deduct the plan price from the user's balance
       $userData->balance -= $planPrice;
       $userData->save();
   
       // Then recharge the user
       $rechargeResult = Package::rechargeUser($userId, $routerName, $planId, 'Mpesa Paybill Manual', 'Mpesa');
   
       if (!$rechargeResult) {
           // Handle failure
       }
   }
   } else {
   
        // Save transaction data to tbl_transactions
   $transaction = ORM::for_table('tbl_transactions')->create();
   $transaction->invoice = $transID;
   $transaction->username = 'unknown ' . $billRefNumber;
   $transaction->plan_name = 'unknown';
   $transaction->price = $amount;
   $transaction->recharged_on = date("Y-m-d"); // Use current date
   $transaction->recharged_time = date("H:i:s"); // Use current time
   $transaction->expiration = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->time = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->method = 'Mpesa Paybill Manual';
   $transaction->routers = 'uknown';
   $transaction->Type = 'Balance';
   $transaction->save();
       // User not found, handle the error
       file_put_contents($filename, date('Y-m-d H:i:s') . " - User with phone number $billRefNumber not found\n", FILE_APPEND);
   }
   }
   
?>