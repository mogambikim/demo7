<?php

/**

 *
 **/
function mpesa_validate_config()
{
    global $config;
    if (empty($config['mpesa_consumer_key']) || empty($config['mpesa_consumer_secret'])) {
        sendTelegram("M-Pesa payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup M-Pesa payment gateway, please tell admin"));
    }
}

function mpesa_show_config()
{
    global $ui, $config;
    $ui->assign('env', json_decode(file_get_contents('system/paymentgateway/mpesa_env.json'), true));
    $ui->assign('_title', 'M-Pesa - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('mpesa.tpl');
}

function mpesa_save_config()
{
    global $admin, $_L;
    $mpesa_consumer_key = _post('mpesa_consumer_key');
    $mpesa_consumer_secret = _post('mpesa_consumer_secret');
    $mpesa_business_code = _post('mpesa_business_code');
    $mpesa_pass_key = _post('mpesa_pass_key');
    $mpesa_env = _post('mpesa_env');
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_consumer_key')->find_one();
    if ($d) {
        $d->value = $mpesa_consumer_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_consumer_key';
        $d->value = $mpesa_consumer_key;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_consumer_secret')->find_one();
    if ($d) {
        $d->value = $mpesa_consumer_secret;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_consumer_secret';
        $d->value = $mpesa_consumer_secret;
        $d->save();
    }

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_business_code')->find_one();
    if ($d) {
        $d->value = $mpesa_business_code;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_business_code';
        $d->value = $mpesa_business_code;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_pass_key')->find_one();
    if ($d) {
        $d->value = $mpesa_pass_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_pass_key';
        $d->value = $mpesa_pass_key;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_env')->find_one();
    if ($d) {
        $d->value = $mpesa_env;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_env';
        $d->value = $mpesa_env;
        $d->save();
    }

    _log('[' . $admin['username'] . ']: M-Pesa ' . Lang::T('Settings Saved Successfully') . json_encode($_POST['mpesa_channel']), 'Admin', $admin['id']);

    r2(U . 'paymentgateway/mpesa', 's', Lang::T('Settings Saved Successfully'));
}


function mpesa_create_transaction($trx, $user)
{
    global $config, $routes;

    //select evironment
    $environment = $config['mpesa_env'];
    $consumer_key = $config['mpesa_consumer_key'];
    $consumer_secret = $config['mpesa_consumer_secret'];
    $Business_Code = $config['mpesa_business_code'];
    $Passkey = $config['mpesa_pass_key'];
    $Type_of_Transaction = 'CustomerPayBillOnline';
    $phone_number = $user['phonenumber'];
    $total_amount = $trx['price'];
    //lets assume this is our callBack url
    //$CallBackURL = 'http://alwayson.com.ng/pfreeispardiusl/index.php?_route=callback/mpesa';
    $CallBackURL = U . 'callback/mpesa';
    $Time_Stamp = date("Ymdhis");
    //password required by api
    $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
    //depend on which environment selected
    if ($environment == "live") {
        $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } elseif ($environment == "sandbox") {
        $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } else {
        return json_encode(["Message" => "invalid application status"]);
    };
    //lets Create access_token as per required
    $curl_Tranfer = curl_init();
    curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
    $curl_Tranfer_response = curl_exec($curl_Tranfer);
    $token = json_decode($curl_Tranfer_response)->access_token;
    // die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $curl_Tranfer2 = curl_init();
    curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
    curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
    //lets initiate a Transaction
    $curl_Tranfer2_post_data = [
        'BusinessShortCode' => $Business_Code,
        'Password' => $password,
        'Timestamp' => $Time_Stamp,
        'TransactionType' => $Type_of_Transaction,
        'Amount' => $trx['price'],
        'PartyA' => $phone_number,
        'PartyB' => $Business_Code,
        'PhoneNumber' => $phone_number,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => $phone_number,
        'TransactionDesc' => $trx['plan_name']
        //'Trxid' => $trx['id'],
        //'AmountToPay' => $trx['price'],
    ];
    //lest check the data that we want to send for error
    //die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $data2_string = json_encode($curl_Tranfer2_post_data);
    curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
    curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));
    $status = $curl_Tranfer2_response->ResponseCode;
    //die(json_encode($curl_Tranfer2_response,JSON_PRETTY_PRINT));
    //die(json_encode($status,JSON_PRETTY_PRINT));

    /**
     * {
     *  "MerchantRequestID": "26309-129914760-1",
     *  "CheckoutRequestID": "ws_CO_22092023134957453718167262",
     *  "ResponseCode": "0", "ResponseDescription": "Success. Request accepted for processing",
     *  "CustomerMessage": "Success. Request accepted for processing"
     * }
     *
     *
     **/
    if ($status == 1) {
        sendTelegram("M-Pesa payment failed\n\n" . json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT));
        r2(U . 'order/package', 'e', Lang::T("Failed to create transaction."));
    }
    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    $d->gateway_trx_id = $curl_Tranfer2_response->CheckoutRequestID;
    //lets use this table "pg_url_payment" to save Timestamp because we will need it later to verify transaction
    $d->pg_url_payment = $Time_Stamp;
    $d->pg_request = $user['id'];
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please check your phone to process payment"));
}


function mpesa_payment_notification()
{

    global $config;
    //responce from mpesa server after payment is initiated and processed
    $callbackJSONData = file_get_contents('php://input');
    $callbackData = json_decode($callbackJSONData);
    $resultCode = $callbackData->Body->stkCallback->ResultCode;
    $resultDesc = $callbackData->Body->stkCallback->ResultDesc;
    $merchantRequestID = $callbackData->Body->stkCallback->MerchantRequestID;
    $checkoutRequestID = $callbackData->Body->stkCallback->CheckoutRequestID;
    $amount = $callbackData->stkCallback->Body->CallbackMetadata->Item[0]->Value;
    $mpesaReceiptNumber = $callbackData->Body->stkCallback->CallbackMetadata->Item[1]->Value;
    $balance = $callbackData->stkCallback->Body->CallbackMetadata->Item[2]->Value;
    $b2CUtilityAccountAvailableFunds = $callbackData->Body->stkCallback->CallbackMetadata->Item[3]->Value;
    $transactionDate = $callbackData->Body->stkCallback->CallbackMetadata->Item[4]->Value;
    $phoneNumber = $callbackData->Body->stkCallback->CallbackMetadata->Item[5]->Value;
    //$trxid = $callbackData->Body->stkCallback->CallbackMetadata->Item[6]->Value;
    //$amountToPay=$callbackData->Body->stkCallback->CallbackMetadata->Item[7]->Value;
    $trx = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $checkoutRequestID)
        ->find_one();
    if (!$trx) {
        return;
    }

    $user = ORM::for_table('tbl_customers')
        ->where('username', $trx['username'])
        ->find_one();
    if (!$trx) {
        return;
    }
    //lets check the status of the Payment sent back from mpesa
    if ($resultDesc == "Confirmation Service not accepted" || $resultCode == 1) {
        $trx->status = 3;
        $trx->save();
        exit();
    }
    //if Payment is confirmed and Successfull
    //lets log the responce from mpesa for audit or any error
    //lets save some value to database

    elseif ($resultDesc == "The service request is processed successfully." && $resultCode == 0 && $trx['status'] != 2) {
        if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'], 'mpesa')) {
            _log('[' . $resultDesc . ']: M-Pesa ' . "Payment Successfull,But Failed to activate your Package" . json_encode($callbackData));
        }
        _log('[' . $resultDesc . ']: M-Pesa ' . "Payment Successfull" . json_encode($callbackData));

        $trx->pg_paid_response = json_encode($callbackData);
        $trx->payment_method = 'M-Pesa';
        $trx->payment_channel = 'M-Pesa StkPush';
        $trx->paid_date = date('Y-m-d H:i:s');
        $trx->status = 2;
        $trx->save();
    } else {
        $trx->status = 1;
        $trx->save();
        exit();
    }
}


