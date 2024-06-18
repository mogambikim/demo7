<?php

use PEAR2\Net\RouterOS;
use PEAR2\Net\RouterOS\Query;
use PEAR2\Net\RouterOS\Request;

include "../init.php";

$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}

$logFile = __DIR__ . '/../removehotspot.log';

// Function to write log messages to a file with a limit of 1000 lines
function logMessage($message) {
    global $logFile;
    $maxLines = 5000;
    
    // Read existing log file
    $logs = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES) : [];
    
    // Add new log message
    $logs[] = $message;
    
    // Keep only the last 1000 lines
    if (count($logs) > $maxLines) {
        $logs = array_slice($logs, -$maxLines);
    }
    
    // Write back to the log file
    file_put_contents($logFile, implode("\n", $logs) . "\n");
}

logMessage("PHP Time\t" . date('Y-m-d H:i:s'));
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    logMessage("MYSQL Time\t" . $row['WAKTU']);
}

echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}

$_c = $config;// Check if columns `state` and `last_seen` exist in tbl_user_recharges, if not, add them
$columns = ORM::for_table('tbl_user_recharges')->raw_query("SHOW COLUMNS FROM `tbl_user_recharges` LIKE 'state'")->find_one();
if (!$columns) {
    ORM::raw_execute("ALTER TABLE `tbl_user_recharges` ADD COLUMN `state` VARCHAR(10) NOT NULL DEFAULT 'Offline'");
}

$columns = ORM::for_table('tbl_user_recharges')->raw_query("SHOW COLUMNS FROM `tbl_user_recharges` LIKE 'last_seen'")->find_one();
if (!$columns) {
    ORM::raw_execute("ALTER TABLE `tbl_user_recharges` ADD COLUMN `last_seen` DATETIME NULL DEFAULT NULL");
}

function handleNotificationsAndUpdateState($router, $currentState) {
    logMessage("Reached handleNotificationsAndUpdateState function for Router ID " . $router['id']);
    
    // Fetch the previous state directly using SQL
    $cacheQuery = "SELECT prev_state FROM tbl_router_cache WHERE router_id = :routerId";
    $cacheParams = array(':routerId' => $router['id']);
    $cacheStatement = ORM::get_db()->prepare($cacheQuery);
    $cacheStatement->execute($cacheParams);
    $cache = $cacheStatement->fetch(PDO::FETCH_ASSOC);

    if ($cache) {
        $previousState = $cache['prev_state'];
        logMessage("Router ID " . $router['id'] . ": Current state is " . $currentState . ", Previous state is " . $previousState);
        
        // Fetch the notification phone number from the app config
        $notificationConfig = ORM::for_table('tbl_appconfig')->where('setting', 'router_notifications')->find_one();
        $phone = $notificationConfig ? $notificationConfig->value : '';
        $router['notification_phone'] = $phone;
        logMessage("Router ID " . $router['id'] . ": Notification phone is " . $phone);
        
        if ($currentState === 'Offline' && $previousState !== 'Offline') {
            // Send offline notification
            $offlineMessage = "Router [[router_name]] ([[router_ip]]) is offline!";
            $message = str_replace('[[router_name]]', $router['name'], $offlineMessage);
            $message = str_replace('[[router_ip]]', $router['ip_address'], $message);
            
            Message::sendRouterStatusNotification($router, $message, 'sms');
            logMessage("Sent offline notification for Router ID " . $router['id']);
        } elseif ($currentState === 'Online' && $previousState !== 'Online') {
            // Send online notification
            $onlineMessage = "Router [[router_name]] ([[router_ip]]) is back online!";
            $message = str_replace('[[router_name]]', $router['name'], $onlineMessage);
            $message = str_replace('[[router_ip]]', $router['ip_address'], $message);
            
            Message::sendRouterStatusNotification($router, $message, 'sms');
            logMessage("Sent online notification for Router ID " . $router['id']);
        } else {
            logMessage("No state change for Router ID " . $router['id'] . ": Current state is " . $currentState . ", Previous state is " . $previousState);
        }

        // Update the previous state to the current state using direct SQL
        logMessage("Updating prev_state for Router ID " . $router['id'] . " from " . $previousState . " to " . $currentState);
        $updateQuery = "UPDATE tbl_router_cache SET prev_state = :currentState WHERE router_id = :routerId";
        $params = array(':currentState' => $currentState, ':routerId' => $router['id']);
        $result = ORM::raw_execute($updateQuery, $params);
        
        if ($result) {
            logMessage("Successfully updated prev_state for Router ID " . $router['id'] . " to " . $currentState);
        } else {
            logMessage("Failed to update prev_state for Router ID " . $router['id']);
        }
    } else {
        logMessage("No cache found for Router ID " . $router['id']);
    }
}

// Check if the prev_state column exists in tbl_router_cache, if not, add it
$columnCheck = ORM::for_table('tbl_router_cache')->raw_query("SHOW COLUMNS FROM `tbl_router_cache` LIKE 'prev_state'")->find_one();
if (!$columnCheck) {
    logMessage("Adding prev_state column to tbl_router_cache.");
    ORM::raw_execute("ALTER TABLE `tbl_router_cache` ADD COLUMN `prev_state` VARCHAR(10) DEFAULT NULL");
} else {
    logMessage("Column prev_state already exists in tbl_router_cache.");
}

// Check if the last_seen column exists in tbl_router_cache, if not, add it
$columnCheck = ORM::for_table('tbl_router_cache')->raw_query("SHOW COLUMNS FROM `tbl_router_cache` LIKE 'last_seen'")->find_one();
if (!$columnCheck) {
    logMessage("Adding last_seen column to tbl_router_cache.");
    ORM::raw_execute("ALTER TABLE `tbl_router_cache` ADD COLUMN `last_seen` DATETIME DEFAULT NULL");
} else {
    logMessage("Column last_seen already exists in tbl_router_cache.");
}

