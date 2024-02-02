<?php
header("Content-Type: application/json");

$response = '{
    "ResultCode": 0, 
    "ResultDesc": "Confirmation Received Successfully"
}';

// DATA
$mpesaResponse = file_get_contents('php://input');

// log the response
$logFile = "M_PESAConfirmationResponse.txt";

// write to file
$log = fopen($logFile, "a");
fwrite($log, $mpesaResponse);
fclose($log);

// Include your config file to establish a database connection
include_once 'config.php';

// Create a new MySQLi instance
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check for a connection error
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$data = json_decode($mpesaResponse, true);

if ($data) {
    // Rest of your code...

$reference = isset($data['TransID']) ? $data['TransID'] : null;
$amount = isset($data['TransAmount']) ? $data['TransAmount'] : null;
$senderPhone = isset($data['MSISDN']) ? $data['MSISDN'] : null;
$billRefNumber = isset($data['BillRefNumber']) ? $data['BillRefNumber'] : null;

if ($reference !== null && $amount !== null) {
    if (!empty($billRefNumber)) {
        // If BillRefNumber is not empty, use it to find the user
        $query = "SELECT * FROM tbl_customers WHERE LOWER(username) = LOWER(?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $billRefNumber);
    } else if ($senderPhone !== null) {
        // If BillRefNumber is empty, use the phone number to find the user
        $phoneNumberLast9Digits = substr($senderPhone, -9);
        $query = "SELECT * FROM tbl_customers WHERE phonenumber LIKE ?";
        $stmt = $mysqli->prepare($query);
        $phoneNumberParam = "%$phoneNumberLast9Digits";
        $stmt->bind_param("s", $phoneNumberParam);
    } else {
        // If both BillRefNumber and phone number are empty, log an error and exit
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Both BillRefNumber and phone number are empty\n", FILE_APPEND);
        exit;
    }

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

            // Insert transaction
            $transactionQuery = "INSERT INTO tbl_transactions (invoice, username, plan_name, price, recharged_on, recharged_time, expiration, time, method, routers, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $transactionStmt = $mysqli->prepare($transactionQuery);

            $invoice = $reference;
            $plan_name = "Mpesa Payment";
            $recharged_on = date('Y-m-d');
            $recharged_time = date('H:i:s');
            $expiration = date('Y-m-d');
            $time = date('H:i:s');
            $method = "Mpesa Payments";
            $routers = "hotspot";

            $transactionStmt->bind_param("sssdsssssss", $invoice, $username, $plan_name, $amount, $recharged_on, $recharged_time, $expiration, $time, $method, $routers, $type);
            $executeSuccess = $transactionStmt->execute();

            if ($executeSuccess === false) {
                file_put_contents($filename, date('Y-m-d H:i:s') . " - Failed to execute transaction query: " . $transactionStmt->error . "\n", FILE_APPEND);
            } else {
                file_put_contents($filename, date('Y-m-d H:i:s') . " - Added transaction: " . $amount . "\n", FILE_APPEND);
            }
        } else {
            file_put_contents($filename, date('Y-m-d H:i:s') . " - Phone number not found in tbl_customers: " . $phoneNumberLast9Digits . "\n", FILE_APPEND);
        }
    }
} else {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Failed to read data from: " . $filename . "\n", FILE_APPEND);
}

echo $response;