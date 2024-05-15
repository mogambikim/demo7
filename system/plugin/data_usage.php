<?php

//session_start();
use PEAR2\Net\RouterOS;

//register_menu(" Data Usage", true, "data_usage", 'AFTER_SETTINGS', 'fa fa-bar-chart');
register_menu('<i class="fa fa-pie-chart"></i> Data Usage', false, 'data_usage_clients', 'AFTER_HISTORY');
register_menu('<i class="fa fa-bar-chart"></i> Live Traffic', false, 'data_usage_traffic', 'AFTER_HISTORY');

function data_usage()
{
  global $ui, $routes;
  _admin();
  $ui->assign('_title', 'Data Usage Monitor');
  $ui->assign('_system_menu', '');
  $admin = Admin::_info();
  $ui->assign('_admin', $admin);
  $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
  $router = $routes['2'];
  if (empty($router)) {
    $router = $routers[0]['id'];
  }
  
  
  $ui->display('data_usage.tpl');
}

// Function to format bytes into KB, MB, GB or TB
function data_usage_formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}


function data_usage_clients()
{
    global $ui, $routes;
    _auth();
    $ui->assign('_title', 'Data Usage Statistics');
    $ui->assign('_system_menu', '');
    $user = User::_info();
    $ui->assign('_user', $user);
    //$router = User::_billing();
    $router = ORM::for_table('tbl_user_recharges')->where('username', $user['username'])->findOne();
    $mikrotik = Mikrotik::info($router['routers']);
    $routerId = $mikrotik['id'];
    //$mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));

    $interfaceTraffic = $client->sendSync(new RouterOS\Request('/interface/print'));
    $interfaceData = [];
    foreach ($interfaceTraffic as $interface) {
        $name = $interface->getProperty('name');
        // Skip interfaces with missing names
        if (empty($name)) {
            continue;
        }

        $interfaceData[$name] = [
            'txBytes' => intval($interface->getProperty('tx-byte')),
            'rxBytes' => intval($interface->getProperty('rx-byte')),
        ];
    }

    //$userList = [];
    foreach ($pppUsers as $pppUser) {
        $username = $pppUser->getProperty('name');
        $address = $pppUser->getProperty('address');
        $uptime = $pppUser->getProperty('uptime');
        $service = $pppUser->getProperty('service');
        $callerid = $pppUser->getProperty('caller-id');
        // Retrieve user usage based on interface name
        $interfaceName = "<pppoe-$user[username]>";

        if ($username === $user['username'] && isset($interfaceData[$interfaceName])) {
            $trafficData = $interfaceData[$interfaceName];
            $txBytes = $trafficData['txBytes'];
            $rxBytes = $trafficData['rxBytes'];

            $userData[] = [
                'tx' => $txBytes,
                'rx' => $rxBytes,
                'total' => $txBytes + $rxBytes,
            ];
            $userTable[] = [
                'username' => $username,
                'address' => $address,
                'uptime' => $uptime,
                'service' => $service,
                'caller_id' => $callerid,
                'tx' => data_usage_formatBytes($txBytes),
                'rx' => data_usage_formatBytes($rxBytes),
                'total' => data_usage_formatBytes($txBytes + $rxBytes),
            ];

             // Update or insert the data usage record for the month
           // data_usage_insertOrUpdate($username, $txBytes, $rxBytes, $txBytes + $rxBytes);
        }
    }

    $jsonUserList = json_encode($userData);
    //echo $jsonUserList;
    $ui->assign('userList', $jsonUserList);
    $ui->assign('router', $routerId);
    $ui->assign('userTable', $userTable);
    $ui->assign('xheader', '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>');
    $ui->assign('xfooter', '');

    $ui->display('data_usage_clients.tpl');
}


