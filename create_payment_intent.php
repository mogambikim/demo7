<?php
require 'vendor/autoload.php';

// Set your secret key. Remember to switch to your live secret key in production.
\Stripe\Stripe::setApiKey('sk_test_51PbKmaRslRg4lJ4nG2ZS7xct6xZmmjJQRfXSHRjQleDAeJNW4yss5cf8I7Rjohe4hcIT5tnHHkhTbqcfRDMm0xla00aVpNt7mh'); // Replace with your secret key

header('Content-Type: application/json');

// Capture the received data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the received data to a file for debugging
file_put_contents('payment_intent_log.txt', print_r($data, true), FILE_APPEND);

$amount = $data['amount'];

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'usd',
        'payment_method_types' => ['card'],
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
