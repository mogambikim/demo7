<?php
// Include your config file to establish a database connection
include_once 'config.php';

// Create a new MySQLi instancetetg
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check for a connection error
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$filename = "webhook_log.txt";


function decodePhoneNumber($hash) {
    $url = "https://api.hashback.co.ke/decode";
    $data = array(
        'hash' => $hash,
        'API_KEY' => 'h24620yxCbH8y'
    );


    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if ($responseData['ResultCode'] == '0') {
        return $responseData['MSISDN'];
    } else {
        return null;
    }
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$event = isset($data['event']) ? $data['event'] : null;
$reference = isset($data['event']['resource']['reference']) ? $data['event']['resource']['reference'] : null;
$amount = isset($data['event']['resource']['amount']) ? $data['event']['resource']['amount'] : null;
$hashedSenderPhone = isset($data['event']['resource']['hashed_sender_phone']) ? $data['event']['resource']['hashed_sender_phone'] : null;

if ($reference !== null && $amount !== null && $hashedSenderPhone !== null) {
    $decodedPhoneNumber = decodePhoneNumber($hashedSenderPhone);
    $phoneNumberLast9Digits = substr($decodedPhoneNumber, -9);

    $query = "SELECT * FROM tbl_customers WHERE phonenumber LIKE ?";
    $stmt = $mysqli->prepare($query);
    $phoneNumberParam = "%$phoneNumberLast9Digits";
    $stmt->bind_param("s", $phoneNumberParam);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $username = $userData['username'];
        $type = $userData['service_type'];
// Update user balance
$balanceUpdateQuery = "UPDATE tbl_customers SET balance = balance + ? WHERE username = ?";
$balanceUpdateStmt = $mysqli->prepare($balanceUpdateQuery);
$balanceUpdateStmt->bind_param("ds", $amount, $username);
$balanceUpdateStmt->execute();

    } else {
        $username = "static";
        $type = "Static";
         // Log that the phone number was not found
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Phone number not found in tbl_customers: " . $phoneNumberLast9Digits . "\n", FILE_APPEND);

    // Log the hashed phone number
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Hashed phone number: " . $hashedSenderPhone . "\n", FILE_APPEND);

    // Log the decoded phone number
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Decoded phone number: " . $decodedPhoneNumber . "\n", FILE_APPEND);

    // Log the reference
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Reference: " . $reference . "\n", FILE_APPEND);

    // Log the amount
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Amount: " . $amount . "\n", FILE_APPEND);

    // Log the username and type
    file_put_contents($filename, date('Y-m-d H:i:s') . " - Type set to null\n", FILE_APPEND);
    }
    if ($username !== null && $type !== null) {
        $transactionQuery = "INSERT INTO tbl_transactions (invoice, username, plan_name, price, recharged_on, recharged_time, expiration, time, method, routers, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $transactionStmt = $mysqli->prepare($transactionQuery);
    
        if ($transactionStmt === false) {
            file_put_contents($filename, date('Y-m-d H:i:s') . " - Failed to prepare transaction query: " . $mysqli->error . "\n", FILE_APPEND);
        } else {
            $invoice = $reference;
            $plan_name = "KopoKopo";
            $recharged_on = date('Y-m-d');
            $recharged_time = date('H:i:s');
            $expiration = date('Y-m-d');
            $time = date('H:i:s');
            $method = "KopoKopo Payments";
            $routers = "hotspot";
    
            $transactionStmt->bind_param("sssdsssssss", $invoice, $username, $plan_name, $amount, $recharged_on, $recharged_time, $expiration, $time, $method, $routers, $type);
            $executeSuccess = $transactionStmt->execute();
    
            if ($executeSuccess === false) {
                file_put_contents($filename, date('Y-m-d H:i:s') . " - Failed to execute transaction query: " . $transactionStmt->error . "\n", FILE_APPEND);
            } else {
                file_put_contents($filename, date('Y-m-d H:i:s') . " - Added transaction: " . $amount . "\n", FILE_APPEND);
            }
        }
    } else {
        file_put_contents($filename, date('Y-m-d H:i:s') . " - Skipped adding transaction due to null username or type\n", FILE_APPEND);
    }
    }