// Fetch all routers
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
    logMessage("Processing Router ID " . $router['id']);
    
    try {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
        
        // Perform a simple API request to check connectivity
        $pingRequest = new RouterOS\Request('/system/identity/print');
        $pingResponse = $client->sendSync($pingRequest);
        
        $resourceRequest = new RouterOS\Request('/system/resource/print');
        $resourceResponse = $client->sendSync($resourceRequest);
        
        $uptime = $resourceResponse->getProperty('uptime');
        $model = $resourceResponse->getProperty('board-name');
        
        $cache = ORM::for_table('tbl_router_cache')->where('router_id', $router['id'])->find_one();
        
        if ($cache) {
            // If an entry exists, update it with the new information
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Online', uptime = :uptime, model = :model, last_seen = NULL WHERE router_id = :routerId";
            $updateParams = array(':uptime' => $uptime, ':model' => $model, ':routerId' => $router['id']);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated successfully.\n";
        } else {
            // If no entry exists, create a new one
            $insertQuery = "INSERT INTO tbl_router_cache (router_id, state, uptime, model) VALUES (:routerId, 'Online', :uptime, :model)";
            $insertParams = array(':routerId' => $router['id'], ':uptime' => $uptime, ':model' => $model);
            $insertResult = ORM::raw_execute($insertQuery, $insertParams);
            
            echo "Router " . $router['id'] . " added successfully.\n";
        }

        logMessage("Calling handleNotificationsAndUpdateState for Router ID " . $router['id']);
        handleNotificationsAndUpdateState($router, 'Online');

    } catch (Exception $e) {
        logMessage("Error with router ID " . $router['id'] . ": " . $e->getMessage());
        
        // Check if an entry exists in the cache table
        $cache = ORM::for_table('tbl_router_cache')->where('router_id', $router['id'])->find_one();
        
        if ($cache) {
            // If an entry exists, update it with the offline status, keeping the model unchanged if it was already set
            $lastSeen = $cache['last_seen'] ? $cache['last_seen'] : date('Y-m-d H:i:s');
            $model = $cache['model'] ? $cache['model'] : $model;
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Offline', uptime = NULL, last_seen = :lastSeen WHERE router_id = :routerId";
            $updateParams = array(':routerId' => $router['id'], ':lastSeen' => $lastSeen);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated with offline status.\n";
        } else {
            // If no entry exists, create a new one with the offline status
            $insertQuery = "INSERT INTO tbl_router_cache (router_id, state, uptime, model, last_seen) VALUES (:routerId, 'Offline', NULL, :model, :lastSeen)";
            $insertParams = array(':routerId' => $router['id'], ':model' => $model, ':lastSeen' => date('Y-m-d H:i:s'));
            $insertResult = ORM::raw_execute($insertQuery, $insertParams);
            
            echo "Router " . $router['id'] . " added with offline status.\n";
        }

        logMessage("Calling handleNotificationsAndUpdateState for Router ID " . $router['id']);
        handleNotificationsAndUpdateState($router, 'Offline');
    }
}

