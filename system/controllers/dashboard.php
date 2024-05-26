<?php

/**
 *  PHP Mikrotik Billing 
 *  by https://freeispradius.com
 **/

_admin();
$ui->assign('_title', Lang::T('Dashboard'));
//$admin = Admin::_info();
$ui->assign('_admin', $admin);

$fdate = date('Y-m-01');
$tdate = date('Y-m-t');
//first day of month
$first_day_month = date('Y-m-01');
$mdate = date('Y-m-d');
$month_n = date('n');

$iday = ORM::for_table('tbl_transactions')
    ->where('recharged_on', $mdate)
    ->where_not_equal('method', 'Customer - Balance')
    ->sum('price');
if ($iday == '') {
    $iday = '0.00';
}
$ui->assign('iday', $iday);

$imonth = ORM::for_table('tbl_transactions')->where_not_equal('method', 'Customer - Balance')->where_gte('recharged_on', $first_day_month)->where_lte('recharged_on', $mdate)->sum('price');
if ($imonth == '') {
    $imonth = '0.00';
}
$ui->assign('imonth', $imonth);

$u_act = ORM::for_table('tbl_user_recharges')->where('status', 'on')->count();
if (empty($u_act)) {
    $u_act = '0';
}
$ui->assign('u_act', $u_act);

$u_all = ORM::for_table('tbl_user_recharges')->count();
if (empty($u_all)) {
    $u_all = '0';
}
$ui->assign('u_all', $u_all);


$c_all = ORM::for_table('tbl_customers')->count();
if (empty($c_all)) {
    $c_all = '0';
}
$ui->assign('c_all', $c_all);

if ($config['hide_uet'] != 'yes') {
    //user expire
    $paginator = Paginator::build(ORM::for_table('tbl_user_recharges'));
    $expire = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $mdate)
        ->offset($paginator['startpoint'])
        ->limit($paginator['limit'])
        ->order_by_desc('expiration')
        ->find_many();

    // Get the total count of expired records for pagination
    $totalCount = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $mdate)
        ->count();

    // Pass the total count and current page to the paginator
    $paginator['total_count'] = $totalCount;

    // Assign the pagination HTML to the template variable
    $ui->assign('paginator', $paginator);
    $ui->assign('expire', $expire);
}
// Count of Hotspot Online Users
$hotspotUsers = ORM::for_table('tbl_user_recharges')
    ->where('type', 'Hotspot')
    ->where('state', 'online')
    ->count();
$ui->assign('hotspotUsers', $hotspotUsers);

// Count of PPPoE Online Users
$pppoeUsers = ORM::for_table('tbl_user_recharges')
    ->where('type', 'PPPOE')
    ->where('state', 'online')
    ->count();
$ui->assign('pppoeUsers', $pppoeUsers);

// Count of Static Online Users
$staticUsers = ORM::for_table('tbl_user_recharges')
    ->where('type', 'Static')
    ->where('state', 'online')
    ->count();
$ui->assign('staticUsers', $staticUsers);

// Total Online Users
$totalOnlineUsers = $hotspotUsers + $pppoeUsers + $staticUsers;
$ui->assign('totalOnlineUsers', $totalOnlineUsers);


//activity log
$dlog = ORM::for_table('tbl_logs')->limit(5)->order_by_desc('id')->find_many();
$ui->assign('dlog', $dlog);
$log = ORM::for_table('tbl_logs')->count();
$ui->assign('log', $log);

// ... (previous code remains the same)

// Prepare data for the graphs
$todayUsageData = [
    ['Category', 'Data Usage'],
    ['Upload', $todayUsage->upload],
    ['Download', $todayUsage->download]
];

$weeklyUsageData = [
    ['Day', 'Upload', 'Download'],
    ['Monday', $weeklyUsage->monday_upload, $weeklyUsage->monday_download],
    ['Tuesday', $weeklyUsage->tuesday_upload, $weeklyUsage->tuesday_download],
    ['Wednesday', $weeklyUsage->wednesday_upload, $weeklyUsage->wednesday_download],
    ['Thursday', $weeklyUsage->thursday_upload, $weeklyUsage->thursday_download],
    ['Friday', $weeklyUsage->friday_upload, $weeklyUsage->friday_download],
    ['Saturday', $weeklyUsage->saturday_upload, $weeklyUsage->saturday_download],
    ['Sunday', $weeklyUsage->sunday_upload, $weeklyUsage->sunday_download]
];

