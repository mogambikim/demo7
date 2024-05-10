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
        $price = Lang::moneyFormat($p['price']);
        echo Message::sendPackageNotification($c, $p['name_plan'], $price, Lang::getNotifText('reminder_1_day'), $config['user_notification_reminder']) . "\n";
    }
}