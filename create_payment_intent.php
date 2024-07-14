<?php
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51PbKmaRslRg4lJ4nG2ZS7xct6xZmmjJQRfXSHRjQleDAeJNW4yss5cf8I7Rjohe4hcIT5tnHHkhTbqcfRDMm0xla00aVpNt7mh'); // Replace with your secret key

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
