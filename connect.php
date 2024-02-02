<?php
include 'config.php';
// Get the router id, mac, and ip from the URL
$router_id = isset($_GET['nux-router']) ? $_GET['nux-router'] : null;
$mac = isset($_GET['nux-mac']) ? $_GET['nux-mac'] : null;
$ip = isset($_GET['nux-ip']) ? $_GET['nux-ip'] : null;
$plan_id = isset($_GET['plan_id']) ? $_GET['plan_id'] : null;

// ... rest of your code ...
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // Generate a random username and password
$username = 'user' . mt_rand(1000, 9999); // Username in the format userXXXX
$password = mt_rand(1000, 9999); // 4 digit pin

// Other user details
$fullname = 'hotspotuser' . mt_rand(1000, 9999); // random user name
$address = 'hotspot';
$phonenumber = '079' . mt_rand(1000000, 9999999); // 10 digit phone number in the format 079XXXXXXX
$email = $fullname . '@gmail.com';
$service_type = 'Hotspot';

    // Insert the new user into your database
    $stmt = $conn->prepare("INSERT INTO tbl_customers (username, password, fullname, address, phonenumber, email, service_type) VALUES (:username, :password, :fullname, :address, :phonenumber, :email, :service_type)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phonenumber', $phonenumber);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':service_type', $service_type);
    $stmt->execute();

    echo 'Data inserted successfully';

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


// Redirect to the login page with the user details as URL parameters
header('Location: ' . APP_URL . '/index.php?_route=login&username=' . urlencode($username) . '&password=' . urlencode($password) . '&from_connect=true&redirect=order/buy/' . urlencode($router_id) . '/' . urlencode($plan_id) . '&nux-router=' . urlencode($router_id) . '&nux-mac=' . urlencode($mac) . '&nux-ip=' . urlencode($ip));
exit;
