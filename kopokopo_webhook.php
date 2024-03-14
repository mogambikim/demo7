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
function decodePhoneNumber($hash) {
    $url = "https://api.hashback.co.ke/decode";
    $data = array(
        'hash' => $hash,
        'API_KEY' => 'h24620yxCbH8y'
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if ($responseData['ResultCode'] == '0') {
        return $responseData['MSISDN'];
    } else {
        return null;
    }
}

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

// Log the input
file_put_contents('back.log', $captureLogs, FILE_APPEND);

// Access the data
$topic = $analizzare->topic;
$id = $analizzare->id;
$createdAt = $analizzare->created_at;
$eventType = $analizzare->event->type;
$resourceId = $analizzare->event->resource->id;
$amount = $analizzare->event->resource->amount;
$status = $analizzare->event->resource->status;
$system = $analizzare->event->resource->system;
$currency = $analizzare->event->resource->currency;
$reference = $analizzare->event->resource->reference;
$tillNumber = $analizzare->event->resource->till_number;
$originationTime = $analizzare->event->resource->origination_time;
$senderLastName = $analizzare->event->resource->sender_last_name;
$senderFirstName = $analizzare->event->resource->sender_first_name;
$hashedSenderPhone = $analizzare->event->resource->hashed_sender_phone;
$senderPhoneNumber = $analizzare->event->resource->sender_phone_number;

$logData = "Topic: $topic\nID: $id\nCreated At: $createdAt\nEvent Type: $eventType\nResource ID: $resourceId\nAmount: $amount\nStatus: $status\nSystem: $system\nCurrency: $currency\nReference: $reference\nTill Number: $tillNumber\nOrigination Time: $originationTime\nSender Last Name: $senderLastName\nSender First Name: $senderFirstName\nHashed Sender Phone: $hashedSenderPhone\nSender Phone Number: $senderPhoneNumber\n";

file_put_contents('capture.txt', $logData, FILE_APPEND);

if ($reference !== null && $amount !== null && $hashedSenderPhone !== null) {
    $decodedPhoneNumber = decodePhoneNumber($hashedSenderPhone);
    $phoneNumberLast9Digits = substr($decodedPhoneNumber, -9);

    // Use the ORM to query the database
    $userData = ORM::for_table('tbl_customers')
        ->where_like('phonenumber', "%$phoneNumberLast9Digits")
        ->find_one();

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
        $paymentGatewayRecord->gateway = 'Kopokopo Manual';
        $paymentGatewayRecord->gateway_trx_id = $reference;
        $paymentGatewayRecord->checkout = $id;
        $paymentGatewayRecord->plan_id = $planId;
        $paymentGatewayRecord->plan_name = $planName;
        $paymentGatewayRecord->routers_id = $routerId;
        $paymentGatewayRecord->routers = $routerName;
        $paymentGatewayRecord->price = $amount;
        $paymentGatewayRecord->pg_url_payment = 'freeispradius.com';
        $paymentGatewayRecord->payment_method = 'kopokopo';
        $paymentGatewayRecord->payment_channel = 'kopokopo';
        $paymentGatewayRecord->pg_request = '';
        $paymentGatewayRecord->pg_paid_response = '';
        $paymentGatewayRecord->expired_date = date("Y-m-d H:i:s"); 
        $paymentGatewayRecord->created_date = date("Y-m-d H:i:s");
        $paymentGatewayRecord->paid_date = date("Y-m-d H:i:s"); 
        $paymentGatewayRecord->status = '2';
    
        $paymentGatewayRecord->save();

     
$transaction = ORM::for_table('tbl_transactions')->create();
$transaction->invoice = $reference;
$transaction->username = $username;
$transaction->plan_name = $planName;
$transaction->price = $amount;
$transaction->recharged_on = date("Y-m-d"); 
$transaction->recharged_time = date("H:i:s"); 
$transaction->expiration = date("Y-m-d H:i:s"); 
$transaction->time = date("Y-m-d H:i:s"); 
$transaction->method = 'Kopokopo Manual';
$transaction->routers = $routerName;
$transaction->Type = 'Balance';
$transaction->save();


$latestRecharge = ORM::for_table('tbl_user_recharges')
    ->where('customer_id', $userId)
    ->order_by_desc('id')
    ->find_one();


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
    $rechargeResult = Package::rechargeUser($userId, $routerName, $planId, 'Kopokopo Manual', 'kopokopo');

    if (!$rechargeResult) {
        // Handle failure
    }
}
} else {
    // Save transaction data to tbl_transactions
    $transaction = ORM::for_table('tbl_transactions')->create();
    $transaction->invoice = $reference;
    $transaction->username = 'unknown ' . $decodedPhoneNumber;
    $transaction->plan_name = 'unknown';
    $transaction->price = $amount;
    $transaction->recharged_on = date("Y-m-d"); // Use current date
    $transaction->recharged_time = date("H:i:s"); // Use current time
    $transaction->expiration = date("Y-m-d H:i:s"); // Use current date-time
    $transaction->time = date("Y-m-d H:i:s"); // Use current date-time
    $transaction->method = 'Kopokopo Manual';
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
