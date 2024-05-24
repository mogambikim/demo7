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

// second_update.php
$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

$receivedTimestamp = date('Y-m-d H:i:s');
file_put_contents('back2.log', "Received callback data in second_update.php at " . $receivedTimestamp . ":\n" . $captureLogs . "\n", FILE_APPEND);

sleep(10);

$processingTimestamp = date('Y-m-d H:i:s');

$response_code = $analizzare->Body->stkCallback->ResultCode;
$resultDesc = $analizzare->Body->stkCallback->ResultDesc;
$merchant_req_id = $analizzare->Body->stkCallback->MerchantRequestID;
$checkout_req_id = $analizzare->Body->stkCallback->CheckoutRequestID;

$amount_paid = $analizzare->Body->stkCallback->CallbackMetadata->Item[0]->Value;
$mpesa_code = $analizzare->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$sender_phone = $analizzare->Body->stkCallback->CallbackMetadata->Item[4]->Value;

$logMessage = "Processing data for Transaction ID: " . $mpesa_code . " started at " . $processingTimestamp . "\n";
file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

$PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
    ->where('checkout', $checkout_req_id)
    ->order_by_desc('id')
    ->find_one();

if (!$PaymentGatewayRecord) {
    $logMessage = "Payment Gateway Record not found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
    exit();
}

$logMessage = "Payment Gateway Record found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Payment Gateway Record Details:\n";
$logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
$logMessage .= "Username: " . $PaymentGatewayRecord->username . "\n";
$logMessage .= "Plan ID: " . $PaymentGatewayRecord->plan_id . "\n";
$logMessage .= "Checkout: " . $PaymentGatewayRecord->checkout . "\n";
file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

$uname = $PaymentGatewayRecord->username;
$plan_id = $PaymentGatewayRecord->plan_id;

$userid = ORM::for_table('tbl_customers')
    ->where('username', $uname)
    ->order_by_desc('id')
    ->find_one();

if (!$userid) {
    $logMessage = "User not found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
    exit();
}

$logMessage = "User found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "User Details:\n";
$logMessage .= "ID: " . $userid->id . "\n";
$logMessage .= "Username: " . $userid->username . "\n";
$logMessage .= "Full Name: " . $userid->fullname . "\n";
$logMessage .= "Phone Number: " . $userid->phonenumber . "\n";
file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

$userid->username = $uname;
$userid->save();

$logMessage = "User updated for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Updated User Details:\n";
$logMessage .= "ID: " . $userid->id . "\n";
$logMessage .= "Username: " . $userid->username . "\n";
file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

$plans = ORM::for_table('tbl_plans')
    ->where('id', $plan_id)
    ->order_by_desc('id')
    ->find_one();

if (!$plans) {
    $logMessage = "Plan not found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
    exit();
}

$logMessage = "Plan found for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "Plan Details:\n";
$logMessage .= "ID: " . $plans->id . "\n";
$logMessage .= "Name: " . $plans->name_plan . "\n";
$logMessage .= "Type: " . $plans->type . "\n";
$logMessage .= "Price: " . $plans->price . "\n";
file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

