<?php

// Include the config file with proper error handling
include 'config.php';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
} catch(PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Assuming $conn is your PDO connection object
$stmt = $conn->prepare("SELECT * FROM tbl_routers WHERE id = :routerId");

// Bind the routerId parameter to the placeholder
$stmt->bindParam(':routerId', $routerId);

// Execute the query
$stmt->execute();

// Fetch the router result
$routerResult = $stmt->fetch(PDO::FETCH_ASSOC);

if ($routerResult) {
    // Router with the specified ID found
    $username = $routerResult['username'];
    $password = $routerResult['password'];
    $routerIpAddress = $routerResult['ip_address'];
} else {
    die('Router not found');
}

require 'system/autoload/PEAR2/Autoload.php';
use PEAR2\Net\RouterOS;

try {
    // Create a RouterOS client
    $client = new RouterOS\Client($routerIpAddress, $username, $password);

    // Create a Util object using the client
    $util = new RouterOS\Util($client);

    // Additional parameters for limited plans
    $typebp = $plans->typebp;
    $data_limit = $plans->data_limit;
    $data_unit = $plans->data_unit;
    $time_limit = $plans->time_limit;
    $time_unit = $plans->time_unit;

    // Prepare user details
    $userDetails = array(
        'name' => $uname,
        'password' => '1234',
        'profile' => $plan_name
    );

    if ($typebp == "Limited") {
        if (!empty($time_limit) && !empty($time_unit)) {
            if ($time_unit == 'Hrs') {
                $timelimit = $time_limit . ":00:00";
            } else {
                $timelimit = "00:" . $time_limit . ":00";
            }
            $userDetails['limit-uptime'] = $timelimit;
        }

        if (!empty($data_limit) && !empty($data_unit)) {
            if ($data_unit == 'GB') {
                $datalimit = $data_limit . "000000000";
            } else {
                $datalimit = $data_limit . "000000";
            }
            $userDetails['limit-bytes-total'] = $datalimit;
        }
    }

    // Add the new hotspot user with limits if applicable
    $addRequest = new RouterOS\Request('/ip/hotspot/user/add');
    $addRequest->setArgument('name', $uname)
        ->setArgument('password', '1234')
        ->setArgument('profile', $plan_name);

    if (isset($userDetails['limit-uptime'])) {
        $addRequest->setArgument('limit-uptime', $userDetails['limit-uptime']);
    }

    if (isset($userDetails['limit-bytes-total'])) {
        $addRequest->setArgument('limit-bytes-total', $userDetails['limit-bytes-total']);
    }

    $client->sendSync($addRequest);

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
