<?php

require_once __DIR__ . '/../../config.php';

register_menu("Mpesa Settings", true, "mpesa_settings", 'SETTINGS', '');
$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);

function mpesa_settings()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'Mpesa Settings');
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update the settings in the database
        update_option('mpesa_settings_consumer_key', _post('mpesa_settings_consumer_key'));
        update_option('mpesa_settings_consumer_secret', _post('mpesa_settings_consumer_secret'));
        update_option('mpesa_settings_business_code', _post('mpesa_settings_business_code'));
        update_option('mpesa_settings_pass_key', _post('mpesa_settings_pass_key'));
        update_option('mpesa_hashback_api', _post('mpesa_hashback_api'));

        $message = 'Settings successfully saved';
    }

    // Get the current settings from the database
    $mpesaSettingsConsumerKey = get_option('mpesa_settings_consumer_key', '');
    $mpesaSettingsConsumerSecret = get_option('mpesa_settings_consumer_secret', '');
    $mpesaSettingsBusinessCode = get_option('mpesa_settings_business_code', '');
    $mpesaSettingsPassKey = get_option('mpesa_settings_pass_key', '');
    $mpesaHashbackApi = get_option('mpesa_hashback_api', '');

    // Assign the current settings to the UI
    $ui->assign('mpesa_settings_consumer_key', $mpesaSettingsConsumerKey);
    $ui->assign('mpesa_settings_consumer_secret', $mpesaSettingsConsumerSecret);
    $ui->assign('mpesa_settings_business_code', $mpesaSettingsBusinessCode);
    $ui->assign('mpesa_settings_pass_key', $mpesaSettingsPassKey);
    $ui->assign('mpesa_hashback_api', $mpesaHashbackApi);

    // Assign the success message to the UI
    $ui->assign('message', $message);

    //$admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('mpesa_settings.tpl');
}