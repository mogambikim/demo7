<?php



include 'config.php';


try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


// Assuming $conn is your PDO connection object
$stmt = $conn->prepare("SELECT * FROM tbl_routers WHERE id = :routerId");

// Bind the routerId parameter to the placeholder
$stmt->bindParam(':routerId', $routerId);

// Execute the query
$stmt->execute();

// Fetch the router result
$routerResult = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if router exists
if ($routerResult) {
    // Router with the specified ID found
    // You can access the router data using $routerResult array
    // For example:
    $username = $routerResult['username'];
    $password = $routerResult['password'];
    $routerIpAddress = $routerResult['ip_address'];
    // You can do further processing here
} else {
    // Router with the specified ID not found
    // Handle the case where the router doesn't exist
}

require 'system/autoload/PEAR2/Autoload.php';






use PEAR2\Net\RouterOS;


try {
    // Create a RouterOS client
     $client = new RouterOS\Client($routerIpAddress, $username, $password);

    // Create a Util object using the client
    $util = new RouterOS\Util($client);

    // Add the new hotspot user
    $util->setMenu('/ip hotspot user')->add(
        array(
            'name' => $uname,
            'password' => '1234',
            'profile' => $plan_name
        )
    );

 //   echo "Hotspot user 'manyenge' added successfully.\n";

    // Now, we will log in the user to obtain their IP address
    // First, we need to find the user's ID
    $userList = $util->setMenu('/ip hotspot active')->getAll();

    $userId = null;
    foreach ($userList as $user) {
        if ($user->getProperty('user') === $uname) {
            $userId = $user->getProperty('.id');
            break;
        }
    }

    if ($userId) {
        // Get the user's IP address
        $userIP = $util->setMenu('/ip hotspot active')->get($userId)->getProperty('address');
       // echo "User 'manyenge' is logged in with IP address: $userIP\n";
    } else {
      //  echo "User 'manyenge' is not logged in.\n";
    }
} catch (Exception $e) {
    // Log any exceptions
    error_log('Exception: ' . $e->getMessage());
    // Output an error message
   // echo 'Error: ' . $e->getMessage();
}