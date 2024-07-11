<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
require_once 'config.php';

// Logging function
function logToFile($filePath, $message, $maxLines = 5000) {
    $lines = file_exists($filePath) ? file($filePath, FILE_IGNORE_NEW_LINES) : [];
    $lines[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if (count($lines) > $maxLines) {
        $lines = array_slice($lines, count($lines) - $maxLines);
    }
    file_put_contents($filePath, implode(PHP_EOL, $lines) . PHP_EOL);
}

// Define log file path
$logFilePath = 'test.log';

// Start of script
logToFile($logFilePath, "Script started");

try {
    // Capture incoming POST data
    $inputData = file_get_contents('php://input');
    logToFile($logFilePath, "Received data: " . $inputData);

    // Decode JSON data to an array
    $data = json_decode($inputData, true);

    // If JSON decoding fails, try to capture URL-encoded form data
    if (json_last_error() !== JSON_ERROR_NONE) {
        parse_str($inputData, $data);
        logToFile($logFilePath, "Decoded URL-encoded form data: " . print_r($data, true));
    } else {
        logToFile($logFilePath, "Decoded JSON data: " . print_r($data, true));
    }

    // Check for necessary data
    if (!isset($data['payment_method_id']) || !isset($data['amount'])) {
        logToFile($logFilePath, "Invalid request: Missing payment_method_id or amount");
        echo json_encode(['error' => 'Invalid request: Missing payment_method_id or amount']);
        exit;
    }

    // Stripe API key
    logToFile($logFilePath, "Setting Stripe API key");
    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);

    $amount = intval($data['amount'] * 100); // Convert amount to cents
    logToFile($logFilePath, "Converted amount to cents: $amount");

    // Create and confirm a PaymentIntent with amount, currency, and return_url
    logToFile($logFilePath, "Creating and confirming PaymentIntent");
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount, // amount in cents
        'currency' => 'gbp',
        'payment_method' => $data['payment_method_id'],
        'confirm' => true,
        'return_url' => 'http://localhost/radius/return.php', // Provide a valid return URL
    ]);

    logToFile($logFilePath, "PaymentIntent created: " . json_encode($paymentIntent));

    // Return the PaymentIntent status in JSON
    $response = ['paymentIntent' => $paymentIntent];
    logToFile($logFilePath, "Sending JSON response: " . json_encode($response));
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} catch (\Stripe\Exception\ApiErrorException $e) {
    logToFile($logFilePath, "Stripe API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    logToFile($logFilePath, "General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

logToFile($logFilePath, "Script ended");
?>
