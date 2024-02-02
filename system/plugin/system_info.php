<?php
register_menu("System Info", true, "system_info", 'SETTINGS', '');

function system_info()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'System Information');
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reload']) && $_POST['reload'] === 'true') {
    $output = array();
    $retcode = 0;

    $os = strtoupper(PHP_OS);

    if (strpos($os, 'WIN') === 0) {
        // Windows OS
        exec('net stop freeradius', $output, $retcode);
        exec('net start freeradius', $output, $retcode);
    } else {
        // Linux OS
        exec('sudo systemctl restart freeradius.service 2>&1', $output, $retcode);
    }
    $ui->assign('output', $output);
    $ui->assign('returnCode', $retcode);
}

  function system_info_get_server_memory_usage()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = array();
        exec('wmic OS get TotalVisibleMemorySize, FreePhysicalMemory /Value', $output);

        $total_memory = null;
        $free_memory = null;

        foreach ($output as $line) {
            if (strpos($line, 'TotalVisibleMemorySize') !== false) {
                $total_memory = intval(preg_replace('/[^0-9]/', '', $line));
            } elseif (strpos($line, 'FreePhysicalMemory') !== false) {
                $free_memory = intval(preg_replace('/[^0-9]/', '', $line));
            }

            if ($total_memory !== null && $free_memory !== null) {
                break;
            }
        }

        if ($total_memory !== null && $free_memory !== null) {
            $total_memory = round($total_memory / 1024);
            $free_memory = round($free_memory / 1024);
            $used_memory = $total_memory - $free_memory;
            $memory_usage_percentage = round($used_memory / $total_memory * 100);

            $memory_usage = [
                'total' => $total_memory,
                'free' => $free_memory,
                'used' => $used_memory,
                'used_percentage' => round($memory_usage_percentage),
            ];

            return $memory_usage;
        }
    } else {
        // Linux system
        $free = shell_exec('free -m');
        $free = (string) trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);

        $total_memory = $mem[1];
        $used_memory = $mem[2];
        $free_memory = $total_memory - $used_memory;
        $memory_usage_percentage = round($used_memory / $total_memory * 100);

        $memory_usage = [
            'total' => $total_memory,
            'free' => $free_memory,
            'used' => $used_memory,
            'used_percentage' => round($memory_usage_percentage),
        ];

        return $memory_usage;
    }

    return null;
}

function system_info_getSystemInfo()
{
    $memory_usage = system_info_get_server_memory_usage();

    // Get the Idiorm ORM instance
    $db = ORM::getDb();
    $serverInfo = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    $databaseName = $db->query('SELECT DATABASE()')->fetchColumn();
    $serverName = gethostname();
    $shellExecEnabled = function_exists('shell_exec');

    // Fallback: Let's use $_SERVER['SERVER_NAME'] if gethostname() is not available
    if (!$serverName) {
        $serverName = $_SERVER['SERVER_NAME'];
    }

    // Retrieve the current time from the database
    $currentTime = $db->query('SELECT CURRENT_TIMESTAMP AS current_time_alias')->fetchColumn();

    $systemInfo = [
        'Server Name' => $serverName,
        'Operating System' => php_uname('s'),
        'System Distro' => system_info_getSystemDistro(),
        'PHP Version' => phpversion(),
        'Server Software' => $_SERVER['SERVER_SOFTWARE'],
        'Server IP Address' => $_SERVER['SERVER_ADDR'],
        'Server Port' => $_SERVER['SERVER_PORT'],
        'Remote IP Address' => $_SERVER['REMOTE_ADDR'],
        'Remote Port' => $_SERVER['REMOTE_PORT'],
        'Database Server' => $serverInfo,
        'Database Name' => $databaseName,
        'System Time' => date("F j, Y g:i a"),
        'Database Time' => date("F j, Y g:i a", strtotime($currentTime)),
        'Shell Exec Enabled' => $shellExecEnabled ? 'Yes' : 'No',

        // Add more system information here
    ];

    return $systemInfo;
}
//Lets get the storage usege
function system_info_get_disk_usage()
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = [];
        exec('wmic logicaldisk where "DeviceID=\'C:\'" get Size,FreeSpace /format:list', $output);

        if (!empty($output)) {
            $total_disk = 0;
            $free_disk = 0;

            foreach ($output as $line) {
                if (strpos($line, 'Size=') === 0) {
                    $total_disk = intval(substr($line, 5));
                } elseif (strpos($line, 'FreeSpace=') === 0) {
                    $free_disk = intval(substr($line, 10));
                }
            }

            $used_disk = $total_disk - $free_disk;
            $disk_usage_percentage = round(($used_disk / $total_disk) * 100, 2);

            $disk_usage = [
                'total' => system_info_format_bytes($total_disk),
                'used' => system_info_format_bytes($used_disk),
                'free' => system_info_format_bytes($free_disk),
                'used_percentage' => $disk_usage_percentage . '%',
            ];

            return $disk_usage;
        }
    } else {
        // Linux system
        $disk = shell_exec('df / --output=size,used,avail,pcent --block-size=1');
        $disk = (string) trim($disk);
        $disk_arr = explode("\n", $disk);
        $disk = explode(" ", preg_replace('/\s+/', ' ', $disk_arr[1]));
        $disk = array_filter($disk);
        $disk = array_merge($disk);

        $total_disk = $disk[0];
        $used_disk = $disk[1];
        $free_disk = $disk[2];
        $disk_usage_percentage = $disk[3];

        $disk_usage = [
            'total' => system_info_format_bytes($total_disk),
            'used' => system_info_format_bytes($used_disk),
            'free' => system_info_format_bytes($free_disk),
            'used_percentage' => $disk_usage_percentage,
        ];

        return $disk_usage;
    }

    return null;
}

