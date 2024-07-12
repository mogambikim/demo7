<?php
global $conn;


require 'vendor/autoload.php';

use Kopokopo\SDK\K2;

// Prepare a statement to get the settings from the database
$stmt = $conn->prepare("SELECT setting, value FROM tbl_appconfig WHERE setting IN ('kopokopo_client_id', 'kopokopo_client_secret', 'kopokopo_api_key', 'kopokopo_till_number', 'kopokopo_webhook')");

// Execute the statement
$stmt->execute();

// Fetch all the settings
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Now you can use the settings in your code
$options = [
    'clientId' => $settings['kopokopo_client_id'],
    'clientSecret' => $settings['kopokopo_client_secret'],
    'apiKey' => $settings['kopokopo_api_key'],
    'baseUrl' => 'https://api.kopokopo.com'
];

$K2 = new K2($options);

$tokens = $K2->TokenService();
$result = $tokens->getToken();

if($result['status'] == 'success'){
    $data = $result['data'];
    $accessToken = $data['accessToken'];

    $webhooks = $K2->Webhooks();

    $response = $webhooks->subscribe([
        'eventType' => 'buygoods_transaction_received',
        'url' => $settings['kopokopo_webhook'], // Use the webhook URL from the settings
        'scope' => 'till',
        'scopeReference' => $settings['kopokopo_till_number'], // Use the till number from the settings
        'accessToken' => $accessToken
    ]);

    // Log the response
    error_log("Response: " . print_r($response, true));

    if($response['status'] == 'success'){
        echo "The resource location is:" . json_encode($response['location']);
    }
}