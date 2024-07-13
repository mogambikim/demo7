<?php
use PEAR2\Net\RouterOS;
use PEAR2\Net\RouterOS\Query;
use PEAR2\Net\RouterOS\Request;

include "../init.php";

$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}

$logFile = __DIR__ . '/../failed_payment_stripe.log';

// Function to write log messages to a file with a limit of 5000 lines
function logMessage($message) {
    global $logFile;
    $maxLines = 5000;
    
    // Read existing log file
    $logs = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES) : [];
    
    // Add new log message
    $logs[] = $message;
    
    // Keep only the last 5000 lines
    if (count($logs) > $maxLines) {
        $logs = array_slice($logs, -$maxLines);
    }
    
    // Write back to the log file
    file_put_contents($logFile, implode("\n", $logs) . "\n");
}

logMessage("PHP Time\t" . date('Y-m-d H:i:s'));
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    logMessage("MYSQL Time\t" . $row['WAKTU']);
}

echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}

// Define the function to remove inactive Hotspot users from the users list
function removeInactiveHotspotUsers($client, $username) {
    // Fetch all Hotspot users
    $usersRequest = new RouterOS\Request('/ip/hotspot/user/print');
    $users = $client->sendSync($usersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

    foreach ($users as $user) {
        if ($user->getProperty('name') === $username) {
            logMessage("Checking user: $username"); // Log user being checked

            // Check if the user is in tbl_user_recharges with status 'off' and type 'Hotspot'
            $userRecharge = ORM::for_table('tbl_user_recharges')
                ->where('username', $username)
                ->where('status', 'off')
                ->where('type', 'Hotspot')
                ->find_one();

            // Check if user is not in the database or is inactive
            if (!$userRecharge) {
                $userRechargeExists = ORM::for_table('tbl_user_recharges')
                    ->where('username', $username)
                    ->find_one();

                if (!$userRechargeExists) {
                    logMessage("User $username is not in the database. Removing from Hotspot users list and active users...");
                    removeUserFromHotspot($client, $user);
                } else {
                    logMessage("User $username is active in the database. Skipping...");
                    continue; // Skip active users
                }
            } else {
                logMessage("User $username is inactive in the database. Removing from Hotspot users list and active users...");
                removeUserFromHotspot($client, $user);
            }
        }
    }
}

// Helper function to remove a user from Hotspot users and active users
function removeUserFromHotspot($client, $user) {
    try {
        $username = $user->getProperty('name');
        // Remove the user from Hotspot users list
        $removeUserRequest = new RouterOS\Request('/ip/hotspot/user/remove');
        $removeUserRequest->setArgument('.id', $user->getProperty('.id'));
        $client->sendSync($removeUserRequest);
        logMessage("Removed user $username from Hotspot users list");

        // Remove the user from Hotspot active users
        $activeUsersRequest = new RouterOS\Request('/ip/hotspot/active/print');
        $activeUsers = $client->sendSync($activeUsersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

        foreach ($activeUsers as $activeUser) {
            if ($activeUser->getProperty('user') == $username) {
                $removeActiveUserRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                $removeActiveUserRequest->setArgument('.id', $activeUser->getProperty('.id'));
                $client->sendSync($removeActiveUserRequest);
                logMessage("Removed inactive Hotspot user: $username from active users");
                break;
            }
        }
    } catch (Exception $e) {
        logMessage("Error removing Hotspot user $username: " . $e->getMessage());
    }
}

// Webhook endpoint to handle failed transactions from Stripe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = @file_get_contents("php://input");
    $event = json_decode($input);

    // Validate the event object
    if (json_last_error() === JSON_ERROR_NONE && isset($event->type)) {
        logMessage("Received event: " . $event->type);

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_failed':
                $username = $event->data->object->metadata->username;
                logMessage("Failed payment for username: $username");

                // Fetch all routers
                $routers = ORM::for_table('tbl_routers')->find_many();

                foreach ($routers as $router) {
                    try {
                        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
                        removeInactiveHotspotUsers($client, $username);
                    } catch (Exception $e) {
                        logMessage("Error with router ID " . $router['id'] . ": " . $e->getMessage());
                    }
                }
                break;
            // Add more cases to handle other event types if needed
        }
    } else {
        logMessage("Invalid event received");
    }
}

if (!$isCli) {
    echo "</pre>";
}
?>
