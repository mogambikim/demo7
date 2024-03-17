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

$captureLogs = file_get_contents("php://input");
$analizzare = json_decode($captureLogs);

$transID = $analizzare->TransID;
$amount = $analizzare->TransAmount;
$billRefNumber = $analizzare->BillRefNumber;

if ($transID !== null && $amount !== null && $billRefNumber !== null) {
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

        if ($userData->balance >= $planPrice && $latestRecharge && $latestRecharge->status == 'off') {
            $deleted_count = ORM::for_table('tbl_user_recharges')
                ->where('username', $username)
                ->delete_many();

            $userData->balance -= $planPrice;
            $userData->save();

            $rechargeResult = Package::rechargeUser($userId, $routerName, $planId, 'Mpesa Paybill Manual', 'Mpesa');
        }
    } else {
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
        $transaction->routers = 'uknown';
        $transaction->Type = 'Balance';
        $transaction->save();
    }
}