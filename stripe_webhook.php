<?php
require_once 'config.php';
require_once 'vendor/autoload.php'; // Include Stripe PHP library
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';
include "system/autoload/Hookers.php";

ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);


\Stripe\Stripe::setApiKey('sk_test_51PbKmaRslRg4lJ4nG2ZS7xct6xZmmjJQRfXSHRjQleDAeJNW4yss5cf8I7Rjohe4hcIT5tnHHkhTbqcfRDMm0xla00aVpNt7mh'); // Replace with your secret key

// Function to manage log file lines
function logToFile($filePath, $message, $maxLines = 5000) {
    // Read existing file content
    $lines = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];
    
    // Add new log entry
    $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;

    // Trim to the maximum number of lines
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }

    // Write the trimmed log back to the file
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

// Define log file path
$logFilePath = 'stripe_webhook.log';

$captureLogs = file_get_contents("php://input");
logToFile($logFilePath, "Received raw data: " . $captureLogs);

$endpoint_secret = 'whsec_...'; // Replace with your endpoint secret
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent(
        $captureLogs, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    logToFile($logFilePath, "Invalid payload: " . $e->getMessage());
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    logToFile($logFilePath, "Invalid signature: " . $e->getMessage());
    http_response_code(400);
    exit();
}

logToFile($logFilePath, "Webhook event received: " . json_encode($event));

// Handle the event
switch ($event->type) {
    case 'checkout.session.async_payment_failed':
    case 'checkout.session.async_payment_succeeded':
    case 'checkout.session.completed':
    case 'checkout.session.expired':
    case 'payment_intent.amount_capturable_updated':
    case 'payment_intent.canceled':
    case 'payment_intent.created':
    case 'payment_intent.partially_funded':
    case 'payment_intent.payment_failed':
    case 'payment_intent.processing':
    case 'payment_intent.requires_action':
    case 'payment_intent.succeeded':
        // Send the event data to your processing script
        $url = APP_URL . '/process_payment.php'; // Adjust URL if needed
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_exec($ch);
        curl_close($ch);

        logToFile($logFilePath, "Sent event to process_payment.php: " . json_encode($event));
        break;
    default:
        logToFile($logFilePath, "Unhandled event type: " . $event->type);
        break;
}

http_response_code(200);
?>
