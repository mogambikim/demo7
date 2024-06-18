<?php
include "../init.php";

/**
 * PHP Mikrotik Billing (https://freeispradius.com/)
 * by https://t.me/freeispradius
 **/

_admin();
$ui->assign('_title', Lang::T('Router Backups'));
$ui->assign('_system_menu', 'networ');

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';

// Function to download backup
function downloadBackup($backup_id) {
    $backup = ORM::for_table('tbl_router_backups')->find_one($backup_id);

    if ($backup) {
        $backupPath = realpath($backup->file_path);
        if ($backupPath) {
            return $backupPath;
        }
    }

    return false;
}

// Function to restore backup
function restoreBackup($backup_id) {
    $backup = ORM::for_table('tbl_router_backups')->find_one($backup_id);

    if ($backup) {
        $backupPath = realpath($backup->file_path);

        if (file_exists($backupPath)) {
            $backupData = file_get_contents($backupPath);
            $router = ORM::for_table('tbl_routers')->find_one($backup->router_id);

            if ($router) {
                try {
                    $client = new RouterOS\Client($router->ip_address, $router->username, $router->password);
                    $fileListRequest = new RouterOS\Request('/file/print');
                    $response = $client->sendSync($fileListRequest);
                    $fileExistsOnRouter = false;

                    foreach ($response as $entry) {
                        if ($entry->getProperty('name') == basename($backupPath)) {
                            $fileExistsOnRouter = true;
                            break;
                        }
                    }

                    if ($fileExistsOnRouter) {
                        $restoreCommand = '/system backup load name="' . basename($backupPath) . '"';
                        $restoreRequest = new RouterOS\Request($restoreCommand);
                        $restoreResponse = $client->sendSync($restoreRequest);

                        if ($restoreResponse->getType() === RouterOS\Response::TYPE_FINAL) {
                            return true;
                        }
                    }
                } catch (Exception $e) {
                    // Handle the exception as needed
                }
            }
        }
    }

    return false;
}

// Function to delete backup
function deleteBackup($backup_id) {
    $backup = ORM::for_table('tbl_router_backups')->find_one($backup_id);

    if ($backup) {
        $backupPath = realpath($backup->file_path);

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        $backup->delete();
        return true;
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
    case 'backup':
        $routers = ORM::for_table('tbl_routers')->find_many();
        $backups = ORM::for_table('tbl_router_backups')
            ->select('tbl_router_backups.*')
            ->select('tbl_routers.name', 'router_name')
            ->join('tbl_routers', array('tbl_router_backups.router_id', '=', 'tbl_routers.id'))
            ->order_by_desc('backup_date')
            ->limit(5)
            ->find_array();

        $ui->assign('routers', $routers);
        $ui->assign('backups', $backups);
        $ui->display('router_backup.tpl');
        break;

    case 'download-backup':
        $route_parts = explode('?', $_GET['_route']);
        parse_str($route_parts[1], $query_params);

        if (isset($query_params['id'])) {
            $backup_id = $query_params['id'];
            $backupFile = downloadBackup($backup_id);

            if ($backupFile) {
                $filePath = realpath($backupFile);
                if (file_exists($filePath)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                    exit;
                } else {
                    r2(U . 'router_backups/backup', 'e', Lang::T('File not found.'));
                }
            } else {
                r2(U . 'router_backups/backup', 'e', Lang::T('Failed to download backup.'));
            }
        } else {
            r2(U . 'router_backups/backup', 'e', Lang::T('Backup ID not specified.'));
        }
        break;

    case 'restore-backup':
        $route_parts = explode('?', $_GET['_route']);
        parse_str($route_parts[1], $query_params);

        if (isset($query_params['id'])) {
            $backup_id = $query_params['id'];
            $result = restoreBackup($backup_id);

            if ($result) {
                r2(U . 'router_backups/backup', 's', Lang::T('Router restored successfully.'));
            } else {
                r2(U . 'router_backups/backup', 'e', Lang::T('Failed to restore router.'));
            }
        } else {
            r2(U . 'router_backups/backup', 'e', Lang::T('Backup ID not specified.'));
        }
        break;

    case 'delete-backup':
        $route_parts = explode('?', $_GET['_route']);
        parse_str($route_parts[1], $query_params);

        if (isset($query_params['id'])) {
            $backup_id = $query_params['id'];

            if (deleteBackup($backup_id)) {
                r2(U . 'router_backups/backup', 's', Lang::T('Backup deleted successfully.'));
            } else {
                r2(U . 'router_backups/backup', 'e', Lang::T('Failed to delete backup.'));
            }
        } else {
            r2(U . 'router_backups/backup', 'e', Lang::T('Backup ID not specified.'));
        }
        break;

    default:
        r2(U . 'routers/list', 's', '');
}
?>
