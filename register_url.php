<?php

$shortcode = '4095091';
$consumerkey    = "uYa5L9RO0Sm18MlMGijANwWrwadSgI1Y9W5zh786uTMSQfGw";
$consumersecret = "r8qppdgHIdjs0gUo4PpyxNmOv0Wl9oMeiuGtAC58QPqhjTmTy7gjCUA4Dr3J4lnv";
$validationurl = "https://corefiber.freeispradius.com/validation_url.php";
//before submitting check if its paybill_webhook or till_webhook before making any mistake
$confirmationurl = "https://corefiber.freeispradius.com/paybill_webhook.php";

$authenticationurl = 'https://api.safaricom.co.ke/oauth/v2/generate?grant_type=client_credentials';
$registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl';
$credentials = base64_encode($consumerkey . ':' . $consumersecret);

$username = $consumerkey;
$password = $consumersecret;
$headers = array(
  'Content-Type: application/json; charset=utf-8'
);

$ch = curl_init($authenticationurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
echo $result = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;
curl_close($ch);


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $registerurl);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token));
$curl_post_data = array(
  'ShortCode' => $shortcode,
  'ResponseType' => 'Completed',
  'ConfirmationURL' => $confirmationurl,
  'ValidationURL' => $validationurl
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
echo $curl_response;