function data_usage_traffic()
{
    global $ui, $routes;
    _auth();
    $ui->assign('_title', 'Live Traffic');
    $ui->assign('_system_menu', '');
    $user = User::_info();
    $ui->assign('_user', $user);
    //$router = User::_billing();
    $router = ORM::for_table('tbl_user_recharges')->where('username', $user['username'])->findOne();
    $mikrotik = Mikrotik::info($router['routers']);
    $routerId = $mikrotik['id'];
    //$mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));

    $interfaceTraffic = $client->sendSync(new RouterOS\Request('/interface/print'));
    $interfaceData = [];
    foreach ($interfaceTraffic as $interface) {
        $name = $interface->getProperty('name');
        // Skip interfaces with missing names
        if (empty($name)) {
            continue;
        }

        $interfaceData[$name] = [
            'txBytes' => intval($interface->getProperty('tx-byte')),
            'rxBytes' => intval($interface->getProperty('rx-byte')),
        ];
    }

    //$userList = [];
    foreach ($pppUsers as $pppUser) {
        $username = $pppUser->getProperty('name');
        $address = $pppUser->getProperty('address');
        $uptime = $pppUser->getProperty('uptime');
        $service = $pppUser->getProperty('service');
        $callerid = $pppUser->getProperty('caller-id');
        // Retrieve user usage based on interface name
        $interfaceName = "<pppoe-$user[username]>";

        if ($username === $user['username'] && isset($interfaceData[$interfaceName])) {
            $trafficData = $interfaceData[$interfaceName];
            $txBytes = $trafficData['txBytes'];
            $rxBytes = $trafficData['rxBytes'];

            $userData[] = [
                'tx' => $txBytes,
                'rx' => $rxBytes,
                'total' => $txBytes + $rxBytes,
            ];
            $userTable[] = [
                'username' => $username,
                'address' => $address,
                'uptime' => $uptime,
                'service' => $service,
                'caller_id' => $callerid,
                'tx' => data_usage_formatBytes($txBytes),
                'rx' => data_usage_formatBytes($rxBytes),
                'total' => data_usage_formatBytes($txBytes + $rxBytes),
            ];
        }
    }
    //$userName = $user['username'];
    $jsonUserList = json_encode($userData);
    $ui->assign('userList', $jsonUserList);
    $ui->assign('router', $routerId);
    $ui->assign('userTable', $userTable);
    $ui->assign('xheader', '');
    $ui->assign('xfooter', '');

    $ui->display('data_usage_traffic.tpl');
}


function data_usage_monitor_traffic()
{
    $router = $_GET["router"];
    $username = $_GET["username"];
    $interfaceName = "<pppoe-$username>";

    // Assuming $routes is an array containing router information
    global $routes;
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

    try {
        $results = $client->sendSync(
            (new RouterOS\Request('/interface/monitor-traffic'))
                ->setArgument('interface', $interfaceName)
                ->setArgument('once', '')
        );

        $txData = array();
        $rxData = array();
        $labels = array();

        foreach ($results as $result) {
            $ftx = $result->getProperty('tx-bits-per-second');
            $frx = $result->getProperty('rx-bits-per-second');
        
            $txData[] = (float) $ftx;
            $rxData[] = (float) $frx;
            
            // Add the label to the labels array
            $labels[] = date('h:i:s A');
        }

        $result = array(
            'labels' => $labels,
            'rows' => array(
                'tx' => $txData, 
                'rx' => $rxData
            )
        );
    } catch (Exception $e) {
        $result = array('error' => $e->getMessage());
    }

    // Debug response
    error_log(json_encode($result));

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
}

function data_usage_insertOrUpdate($username, $txBytes, $rxBytes, $totalBytes)
{
    $tableName = 'tbl_data_usage';

    $tableExists = false;
    $stmt = ORM::get_db()->query("SHOW TABLES LIKE '$tableName'");
    if ($stmt) {
        $tableExists = $stmt->rowCount() > 0;
    }

    if (!$tableExists) {
        $createTableQuery = "CREATE TABLE $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255),
            tx_bytes BIGINT,
            rx_bytes BIGINT,
            total_bytes BIGINT,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        ORM::raw_execute($createTableQuery);
    }

    ORM::for_table($tableName)->create([
        'username' => $username,
        'tx_bytes' => $txBytes,
        'rx_bytes' => $rxBytes,
        'total_bytes' => $totalBytes,
        'timestamp' => date('Y-m-d H:i:s'), // Set the current timestamp
    ])->save();
}