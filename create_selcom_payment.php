<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';
include "system/autoload/Hookers.php";

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);

// Function to manage log file lines
function logToFile($filePath, $message, $maxLines = 5000) {
    $lines = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];
    $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

// Log the received input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);
logToFile('create_selcom_payment.log', "Received data: " . print_r($data, true));

// Extract necessary fields
$buyer_name = $data['buyer_name'];
$buyer_email = $data['buyer_email'];
$buyer_phone = $data['buyer_phone'];
$amount = $data['amount'];
$planId = $data['plan_id']; // Extracted from incoming data
$routerId = $data['router_id']; // Extracted from incoming data

// Generate a unique reference for the order
$reference = uniqid();

$apiKey = 'TILL61056399-ed669eb84bee8a8e';
$apiSecret = '621a499113380bc0a1ce580ce4acb936878826';
$baseUrl = 'https://apigw.selcommobile.com/v1';
$vendor = "TILL61056399";

/**
 * @param $parameters
 * @param $signed_fields
 * @param $request_timestamp
 * @param $api_secret
 * @return string
 */
function computeSignature($parameters, $signed_fields, $request_timestamp, $api_secret) {
    $fields_order = explode(',', $signed_fields);
    $sign_data = "timestamp=$request_timestamp";
    foreach ($fields_order as $key) {
        $sign_data .= "&$key=" . $parameters[$key];
    }
    return base64_encode(hash_hmac('sha256', $sign_data, $api_secret, true));
}

/**
 * @param $url
 * @param $isPost
 * @param $json
 * @param $authorization
 * @param $digest
 * @param $signed_fields
 * @param $timestamp
 * @return mixed
 */
function sendJSONPost($url, $isPost, $json, $authorization, $digest, $signed_fields, $timestamp) {
    $headers = [
        "Content-Type: application/json;charset=\"utf-8\"",
        "Accept: application/json",
        "Cache-Control: no-cache",
        "Authorization: SELCOM $authorization",
        "Digest-Method: HS256",
        "Digest: $digest",
        "Timestamp: $timestamp",
        "Signed-Fields: $signed_fields",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    if ($isPost) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);
    $response = json_decode($result, true);
    return $response;
}

$api_endpoint = "/checkout/create-order";
$url = $baseUrl . $api_endpoint;

$isPost = true;

// Define the redirect URL
$redirectUrl = APP_URL . '/selcom_success.html?username=' . urlencode($buyer_phone) . '&password=1234';

$req = [
    "vendor" => $vendor,
    "order_id" => $reference,
    "buyer_email" => $buyer_email,
    "buyer_name" => $buyer_name,
    "buyer_userid" => "",
    "buyer_phone" => $buyer_phone,
    "gateway_buyer_uuid" => "",
    "amount" => $amount,
    "currency" => "TZS",
    "payment_methods" => "ALL",
    "redirect_url" => base64_encode($redirectUrl),
    "cancel_url" => base64_encode(APP_URL),
    "webhook" => base64_encode(APP_URL . '/selcom_webhook.php'),
    "billing.firstname" => "John",
    "billing.lastname" => "Doe",
    "billing.address_1" => "969 Market",
    "billing.address_2" => "",
    "billing.city" => "Dar es Salaam",
    "billing.state_or_region" => "CA",
    "billing.postcode_or_pobox" => "82818",
    "billing.country" => "TZ",
    "billing.phone" => "25578922222",
    "buyer_remarks" => "None",
    "merchant_remarks" => "None",
    "no_of_items" => 1
];

$authorization = base64_encode($apiKey);
$timestamp = date('c'); //2019-02-26T09:30:46+03:00

$signed_fields = implode(',', array_keys($req));
$digest = computeSignature($req, $signed_fields, $timestamp, $apiSecret);

// Get the plan name and router name
$plan = ORM::for_table('tbl_plans')->where('id', $planId)->find_one();
$router = ORM::for_table('tbl_routers')->where('id', $routerId)->find_one();

if (!$plan || !$router) {
    echo json_encode(['error' => 'Invalid plan or router id']);
    exit();
}

$plan_name = $plan->name_plan;
$router_name = $router->name;

// Insert payment gateway record before sending request
$d = ORM::for_table('tbl_payment_gateway')->create();
$d->username = $buyer_phone;
$d->gateway = 'Selcom';
$d->plan_id = $planId;
$d->plan_name = $plan_name;
$d->routers_id = $routerId;
$d->routers = $router_name;
$d->price = $amount;
$d->payment_method = 'Selcom';
$d->payment_channel = 'Selcom';
$d->created_date = date('Y-m-d H:i:s');
$d->pg_url_payment = '';
$d->status = 1;
$d->gateway_trx_id = $reference;
$d->save();

$response = sendJSONPost($url, $isPost, json_encode($req), $authorization, $digest, $signed_fields, $timestamp);

// Update payment gateway record with response
$d->pg_request = json_encode($req);
$d->pg_paid_response = json_encode($response);
$d->save();

echo json_encode($response);
?>
