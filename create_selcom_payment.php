<?php
require_once 'config.php';
require_once 'vendor/autoload.php';
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';
include "system/autoload/Hookers.php";

use Selcom\ApigwClient\Client;

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

function logToPaymentGateway($username, $paymentMethod, $gateway, $planId, $routerId) {
    $Planname = ORM::for_table('tbl_plans')->where('id', $planId)->find_one();
    $Findrouter = ORM::for_table('tbl_routers')->where('id', $routerId)->find_one();
    $rname = $Findrouter->name;
    $price = $Planname->price;
    $Planname = $Planname->name_plan;

    $Checkorders = ORM::for_table('tbl_payment_gateway')->where('username', $username)->where('status', 1)->find_many();
    if ($Checkorders) {
        foreach ($Checkorders as $Dorder) {
            $Dorder->delete();
        }
    }

    $d = ORM::for_table('tbl_payment_gateway')->create();
    $d->username = $username;
    $d->gateway = $gateway;
    $d->plan_id = $planId;
    $d->plan_name = $Planname;
    $d->routers_id = $routerId;
    $d->routers = $rname;
    $d->price = $price;
    $d->payment_method = $paymentMethod;
    $d->payment_channel = $gateway;
    $d->created_date = date('Y-m-d H:i:s');
    $d->paid_date = date('Y-m-d H:i:s');
    $d->expired_date = date('Y-m-d H:i:s');
    $d->pg_url_payment = '';
    $d->status = 1;
    $d->save();
}

// Directly use the provided API key and secret for testing
$selcom_api_key = 'TILL61056399-ed669eb84bee8a8e';
$selcom_api_secret = '621a499113380bc0a1ce580ce4acb936878826';

class SelcomClient {
    private $config;
    private $client;

    public function __construct($api_key, $api_secret) {
        $this->config = [
            'api_key' => $api_key,
            'api_secret' => $api_secret,
            'base_url' => 'https://apigw.selcommobile.com/v1', // replace with the correct base URL
        ];
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $this->config['base_url'],
            'timeout'  => 30.0,
            'verify'   => false, // Disable SSL verification for testing
        ]);
    }

    private function getHeaders($data) {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $digestMethod = 'HS256';
        $signedFields = implode(',', array_keys($data));
        $dataString = http_build_query($data);
        $digest = base64_encode(hash_hmac('sha256', $dataString, $this->config['api_secret'], true));

        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'SELCOM ' . base64_encode($this->config['api_key']),
            'Timestamp' => $timestamp,
            'Digest-Method' => $digestMethod,
            'Digest' => $digest,
            'Signed-Fields' => $signedFields,
        ];
    }

    public function sendRequest($method, $endpoint, $data) {
        $headers = $this->getHeaders($data);
        try {
            $response = $this->client->request($method, $endpoint, [
                'headers' => $headers,
                'json' => $data,
            ]);
            return json_decode($response->getBody(), true);
        } catch (GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody(), true);
            }
            return ['error' => $e->getMessage()];
        }
    }
}

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$amount = $data['amount'];
$phone_number = $data['phone_number']; // Get phone number from request data

$selcomClient = new SelcomClient($selcom_api_key, $selcom_api_secret);

try {
    $paymentData = [
        'vendor' => 'TILL61056399',
        'order_id' => uniqid('ORDER_'),
        'buyer_email' => 'john@example.com',
        'buyer_name' => 'John Joh',
        'buyer_phone' => $phone_number,
        'amount' => $amount,
        'currency' => 'TZS',
        'redirect_url' => base64_encode('https://example.com/success'),
        'cancel_url' => base64_encode('https://example.com/cancel'),
        'webhook' => base64_encode('https://example.com/webhook'),
        'buyer_remarks' => 'None',
        'merchant_remarks' => 'None',
        'no_of_items' => 1
    ];

    $response = $selcomClient->sendRequest('POST', '/checkout/create-order-minimal', $paymentData);
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
