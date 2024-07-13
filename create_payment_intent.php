<?php
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('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'); // Replace with your secret key

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
