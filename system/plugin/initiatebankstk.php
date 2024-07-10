<?php

function initiatebankstk()
{
    
   
   
   
   
   
    
    
  
    
             $username=$_POST['username'];
             $phone=$_POST['phone'];
  
  
  
            $phone = (substr($phone, 0,1) == '+') ? str_replace('+', '', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
            $phone = (substr($phone, 0,1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;
    
  
  

        $bankaccount = ORM::for_table('tbl_appconfig')
    ->where('setting', 'Stkbankacc')
    ->find_one();

     $bankname = ORM::for_table('tbl_appconfig')
    ->where('setting', 'Stkbankname')
    ->find_one();

       $bankaccount = ($bankaccount) ? $bankaccount->value : null;
          $bankname = ($bankname) ? $bankname->value : null;

       // echo $bankname;
          

       $CheckId = ORM::for_table('tbl_customers')
       ->where('username', $username)
       ->order_by_desc('id')
       ->find_one();
   
   $CheckUser = ORM::for_table('tbl_customers')
       ->where('username', $username)
       ->find_many();
   
   $UserId = $CheckId->id;
   
   if (!empty($CheckUser)) {
       ORM::for_table('tbl_customers')
           ->where('username', $username)
           ->where_not_equal('id', $UserId)
           ->delete_many();
   }
   
         


















          
          
        if (empty($bankaccount) || empty($bankname)) {
            
            
      echo    $error="<script>toastr.error('Could not complete the payment req, please contact admin');</script>";
      
      
    die();
 }

          
          
          
          
           $getpaybill = ORM::for_table('tbl_banks')
    ->where('name', $bankname)
    ->find_one();
          
          
          
      
          
          
          
          
          $paybill=$getpaybill->paybill;
          
          
          
        // echo $paybill;
          
          
          
          
          
          
          
          
          
          $cburl = U . 'callback/BankStkPush' ;
          
          
           $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
            ->where('username', $username)
            ->where('status', 1) // Add this line to filter by status
            ->order_by_desc('id')
            ->find_one();


             
    $ThisUser= ORM::for_table('tbl_customers')
    ->where('username', $username)
    ->order_by_desc('id')
    ->find_one();



    $ThisUser->phonenumber=$phone;
    // $ThisUser->username=$phone;
    $ThisUser->save();


    
 
          
         $amount=$PaymentGatewayRecord->price;
          
          if(!$PaymentGatewayRecord){
              
                    echo    $error="<script>toastr.error('Could not complete the payment req, please contact administrator');</script>";
              
              die();
          }
          
          
          
          
          
          
          
            
  $consumerKey = '3AmVP1WFDQn7GrDH8GcSSKxcAvnJdZGC'; //Fill with your app Consumer Key
  $consumerSecret = '71Lybl6jUtxM0F35'; // Fill with your app Secret

  $headers = ['Content-Type:application/json; charset=utf8'];

  $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);('');

  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
  $result = json_decode($result);

  $access_token = $result->access_token;

 // echo  $access_token;
  
  curl_close($curl);


// Initiate Stk push

$stk_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$PartyA = $phone; // This is your phone number, 
  $AccountReference = $bankaccount; 
  $TransactionDesc = 'TestMapayment';
  $Amount = $amount;
  $BusinessShortCode='4137989';
  $Passkey='3a45e88faa037b86fbd0c494676d71c3c23574203b2bf721066f90598bbd8bb8';
  $Timestamp = date("YmdHis",time());    
  $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);
  $CallBackURL = $cburl; 
 

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stk_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header


$curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $paybill,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $CallBackURL,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);
//print_r($curl_response);

//echo $curl_response;

$mpesaResponse = json_decode($curl_response);




$responseCode = $mpesaResponse->ResponseCode;
$resultDesc = $mpesaResponse->resultDesc;
$MerchantRequestID = $mpesaResponse->MerchantRequestID;
$CheckoutRequestID = $mpesaResponse->CheckoutRequestID;
              

       if($responseCode=="0"){
           date_default_timezone_set('Africa/Nairobi'); 
          $now=date("Y-m-d H:i:s");

// $username=$phone;
          
        $PaymentGatewayRecord->pg_paid_response = $resultDesc;
        $PaymentGatewayRecord->username = $username;
        $PaymentGatewayRecord->checkout = $CheckoutRequestID;
       $PaymentGatewayRecord->payment_method = 'Mpesa Stk Push';
       $PaymentGatewayRecord->payment_channel = 'Mpesa Stk Push';
        $PaymentGatewayRecord->save();
        

        // After saving the PaymentGatewayRecord, add the following cURL request to post CheckoutRequestID to query.php
// After saving the PaymentGatewayRecord, add the following cURL request to post CheckoutRequestID to query.php
$queryUrl = APP_URL . '/query.php';
$postData = http_build_query(['CheckoutRequestID' => $CheckoutRequestID]);

// Command to send the cURL request in the background
$command = "curl -X POST -d \"$postData\" \"$queryUrl\" > /dev/null 2>&1 &";

// Use popen to execute the command without blocking the script
$handle = popen($command, 'r');
pclose($handle);

// Log the CheckoutRequestID in the error log
        
        
        if(!empty($_POST['channel'])){


        
        }else{
          echo    $error="<script>toastr.success('Enter Mpesa Pin to complete');</script>";

        }
  
       }else{
           
       echo    $error="<script>toastr.error('We could not complete the payment for you, please contact administrator');</script>";
       }

































}

