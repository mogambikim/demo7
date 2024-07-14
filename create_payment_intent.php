<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);

// Retrieve the Stripe secret key from the database
$stripe_secret_key_record = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_secret_key')->find_one();

if ($stripe_secret_key_record) {
    $stripe_secret_key = $stripe_secret_key_record->value;
} else {
    echo json_encode(['error' => 'Stripe secret key not found in database']);
    exit();
}

\Stripe\Stripe::setApiKey($stripe_secret_key);

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$amount = $data['amount'];
$phone_number = $data['phone_number']; // Get phone number from request data

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'gbp',
        'payment_method_types' => ['card'],
        'metadata' => ['phone_number' => $phone_number], // Add phone number to metadata
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
