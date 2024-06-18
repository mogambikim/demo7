<?php
// Start the session
session_start();

// Include the main configuration file
include '../../config.php';

// Function to extract API token from settings URL
function extract_api_token($url) {
    preg_match('/api=([^|]+)\|([^&]+)/', $url, $matches);
    if (isset($matches[1]) && isset($matches[2])) {
        return $matches[1] . '|' . $matches[2];
    }
    return null;
}

// Function to get sms_url from the main database
function get_sms_url_from_main_db() {
    global $db_host, $db_user, $db_password, $db_name;

    // Create connection to the main database
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get the sms_url value
    $result = $conn->query("SELECT value FROM tbl_appconfig WHERE setting = 'sms_url' LIMIT 1");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $sms_url = $row['value'];
    } else {
        die("No sms_url found in the main database.");
    }

    // Close the connection
    $conn->close();

    return $sms_url;
}

// Function to get SMS units from the SMS database
function get_sms_units($api_token) {
    // Adjust the path to include the SMS configuration file
    include '../../sms_config.php';

    // Create connection to the SMS database
    $conn = new mysqli($sms_db_host, $sms_db_user, $sms_db_password, $sms_db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT sms_unit FROM cg_users WHERE api_token = ?");
    $stmt->bind_param("s", $api_token);
    $stmt->execute();
    $stmt->bind_result($sms_unit);
    $stmt->fetch();

    // Close the connection
    $stmt->close();
    $conn->close();

    return $sms_unit;
}

// Get sms_url from the main database
$sms_url = get_sms_url_from_main_db();

// Extract API token from the sms_url
$api_token = extract_api_token($sms_url);
if (!$api_token) {
    die("Failed to extract API token from sms_url.");
}

// Get SMS units from the SMS database
$sms_units = get_sms_units($api_token);

// Store SMS units in session
$_SESSION['sms_units'] = $sms_units;
?>
