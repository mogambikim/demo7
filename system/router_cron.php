<?php

use PEAR2\Net\RouterOS;

include "../init.php";

$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
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

// Fetch all routers
$routers = ORM::for_table('tbl_routers')->find_many();

foreach ($routers as $router) {
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
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Online', uptime = ?, model = ? WHERE router_id = ?";
            $updateParams = array($uptime, $model, $router['id']);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated successfully.\n";
        } else {
            // If no entry exists, create a new one
            $cache = ORM::for_table('tbl_router_cache')->create();
            $cache->router_id = $router['id'];
            $cache->state = 'Online';
            $cache->uptime = $uptime;
            $cache->model = $model;
            $cache->save();
            
            echo "Router " . $router['id'] . " added successfully.\n";
        }
    } catch (Exception $e) {
        // Check if an entry exists in the cache table
        $cache = ORM::for_table('tbl_router_cache')->where('router_id', $router['id'])->find_one();
        
        if ($cache) {
            // If an entry exists, update it with the offline status
            $updateQuery = "UPDATE tbl_router_cache SET state = 'Offline', uptime = NULL, model = NULL WHERE router_id = ?";
            $updateParams = array($router['id']);
            $updateResult = ORM::raw_execute($updateQuery, $updateParams);
            
            echo "Router " . $router['id'] . " updated with offline status.\n";
        } else {
            // If no entry exists, create a new one with the offline status
            $cache = ORM::for_table('tbl_router_cache')->create();
            $cache->router_id = $router['id'];
            $cache->state = 'Offline';
            $cache->uptime = null;
            $cache->model = null;
            $cache->save();
            
            echo "Router " . $router['id'] . " added with offline status.\n";
        }
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
        logToFile("Error with router ID " . $router['id'] . ": " . $e->getMessage());
    }
}