function fetchDataUsageFromRouters($client, $customers) {
    $dataUsage = array();

    try {
        // Fetch queue list
        $printRequest = new RouterOS\Request('/queue/simple/print');
        $printRequest->setArgument('.proplist', '.id,name,bytes');
        $queueList = $client->sendSync($printRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

        foreach ($queueList as $queue) {
            $name = $queue->getProperty('name');
            $bytesData = $queue->getProperty('bytes');

            // Split the bytes data into upload and download values
            list($uploadBytes, $downloadBytes) = explode('/', $bytesData);

            // Initialize a flag to track if a customer is found for the queue
            $customerFound = false;

            // Identify the customer based on the queue name
            foreach ($customers as $customer) {


                if (strpos($name, '<hotspot-' . $customer['username'] . '>') !== false ||
                    strpos($name, '<pppoe-' . $customer['username'] . '>') !== false ||
                    strpos($name, 'Queue-' . $customer['username']) === 0) {

                    if (!isset($dataUsage[$customer['id']])) {
                        $dataUsage[$customer['id']] = array(
                            'upload' => 0,
                            'download' => 0
                        );
                    }

                    // Add the upload and download bytes to the customer's data usage
                    $dataUsage[$customer['id']]['upload'] += $uploadBytes;
                    $dataUsage[$customer['id']]['download'] += $downloadBytes;
                    // Set the flag to indicate that a customer is found for the queue
                    $customerFound = true;

                    break;
                }
            }

            // Log if no customer is found for the queue
            if (!$customerFound) {
            }
        }
    } catch (Exception $e) {
    }

    return $dataUsage;
}

function updateDataUsageInDatabase($customerId, $uploadUsage, $downloadUsage) {
    try {
        // Check if a record exists for the customer in the tbl_data_usage table
        $dataUsageRecord = ORM::for_table('tbl_data_usage')
            ->where('customer_id', $customerId)
            ->find_one();

        if ($dataUsageRecord) {
            // Record exists, compare the values and update accordingly
            $oldUpload = $dataUsageRecord->get('old_upload');
            $oldDownload = $dataUsageRecord->get('old_download');
            $prevUpload = $dataUsageRecord->get('prev_upload');
            $prevDownload = $dataUsageRecord->get('prev_download');

            if ($uploadUsage >= $prevUpload && $downloadUsage >= $prevDownload) {
                // Router hasn't rebooted, update the old and new data usage
                $uploadDiff = $uploadUsage - $prevUpload;
                $downloadDiff = $downloadUsage - $prevDownload;
                $dataUsageRecord->set('old_upload', $oldUpload + $uploadDiff);
                $dataUsageRecord->set('old_download', $oldDownload + $downloadDiff);
            } else {
                // Router has rebooted, calculate the difference and add it to the old data usage
                $uploadDiff = $uploadUsage - $prevUpload;
                $downloadDiff = $downloadUsage - $prevDownload;

                if ($uploadDiff < 0) {
                    $uploadDiff = $uploadUsage;
                }
                if ($downloadDiff < 0) {
                    $downloadDiff = $downloadUsage;
                }

                $dataUsageRecord->set('old_upload', $oldUpload + $uploadDiff);
                $dataUsageRecord->set('old_download', $oldDownload + $downloadDiff);
            }

            // Update the previous, new, upload, and download values
            $dataUsageRecord->set('prev_upload', $uploadUsage);
            $dataUsageRecord->set('prev_download', $downloadUsage);
            $dataUsageRecord->set('new_upload', $uploadUsage);
            $dataUsageRecord->set('new_download', $downloadUsage);
            $dataUsageRecord->set('upload', $oldUpload + $uploadDiff);
            $dataUsageRecord->set('download', $oldDownload + $downloadDiff);
            $dataUsageRecord->set('updated_at', date('Y-m-d H:i:s'));
            $dataUsageRecord->save();


            // Update daily data usage
            updateDailyDataUsage($customerId, $oldUpload + $uploadDiff, $oldDownload + $downloadDiff);

            // Update weekly data usage
            updateWeeklyDataUsage($customerId, $oldUpload + $uploadDiff, $oldDownload + $downloadDiff);

            // Update monthly data usage
            updateMonthlyDataUsage($customerId, $oldUpload + $uploadDiff, $oldDownload + $downloadDiff);

        } else {

            // Record doesn't exist, create a new record with the customer ID and data usage values
            $dataUsageRecord = ORM::for_table('tbl_data_usage')->create();
            $dataUsageRecord->set('customer_id', $customerId);
            $dataUsageRecord->set('old_upload', $uploadUsage);
            $dataUsageRecord->set('old_download', $downloadUsage);
            $dataUsageRecord->set('prev_upload', $uploadUsage);
            $dataUsageRecord->set('prev_download', $downloadUsage);
            $dataUsageRecord->set('new_upload', $uploadUsage);
            $dataUsageRecord->set('new_download', $downloadUsage);
            $dataUsageRecord->set('upload', $uploadUsage);
            $dataUsageRecord->set('download', $downloadUsage);
            $dataUsageRecord->set('updated_at', date('Y-m-d H:i:s'));
            $dataUsageRecord->save();

            // Update daily data usage
            updateDailyDataUsage($customerId, $uploadUsage, $downloadUsage);

            // Update weekly data usage
           updateWeeklyDataUsage($customerId, $uploadUsage, $downloadUsage);

            // Update monthly data usage
            updateMonthlyDataUsage($customerId, $uploadUsage, $downloadUsage);
        }
    } catch (Exception $e) {

    }
}

function updateDailyDataUsage($customerId, $uploadUsage, $downloadUsage) {
    try {

        // Check if the tbl_daily_data_usage table exists
        $dailyDataUsageTableExists = ORM::for_table('tbl_daily_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_daily_data_usage'")->find_one();

        if (!$dailyDataUsageTableExists) {
            // Create the tbl_daily_data_usage table
            $dailyDataUsageTableQuery = "CREATE TABLE `tbl_daily_data_usage` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) NOT NULL,
                `upload` bigint(20) NOT NULL DEFAULT '0',
                `download` bigint(20) NOT NULL DEFAULT '0',
                `yesterday_total_upload` bigint(20) NOT NULL DEFAULT '0',
                `yesterday_total_download` bigint(20) NOT NULL DEFAULT '0',
                `today_total_upload` bigint(20) NOT NULL DEFAULT '0',
                `today_total_download` bigint(20) NOT NULL DEFAULT '0',
                `date` date NOT NULL,
                PRIMARY KEY (`id`),
                KEY `customer_id` (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            ORM::raw_execute($dailyDataUsageTableQuery);
        } else {
        }

        // Check if the required columns exist in the tbl_daily_data_usage table
        $requiredColumns = ['yesterday_total_upload', 'yesterday_total_download', 'today_total_upload', 'today_total_download'];
        foreach ($requiredColumns as $column) {
            $columnExists = ORM::for_table('tbl_daily_data_usage')->raw_query("SHOW COLUMNS FROM `tbl_daily_data_usage` LIKE '$column'")->find_one();
            if (!$columnExists) {
                // Add the missing column to the table
                $alterQuery = "ALTER TABLE `tbl_daily_data_usage` ADD COLUMN `$column` bigint(20) NOT NULL DEFAULT '0'";
                ORM::raw_execute($alterQuery);
            }
        }

        // Get the previous day's date
        $previousDate = date('Y-m-d', strtotime('-1 day'));

        // Check if it's the first day of the month
        if (date('j') === '1') {

            // Set today's usage as the total usage from tbl_data_usage since the counters have been reset
            $todayUploadUsage = $uploadUsage;
            $todayDownloadUsage = $downloadUsage;

            // Create a new daily data usage record for the current date
            $dailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')->create();
            $dailyDataUsageRecord->set('customer_id', $customerId);
            $dailyDataUsageRecord->set('upload', $todayUploadUsage);
            $dailyDataUsageRecord->set('download', $todayDownloadUsage);
            $dailyDataUsageRecord->set('yesterday_total_upload', 0);
            $dailyDataUsageRecord->set('yesterday_total_download', 0);
            $dailyDataUsageRecord->set('today_total_upload', $uploadUsage);
            $dailyDataUsageRecord->set('today_total_download', $downloadUsage);
            $dailyDataUsageRecord->set('date', date('Y-m-d'));
            $dailyDataUsageRecord->save();

        } else {
            // Check if a record exists for the customer and current date in the tbl_daily_data_usage table
            $dailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')
                ->where('customer_id', $customerId)
                ->where('date', date('Y-m-d'))
                ->find_one();

            if ($dailyDataUsageRecord) {

                // Update today's total upload and download
                $dailyDataUsageRecord->set('today_total_upload', $uploadUsage);
                $dailyDataUsageRecord->set('today_total_download', $downloadUsage);

                // Calculate today's data usage by subtracting yesterday's total usage from today's total usage
                $todayUploadUsage = $uploadUsage - $dailyDataUsageRecord->get('yesterday_total_upload');
                $todayDownloadUsage = $downloadUsage - $dailyDataUsageRecord->get('yesterday_total_download');

                // Update the daily data usage record with today's usage
                $dailyDataUsageRecord->set('upload', $todayUploadUsage);
                $dailyDataUsageRecord->set('download', $todayDownloadUsage);
                $dailyDataUsageRecord->save();
            } else {
                // Check if a record exists for the customer and previous date in the tbl_daily_data_usage table
                $previousDailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')
                    ->where('customer_id', $customerId)
                    ->where('date', $previousDate)
                    ->find_one();

                if ($previousDailyDataUsageRecord) {

                    // Calculate today's data usage by subtracting yesterday's total usage from today's total usage
                    $todayUploadUsage = $uploadUsage - $previousDailyDataUsageRecord->get('today_total_upload');
                    $todayDownloadUsage = $downloadUsage - $previousDailyDataUsageRecord->get('today_total_download');

                    // Create a new daily data usage record for the current date
                    $dailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')->create();
                    $dailyDataUsageRecord->set('customer_id', $customerId);
                    $dailyDataUsageRecord->set('upload', $todayUploadUsage);
                    $dailyDataUsageRecord->set('download', $todayDownloadUsage);
                    $dailyDataUsageRecord->set('yesterday_total_upload', $previousDailyDataUsageRecord->get('today_total_upload'));
                    $dailyDataUsageRecord->set('yesterday_total_download', $previousDailyDataUsageRecord->get('today_total_download'));
                    $dailyDataUsageRecord->set('today_total_upload', $uploadUsage);
                    $dailyDataUsageRecord->set('today_total_download', $downloadUsage);
                    $dailyDataUsageRecord->set('date', date('Y-m-d'));
                    $dailyDataUsageRecord->save();
                } else {

                    // Set today's usage as the total usage since there's no previous record
                    $todayUploadUsage = $uploadUsage;
                    $todayDownloadUsage = $downloadUsage;

                    // Create a new daily data usage record for the current date
                    $dailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')->create();
                    $dailyDataUsageRecord->set('customer_id', $customerId);
                    $dailyDataUsageRecord->set('upload', $todayUploadUsage);
                    $dailyDataUsageRecord->set('download', $todayDownloadUsage);
                    $dailyDataUsageRecord->set('yesterday_total_upload', 0);
                    $dailyDataUsageRecord->set('yesterday_total_download', 0);
                    $dailyDataUsageRecord->set('today_total_upload', $uploadUsage);
                    $dailyDataUsageRecord->set('today_total_download', $downloadUsage);
                    $dailyDataUsageRecord->set('date', date('Y-m-d'));
                    $dailyDataUsageRecord->save();
                }
            }
        }
    } catch (Exception $e) {
    }
}
                
function updateWeeklyDataUsage($customerId) {
    try {
        
        // Get the start and end dates of the current week
        $weekStartDate = date('Y-m-d', strtotime('last monday'));
        $weekEndDate = date('Y-m-d', strtotime('next sunday'));
        
        // Get the current day of the week (monday, tuesday, etc.)
        $currentDay = strtolower(date('l'));
        
        // Check if the columns for each day of the week exist in the tbl_weekly_data_usage table
        $requiredColumns = [
            'monday_upload', 'monday_download',
            'tuesday_upload', 'tuesday_download',
            'wednesday_upload', 'wednesday_download',
            'thursday_upload', 'thursday_download',
            'friday_upload', 'friday_download',
            'saturday_upload', 'saturday_download',
            'sunday_upload', 'sunday_download'
        ];
        
        $missingColumns = [];
        
        foreach ($requiredColumns as $column) {
            $columnExists = ORM::for_table('tbl_weekly_data_usage')->raw_query("SHOW COLUMNS FROM `tbl_weekly_data_usage` LIKE '$column'")->find_one();
            if (!$columnExists) {
                $missingColumns[] = $column;
            }
        }
        
        if (!empty($missingColumns)) {
            
            // Create the missing columns
            $alterTableQuery = "ALTER TABLE `tbl_weekly_data_usage` ";
            foreach ($missingColumns as $index => $column) {
                $alterTableQuery .= "ADD COLUMN `$column` BIGINT(20) NOT NULL DEFAULT '0'";
                if ($index < count($missingColumns) - 1) {
                    $alterTableQuery .= ", ";
                }
            }
            ORM::raw_execute($alterTableQuery);
        } else {
        }
        
        // Retrieve the daily data usage for the current date from the tbl_daily_data_usage table
        $dailyDataUsageRecord = ORM::for_table('tbl_daily_data_usage')
            ->where('customer_id', $customerId)
            ->where('date', date('Y-m-d'))
            ->find_one();
        
        if ($dailyDataUsageRecord) {
            $todayUploadUsage = $dailyDataUsageRecord->get('upload');
            $todayDownloadUsage = $dailyDataUsageRecord->get('download');
            
        } else {
            $todayUploadUsage = 0;
            $todayDownloadUsage = 0;
        }
        
        // Check if a record exists for the customer and current week in the tbl_weekly_data_usage table
        $weeklyDataUsageRecord = ORM::for_table('tbl_weekly_data_usage')
            ->where('customer_id', $customerId)
            ->where('week_start_date', $weekStartDate)
            ->where('week_end_date', $weekEndDate)
            ->find_one();
        
        if ($weeklyDataUsageRecord) {
            
            // Check if today is Monday
            if ($currentDay === 'monday') {
                // Reset all values to zero for the new week
                foreach ($requiredColumns as $column) {
                    $weeklyDataUsageRecord->set($column, 0);
                }
            }
            
            // Update the upload and download values for the current day
            $weeklyDataUsageRecord->set($currentDay . '_upload', $todayUploadUsage);
            $weeklyDataUsageRecord->set($currentDay . '_download', $todayDownloadUsage);
            $weeklyDataUsageRecord->save();
        } else {
            
            // Record doesn't exist, create a new record with the customer ID, upload, download, week start date, and week end date
            $weeklyDataUsageRecord = ORM::for_table('tbl_weekly_data_usage')->create();
            $weeklyDataUsageRecord->set('customer_id', $customerId);
            $weeklyDataUsageRecord->set($currentDay . '_upload', $todayUploadUsage);
            $weeklyDataUsageRecord->set($currentDay . '_download', $todayDownloadUsage);
            $weeklyDataUsageRecord->set('week_start_date', $weekStartDate);
            $weeklyDataUsageRecord->set('week_end_date', $weekEndDate);
            $weeklyDataUsageRecord->save();
        }
    } catch (Exception $e) {
    }
}
                
                // Function to update monthly data usage
                function updateMonthlyDataUsage($customerId, $uploadUsage, $downloadUsage) {
                    try {
                        
                        // Get the current month and year
                        $currentMonth = date('m');
                        $currentYear = date('Y');
                        
                        // Array of month names
                        $monthNames = array(
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December'
                        );
                        
                        // Check if the tbl_monthly_data_usage table exists
                        $monthlyDataUsageTableExists = ORM::for_table('tbl_monthly_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_monthly_data_usage'")->find_one();
                        
                        if (!$monthlyDataUsageTableExists) {
                            // Create the tbl_monthly_data_usage table
                            $monthlyDataUsageTableQuery = "CREATE TABLE `tbl_monthly_data_usage` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `customer_id` int(11) NOT NULL,
                                `year` int(4) NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `customer_id` (`customer_id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                            
                            ORM::raw_execute($monthlyDataUsageTableQuery);
                        } else {
                        }
                        
                        // Check if the columns for each month exist in the tbl_monthly_data_usage table
                        foreach ($monthNames as $monthName) {
                            $uploadColumn = strtolower($monthName) . '_upload';
                            $downloadColumn = strtolower($monthName) . '_download';
                            
                            $uploadColumnExists = ORM::for_table('tbl_monthly_data_usage')->raw_query("SHOW COLUMNS FROM `tbl_monthly_data_usage` LIKE '$uploadColumn'")->find_one();
                            $downloadColumnExists = ORM::for_table('tbl_monthly_data_usage')->raw_query("SHOW COLUMNS FROM `tbl_monthly_data_usage` LIKE '$downloadColumn'")->find_one();
                            
                            if (!$uploadColumnExists) {
                                // Add the missing upload column
                                $alterQuery = "ALTER TABLE `tbl_monthly_data_usage` ADD COLUMN `$uploadColumn` bigint(20) NOT NULL DEFAULT '0'";
                                ORM::raw_execute($alterQuery);
                            }
                            
                            if (!$downloadColumnExists) {
                                // Add the missing download column
                                $alterQuery = "ALTER TABLE `tbl_monthly_data_usage` ADD COLUMN `$downloadColumn` bigint(20) NOT NULL DEFAULT '0'";
                                ORM::raw_execute($alterQuery);
                            }
                        }
                        
                        // Check if a record exists for the customer and current year in the tbl_monthly_data_usage table
                        $monthlyDataUsageRecord = ORM::for_table('tbl_monthly_data_usage')
                            ->where('customer_id', $customerId)
                            ->where('year', $currentYear)
                            ->find_one();
                        
                        if ($monthlyDataUsageRecord) {
                            
                            // Update the upload and download values for the current month
                            $monthColumnUpload = strtolower($monthNames[$currentMonth]) . '_upload';
                            $monthColumnDownload = strtolower($monthNames[$currentMonth]) . '_download';
                            
                            $monthlyDataUsageRecord->set($monthColumnUpload, $uploadUsage);
                            $monthlyDataUsageRecord->set($monthColumnDownload, $downloadUsage);
                            $monthlyDataUsageRecord->save();
                        } else {
                            
                            // Create a new record with the customer ID, year, and data usage for all months
                            $monthlyDataUsageRecord = ORM::for_table('tbl_monthly_data_usage')->create();
                            $monthlyDataUsageRecord->set('customer_id', $customerId);
                            $monthlyDataUsageRecord->set('year', $currentYear);
                            
                            foreach ($monthNames as $monthNumber => $monthName) {
                                $monthColumnUpload = strtolower($monthName) . '_upload';
                                $monthColumnDownload = strtolower($monthName) . '_download';
                                
                                if ($monthNumber == $currentMonth) {
                                    $monthlyDataUsageRecord->set($monthColumnUpload, $uploadUsage);
                                    $monthlyDataUsageRecord->set($monthColumnDownload, $downloadUsage);
                                } else {
                                    $monthlyDataUsageRecord->set($monthColumnUpload, 0);
                                    $monthlyDataUsageRecord->set($monthColumnDownload, 0);
                                }
                            }
                            
                            $monthlyDataUsageRecord->save();
                        }
                    } catch (Exception $e) {
                    }
                }
                
                // Function to check if the counters have been reset for a specific month
                function isCountersResetForMonth($customerId, $month, $year) {
                    $resetRecord = ORM::for_table('tbl_reset_counters')
                        ->where('customer_id', $customerId)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->find_one();
                    return $resetRecord !== false;
                }
                
                // Function to mark the counters as reset for a specific month
                function markCountersResetForMonth($customerId, $month, $year) {
                    $resetRecord = ORM::for_table('tbl_reset_counters')->create();
                    $resetRecord->set('customer_id', $customerId);
                    $resetRecord->set('month', $month);
                    $resetRecord->set('year', $year);
                    $resetRecord->save();
                }
                
                // Function to reset the counters in Mikrotik router
                function resetMikrotikCounters($customerId) {
                    try {
                        // Get the router associated with the customer
                        $customer = ORM::for_table('tbl_customers')
                            ->where('id', $customerId)
                            ->find_one();
                        $routerId = $customer->get('router_id');
                        
                        // Get the router credentials
                        $router = ORM::for_table('tbl_routers')
                            ->where('id', $routerId)
                            ->find_one();
                        
                        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
                        
                        // Reset the counters for the customer's queue
                        $resetRequest = new RouterOS\Request('/queue/simple/reset-counters');
                        $resetRequest->setArgument('~name', '<pppoe-' . $customer['username'] . '>');
                        $client->sendSync($resetRequest);
                    } catch (Exception $e) {
                    }
                }
                
                try {
                // Check if the tbl_data_usage table exists
                $dataUsageTableExists = ORM::for_table('tbl_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_data_usage'")->find_one();
                
                if (!$dataUsageTableExists) {
                    // Create the tbl_data_usage table
                    $dataUsageTableQuery = "CREATE TABLE `tbl_data_usage` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NOT NULL,
                        `old_upload` bigint(20) NOT NULL DEFAULT '0',
                        `old_download` bigint(20) NOT NULL DEFAULT '0',
                        `prev_upload` bigint(20) NOT NULL DEFAULT '0',
                        `prev_download` bigint(20) NOT NULL DEFAULT '0',
                        `new_upload` bigint(20) NOT NULL DEFAULT '0',
                        `new_download` bigint(20) NOT NULL DEFAULT '0',
                        `upload` bigint(20) NOT NULL DEFAULT '0',
                        `download` bigint(20) NOT NULL DEFAULT '0',
                        `updated_at` datetime NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `customer_id` (`customer_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                
                    ORM::raw_execute($dataUsageTableQuery);
                } else {
                    // Check if each column exists before altering the table
                    $columnsToCheck = array('old_upload', 'old_download', 'prev_upload', 'prev_download', 'new_upload', 'new_download', 'upload', 'download');
                
                    foreach ($columnsToCheck as $column) {
                        $columnExists = ORM::for_table('tbl_data_usage')->raw_query("SHOW COLUMNS FROM `tbl_data_usage` LIKE '$column'")->find_one();
                
                        if (!$columnExists) {
                            // Add the column if it doesn't exist
                            $alterTableQuery = "ALTER TABLE `tbl_data_usage` ADD COLUMN `$column` bigint(20) NOT NULL DEFAULT '0';";
                            ORM::raw_execute($alterTableQuery);
                        }
                    }
                }
                
                // Check if the tbl_daily_data_usage table exists
                $dailyDataUsageTableExists = ORM::for_table('tbl_daily_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_daily_data_usage'")->find_one();
                
                if (!$dailyDataUsageTableExists) {
                    // Create the tbl_daily_data_usage table
                    $dailyDataUsageTableQuery = "CREATE TABLE `tbl_daily_data_usage` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NOT NULL,
                        `upload` bigint(20) NOT NULL DEFAULT '0',
                        `download` bigint(20) NOT NULL DEFAULT '0',
                        `date` date NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `customer_id` (`customer_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                
                    ORM::raw_execute($dailyDataUsageTableQuery);
                }
                
                // Check if the tbl_weekly_data_usage table exists
                $weeklyDataUsageTableExists = ORM::for_table('tbl_weekly_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_weekly_data_usage'")->find_one();
                
                if (!$weeklyDataUsageTableExists) {
                    // Create the tbl_weekly_data_usage table
                    $weeklyDataUsageTableQuery = "CREATE TABLE `tbl_weekly_data_usage` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NOT NULL,
                        `upload` bigint(20) NOT NULL DEFAULT '0',
                        `download` bigint(20) NOT NULL DEFAULT '0',
                        `week_start_date` date NOT NULL,
                        `week_end_date` date NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `customer_id` (`customer_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                
                    ORM::raw_execute($weeklyDataUsageTableQuery);
                }
                
                // Check if the tbl_monthly_data_usage table exists
                $monthlyDataUsageTableExists = ORM::for_table('tbl_monthly_data_usage')->raw_query("SHOW TABLES LIKE 'tbl_monthly_data_usage'")->find_one();
                
                if (!$monthlyDataUsageTableExists) {
                    // Create the tbl_monthly_data_usage table
                    $monthlyDataUsageTableQuery = "CREATE TABLE `tbl_monthly_data_usage` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NOT NULL,
                        `upload` bigint(20) NOT NULL DEFAULT '0',
                        `download` bigint(20) NOT NULL DEFAULT '0',
                        `month` int(2) NOT NULL,
                        `year` int(4) NOT NULL,
                        PRIMARY KEY (`id`),
                        KEY `customer_id` (`customer_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                
                    ORM::raw_execute($monthlyDataUsageTableQuery);
                }
                
                // Fetch all routers from the database
                $routers = ORM::for_table('tbl_routers')->find_many();
                
                foreach ($routers as $router) {
                    try {
                        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
                
                        // Get all customer accounts associated with the router
                        $customers = ORM::for_table('tbl_customers')
                            ->where('router_id', $router['id'])
                            ->find_many();
                
                        // Fetch data usage from queues for each customer
                        $dataUsage = fetchDataUsageFromRouters($client, $customers);
                
                        foreach ($dataUsage as $customerId => $usage) {
                            // Update data usage in the database for the specific customer
                            updateDataUsageInDatabase($customerId, $usage['upload'], $usage['download']);
                        }
                
                    } catch (Exception $e) {
                    }
                }
                } catch (Exception $e) {
                }

// Check if columns `state` and `last_seen` exist, if not, add them
$columns = ORM::for_table('tbl_user_recharges')->raw_query("SHOW COLUMNS FROM `tbl_user_recharges` LIKE 'state'")->find_one();
if (!$columns) {
    ORM::raw_execute("ALTER TABLE `tbl_user_recharges` ADD COLUMN `state` VARCHAR(10) NOT NULL DEFAULT 'Offline'");
}

$columns = ORM::for_table('tbl_user_recharges')->raw_query("SHOW COLUMNS FROM `tbl_user_recharges` LIKE 'last_seen'")->find_one();
if (!$columns) {
    ORM::raw_execute("ALTER TABLE `tbl_user_recharges` ADD COLUMN `last_seen` DATETIME NULL DEFAULT NULL");
}

// Fetch all routers
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
    try {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);

        // Fetch active users for hotspot, PPPoE, and queue
        $hotspotUsers = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);
        $pppoeUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);
        $queueUsers = $client->sendSync(new RouterOS\Request('/queue/simple/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);

        // Fetch customers connected to this router
        $customers = ORM::for_table('tbl_customers')->where('router_id', $router['id'])->find_many();

        foreach ($customers as $customer) {
            // Check if the user's status is 'off' and update state to 'Offline' if it is
            $userRecharge = ORM::for_table('tbl_user_recharges')->where('username', $customer['username'])->find_one();
            if ($userRecharge && $userRecharge->status == 'off') {
                $userRecharge->state = 'Offline';
                $userRecharge->save();
                continue; // Skip further checks for this user
            }

            $isOnline = false;

            // Check if the user is online in hotspot users
            foreach ($hotspotUsers as $activeUser) {
                if ($activeUser->getProperty('user') == $customer['username']) {
                    $isOnline = true;
                    break;
                }
            }

            // Check if the user is online in PPPoE users
            foreach ($pppoeUsers as $activeUser) {
                if ($activeUser->getProperty('name') == $customer['username']) {
                    $isOnline = true;
                    break;
                }
            }

            // Check if the user is online in queue users (assuming traffic passing check) and is a static user
            foreach ($queueUsers as $queueUser) {
                if (strpos($queueUser->getProperty('name'), 'Queue-') === 0) {
                    $staticUsername = str_replace('Queue-', '', $queueUser->getProperty('name'));
                    if ($staticUsername == $customer['username']) {
                        $traffic = explode('/', $queueUser->getProperty('bytes'));
                        if ($traffic[0] > 0 || $traffic[1] > 0) {
                            $isOnline = true;
                            break;
                        }
                    }
                }
            }

            $state = $isOnline ? 'Online' : 'Offline';

            // Update user's state and last seen in tbl_user_recharges
            if ($userRecharge) {
                if ($userRecharge->state == 'Online' && $state == 'Offline') {
                    $userRecharge->last_seen = date('Y-m-d H:i:s');
                }
                $userRecharge->state = $state;
                $userRecharge->save();
            }
        }

    } catch (Exception $e) {
        // Handle exceptions
        echo "Error with router ID " . $router['id'] . ": " . $e->getMessage() . "\n";
        logMessage("Error with router ID " . $router['id'] . ": " . $e->getMessage());
    }
}

// Fetch all routers
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
    try {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);

        // Fetch active users for hotspot
        $hotspotUsers = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);

        // Log the active Hotspot users
        logMessage("Active Hotspot Users for Router ID " . $router['id'] . ":");
        foreach ($hotspotUsers as $user) {
            logMessage("User: " . $user->getProperty('user'));
        }

        // Remove inactive Hotspot users
        removeInactiveHotspotUsers($client);

    } catch (Exception $e) {
        // Handle exceptions
        logMessage("Error with router ID " . $router['id'] . ": " . $e->getMessage());
    }
}

// Define the function to remove inactive Hotspot users and from the users list
function removeInactiveHotspotUsers($client) {
    // Fetch all Hotspot users
    $usersRequest = new RouterOS\Request('/ip/hotspot/user/print');
    $users = $client->sendSync($usersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

    foreach ($users as $user) {
        $username = $user->getProperty('name');
        logMessage("Checking user: $username"); // Log user being checked

        // Check if the user is in tbl_user_recharges with status 'off' and type 'Hotspot'
        $userRecharge = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->where('status', 'off')
            ->where('type', 'Hotspot')
            ->find_one();

        if ($userRecharge) {
            logMessage("User $username is inactive in the database. Removing from Hotspot users list and active users...");
            try {
                // Remove the user from Hotspot users list
                $removeUserRequest = new RouterOS\Request('/ip/hotspot/user/remove');
                $removeUserRequest->setArgument('.id', $user->getProperty('.id'));
                $client->sendSync($removeUserRequest);
                logMessage("Removed user $username from Hotspot users list");

                // Remove the user from Hotspot active users
                $activeUsersRequest = new RouterOS\Request('/ip/hotspot/active/print');
                $activeUsers = $client->sendSync($activeUsersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

                foreach ($activeUsers as $activeUser) {
                    if ($activeUser->getProperty('user') == $username) {
                        $removeActiveUserRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                        $removeActiveUserRequest->setArgument('.id', $activeUser->getProperty('.id'));
                        $client->sendSync($removeActiveUserRequest);
                        logMessage("Removed inactive Hotspot user: $username from active users");
                        break;
                    }
                }
            } catch (Exception $e) {
                logMessage("Error removing Hotspot user $username: " . $e->getMessage());
            }
        } else {
            logMessage("User $username is not inactive or not of type Hotspot in the database.");
        }
    }
}

// Fetch all routers
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
    try {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);

        // Fetch active users for PPPoE
        $pppoeUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'))->getAllOfType(RouterOS\Response::TYPE_DATA);

        // Log the active PPPoE users
        logMessage("Active PPPoE Users for Router ID " . $router['id'] . ":");
        foreach ($pppoeUsers as $user) {
            logMessage("User: " . $user->getProperty('name'));
        }

        // Remove inactive PPPoE users
        removeInactivePPPoEUsers($client);

    } catch (Exception $e) {
        // Handle exceptions
        logMessage("Error with router ID " . $router['id'] . ": " . $e->getMessage());
    }
}

// Define the function to remove inactive PPPoE users and from the users list
function removeInactivePPPoEUsers($client) {
    // Fetch all PPPoE users
    $usersRequest = new RouterOS\Request('/ppp/secret/print');
    $users = $client->sendSync($usersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

    foreach ($users as $user) {
        $username = $user->getProperty('name');
        logMessage("Checking user: $username"); // Log user being checked

        // Check if the user is in tbl_user_recharges with status 'off' and type 'PPPoE'
        $userRecharge = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->where('status', 'off')
            ->where('type', 'PPPoE')
            ->find_one();

        if ($userRecharge) {
            logMessage("User $username is inactive in the database. Removing from PPPoE users list and active users...");
            try {
                // Remove the user from PPPoE users list
                $removeUserRequest = new RouterOS\Request('/ppp/secret/remove');
                $removeUserRequest->setArgument('.id', $user->getProperty('.id'));
                $client->sendSync($removeUserRequest);
                logMessage("Removed user $username from PPPoE users list");

                // Remove the user from PPPoE active users
                $activeUsersRequest = new RouterOS\Request('/ppp/active/print');
                $activeUsers = $client->sendSync($activeUsersRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

                foreach ($activeUsers as $activeUser) {
                    if ($activeUser->getProperty('name') == $username) {
                        $removeActiveUserRequest = new RouterOS\Request('/ppp/active/remove');
                        $removeActiveUserRequest->setArgument('.id', $activeUser->getProperty('.id'));
                        $client->sendSync($removeActiveUserRequest);
                        logMessage("Removed inactive PPPoE user: $username from active users");
                        break;
                    }
                }
            } catch (Exception $e) {
                logMessage("Error removing PPPoE user $username: " . $e->getMessage());
            }
        } else {
            logMessage("User $username is not inactive or not of type PPPoE in the database.");
        }
    }
}

// Function to remove a static user
function removeStaticUser($client, $username) {
    global $_app_stage;
    if ($_app_stage == 'demo') {
        logMessage("Demo mode - skipping removal for user: $username");
        return null;
    }

    // Retrieve the customer data from the database
    $customer = ORM::for_table('tbl_customers')->where('username', $username)->find_one();

    if (!$customer) {
        // Handle the case where the customer was not found in the database
        logMessage("Customer $username not found in the database.");
        return;
    }

    // Get the IP address from the customer data
    $ipAddress = $customer->ip_address;
    logMessage("Customer $username found with IP address: $ipAddress");

    try {
        // Find the address list entry
        $findAddressListRequest = new RouterOS\Request('/ip/firewall/address-list/print');
        $findAddressListRequest->setQuery(Query::where('list', 'allowed')->andWhere('address', $ipAddress));
        $addressListResponses = $client->sendSync($findAddressListRequest);
        foreach ($addressListResponses as $addressListResponse) {
            if ($addressListResponse->getType() === RouterOS\Response::TYPE_DATA) {
                $addressListId = $addressListResponse->getProperty('.id');
                // Verify if the address exactly matches the IP address from customer data
                $address = $addressListResponse->getProperty('address');
                if ($address === $ipAddress) {
                    // Remove the address list entry only if it matches the IP address
                    $removeAddressListRequest = new RouterOS\Request('/ip/firewall/address-list/remove');
                    $removeAddressListRequest->setArgument('.id', $addressListId);
                    $client->sendSync($removeAddressListRequest);
                    logMessage("Removed address list entry for IP: $ipAddress");
                }
            }
        }

        $findQueueRequest = new RouterOS\Request('/queue/simple/print');
        $findQueueRequest->setQuery(Query::where('target', $ipAddress . '/32'));
        $queueResponses = $client->sendSync($findQueueRequest);

        foreach ($queueResponses as $queueResponse) {
            if ($queueResponse->getType() === RouterOS\Response::TYPE_DATA) {
                $queueId = $queueResponse->getProperty('.id');
                // Verify if the queue target exactly matches the IP address
                $target = $queueResponse->getProperty('target');
                if ($target === $ipAddress . '/32') {
                    // Remove the queue only if it matches the IP address
                    $removeQueueRequest = new RouterOS\Request('/queue/simple/remove');
                    $removeQueueRequest->setArgument('.id', $queueId);
                    $client->sendSync($removeQueueRequest);
                    logMessage("Removed queue for IP: $ipAddress");
                }
            }
        }

    } catch (Exception $e) {
        logMessage("Error removing static user $username: " . $e->getMessage());
    }
}

// Define the function to remove inactive static users
function removeInactiveStaticUsers($client) {
    // Fetch all queue entries
    $queueRequest = new RouterOS\Request('/queue/simple/print');
    $queues = $client->sendSync($queueRequest)->getAllOfType(RouterOS\Response::TYPE_DATA);

    if (empty($queues)) {
        logMessage("No queue entries found.");
        return;
    }

    foreach ($queues as $queue) {
        $queueName = $queue->getProperty('name');

        if (strpos($queueName, 'Queue-') === 0) {
            $username = substr($queueName, 6); // Extract the username part
            logMessage("Checking static user: $username in queue $queueName");

            // Check if the user is in tbl_user_recharges with status 'off' and type 'Static'
            $userRecharge = ORM::for_table('tbl_user_recharges')
                ->where('username', $username)
                ->where('status', 'off')
                ->where('type', 'Static')
                ->find_one();

            if ($userRecharge) {
                logMessage("User $username is inactive in the database. Removing from queues and address list...");
                try {
                    // Remove the user from the queue
                    $removeQueueRequest = new RouterOS\Request('/queue/simple/remove');
                    $removeQueueRequest->setArgument('.id', $queue->getProperty('.id'));
                    $client->sendSync($removeQueueRequest);
                    logMessage("Removed queue $queueName for user $username");

                    // Remove the user from the address list
                    removeStaticUser($client, $username);
                } catch (Exception $e) {
                    logMessage("Error removing static user $username: " . $e->getMessage());
                }
            } else {
                logMessage("User $username is not inactive or not of type Static in the database.");
            }
        }
    }
}

// Example usage to trigger the function
// This part will be executed when the script runs
try {
    // Fetch all routers and process each one
    $routers = ORM::for_table('tbl_routers')->find_many();

    foreach ($routers as $router) {
        $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);
        logMessage("Starting processing for Router ID " . $router['id']);
        removeInactiveStaticUsers($client);
        logMessage("Finished processing for Router ID " . $router['id']);
    }
} catch (Exception $e) {
    logMessage("General error: " . $e->getMessage());


}

$lastBackupFile = __DIR__ . '/../last_backup_date.txt';

$backupDir = __DIR__ . '/../backups';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0777, true);
    logMessage("Created backup directory at $backupDir");
}


function performBackup() {
    global $lastBackupFile;
    
    $today = date('Y-m-d');
    $lastBackupDate = @file_get_contents($lastBackupFile);

    // If the last backup date is today, skip the backup
    if ($lastBackupDate === $today) {
        logMessage("Backup already performed today, exiting script.");
        return;
    }

    // Update the last backup date
    file_put_contents($lastBackupFile, $today);

    $routers = ORM::for_table('tbl_routers')->find_many();
    foreach ($routers as $router) {
        try {
            logMessage("Starting backup for router with IP: " . $router['ip_address']);
            
            $client = new RouterOS\Client($router['ip_address'], $router['username'], $router['password']);

            // Save the backup on the router
            $backupName = 'freeispradius_backup_' . date('Ymd_His');
            $backup = new RouterOS\Request('/system/backup/save');
            $backup->setArgument('name', $backupName);
            $response = $client->sendSync($backup);
            logMessage("Backup save response: " . json_encode($response->getAllOfType(RouterOS\Response::TYPE_FINAL)));

            // Connect to the router via FTP
            $ftp = ftp_connect($router['ip_address']);
            if (!$ftp) {
                logMessage("Failed to connect to FTP server at " . $router['ip_address']);
                continue;
            }
            
            logMessage("Connected to FTP server at " . $router['ip_address']);
            
            if (!ftp_login($ftp, $router['username'], $router['password'])) {
                logMessage("Failed to login to FTP server at " . $router['ip_address'] . " with username " . $router['username']);
                ftp_close($ftp);
                continue;
            }
            
            ftp_pasv($ftp, true);
            logMessage("Logged in to FTP server and set passive mode");

            $remoteFile = $backupName . '.backup';
            $localFile = __DIR__ . '/../backups/' . $remoteFile;
            
            if (!file_exists(__DIR__ . '/../backups/')) {
                mkdir(__DIR__ . '/../backups/', 0777, true);
                logMessage("Created backups directory at " . __DIR__ . '/../backups/');
            }

            if (ftp_get($ftp, $localFile, $remoteFile, FTP_BINARY)) {
                logMessage("Successfully downloaded backup file $remoteFile to $localFile");
                ftp_close($ftp);
                saveBackupRecord($router['id'], $localFile);
                manageBackupRetention($router['id'], $client, $backupName);
            } else {
                logMessage("Failed to download backup file $remoteFile to $localFile");
                ftp_close($ftp);
            }
        } catch (Exception $e) {
            logMessage("Backup error for router with IP " . $router['ip_address'] . ": " . $e->getMessage());
        }
    }
}



function saveBackupRecord($router_id, $backupPath) {
    $backupDate = date('Y-m-d H:i:s');

    // Save the backup record to the database
    $backupRecord = ORM::for_table('tbl_router_backups')->create();
    $backupRecord->set('router_id', $router_id);
    $backupRecord->set('backup_date', $backupDate);
    $backupRecord->set('file_path', $backupPath);
    $backupRecord->save();

    logMessage("Backup record saved for router ID $router_id at $backupDate with path $backupPath");
}
function manageBackupRetention($router_id, $client, $backupName) {
    $backupDir = __DIR__ . '/../backups/';
    $maxBackups = 5;

    // Get all backup files for the server
    $backupFiles = glob($backupDir . 'freeispradius_backup_*.backup');

    // Sort the files by modification time in descending order (newest first)
    usort($backupFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // If there are more than $maxBackups, delete the oldest ones on the server
    if (count($backupFiles) > $maxBackups) {
        $filesToDelete = array_slice($backupFiles, $maxBackups);

        foreach ($filesToDelete as $file) {
            if (unlink($file)) {
                logMessage("Deleted old backup file from server: $file");
            } else {
                logMessage("Failed to delete old backup file from server: $file");
            }
        }
    }

    // Fetch backup files from the router
    try {
        logMessage("Fetching backup files from router");
        $request = new RouterOS\Request('/file/print');
        $response = $client->sendSync($request);
        $routerFiles = $response->getAllOfType(RouterOS\Response::TYPE_DATA);

        // Convert RouterOS response collection to an array of file names
        $routerFileNames = [];
        foreach ($routerFiles as $file) {
            $fileName = $file->getProperty('name');
            if (strpos($fileName, 'freeispradius_backup_') === 0) {
                $routerFileNames[] = $file;
            }
        }

        // Sort router backup files by creation time, oldest first
        usort($routerFileNames, function($a, $b) {
            return strtotime($a->getProperty('creation-time')) - strtotime($b->getProperty('creation-time'));
        });

        // Log the sorted backup files for debugging
        foreach ($routerFileNames as $file) {
            logMessage("Backup file on router: " . $file->getProperty('name') . ", created at: " . $file->getProperty('creation-time'));
        }

        // Check if there are more than $maxBackups and delete the oldest ones
        if (count($routerFileNames) > $maxBackups) {
            $filesToDelete = array_slice($routerFileNames, 0, count($routerFileNames) - $maxBackups);
            $fileNamesToDelete = array_map(function($file) {
                return $file->getProperty('name');
            }, $filesToDelete);

            foreach ($fileNamesToDelete as $fileName) {
                logMessage("Attempting to delete file from router: " . $fileName);
                $deleteRequest = new RouterOS\Request('/file/remove');
                $deleteRequest->setArgument('numbers', $fileName);
                $deleteResponse = $client->sendSync($deleteRequest);

                // Check if the delete operation was successful
                if ($deleteResponse->getType() === RouterOS\Response::TYPE_FINAL) {
                    logMessage("Deleted old backup file from router: " . $fileName);
                } else {
                    logMessage("Failed to delete old backup file from router: " . $fileName . ". Response: " . json_encode($deleteResponse->getAll()));
                }
            }
        }
    } catch (Exception $e) {
        logMessage("Exception while managing router backups: " . $e->getMessage());
    }
}



// Check if the tbl_router_backups table exists, if not, create it
$tableCheck = ORM::for_table('tbl_router_backups')->raw_query("SHOW TABLES LIKE 'tbl_router_backups'")->find_one();
if (!$tableCheck) {
    logMessage("Creating tbl_router_backups table.");
    ORM::raw_execute("
        CREATE TABLE tbl_router_backups (
            id INT NOT NULL AUTO_INCREMENT,
            router_id INT NOT NULL,
            backup_date DATETIME NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");
} else {
    logMessage("Table tbl_router_backups already exists.");
}

// Perform the backup every time the script runs for testing purposes
performBackup();