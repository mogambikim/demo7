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

    _log('[' . $admin['username'] . ']: Stk Bank details ' . Lang::T('Settings Saved Successfully'), 'Admin', $admin['id']);

    r2(U . 'paymentgateway/BankStkPush', 's', Lang::T('Settings Saved Successfully'));
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

function BankStkPush_payment_notification()
{
    $captureLogs = file_get_contents("php://input");
    $analizzare = json_decode($captureLogs);

    // Log the received callback data in back.log file
    file_put_contents('back.log', $captureLogs, FILE_APPEND);

    // Send the callback data to second_update.php using cURL asynchronously
    $url = APP_URL . '/second_update.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $captureLogs);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_exec($ch);
    curl_close($ch);

 /*   // Send the callback data to third_update.php using cURL asynchronously
$url_third = APP_URL . '/third_update.php';
$ch_third = curl_init($url_third);
curl_setopt($ch_third, CURLOPT_POST, true);
curl_setopt($ch_third, CURLOPT_POSTFIELDS, $captureLogs);
curl_setopt($ch_third, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch_third, CURLOPT_TIMEOUT, 1);
curl_exec($ch_third);
curl_close($ch_third);

// Send the callback data to final_update.php using cURL asynchronously
$url_final = APP_URL . '/final_update.php';
$ch_final = curl_init($url_final);
curl_setopt($ch_final, CURLOPT_POST, true);
curl_setopt($ch_final, CURLOPT_POSTFIELDS, $captureLogs);
curl_setopt($ch_final, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch_final, CURLOPT_TIMEOUT, 1);
curl_exec($ch_final);
curl_close($ch_final);*/

// Send the callback data to transactions.php using cURL asynchronously
$url_transactions = APP_URL . '/transactions.php';
$ch_transactions = curl_init($url_transactions);
curl_setopt($ch_transactions, CURLOPT_POST, true);
curl_setopt($ch_transactions, CURLOPT_POSTFIELDS, $captureLogs);
curl_setopt($ch_transactions, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch_transactions, CURLOPT_TIMEOUT, 1);
curl_exec($ch_transactions);
curl_close($ch_transactions);

    $response_code   = $analizzare->Body->stkCallback->ResultCode;
    $resultDesc      = ($analizzare->Body->stkCallback->ResultDesc);
    $merchant_req_id = ($analizzare->Body->stkCallback->MerchantRequestID);
    $checkout_req_id = ($analizzare->Body->stkCallback->CheckoutRequestID);
    
    
        $amount_paid     = ($analizzare->Body->stkCallback->CallbackMetadata->Item['0']->Value);//get the amount value
         $mpesa_code      = ($analizzare->Body->stkCallback->CallbackMetadata->Item['1']->Value);//mpesa transaction code..
         $sender_phone    = ($analizzare->Body->stkCallback->CallbackMetadata->Item['4']->Value);//Telephone Number
        
        
        
       
       
        
        $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $checkout_req_id)
      //  ->where('status', 1) // Add this line to filter by status
        ->order_by_desc('id')
        ->find_one();

        $uname=$PaymentGatewayRecord->username;
        
        
            $plan_id=$PaymentGatewayRecord->plan_id;
        
        
        $mac_address=$PaymentGatewayRecord->mac_address;
        
        $user=$PaymentGatewayRecord;


        $userid = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->order_by_desc('id')
        ->find_one();

       $userid->username=$uname;
       $userid->save();


       

  $plans = ORM::for_table('tbl_plans')
        ->where('id', $plan_id)
        
        ->order_by_desc('id')
        ->find_one();







  
        
        
       
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
            $existingTransaction = ORM::for_table('tbl_payment_gateway')
                ->where('gateway_trx_id', $mpesa_code)
                ->find_one();
        
            if ($existingTransaction) {
                exit();
            }
        
            $now = date('Y-m-d H:i:s');
            $date = date('Y-m-d');
            $time= date('H:i:s');
        
            $check_mpesa = ORM::for_table('tbl_payment_gateway')
                ->where('gateway_trx_id', $mpesa_code)
                ->find_one();


// if($check_mpesa){
    
//     echo "double callback, ignore one";
    