function system_info_format_bytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function system_info_getSystemDistro()
{
    $distro = '';

    // Lets retrieve the system distribution based on the operating system
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        $distro = shell_exec('lsb_release -d');
        if ($distro) {
            $distro = trim(substr($distro, strpos($distro, ':') + 1));
        }
    } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $distro = system_info_getWindowsVersion();
    }

    // We can add more conditions for different operating systems if needed but only Windows and Linux for now

    return $distro;
}

function system_info_getWindowsVersion()
{
    $version = '';

    if (function_exists('shell_exec')) {

        $output = shell_exec('ver');
        if ($output) {
            $lines = explode("\n", $output);
            if (isset($lines[0])) {
                $version = trim($lines[0]);
            }
        }
    }

    if (empty($version) && function_exists('php_uname')) {

        $version = php_uname('v');
    }

    if (empty($version)) {

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $version = $_SERVER['SERVER_SOFTWARE'];
        } elseif (isset($_SERVER['WINDIR'])) {
            $version = $_SERVER['WINDIR'];
        }
    }

    return $version;
}
function system_info_generateServiceTable()
{
    function system_info_check_service($service_name)
    {
        if (empty($service_name)) {
            return false;
        }

        $os = strtoupper(PHP_OS);

        if (strpos($os, 'WIN') === 0) {
            // Windows OS
            $command = sprintf('sc query "%s" | findstr RUNNING', $service_name);
            exec($command, $output, $result_code);
            return $result_code === 0 || !empty($output);
        } else {
            // Linux OS
            $command = sprintf("pgrep %s", escapeshellarg($service_name));
            exec($command, $output, $result_code);
            return $result_code === 0;
        }
    }

    $services_to_check = array("FreeRADIUS", "MySQL", "MariaDB", "Cron", "SSHd");

    $table = array(
        'title' => 'Service Status',
        'rows' => array()
    );

    foreach ($services_to_check as $service_name) {
        $running = system_info_check_service(strtolower($service_name));
        $class = ($running) ? "label pull-right bg-green" : "label pull-right bg-red";
        $label = ($running) ? "running" : "not running";

        $value = sprintf('<small class="%s">%s</small>', $class, $label);

        $table['rows'][] = array($service_name, $value);
    }

    return $table;
}

    $systemInfo = system_info_getSystemInfo();

    $ui->assign('systemInfo', $systemInfo);
    $ui->assign('disk_usage', system_info_get_disk_usage());
    $ui->assign('memory_usage', system_info_get_server_memory_usage());
    $ui->assign('serviceTable', system_info_generateServiceTable());

    // Display the template
    $ui->display('system_info.tpl');
}
