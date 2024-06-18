<?php

use PEAR2\Net\RouterOS;

register_menu(" Interface Monitor", true, "interface_ui", 'NETWORK', '', "New", "green");

function interface_ui() {
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Interface Monitor');
    $ui->assign('_system_menu', 'Interface Monitor');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $routerId = $routes['2'] ?? $routers[0]['id'];
    $interfaces = get_interfaces_list();
    $ui->assign('xheader', '
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th.custom-class {
                background-color: #f2f2f2;
                color: #000;
                font-weight: bold;
            }
            tr.even-row {
                background-color: #f2f2f2;
            }
            tr.custom-class {
                color: blue;
                font-weight: bold;
            }
            #ppp-table th, #ppp-table td {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                width: 100px;
            }
            .chart-canvas {
                width: 100px;
                height: 80px;
            }
            .chart-canvas {
                width: 400px;
                height: 200px;
            }
            .form-control {
                width: 200px;
                height: 30px;
            }
        </style>
    ');

    $ui->assign('routers', $routers);
    $ui->assign('router', $routerId);
    $ui->assign('interfaces', $interfaces);

    // Tampilkan template monitor_interface.tpl menggunakan Smarty
    $ui->display('monitor_interface.tpl');
}

function interface_get_data() {
    global $routes;
    $routerId = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
    $client = new RouterOS\Client($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

    // Fungsi untuk mendapatkan daftar interface
    $interfaceList = get_interfaces_list();

    // Fungsi untuk memformat bytes
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Fungsi untuk mendapatkan data interface
    function monitor_traffic($interface)
    {
        global $routes;
        $router = $routes['2'];
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        try {
            $results = $client->sendSync(
                (new RouterOS\Request('/interface/monitor-traffic'))
                    ->setArgument('interface', $interface)
                    ->setArgument('once', '')
            );

            $rows = [];
            $labels = [];

            foreach ($results as $result) {
                $ftx = $result->getProperty('tx-bits-per-second');
                $frx = $result->getProperty('rx-bits-per-second');

                $rows[] = [
                    'tx' => $ftx,
                    'rx' => $frx
                ];
                $labels[] = date('H:i:s');
            }

            $result = [
                'labels' => $labels,
                'rows' => $rows
            ];

            return $result;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Fungsi untuk memperbarui data traffic
    function traffic_update()
    {
        global $routes;
        $router = $routes['2'];
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $traffic = $client->sendSync(new RouterOS\Request('/interface/print'));

        $interfaceData = [];
        foreach ($traffic as $interface) {
            $name = $interface->getProperty('name');
            // Skip interfaces with missing names
            if (empty($name)) {
                continue;
            }

            $txBytes = intval($interface->getProperty('tx-byte'));
            $rxBytes = intval($interface->getProperty('rx-byte'));
            $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $interfaceData[] = [
                'name' => $name,
                'status' => $interface->getProperty('running') === 'true' ? '
                <b>Internet : </b> <small class="label bg-green">up</small>' : '
                <b>Internet : </b> <small class="label bg-red">down</small>',
                'tx' => formatBytes($txBytes),
                'rx' => formatBytes($rxBytes),
                'total' => formatBytes($txBytes + $rxBytes)
            ];
        }
    }

    // Kembalikan hasil jika perlu
    return $interfaceList;
}

function monitor_traffic()
{
    $interface = $_GET["interface"]; // Ambil interface dari parameter GET

    // Contoh koneksi ke MikroTik menggunakan library tertentu (misalnya menggunakan ORM dan MikroTik API wrapper)
    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

    try {
        $results = $client->sendSync(
            (new RouterOS\Request('/interface/monitor-traffic'))
                ->setArgument('interface', $interface)
                ->setArgument('once', '')
        );

        $rows = array();
        $rows2 = array();
        $labels = array();

        foreach ($results as $result) {
            $ftx = $result->getProperty('tx-bits-per-second');
            $frx = $result->getProperty('rx-bits-per-second');

            // Timestamp dalam milidetik (millisecond)
            $timestamp = time() * 1000;

            $rows[] = $ftx;
            $rows2[] = $frx;
            $labels[] = $timestamp; // Tambahkan timestamp ke dalam array labels
        }

        $result = array(
            'labels' => $labels,
            'rows' => array(
                'tx' => $rows,
                'rx' => $rows2
            )
        );
    } catch (Exception $e) {
        $result = array('error' => $e->getMessage());
    }

    // Set header untuk respons JSON
    header('Content-Type: application/json');
    echo json_encode($result);
}

function traffic_update()
{
    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $traffic = $client->sendSync(new RouterOS\Request('/interface/print'));

    $interfaceData = [];
    foreach ($traffic as $interface) {
        $name = $interface->getProperty('name');
        // Skip interfaces with missing names
        if (empty($name)) {
            continue;
        }

        $txBytes = intval($interface->getProperty('tx-byte'));
        $rxBytes = intval($interface->getProperty('rx-byte'));
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $interfaceData[] = [
            'name' => $name,
            'status' => $interface->getProperty('running') === 'true' ? '
            <b>Internet : </b> <small class="label bg-green">up</small>' : '
            <b>Internet : </b> <small class="label bg-red">down</small>',
            'tx' => mikrotik_monitor_formatBytes($txBytes),
            'rx' => mikrotik_monitor_formatBytes($rxBytes),
            'total' => mikrotik_monitor_formatBytes($txBytes + $rxBytes)
        ];
    }
}