<?php
use PEAR2\Net\RouterOS;

// Fungsi untuk mendaftarkan menu pemantauan beban CPU
register_menu("CPU Load", true, "cpuload_ui", 'AFTER_SETTINGS', 'fa fa-pie-chart', "New", "green");

function cpuload_ui()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Pemantauan Beban');
    $ui->assign('_system_menu', 'Pemantauan Beban');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $router = $routes[2] ?? $routers[0]['id'];
    // Navbar
    $ui->assign('xheader', '
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card {
            flex: 1 1 calc(33.333% - 1rem);
            margin-bottom: 1rem;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header-bg-info {
            background-color: #0d6efd; /* Bootstrap primary color */
            color: #fff;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-header-bg-success {
            background-color: #009174; /* Bootstrap primary color */
            color: #fff;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-header-bg-warning {
            background-color: #d0d414; /* Bootstrap primary color */
            color: #fff;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-header-bg-danger {
            background-color: #12b9bd; /* Bootstrap primary color */
            color: #fff;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            padding: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
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
        #ppp-table th,
        #ppp-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100px;
        }
        .chart-canvas {
            width: 100px;
            height: 80px;
        }
        @media only screen and (max-width: 768px) {
            .card {
                flex: 1 1 calc(100% - 1rem);
            }
        }
        @media only screen and (min-width: 769px) {
            .card {
                flex: 1 1 calc(33.333% - 1rem);
            }
        }
    </style>
');

$ui->assign('routers', $routers);
$ui->assign('router', $router);
$ui->display('cpuload.tpl');

    
}


function monitoring()
{
    global $routes;
    $router = $routes[2];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $health = $client->sendSync(new RouterOS\Request('/system health print'));
    $res = $client->sendSync(new RouterOS\Request('/system resource print'));

    function mikrotik_monitor_formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    echo '
        <div class="card-container">
            <div class="card">
                <div class="card-header-bg-info text-white">Platform Information</div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr><th>Platform</th><td>' . $res->getProperty('platform') . '</td></tr>
                            <tr><th>Board</th><td>' . $res->getProperty('board-name') . '</td></tr>
                            <tr><th>Arch</th><td>' . $res->getProperty('architecture-name') . '</td></tr>
                            <tr><th>Version</th><td>' . $res->getProperty('version') . '</td></tr>
                            <tr><th>Mem used/free</th><td>' . mikrotik_monitor_formatSize($res->getProperty('total-memory') - $res->getProperty('free-memory')) . ' / ' . mikrotik_monitor_formatSize($res->getProperty('free-memory')) . '</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header-bg-success text-white">System Information</div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr><th>Uptime</th><td>' . $res->getProperty('uptime') . '</td></tr>
                            <tr><th>Build time</th><td>' . $res->getProperty('build-time') . '</td></tr>
                            <tr><th>Factory Software</th><td>' . $res->getProperty('factory-software') . '</td></tr>
                            <tr><th>Free Hdd Space</th><td>' . mikrotik_monitor_formatSize($res->getProperty('free-hdd-space')) . '</td></tr>
                            <tr><th>Total Memory</th><td>' . mikrotik_monitor_formatSize($res->getProperty('total-memory')) . '</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header-bg-warning text-white">Hardware Information</div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr><th>CPU</th><td>' . $res->getProperty('cpu') . '</td></tr>
                            <tr><th>CPU count/freq/load</th><td>' . $res->getProperty('cpu-count') . '/' . $res->getProperty('cpu-frequency') . '/' . $res->getProperty('cpu-load') . '</td></tr>
                            <tr><th>Hdd</th><td>' . mikrotik_monitor_formatSize($res->getProperty('free-hdd-space')) . ' / ' . mikrotik_monitor_formatSize($res->getProperty('total-hdd-space')) . '</td></tr>
                            <tr><th>Write Total</th><td>' . $res->getProperty('write-sect-total') . '</td></tr>
                            <tr><th>Write Since Reboot</th><td>' . $res->getProperty('write-sect-since-reboot') . '</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    ';
}

function get_monitoring_data()
{
    global $routes;
    $router = $routes[2];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $health = $client->sendSync(new RouterOS\Request('/system health print'));
    $res = $client->sendSync(new RouterOS\Request('/system resource print'));

    $cpu_load = $res->getProperty('cpu-load') ?? 'N/A';
    $temperature = $health->getProperty('temperature') ?? 'N/A';
    $voltage = $health->getProperty('voltage') ?? 'N/A';
    $current_time = date('H:i:s'); // Assuming current time formatted as 'HH:MM:SS'
    
    $data = [
        'cpu_load' => $cpu_load,
        'temperature' => $temperature,
        'voltage' => $voltage,
        'current_time' => $current_time
    ];

// Convert array to JSON and output
echo json_encode($data);
}