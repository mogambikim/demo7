<?php
require_once 'config.php';
require_once 'system/orm.php';
require_once 'system/autoload/PEAR2/Autoload.php';
include "system/autoload/Hookers.php";

// ORM configuration
ORM::configure("mysql:host=$db_host;dbname=$db_name");
ORM::configure('username', $db_user);
ORM::configure('password', $db_password);
ORM::configure('return_result_sets', true);
ORM::configure('logging', true);

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
$logFilePath = 'stk_push_query.log';

function queryStkPush($CheckoutRequestID) {
    global $logFilePath;

    // Ensure CheckoutRequestID is valid
    if (empty($CheckoutRequestID)) {
        logToFile($logFilePath, "Invalid CheckoutRequestID: $CheckoutRequestID");
        return null;
    }

    // Fetch M-Pesa credentials from tbl_appconfig table
    $consumerKey = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_till_consumer_key')->find_one()->value;
    $consumerSecret = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_till_consumer_secret')->find_one()->value;
    $BusinessShortCode = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_till_shortcode_code')->find_one()->value;
    $Passkey = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_till_pass_key')->find_one()->value;

    $headers = ['Content-Type:application/json; charset=utf8'];

    $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    logToFile($logFilePath, "Requesting access token");

    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
    $result = curl_exec($curl);

    if ($result === FALSE) {
        $error = 'Curl failed: ' . curl_error($curl);
        logToFile($logFilePath, $error);
        die($error);
    }

    $result = json_decode($result);

    if (isset($result->access_token)) {
        $access_token = $result->access_token;
        logToFile($logFilePath, "Access token obtained successfully");
    } else {
        $error = 'Failed to get access token.';
        logToFile($logFilePath, $error);
        die($error);
    }

    curl_close($curl);

    $urlfinal = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';

    logToFile($logFilePath, "Querying STK Push status for CheckoutRequestID: $CheckoutRequestID");

    $curlfinal = curl_init();
    curl_setopt($curlfinal, CURLOPT_URL, $urlfinal);
    curl_setopt($curlfinal, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token)); // Setting custom header

    $Timestamp = date("YmdHis", time());
    $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

    $curl_post_data_final = array(
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'CheckoutRequestID' => $CheckoutRequestID
    );

    $data_string_final = json_encode($curl_post_data_final);

    curl_setopt($curlfinal, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlfinal, CURLOPT_POST, true);
    curl_setopt($curlfinal, CURLOPT_POSTFIELDS, $data_string_final);

    $curl_responsefinal = curl_exec($curlfinal);

    if ($curl_responsefinal === FALSE) {
        $error = 'Curl failed: ' . curl_error($curlfinal);
        logToFile($logFilePath, $error);
        die($error);
    }

    logToFile($logFilePath, "Raw response: " . $curl_responsefinal); // Log the raw response

    curl_close($curlfinal);

    return $curl_responsefinal; // Return the full raw response
}

// Example usage with provided CheckoutRequestID
$logFilePath = 'stk_push_query.log'; // Define the path for the log file
$latestPayments = ORM::for_table('tbl_payment_gateway')
    ->order_by_desc('id')
    ->limit(3) // Fetch the last 3 records to check the last two
    ->find_many();

$processedCount = 0;

foreach ($latestPayments as $payment) {
    if ($processedCount >= 2) {
        break;
    }

    if ($payment->query_status == 0) { // Assuming query_status 0 means not yet processed
        $CheckoutRequestID = $payment->checkout;
        logToFile($logFilePath, "Processing CheckoutRequestID: $CheckoutRequestID");

        // Query the STK Push status and capture the full response
        $response = queryStkPush($CheckoutRequestID);

        // Send the full response as a callback to the specified URL
        $callbackUrl = APP_URL . '/query_update.php';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $callbackUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $response);
        $response = curl_exec($curl);
        curl_close($curl);

        logToFile($logFilePath, "Callback response: " . $response);

        // Update the query_status to 1 (processed)
        $payment->query_status = 1;
        $payment->save();
    } else {
        logToFile($logFilePath, "CheckoutRequestID: $payment->checkout has already been processed.");
        $processedCount++;
    }
}

if ($processedCount == 0) {
    echo "No payments found to process.";
}
?>
