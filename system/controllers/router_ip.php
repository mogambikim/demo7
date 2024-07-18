<?php
include "../init.php";

/**
 * PHP Mikrotik Billing (https://freeispradius.com/)
 * by https://t.me/freeispradius
 **/

_admin();
$ui->assign('_title', Lang::T('Router IP Addresses'));
$ui->assign('_system_menu', 'networ');

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';

// Function to get IP addresses from MikroTik
function getIPAddresses($router_id) {
    $router = ORM::for_table('tbl_routers')->find_one($router_id);

    if ($router) {
        try {
            $client = new RouterOS\Client($router->ip_address, $router->username, $router->password);
            $ipAddressRequest = new RouterOS\Request('/ip/address/print');
            $response = $client->sendSync($ipAddressRequest);

            $ipAddresses = [];
            foreach ($response as $entry) {
                $ipAddresses[] = [
                    'address' => $entry->getProperty('address'),
                    'network' => $entry->getProperty('network'),
                    'interface' => $entry->getProperty('interface'),
                ];
            }

            return $ipAddresses;
        } catch (Exception $e) {
            // Handle the exception as needed
        }
    }

    return [];
}

// Function to add a new IP address to MikroTik
function addIPAddress($router_id, $address, $network, $interface) {
    $router = ORM::for_table('tbl_routers')->find_one($router_id);

    if ($router) {
        try {
            $client = new RouterOS\Client($router->ip_address, $router->username, $router->password);
            $addRequest = new RouterOS\Request('/ip/address/add', [
                'address' => $address,
                'network' => $network,
                'interface' => $interface,
            ]);
            $response = $client->sendSync($addRequest);

            return $response->getType() === RouterOS\Response::TYPE_FINAL;
        } catch (Exception $e) {
            // Handle the exception as needed
        }
    }

    return false;
}

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
}

// Extract the action and query parameters
$action = strtok($routes['1'], '?');
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($queryString, $queryParams);

switch ($action) {
    case 'view-ips':
        if (isset($queryParams['id'])) {
            $router_id = $queryParams['id'];
            $ipAddresses = getIPAddresses($router_id);

            $ui->assign('router_id', $router_id);
            $ui->assign('ipAddresses', $ipAddresses);
            $ui->display('router_ip.tpl');
        } else {
            $routers = ORM::for_table('tbl_routers')->find_many();
            $ui->assign('routers', $routers);
            $ui->display('select_router.tpl');
        }
        break;

    case 'add-ip':
        if (isset($_POST['router_id'])) {
            $router_id = $_POST['router_id'];
            $address = $_POST['address'];
            $network = $_POST['network'];
            $interface = $_POST['interface'];
            
            $result = addIPAddress($router_id, $address, $network, $interface);

            if ($result) {
                r2(U . 'router_ip/view-ips?id=' . $router_id, 's', Lang::T('IP address added successfully.'));
            } else {
                r2(U . 'router_ip/view-ips?id=' . $router_id, 'e', Lang::T('Failed to add IP address.'));
            }
        } else {
            r2(U . 'routers/list', 'e', Lang::T('Router ID not specified.'));
        }
        break;

    default:
        $routers = ORM::for_table('tbl_routers')->find_many();
        $ui->assign('routers', $routers);
        $ui->display('select_router.tpl');
        break;
}
?>
