<?php
// Include the SMS configuration
include 'sms_config.php';

// Create connection to the SMS database
$conn = new mysqli($sms_db_host, $sms_db_user, $sms_db_password, $sms_db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection to the SMS database was successful!";
}

// Close the connection
$conn->close();
?>