$monthlyUsageData = [
    ['Month', 'Upload', 'Download'],
    ['Jan', $monthlyUsage->january_upload, $monthlyUsage->january_download],
    ['Feb', $monthlyUsage->february_upload, $monthlyUsage->february_download],
    ['Mar', $monthlyUsage->march_upload, $monthlyUsage->march_download],
    ['Apr', $monthlyUsage->april_upload, $monthlyUsage->april_download],
    ['May', $monthlyUsage->may_upload, $monthlyUsage->may_download],
    ['Jun', $monthlyUsage->june_upload, $monthlyUsage->june_download],
    ['Jul', $monthlyUsage->july_upload, $monthlyUsage->july_download],
    ['Aug', $monthlyUsage->august_upload, $monthlyUsage->august_download],
    ['Sep', $monthlyUsage->september_upload, $monthlyUsage->september_download],
    ['Oct', $monthlyUsage->october_upload, $monthlyUsage->october_download],
    ['Nov', $monthlyUsage->november_upload, $monthlyUsage->november_download],
    ['Dec', $monthlyUsage->december_upload, $monthlyUsage->december_download]
];

// Assign the graph data to the Smarty template
$ui->assign('todayUsageData', json_encode($todayUsageData));
$ui->assign('weeklyUsageData', json_encode($weeklyUsageData));
$ui->assign('monthlyUsageData', json_encode($monthlyUsageData));
// Get the count of users per service type
$serviceTypes = ORM::for_table('tbl_customers')
    ->select('service_type')
    ->select_expr('COUNT(*)', 'count')
    ->group_by('service_type')
    ->find_array();

// Calculate the total number of users
$totalUsers = array_sum(array_column($serviceTypes, 'count'));

// Calculate the percentage for each service type
foreach ($serviceTypes as &$serviceType) {
    $serviceType['percentage'] = round(($serviceType['count'] / $totalUsers) * 100, 2);
}

// Assign the service types to the Smarty template
$ui->assign('serviceTypes', $serviceTypes);

// Get the last 5 transactions
$lastTransactions = ORM::for_table('tbl_transactions')
    ->select_many('username', 'price', 'recharged_on')
    ->order_by_desc('id') // Order by the 'id' column instead of 'tbl_transactions'
    ->limit(5)
    ->find_array();

// Assign the last transactions to the Smarty template
$ui->assign('lastTransactions', $lastTransactions);


//code for best selling packages
// Get the current month and year
$currentMonth = date('n');
$currentYear = date('Y');

// Get the best-selling packages for the current month
$bestSellingPackages = ORM::for_table('tbl_user_recharges')
    ->select('tbl_plans.name_plan')
    ->select('tbl_plans.price')
    ->select_expr('COUNT(*)', 'sales')
    ->select_expr('SUM(tbl_plans.price)', 'revenue')
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where_raw('MONTH(tbl_user_recharges.recharged_on) = ?', array($currentMonth))
    ->where_raw('YEAR(tbl_user_recharges.recharged_on) = ?', array($currentYear))
    ->group_by('tbl_user_recharges.plan_id')
    ->order_by_desc('sales')
    ->limit(5)
    ->find_array();

// Format the price and revenue
foreach ($bestSellingPackages as &$package) {
    $package['formattedPrice'] = number_format($package['price'], 2, '.', ',');
    $package['formattedRevenue'] = number_format($package['revenue'], 2, '.', ',');
}


//code for transactions per router
// Get the currency code from tbl_appconfig
$currencyCode = ORM::for_table('tbl_appconfig')
    ->where('setting', 'currency_code')
    ->find_one()
    ->value;

// Assign the best-selling packages and currency code to the Smarty template
$ui->assign('bestSellingPackages', $bestSellingPackages);
$ui->assign('currencyCode', $currencyCode);