function mpesa_get_status($trx, $user)
{
    global $config, $routes;
    function mpesa_get_status($trx, $user)
{
    global $config, $routes;
	
	if ($trx->status == 2){
		r2(U . "order/view/" . $trx['id'], 's', Lang::T("Transaction has been completed."));
		die();
		
	}elseif ($trx->status == 1){


    $environment = $config['mpesa_env'];
    $consumer_key = $config['mpesa_consumer_key'];
    $consumer_secret = $config['mpesa_consumer_secret'];
    $Business_Code = $config['mpesa_business_code'];
    $Passkey = $config['mpesa_pass_key'];
    //Timestamp that we save earlier in pg_url_payment database
    $Time_Stamp = $trx['pg_url_payment'];
    $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
    if ($environment == "live") {
        $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } elseif ($environment == "sandbox") {
        $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
        $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } else {
        return json_encode(["Message" => "invalid application status"]);
    };
    $curl_Tranfer = curl_init();
    curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
    $curl_Tranfer_response = curl_exec($curl_Tranfer);
    $token = json_decode($curl_Tranfer_response)->access_token;
    // die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $curl_Tranfer2 = curl_init();
    curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
    curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
    //lest verify the transaction by sending data to mpesa transaction query portal from nuxbil
    $curl_Tranfer2_post_data = [
        'BusinessShortCode' => $Business_Code,
        'Password' => $password,
        'Timestamp' => $Time_Stamp,
        'CheckoutRequestID' => $trx['gateway_trx_id']
    ];
    //die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $data2_string = json_encode($curl_Tranfer2_post_data);
    curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
    curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));

    //server responce will be
    /**
     * {
     *  "ResponseCode":"0",
     *  "ResponseDescription": "The service request has been accepted successfully",
     * "MerchantRequestID":"22205-34066-1",
     * "CheckoutRequestID": "ws_CO_13012021093521236557",
     * "ResultCode":"0",
     * "ResultDesc":"The service request is processed successfully.",
     *  }
     *
     *
     **/


    $callbackJSONData = file_get_contents('php://input');
    $callbackData = json_decode($callbackJSONData);
    $responseCode = $callbackData->ResponseCode;
    $responseDescription = $callbackData->ResponseDescription;
    $merchantRequestID = $callbackData->MerchantRequestID;
    $checkoutRequestID = $callbackData->CheckoutRequestID;
    $resultCode = $callbackData->ResultCode;
    $resultDesc = $callbackData->ResultDesc;
    //if responce is Failed
    if ($responseDescription === "The service request has failed" || $resultDesc === "Request canceled by the user" ||  $responseCode === 1) {
        r2(U . "order/view/" . $trx['id'], 'w', Lang::T("Transaction still unpaid."));
        //if responce is Successfull, activate the plan or balance
    } elseif (($responseDescription === "The service request has been accepted successfully." || $resultDesc == "The service request is processed successfully"  || $responseCode === 0) && $trx['status'] != 2) {
        if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'],  'M-Pesa')) {
            r2(U . "order/view/" . $trx['id'], 'd', Lang::T("Failed to activate your Package, try again later."));
        }
        _log('[' . $checkoutRequestID . ']: M-Pesa ' . "Payment Successfull" . json_encode($callbackData));
        $trx->pg_paid_response = json_encode($callbackData);
        $trx->payment_method = 'M-Pesa';
        $trx->payment_channel = 'M-Pesa StkPush';
        $trx->paid_date = date('Y-m-d H:i:s');
        $trx->status = 2;
        $trx->save();
        r2(U . "order/view/" . $trx['id'], 's', Lang::T("Transaction has been paid."));
    } else if ($trx['status'] == 2) {
        r2(U . "order/view/" . $trx['id'], 'd', Lang::T("Transaction has been paid.."));
    }
}
}
}
