<?php

use PEAR2\Net\RouterOS;

include "../init.php";

$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}

echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}

$_c = $config;
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
    try {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
        
        // Perform a simple API request to check connectivity
        $pingRequest = new RouterOS\Request('/system/identity/print');
        $pingResponse = $client->sendSync($pingRequest);
        
        $resourceRequest = new RouterOS\Request('/system/resource/print');
        $resourceResponse = $client->sendSync($resourceRequest);
        
        $uptime = $resourceResponse->getProperty('uptime');
        $model = $resourceResponse->getProperty('board-name');
        
        $cache = ORM::for_table('tbl_router_cache')->where('router_id', $router['id'])->find_one();
        
        if ($cache) {
            // If an entry exists, update it with the new information
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Online', uptime = ?, model = ? WHERE router_id = ?";
            $updateParams = array($uptime, $model, $router['id']);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated successfully.\n";
        } else {
            // If no entry exists, create a new one
            $cache = ORM::for_table('tbl_router_cache')->create();
            $cache->router_id = $router['id'];
            $cache->state = 'Online';
            $cache->uptime = $uptime;
            $cache->model = $model;
            $cache->save();
            
            echo "Router " . $router['id'] . " added successfully.\n";
        }
    } catch (Exception $e) {
        // Check if an entry exists in the cache table
        $cache = ORM::for_table('tbl_router_cache')->where('router_id', $router['id'])->find_one();
        
        if ($cache) {
            // If an entry exists, update it with the offline status
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Offline', uptime = NULL, model = NULL WHERE router_id = ?";
            $updateParams = array($router['id']);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated with offline status.\n";
        } else {
            // If no entry exists, create a new one with the offline status
            $cache = ORM::for_table('tbl_router_cache')->create();
            $cache->router_id = $router['id'];
            $cache->state = 'Offline';
            $cache->uptime = null;
            $cache->model = null;
            $cache->save();
            
            echo "Router " . $router['id'] . " added with offline status.\n";
        }
    }
}