// Get transactions per router
$transactionsPerRouter = ORM::for_table('tbl_user_recharges')
    ->select('tbl_user_recharges.routers', 'router_name')
    ->select_expr('COUNT(*)', 'transactions')
    ->select_expr('SUM(tbl_plans.price)', 'totalAmount')
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->group_by('tbl_user_recharges.routers')
    ->order_by_desc('transactions')
    ->find_array();

// Calculate the total transactions and total amount
$totalTransactions = array_sum(array_column($transactionsPerRouter, 'transactions'));
$totalAmount = array_sum(array_column($transactionsPerRouter, 'totalAmount'));

// Calculate the percentage for each router
if ($totalTransactions > 0) {
    foreach ($transactionsPerRouter as &$router) {
        $router['percentage'] = round(($router['transactions'] / $totalTransactions) * 100, 2);
        $router['formattedAmount'] = number_format($router['totalAmount'], 2, '.', ',');
    }
} else {
    foreach ($transactionsPerRouter as &$router) {
        $router['percentage'] = 0;
        $router['formattedAmount'] = number_format($router['totalAmount'], 2, '.', ',');
    }
}

// Get the currency code from tbl_appconfig
$currencyCode = ORM::for_table('tbl_appconfig')
    ->where('setting', 'currency_code')
    ->find_one()
    ->value;

// Assign the transactions per router and currency code to the Smarty template
$ui->assign('transactionsPerRouter', $transactionsPerRouter);
$ui->assign('currencyCode', $currencyCode);



// Fetch today's total data usage for all users
$todayDate = date('Y-m-d');
$todayUpload = ORM::for_table('tbl_daily_data_usage')
    ->where_raw('DATE(date) = ?', array($todayDate))
    ->sum('upload');

$todayDownload = ORM::for_table('tbl_daily_data_usage')
    ->where_raw('DATE(date) = ?', array($todayDate))
    ->sum('download');

$ui->assign('todayUpload', $todayUpload);
$ui->assign('todayDownload', $todayDownload);


// Fetch top 5 downloaders
$topDownloadersQuery = "
    SELECT c.username, du.download
    FROM tbl_data_usage AS du
    JOIN tbl_customers AS c ON du.customer_id = c.id
    ORDER BY du.download DESC
    LIMIT 5
";

$topDownloadersResult = ORM::for_table('tbl_data_usage')
    ->raw_query($topDownloadersQuery)
    ->find_array();

$ui->assign('topDownloaders', $topDownloadersResult);

// Fetch weekly data usage for all users
$weekStartDate = date('Y-m-d', strtotime('last monday'));
$weekEndDate = date('Y-m-d', strtotime('next sunday'));

$mondayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('monday_upload');

$mondayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('monday_download');

$tuesdayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('tuesday_upload');

$tuesdayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('tuesday_download');

$wednesdayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('wednesday_upload');

$wednesdayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('wednesday_download');

$thursdayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('thursday_upload');

$thursdayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('thursday_download');

$fridayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('friday_upload');

$fridayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('friday_download');

$saturdayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('saturday_upload');

$saturdayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('saturday_download');

$sundayUpload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('sunday_upload');

$sundayDownload = ORM::for_table('tbl_weekly_data_usage')
    ->where_raw('week_start_date = ?', array($weekStartDate))
    ->sum('sunday_download');

$ui->assign('mondayUpload', $mondayUpload);
$ui->assign('mondayDownload', $mondayDownload);
$ui->assign('tuesdayUpload', $tuesdayUpload);
$ui->assign('tuesdayDownload', $tuesdayDownload);
$ui->assign('wednesdayUpload', $wednesdayUpload);
$ui->assign('wednesdayDownload', $wednesdayDownload);
$ui->assign('thursdayUpload', $thursdayUpload);
$ui->assign('thursdayDownload', $thursdayDownload);
$ui->assign('fridayUpload', $fridayUpload);
$ui->assign('fridayDownload', $fridayDownload);
$ui->assign('saturdayUpload', $saturdayUpload);
$ui->assign('saturdayDownload', $saturdayDownload);
$ui->assign('sundayUpload', $sundayUpload);
$ui->assign('sundayDownload', $sundayDownload);

