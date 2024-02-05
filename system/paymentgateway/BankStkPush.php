<?php


function BankStkPush_validate_config()
{
    global $config;
    if (empty($config['Stkbankacc']) || empty($config['Stkbankname']) ) {
        sendTelegram("Bank Stk payment gateway not configured");
        r2(U . 'order/balance', 'w', Lang::T("Admin has not yet setup the payment gateway, please tell admin"));
    }
}

function BankStkPush_show_config()
{
    global $ui, $config;
    //$ui->assign('env', json_decode(file_get_contents('system/paymentgateway/kopokopo_env.json'), true));
    $ui->assign('_title', 'Bank Stk Push - ' . $config['CompanyName']);
    $ui->display('bankstkpush.tpl');
}

function BankStkPush_save_config()
{
    global $admin, $_L;
    $bankacc = _post('account');
    $bankname = _post('bankname');
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'Stkbankacc')->find_one();
    if ($d) {
        $d->value = $bankacc;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'Stkbankacc';
        $d->value = $bankacc;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'Stkbankname')->find_one();
    if ($d) {
        $d->value = $bankname;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'Stkbankname';
        $d->value = $bankname;
        $d->save();
    }

    _log('[' . $admin['username'] . ']: Stk Bank details ' . $_L['Settings_Saved_Successfully'], 'Admin', $admin['id']);

    r2(U . 'paymentgateway/BankStkPush', 's', $_L['Settings_Saved_Successfully']);
}


function BankStkPush_create_transaction($trx, $user )
{
    
    
  $url=(U. "plugin/initiatebankstk");
    
     $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    $d->gateway_trx_id = '';
    $d->payment_method = 'Bank Stk Push';
    $d->pg_url_payment = $url;
    $d->pg_request = '';
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));

    die();
    
    
    
    
    

}



function BankStkPush_payment_notification()
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
