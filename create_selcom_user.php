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

// Retrieve the Selcom API key and secret from the database
$selcom_api_key_record = ORM::for_table('tbl_appconfig')->where('setting', 'selcom_api_key')->find_one();
$selcom_api_secret_record = ORM::for_table('tbl_appconfig')->where('setting', 'selcom_api_secret')->find_one();

if ($selcom_api_key_record && $selcom_api_secret_record) {
    $selcom_api_key = $selcom_api_key_record->value;
    $selcom_api_secret = $selcom_api_secret_record->value;
} else {
    echo json_encode(['error' => 'Selcom API credentials not found in database']);
    exit();
}

class SelcomClient {
    private $config;
    private $client;

    public function __construct($api_key, $api_secret) {
        $this->config = [
            'api_key' => $api_key,
            'api_secret' => $api_secret,
            'base_url' => 'https://api.selcom.net', // replace with the correct base URL
        ];
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $this->config['base_url'],
            'timeout'  => 30.0,
            'verify'   => false, // Disable SSL verification
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

$phone = $data['phone_number'];
$fullName = $data['full_name'];
$email = $data['email'];
$address = $data['address'];
$planId = $data['plan_id'];
$routerId = $data['router_id'];
$amount = $data['amount'] * 100; // Convert to cents

$selcomClient = new SelcomClient($selcom_api_key, $selcom_api_secret);

// Create user in the database
$Userexist = ORM::for_table('tbl_customers')->where('username', $phone)->find_one();
if (!$Userexist) {
    $createUser = ORM::for_table('tbl_customers')->create();
    $createUser->username = $phone;
    $createUser->password = '1234';
    $createUser->fullname = $fullName;
    $createUser->phonenumber = $phone;
    $createUser->pppoe_password = '1234';
    $createUser->address = $address;
    $createUser->email = $email;
    $createUser->service_type = 'Hotspot';
    $createUser->router_id = $routerId;
    $createUser->save();
}

// Log to payment gateway
logToPaymentGateway($phone, 'Selcom Payment', 'Selcom', $planId, $routerId);

// Create Selcom payment request
try {
    $paymentData = [
        'transid' => uniqid('SEL'),
        'utilitycode' => 'UTILITY_CODE',
        'utilityref' => '654944949',
        'amount' => $amount,
        'vendor' => '66546846845',
        'pin' => '48585',
        'msisdn' => $phone,
    ];

    $response = $selcomClient->sendRequest('POST', '/v1/utilitypayment/process', $paymentData);
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