// Fetch monthly data usage for all users
$currentYear = date('Y');

$januaryUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('january_upload');

$januaryDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('january_download');

$februaryUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('february_upload');

$februaryDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('february_download');

$marchUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('march_upload');

$marchDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('march_download');

$aprilUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('april_upload');

$aprilDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('april_download');

$mayUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('may_upload');

$mayDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('may_download');

$juneUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('june_upload');

$juneDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('june_download');

$julyUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('july_upload');

$julyDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('july_download');

$augustUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('august_upload');

$augustDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('august_download');

$septemberUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('september_upload');

$septemberDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('september_download');

$octoberUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('october_upload');

$octoberDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('october_download');

$novemberUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('november_upload');

$novemberDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('november_download');

$decemberUpload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('december_upload');

$decemberDownload = ORM::for_table('tbl_monthly_data_usage')
    ->where('year', $currentYear)
    ->sum('december_download');

$ui->assign('januaryUpload', $januaryUpload);
$ui->assign('januaryDownload', $januaryDownload);
$ui->assign('februaryUpload', $februaryUpload);
$ui->assign('februaryDownload', $februaryDownload);
$ui->assign('marchUpload', $marchUpload);
$ui->assign('marchDownload', $marchDownload);
$ui->assign('aprilUpload', $aprilUpload);
$ui->assign('aprilDownload', $aprilDownload);
$ui->assign('mayUpload', $mayUpload);
$ui->assign('mayDownload', $mayDownload);
$ui->assign('juneUpload', $juneUpload);
$ui->assign('juneDownload', $juneDownload);
$ui->assign('julyUpload', $julyUpload);
$ui->assign('julyDownload', $julyDownload);
$ui->assign('augustUpload', $augustUpload);
$ui->assign('augustDownload', $augustDownload);
$ui->assign('septemberUpload', $septemberUpload);
$ui->assign('septemberDownload', $septemberDownload);
$ui->assign('octoberUpload', $octoberUpload);
$ui->assign('octoberDownload', $octoberDownload);
$ui->assign('novemberUpload', $novemberUpload);
$ui->assign('novemberDownload', $novemberDownload);
$ui->assign('decemberUpload', $decemberUpload);
$ui->assign('decemberDownload', $decemberDownload);

// Smarty modifier function to convert bytes to the nearest GB
function smarty_modifier_convert_bytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unit = 0;
    while ($bytes >= 1024 && $unit < 4) {
        $bytes /= 1024;
        $unit++;
    }
    return round($bytes, 2) . ' ' . $units[$unit];
}

$ui->registerPlugin('modifier', 'convert_bytes', 'smarty_modifier_convert_bytes');

if ($config['hide_vs'] != 'yes') {
    $cacheStocksfile = $CACHE_PATH . File::pathFixer('/VoucherStocks.temp');
    $cachePlanfile = $CACHE_PATH . File::pathFixer('/VoucherPlans.temp');
    //Cache for 5 minutes
    if (file_exists($cacheStocksfile) && time() - filemtime($cacheStocksfile) < 600) {
        $stocks = json_decode(file_get_contents($cacheStocksfile), true);
        $plans = json_decode(file_get_contents($cachePlanfile), true);
    }else{
        // Count stock
        $tmp = $v = ORM::for_table('tbl_plans')->select('id')->select('name_plan')->find_many();
        $plans = array();
        $stocks = array("used" => 0, "unused" => 0);
        $n = 0;
        foreach ($tmp as $plan) {
            $unused = ORM::for_table('tbl_voucher')
                ->where('id_plan', $plan['id'])
                ->where('status', 0)->count();
            $used = ORM::for_table('tbl_voucher')
                ->where('id_plan', $plan['id'])
                ->where('status', 1)->count();
            if ($unused > 0 || $used > 0) {
                $plans[$n]['name_plan'] = $plan['name_plan'];
                $plans[$n]['unused'] = $unused;
                $plans[$n]['used'] = $used;
                $stocks["unused"] += $unused;
                $stocks["used"] += $used;
                $n++;
            }
        }
        file_put_contents($cacheStocksfile, json_encode($stocks));
        file_put_contents($cachePlanfile, json_encode($plans));
    }
}
//chat for months month per month
$cacheMRfile = File::pathFixer('/monthlyRegistered.temp');
//Cache for 1 hour
if (file_exists($cacheMRfile) && time() - filemtime($cacheMRfile) < 3600) {
    $monthlyRegistered = json_decode(file_get_contents($cacheMRfile), true);
}else{
    //Monthly Registered Customers
    $result = ORM::for_table('tbl_customers')
        ->select_expr('MONTH(created_at)', 'month')
        ->select_expr('COUNT(*)', 'count')
        ->where_raw('YEAR(created_at) = YEAR(NOW())')
        ->group_by_expr('MONTH(created_at)')
        ->find_many();

    $monthlyRegistered = [];
    foreach ($result as $row) {
        $monthlyRegistered[] = [
            'date' => $row->month,
            'count' => $row->count
        ];
    }
    file_put_contents($cacheMRfile, json_encode($monthlyRegistered));
}