//     die;
    
    
// }




 $plan_type=$plans->type;
              
           $UserId=$userid->id;    
            
              
               
                if($plan_type=="Hotspot"){
             
            
            // echo $mpesa_code;
            // die;
             
             
      $plan_id=$plans->id;

    
             
             
             
$validity = $plans->validity;
$units = $plans->validity_unit;

// Convert units to seconds
$unit_in_seconds = [
    'Mins' => 60,
    'Hrs' => 3600,
    'Days' => 86400,
    'Months' => 2592000 // Assuming 30 days per month for simplicity
];

// Get the unit in seconds
$unit_seconds = $unit_in_seconds[$units];

// Calculate expiry timestamp
$expiry_timestamp = time() + ($validity * $unit_seconds);

// Extract date and time components
$expiry_date = date("Y-m-d", $expiry_timestamp);
$expiry_time = date("H:i:s", $expiry_timestamp);

 //"Expiry Time: $expiry_time";
       
     
   
             
     $recharged_on=date("Y:m:d");
     $recharged_time=date("H:i:s");
             
              
             
             $updated_count = ORM::for_table('tbl_user_recharges')
        ->where('username', $uname)
        ->where('status', 'on')
        ->find_many(); // Find the matching records

    foreach ($updated_count as $record) {
        $record->status = 'off'; // Update status to 'off'
        $record->save(); // Save the updated record
    }  
             
             
             
             
      
       $plan_name=$plans->name_plan;
    $routerId=$PaymentGatewayRecord->routers_id;
      

       $rname= ORM::for_table('tbl_routers')
        ->where('id', $routerId)
        ->find_one();
      
      $routername=$rname->name;
      
    $deleted_count = ORM::for_table('tbl_user_recharges')
    ->where('username', $uname)
    //     ->where('status', 'on')
     ->delete_many();
   
      
try {
    // Insert into tbl_user_recharges
    ORM::for_table('tbl_user_recharges')->create(array(
        'customer_id' => $UserId,
        'username' => $uname,
        'plan_id' => $plan_id,
        'namebp' => $plan_name,
        'recharged_on' => $recharged_on,
        'recharged_time' => $recharged_time,
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        // 'mac_address' => $mac_address,
        'status' => "on",
        'method' => $PaymentGatewayRecord->gateway."-".$mpesa_code,
        'routers' => $routername,
        'type' => $plan_type
    ))->save();

    // Insert into tbl_transactions
    ORM::for_table('tbl_transactions')->create(array(
        'invoice' => $mpesa_code, // Assuming you have this value available
        'username' => $uname,
        'plan_name' => $plan_name,
        'price' => $amount_paid, // Assuming you have this value available
        'recharged_on' => $recharged_on,
        'recharged_time' => $recharged_time,
        'expiration' => $expiry_date,
        'time' => $expiry_time,
        'method' => $PaymentGatewayRecord->gateway."-".$mpesa_code,
        'routers' => $routername,
        'type' => $plan_type
    ))->save();

    // Fetch the customer details
    $customer = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->find_one();

    if ($customer) {
        // Prepare the customer and transaction data
        $cust = array(
            'phonenumber' => $customer->phonenumber,
            'fullname' => $customer->fullname,
            'password' => $customer->password
        );
        $trx = array(
            'invoice' => $mpesa_code,
            'recharged_on' => $recharged_on,
            'recharged_time' => $recharged_time,
            'method' => $PaymentGatewayRecord->gateway."-".$mpesa_code,
            'type' => $plan_type,
            'plan_name' => $plan_name,
            'price' => $amount_paid, // Assuming you have this value available
            'username' => $uname,
            'expiration' => $expiry_date,
            'time' => $expiry_time
        );


        Message::sendInvoice($cust, $trx);

        $file_path = 'system/adduser.php';

        // Check if the file exists
        
            // Include the file
            include_once $file_path;
        
    } else {

    }
} catch (Exception $e) {
    echo "Error occurred: " . $e->getMessage();
}

                   $PaymentGatewayRecord->status = 2;
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->save();
      
      
      die;
  }
              
              
              
              
              
              
              
             
        

              


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
              



                    $PaymentGatewayRecord->status = 2;
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->save();


                  }











              
              
              
              

             
             
             
         }
        
            
         
            
            
}