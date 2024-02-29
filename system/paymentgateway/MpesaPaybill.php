<?php

/**

 *
 **/
function MpesaPaybill_validate_config()
{
    global $config;
    if (empty($config['mpesa_consumer_key']) || empty($config['mpesa_consumer_secret'])) {
        sendTelegram("M-Pesa payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup M-Pesa payment gateway, please tell admin"));
    }
}

function MpesaPaybill_show_config()
{
    global $ui, $config;
    $ui->assign('env', json_decode(file_get_contents('system/paymentgateway/mpesa_env.json'), true));
    $ui->assign('_title', 'M-Pesa - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('mpesa.tpl');
}

function MpesaPaybill_save_config()
{
    global $admin, $_L;
    $mpesa_paybill = _post('mpesa_paybill');
  

    
     $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_paybill')->find_one();
    if ($d) {
        $d->value = $mpesa_paybill;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'mpesa_paybill';
        $d->value = $mpesa_paybill;
        $d->save();
    }
   


    _log('[' . $admin['username'] . ']: M-Pesa ' . $_L['Settings_Saved_Successfully'] . json_encode($_POST['mpesa_channel']), 'Admin', $admin['id']);

    r2(U . 'paymentgateway/MpesaPaybill', 's', $_L['Settings_Saved_Successfully']);
}



function MpesaPaybill_create_transaction($trx, $user )
{
    
    
  $url=(U. "plugin/initiatePaybillStk");
    
     $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    $d->gateway_trx_id = '';
    $d->payment_method = 'Mpesa Paybill STK';
    $d->pg_url_payment = $url;
    $d->pg_request = '';
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));

    die();
    
    
    
    
    

}



function MpesaPaybill_payment_notification()
{
    $captureLogs = file_get_contents("php://input");
       
    $analizzare = json_decode($captureLogs);
///  sleep(10);
  file_put_contents('back.log',$captureLogs,FILE_APPEND);
  $response_code   = $analizzare->Body->stkCallback->ResultCode;
    $resultDesc      = ($analizzare->Body->stkCallback->ResultDesc);
    $merchant_req_id = ($analizzare->Body->stkCallback->MerchantRequestID);
    $checkout_req_id = ($analizzare->Body->stkCallback->CheckoutRequestID);
    
    
        $amount_paid     = ($analizzare->Body->stkCallback->CallbackMetadata->Item['0']->Value);//get the amount value
         $mpesa_code      = ($analizzare->Body->stkCallback->CallbackMetadata->Item['1']->Value);//mpesa transaction code..
         $sender_phone    = ($analizzare->Body->stkCallback->CallbackMetadata->Item['4']->Value);//Telephone Number
        
        
        
       
        
        
        $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $checkout_req_id)
        ->where('status', 1) // Add this line to filter by status
        ->order_by_desc('id')
        ->find_one();

        $uname=$PaymentGatewayRecord->username;
        
        $user=$PaymentGatewayRecord;


        $userid = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->order_by_desc('id')
        ->find_one();

       $userid->username=$uname;
       $userid->save();


       



















        $UserId=$userid->id;
        
        
        
       if ($response_code=="1032")
         {
         $now = date('Y-m-d H:i:s');   
        $PaymentGatewayRecord->paid_date = $now;
        $PaymentGatewayRecord->status = 4;
        $PaymentGatewayRecord->save();
        
        exit();
            
         }
         
         
       
         
        if($response_code=="1037"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'User failed to enter pin';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
         if($response_code=="1"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'Not enough balance';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
        
           if($response_code=="2001"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'Wrong Mpesa pin';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
          if($response_code=="0"){
             
               $now = date('Y-m-d H:i:s');
               
                  $date = date('Y-m-d');
                  $time= date('H:i:s');








                  if (!Package::rechargeUser($UserId, $user['routers'], $user['plan_id'], $user['gateway'], $mpesa_code)){






                    $PaymentGatewayRecord->status = 2;
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->save();
                     

                   

                     
                     $username=$PaymentGatewayRecord->username;
                       
                  // Save transaction data to tbl_transactions
                 $transaction = ORM::for_table('tbl_transactions')->create();
                 $transaction->invoice = $mpesa_code;
                 $transaction->username = $PaymentGatewayRecord->username;
                 $transaction->plan_name = $PaymentGatewayRecord->plan_name;
                 $transaction->price = $amount_paid;
                 $transaction->recharged_on = $date;
                 $transaction->recharged_time = $time;
                 $transaction->expiration = $now;
                 $transaction->time = $now;
                $transaction->method = $PaymentGatewayRecord->payment_method;
                $transaction->routers = 0;
                $transaction->Type = 'Balance';
                $transaction->save();


                  } else{


               //lets update tbl_recharges
/*
               $transaction = ORM::for_table('tbl_transactions')->create();
               $transaction->invoice = $mpesa_code;
               $transaction->username = $PaymentGatewayRecord->username;
               $transaction->plan_name = $PaymentGatewayRecord->plan_name;
               $transaction->price = $amount_paid;
               $transaction->recharged_on = $date;
               $transaction->recharged_time = $time;
               $transaction->expiration = $now;
               $transaction->time = $now;
                $transaction->method = $PaymentGatewayRecord->payment_method;
                $transaction->routers = 0;
               $transaction->Type = $PaymentGatewayRecord->routers;
               $transaction->save();

*/
                        



                    $PaymentGatewayRecord->status = 2;
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->save();


                  }











              /*
              
              
                  $checkid = ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->find_one();
              
              
              
              
              
              $customerid=$checkid->id;
              
              
              
              
              
              
              
             $recharge = ORM::for_table('tbl_user_recharges')->create();
             $recharge->customer_id = $customerid;
             $recharge->username = $PaymentGatewayRecord->username;
             $recharge->plan_id = $PaymentGatewayRecord->plan_id;
             $recharge->price = $amount_paid;
             $recharge->recharged_on = $date;
             $recharge->recharged_time = $time;
             $recharge->expiration = $now;
              $recharge->time = $now;
              $recharge->method = $PaymentGatewayRecord->payment_method;
             $recharge->routers = 0;
             $recharge->Type = 'Balance';
            $recharge->save();
              
              
              
              */
              
              
              
              
               $user = ORM::for_table('tbl_customers')
              ->where('username', $username)
               ->find_one();
              
               $currentBalance = $user->balance;
              
                $user->balance = $currentBalance + $amount_paid;
                $user->save();
              
               exit();
             
             
             
         }
        
            
         
            
            
}


function MpesaPaybill_get_status($trx, $user)
{
    global $config, $routes;
    function MpesaPaybill_get_status($trx, $user)
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
