<?php


require_once __DIR__ . '/../../config.php';

register_menu("KopoKopo Settings", true, "kopokopo_settings", 'SETTINGS', '');
$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
// rest of your code...
function get_option($option_name, $default = '') {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = ?");
        $stmt->bindValue(1, $option_name);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['value'] : $default;
    } catch (PDOException $e) {
        error_log("Error getting option: " . $e->getMessage(), 3, __DIR__ . "/error.log");
        return $default;
    }
}
function update_option($option_name, $new_value) {
    global $conn;
    try {
        // Check if the setting already exists
        $checkStmt = $conn->prepare("SELECT 1 FROM tbl_appconfig WHERE setting = ?");
        $checkStmt->bindValue(1, $option_name);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn()) {
            // The setting exists, update it
            $stmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = ?");
            $stmt->bindValue(1, $new_value);
            $stmt->bindValue(2, $option_name);
        } else {
            // The setting doesn't exist, insert it
            $stmt = $conn->prepare("INSERT INTO tbl_appconfig (setting, value) VALUES (?, ?)");
            $stmt->bindValue(1, $option_name);
            $stmt->bindValue(2, $new_value);
        }

        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error updating option: " . $e->getMessage(), 3, __DIR__ . "/error.log");
    }
}
function kopokopo_settings()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'KopoKopo Settings');
    $ui->assign('_system_menu', 'settings');

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update the settings in the database
        update_option('kopokopo_client_id', _post('kopokopo_client_id'));
        update_option('kopokopo_client_secret', _post('kopokopo_client_secret'));
        update_option('kopokopo_api_key', _post('kopokopo_api_key'));
        update_option('kopokopo_till_number', _post('kopokopo_till_number'));
        update_option('kopokopo_webhook', _post('kopokopo_webhook'));
     

        $message = 'Settings successfully saved';

        // Include the subscribe_kopokopo.php script
        require __DIR__ . '/../../subscribe_kopokopo.php';

        update_option('hashback_api', _post('hashback_api'));
    
    }

    // Get the current settings from the database
    $kopokopoClientId = get_option('kopokopo_client_id', '');
    $kopokopoClientSecret = get_option('kopokopo_client_secret', '');
    $kopokopoApiKey = get_option('kopokopo_api_key', '');
    $kopokopoTillNumber = get_option('kopokopo_till_number', '');
    $kopokopoWebhook = get_option('kopokopo_webhook', '');
    $hashbackApiKey = get_option('hashback_api', '');

    // Assign the current settings to the UI
    $ui->assign('kopokopo_client_id', $kopokopoClientId);
    $ui->assign('kopokopo_client_secret', $kopokopoClientSecret);
    $ui->assign('kopokopo_api_key', $kopokopoApiKey);
    $ui->assign('kopokopo_till_number', $kopokopoTillNumber);
    $ui->assign('kopokopo_webhook', $kopokopoWebhook);
    $ui->assign('hashback_api',  $hashbackApiKey);

    // Assign the success message to the UI
    $ui->assign('message', $message);

    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('kopokopo_settings.tpl');
}