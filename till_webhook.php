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

// Retrieve the SMS URL from the tbl_appconfig table
$smsUrlConfig = ORM::for_table('tbl_appconfig')->where('setting', 'sms_url')->find_one();
if ($smsUrlConfig) {
    $config['sms_url'] = $smsUrlConfig->value;
} else {
    // Set a default SMS URL if not found in the database
    $config['sms_url'] = 'https://example.com/sms/send?api=YOUR_API_KEY&SenderId=YOUR_SENDER_ID&msg=[text]&phone=[number]';
}

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

// Log the input
file_put_contents('back.log', $captureLogs, FILE_APPEND);

// Access the data
$transID = $analizzare->TransID;
$transTime = $analizzare->TransTime;
$amount = $analizzare->Amount;
$businessShortCode = $analizzare->ShortCode;
$msisdn = $analizzare->Msisdn;

$logData = "TransID: $transID\nTransTime: $transTime\nAmount: $amount\nBusinessShortCode: $businessShortCode\nInvoiceNumber: $invoiceNumber\nOrgAccountBalance: $orgAccountBalance\nThirdPartyTransID: $thirdPartyTransID\nMSISDN: $msisdn\nFirst Name: $firstName\nMiddle Name: $middleName\nLast Name: $lastName\n";

file_put_contents('capture.txt', $logData, FILE_APPEND);

if ($transID !== null && $amount !== null && $msisdn !== null) {
    $phoneNumberLast9Digits = substr($msisdn, -9);

    // Use the ORM to query the database
    $userData = ORM::for_table('tbl_customers')
        ->where_like('phonenumber', "%$phoneNumberLast9Digits")
        ->find_one();

    // ... rest of your code ...


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
    
// Create a new record in tbl_payment_gateway
$paymentGatewayRecord = ORM::for_table('tbl_payment_gateway')->create();

$paymentGatewayRecord->username = $username;
$paymentGatewayRecord->gateway = 'Till Number Manual';
$paymentGatewayRecord->gateway_trx_id = $transID; // Use $transID instead of $reference
$paymentGatewayRecord->checkout = $transID; // Use $transID instead of $id
$paymentGatewayRecord->plan_id = $planId;
$paymentGatewayRecord->plan_name = $planName;
$paymentGatewayRecord->routers_id = $routerId;
$paymentGatewayRecord->routers = $routerName;
$paymentGatewayRecord->price = $amount;
$paymentGatewayRecord->pg_url_payment = 'till.freeispradius.com';
$paymentGatewayRecord->payment_method = 'Till Number';
$paymentGatewayRecord->payment_channel = 'Till Number';
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
   $transaction->method = 'Mpesa Till Manual';
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
       $rechargeResult = Package::rechargeUser($userId, $routerName, $planId, 'Mpesa Till Manual', 'Mpesa Till');
   
       if (!$rechargeResult) {
           // Handle failure
       }
   }
   } else {
   
        // Save transaction data to tbl_transactions
   $transaction = ORM::for_table('tbl_transactions')->create();
   $transaction->invoice = $transID;
   $transaction->username = 'unknown ' . $msisdn;
   $transaction->plan_name = 'unknown';
   $transaction->price = $amount;
   $transaction->recharged_on = date("Y-m-d"); // Use current date
   $transaction->recharged_time = date("H:i:s"); // Use current time
   $transaction->expiration = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->time = date("Y-m-d H:i:s"); // Use current date-time
   $transaction->method = 'Mpesa Till Manual';
   $transaction->routers = 'uknown';
   $transaction->Type = 'Balance';
   $transaction->save();
   try {
    // Load the notifications from the JSON file
    $notifications = json_decode(file_get_contents('system/uploads/notifications.json'), true);

    if (isset($notifications['custom_message'])) {
        $customMessage = $notifications['custom_message'];
    
        $customMessage = str_replace('[[amount]]', $amount, $customMessage);
        $customMessage = str_replace('[[phone]]', $decodedPhoneNumber, $customMessage);
    
        $result = Message::sendUnknownPayment($decodedPhoneNumber, $amount, $customMessage, 'sms');
    } else {
    }
    } catch (Exception $e) {
    }
   }
}
   