if ($response_code == "0") {
    try {
        ORM::get_db()->beginTransaction();

        $existingTransaction = ORM::for_table('tbl_payment_gateway')
            ->where('gateway_trx_id', $mpesa_code)
            ->find_one();

        if ($existingTransaction) {
            $logMessage = "Transaction already exists for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . ". Updating data.\n";
            file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

            $existingTransaction->paid_date = date('Y-m-d H:i:s');
            $existingTransaction->save();

            $logMessage = "Transaction updated for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
            $logMessage .= "Updated Transaction Details:\n";
            $logMessage .= "ID: " . $existingTransaction->id . "\n";
            $logMessage .= "Username: " . $existingTransaction->username . "\n";
            $logMessage .= "Plan ID: " . $existingTransaction->plan_id . "\n";
            $logMessage .= "Gateway Transaction ID: " . $existingTransaction->gateway_trx_id . "\n";
            $logMessage .= "Paid Date: " . $existingTransaction->paid_date . "\n";
            file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
        } else {
            $logMessage = "Creating new transaction for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
            file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

            $now = date('Y-m-d H:i:s');
            $date = date('Y-m-d');
            $time = date('H:i:s');

            $plan_type = $plans->type;
            $UserId = $userid->id;

            if ($plan_type == "Hotspot") {
                $plan_id = $plans->id;
                $validity = $plans->validity;
                $units = $plans->validity_unit;

                $unit_in_seconds = [
                    'Mins' => 60,
                    'Hrs' => 3600,
                    'Days' => 86400,
                    'Months' => 2592000 // Assuming 30 days per month for simplicity
                ];

                $unit_seconds = $unit_in_seconds[$units];
                $expiry_timestamp = time() + ($validity * $unit_seconds);
                $expiry_date = date("Y-m-d", $expiry_timestamp);
                $expiry_time = date("H:i:s", $expiry_timestamp);

                $recharged_on = date("Y-m-d");
                $recharged_time = date("H:i:s");

                $logMessage = "Updating user recharges for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                $updated_count = ORM::for_table('tbl_user_recharges')
                    ->where('username', $uname)
                    ->find_many();

                foreach ($updated_count as $record) {
                    $record->status = 'on';
                    $record->save();

                    $logMessage = "User recharge updated for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                    $logMessage .= "Updated User Recharge Details:\n";
                    $logMessage .= "ID: " . $record->id . "\n";
                    $logMessage .= "Username: " . $record->username . "\n";
                    $logMessage .= "Plan ID: " . $record->plan_id . "\n";
                    $logMessage .= "Status: " . $record->status . "\n";
                    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                }

                $plan_name = $plans->name_plan;
                $routerId = $PaymentGatewayRecord->routers_id;

                $file_path = 'system/adduser.php';
                include_once $file_path;

                $rname = ORM::for_table('tbl_routers')
                    ->where('id', $routerId)
                    ->find_one();

                $routername = $rname->name;

                $logMessage = "Deleting existing user recharges for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                ORM::for_table('tbl_user_recharges')
                    ->where('username', $uname)
                    ->delete_many();

                $logMessage = "Creating new user recharge for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                $userRecharge = ORM::for_table('tbl_user_recharges')->create(array(
                    'customer_id' => $UserId,
                    'username' => $uname,
                    'plan_id' => $plan_id,
                    'namebp' => $plan_name,
                    'recharged_on' => $recharged_on,
                    'recharged_time' => $recharged_time,
                    'expiration' => $expiry_date,
                    'time' => $expiry_time,
                    'status' => "on",
                    'method' => $PaymentGatewayRecord->gateway . "-" . $mpesa_code,
                    'routers' => $routername,
                    'type' => $plan_type
                ))->save();

                $logMessage = "New user recharge created for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                $logMessage .= "New User Recharge Details:\n";
                $logMessage .= "ID: " . $userRecharge->id . "\n";
                $logMessage .= "Customer ID: " . $userRecharge->customer_id . "\n";
                $logMessage .= "Username: " . $userRecharge->username . "\n";
                $logMessage .= "Plan ID: " . $userRecharge->plan_id . "\n";
                $logMessage .= "Name: " . $userRecharge->namebp . "\n";
                $logMessage .= "Recharged On: " . $userRecharge->recharged_on . "\n";
                $logMessage .= "Recharged Time: " . $userRecharge->recharged_time . "\n";
                $logMessage .= "Expiration: " . $userRecharge->expiration . "\n";
                $logMessage .= "Time: " . $userRecharge->time . "\n";
                $logMessage .= "Status: " . $userRecharge->status . "\n";
                $logMessage .= "Method: " . $userRecharge->method . "\n";
                $logMessage .= "Routers: " . $userRecharge->routers . "\n";
                $logMessage .= "Type: " . $userRecharge->type . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                $logMessage = "User recharge status set to 'on' for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                $logMessage = "Creating new transaction for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);

                $transaction = ORM::for_table('tbl_transactions')->create(array(
                    'invoice' => $mpesa_code,
                    'username' => $uname,
                    'plan_name' => $plan_name,
                    'price' => $amount_paid,
                    'recharged_on' => $recharged_on,
                    'recharged_time' => $recharged_time,
                    'expiration' => $expiry_date,
                    'time' => $expiry_time,
                    'method' => $PaymentGatewayRecord->gateway . "-" . $mpesa_code,
                    'routers' => $routername,
                    'type' => $plan_type
                ))->save();

                $logMessage = "New transaction created for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                $logMessage .= "New Transaction Details:\n";
                $logMessage .= "ID: " . $transaction->id . "\n";
                $logMessage .= "Invoice: " . $transaction->invoice . "\n";
                $logMessage .= "Username: " . $transaction->username . "\n";
                $logMessage .= "Plan Name: " . $transaction->plan_name . "\n";
                $logMessage .= "Price: " . $transaction->price . "\n";
                $logMessage .= "Recharged On: " . $transaction->recharged_on . "\n";
                $logMessage .= "Recharged Time: " . $transaction->recharged_time . "\n";
                $logMessage .= "Expiration: " . $transaction->expiration . "\n";
                $logMessage .= "Time: " . $transaction->time . "\n";
                $logMessage .= "Method: " . $transaction->method . "\n";
                $logMessage .= "Routers: " . $transaction->routers . "\n";
                $logMessage .= "Type: " . $transaction->type . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                
                $PaymentGatewayRecord->status = 2;
                $PaymentGatewayRecord->paid_date = $now;
                $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                $PaymentGatewayRecord->save();
                
                $logMessage = "Payment Gateway Record updated for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                $logMessage .= "Updated Payment Gateway Record Details:\n";
                $logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
                $logMessage .= "Status: " . $PaymentGatewayRecord->status . "\n";
                $logMessage .= "Paid Date: " . $PaymentGatewayRecord->paid_date . "\n";
                $logMessage .= "Gateway Transaction ID: " . $PaymentGatewayRecord->gateway_trx_id . "\n";
                file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                            }
                        }
                
                        ORM::get_db()->commit();
                        $logMessage = "Transaction processed successfully for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                        file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                    } catch (Exception $e) {
                        ORM::get_db()->rollBack();
                        $logMessage = "Error processing transaction for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . ". Error: " . $e->getMessage() . "\n";
                        file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                        exit();
                    }
                }
                
                if ($response_code == "1032") {
                    $now = date('Y-m-d H:i:s');
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->status = 4;
                    $PaymentGatewayRecord->save();
                
                    $logMessage = "Transaction canceled by user for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                    $logMessage .= "Updated Payment Gateway Record Details:\n";
                    $logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
                    $logMessage .= "Paid Date: " . $PaymentGatewayRecord->paid_date . "\n";
                    $logMessage .= "Status: " . $PaymentGatewayRecord->status . "\n";
                    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                
                    exit();
                }
                
                if ($response_code == "1037") {
                    $PaymentGatewayRecord->status = 1;
                    $PaymentGatewayRecord->pg_paid_response = 'User failed to enter pin';
                    $PaymentGatewayRecord->save();
                
                    $logMessage = "User failed to enter PIN for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                    $logMessage .= "Updated Payment Gateway Record Details:\n";
                    $logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
                    $logMessage .= "Status: " . $PaymentGatewayRecord->status . "\n";
                    $logMessage .= "Response: " . $PaymentGatewayRecord->pg_paid_response . "\n";
                    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                
                    exit();
                }
                
                if ($response_code == "1") {
                    $PaymentGatewayRecord->status = 1;
                    $PaymentGatewayRecord->pg_paid_response = 'Not enough balance';
                    $PaymentGatewayRecord->save();
                
                    $logMessage = "Not enough balance for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                    $logMessage .= "Updated Payment Gateway Record Details:\n";
                    $logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
                    $logMessage .= "Status: " . $PaymentGatewayRecord->status . "\n";
                    $logMessage .= "Response: " . $PaymentGatewayRecord->pg_paid_response . "\n";
                    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                
                    exit();
                }
                
                if ($response_code == "2001") {
                    $PaymentGatewayRecord->status = 1;
                    $PaymentGatewayRecord->pg_paid_response = 'Wrong Mpesa pin';
                    $PaymentGatewayRecord->save();
                
                    $logMessage = "Wrong M-Pesa PIN for Transaction ID: " . $mpesa_code . " at " . date('Y-m-d H:i:s') . "\n";
                    $logMessage .= "Updated Payment Gateway Record Details:\n";
                    $logMessage .= "ID: " . $PaymentGatewayRecord->id . "\n";
                    $logMessage .= "Status: " . $PaymentGatewayRecord->status . "\n";
                    $logMessage .= "Response: " . $PaymentGatewayRecord->pg_paid_response . "\n";
                    file_put_contents('secondupdate.log', $logMessage, FILE_APPEND);
                
                    exit();
                }