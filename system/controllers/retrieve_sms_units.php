<?php
// Include the necessary files for database connections
include '../../sms_config.php';

// Verify inclusion and variable values
echo "SMS DB Host: " . $sms_db_host . "<br>";
echo "SMS DB User: " . $sms_db_user . "<br>";
echo "SMS DB Password: " . $sms_db_password . "<br>";
echo "SMS DB Name: " . $sms_db_name . "<br>";

// Function to extract API token from settings URL
function extract_api_token($url) {
    echo "Extracting API token from URL: $url<br>";
    preg_match('/api=([^|]+)\|([^&]+)/', $url, $matches);
    if (isset($matches[1]) && isset($matches[2])) {
        $token = $matches[1] . '|' . $matches[2];
        echo "Extracted API token: $token<br>";
        return $token;
    }
    echo "Failed to extract API token<br>";
    return null;
}

// Function to get SMS units from the SMS database
function get_sms_units($api_token, $sms_db_host, $sms_db_user, $sms_db_password, $sms_db_name) {
    echo "Connecting to SMS database with the following parameters:<br>";
    echo "Host: $sms_db_host<br>";
    echo "User: $sms_db_user<br>";
    echo "Password: $sms_db_password<br>";
    echo "Database: $sms_db_name<br>";

    // Create connection to the SMS database
    $conn = new mysqli($sms_db_host, $sms_db_user, $sms_db_password, $sms_db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "<br>");
    }
    echo "Connected to SMS database successfully<br>";

    // Prepare and execute the query
    echo "Preparing query to get SMS units<br>";
    $stmt = $conn->prepare("SELECT sms_unit FROM cg_users WHERE api_token = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error . "<br>");
    }
    echo "Query prepared successfully<br>";

    $stmt->bind_param("s", $api_token);
    echo "Binding parameters<br>";
    $stmt->execute();
    echo "Executing query<br>";

    $stmt->bind_result($sms_unit);
    $stmt->fetch();
    echo "Query executed and fetched result<br>";

    // Close the connection
    $stmt->close();
    $conn->close();
    echo "Connection closed<br>";

    return $sms_unit;
}

// Example SMS URL for testing
$sms_url = "https://sms.freeispradius.com/sms/send?api=1|25qbsrYwfGN1YiSGOK4cfEtSGSHYUby1X2pnn5EH&SenderId=TOPSPEED&msg=[text]&phone=[number]";

// Extract API token from the sms_url
$api_token = extract_api_token($sms_url);
if (!$api_token) {
    die("Failed to extract API token from sms_url.");
}
echo "API Token: " . $api_token . "<br>";

// Get SMS units from the SMS database
$sms_units = get_sms_units($api_token, $sms_db_host, $sms_db_user, $sms_db_password, $sms_db_name);
echo "SMS Units: " . $sms_units . "<br>";
