<?php
// Adjust the path to the vendor directory
require '../vendor/autoload.php';
require '../config.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

\Stripe\Stripe::setApiKey($config['stripe_secret_key']);

$logFile = '../../test.log';

function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

logMessage("Received payment request: " . json_encode($data));

try {
    // Convert amount to cents
    $amount = intval($data['amount'] * 100);

    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount, // amount in cents
        'currency' => 'gbp',
        'payment_method' => $data['payment_method_id'],
        'confirmation_method' => 'manual',
        'confirm' => true,
    ]);

    logMessage("PaymentIntent created: " . json_encode($paymentIntent));

    // Respond with the payment intent status
    echo json_encode([
        'paymentIntent' => $paymentIntent,
    ]);
} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
