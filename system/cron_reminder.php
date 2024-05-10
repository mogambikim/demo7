<?php
/**
 * PHP Mikrotik Billing (https://gl/)
 * This file for reminding user about expiration
 * Example to run every at 7:00 in the morning
 * 0 7 * * * /usr/bin/php /var/www/system/cron_reminder.php
 */

// Check if the script is being run from the command line
if (php_sapi_name() !== 'cli') {
    // If not, exit with an error message
    die("This script can only be run from the command line.");
}

include "../init.php";
$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}

// Rest of the script...

//files were deleted here
$d = ORM::for_table('tbl_user_recharges')->where('status', 'on')->find_many();
run_hook('cronjob_reminder'); #HOOK

echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}

$currentDate = date('Y-m-d');
foreach ($d as $ds) {
    $daysRemaining = (strtotime($ds['expiration']) - strtotime($currentDate)) / (60 * 60 * 24);
    if ($daysRemaining >= 7 && $daysRemaining < 8) {
        $u = ORM::for_table('tbl_user_recharges')->where('id', $ds['id'])->find_one();
        $p = ORM::for_table('tbl_plans')->where('id', $u['plan_id'])->find_one();
        $c = ORM::for_table('tbl_customers')->where('id', $ds['customer_id'])->find_one();
        $price = Lang::moneyFormat($p['price']);
        echo Message::sendPackageNotification($c, $p['name_plan'], $price, Lang::getNotifText('reminder_7_day'), $config['user_notification_reminder']) . "\n";
    } else if ($daysRemaining >= 3 && $daysRemaining < 4) {
        $u = ORM::for_table('tbl_user_recharges')->where('id', $ds['id'])->find_one();
        $p = ORM::for_table('tbl_plans')->where('id', $u['plan_id'])->find_one();
        $c = ORM::for_table('tbl_customers')->where('id', $ds['customer_id'])->find_one();
        $price = Lang::moneyFormat($p['price']);
        echo Message::sendPackageNotification($c, $p['name_plan'], $price, Lang::getNotifText('reminder_3_day'), $config['user_notification_reminder']) . "\n";
    } else if ($daysRemaining >= 1 && $daysRemaining < 2) {
        $u = ORM::for_table('tbl_user_recharges')->where('id', $ds['id'])->find_one();
        $p = ORM::for_table('tbl_plans')->where('id', $u['plan_id'])->find_one();
        $c = ORM::for_table('tbl_customers')->where('id', $ds['customer_id'])->find_one();
        $price = Lang::moneyFormat($p['price']);
        echo Message::sendPackageNotification($c, $p['name_plan'], $price, Lang::getNotifText('reminder_1_day'), $config['user_notification_reminder']) . "\n";
    }
}