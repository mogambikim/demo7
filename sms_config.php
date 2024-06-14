<?php

// Database ultimatesms
$sms_db_host     = 'localhost';
$sms_db_user     = 'root';
$sms_db_password = '';
$sms_db_name     = 'ultimatesms';

$_sms_app_stage = 'Live';

if ($_sms_app_stage != 'Live') {
    error_reporting(E_ERROR);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}
?>