$cacheMRfile = File::pathFixer('/monthlyRegistered.temp');
//Cache for 1 hour
if (file_exists($cacheMRfile) && time() - filemtime($cacheMRfile) < 3600) {
    $monthlyRegistered = json_decode(file_get_contents($cacheMRfile), true);
}else{
    //Monthly Registered Customers
    $result = ORM::for_table('tbl_customers')
        ->select_expr('MONTH(created_at)', 'month')
        ->select_expr('COUNT(*)', 'count')
        ->where_raw('YEAR(created_at) = YEAR(NOW())')
        ->group_by_expr('MONTH(created_at)')
        ->find_many();

    $monthlyRegistered = [];
    foreach ($result as $row) {
        $monthlyRegistered[] = [
            'date' => $row->month,
            'count' => $row->count
        ];
    }
    file_put_contents($cacheMRfile, json_encode($monthlyRegistered));
}

$cacheMSfile = $CACHE_PATH . File::pathFixer('/monthlySales.temp');
//Cache for 12 hours
if (file_exists($cacheMSfile) && time() - filemtime($cacheMSfile) < 43200) {
    $monthlySales = json_decode(file_get_contents($cacheMSfile), true);
}else{
    // Query to retrieve monthly data
    $results = ORM::for_table('tbl_transactions')
        ->select_expr('MONTH(recharged_on)', 'month')
        ->select_expr('SUM(price)', 'total')
        ->where_raw("YEAR(recharged_on) = YEAR(CURRENT_DATE())") // Filter by the current year
        ->where_not_equal('method', 'Customer - Balance')
        ->group_by_expr('MONTH(recharged_on)')
        ->find_many();

    // Create an array to hold the monthly sales data
    $monthlySales = array();

    // Iterate over the results and populate the array
    foreach ($results as $result) {
        $month = $result->month;
        $totalSales = $result->total;

        $monthlySales[$month] = array(
            'month' => $month,
            'totalSales' => $totalSales
        );
    }

    // Fill in missing months with zero sales
    for ($month = 1; $month <= 12; $month++) {
        if (!isset($monthlySales[$month])) {
            $monthlySales[$month] = array(
                'month' => $month,
                'totalSales' => 0
            );
        }
    }

    // Sort the array by month
    ksort($monthlySales);

    // Reindex the array
    $monthlySales = array_values($monthlySales);
    file_put_contents($cacheMSfile, json_encode($monthlySales));
}
// Assign the data to the Smarty template
$ui->assign('todayUsageData', json_encode($todayUsageData));
$ui->assign('weeklyUsageData', json_encode($weeklyUsageData));
$ui->assign('monthlyUsageData', json_encode($monthlyUsageData));
// Assign the monthly sales data to Smarty
$ui->assign('monthlySales', $monthlySales);
$ui->assign('xfooter', '');
$ui->assign('monthlyRegistered', $monthlyRegistered);
$ui->assign('stocks', $stocks);
$ui->assign('plans', $plans);
run_hook('view_dashboard'); #HOOK
$ui->display('dashboard.tpl');