{include file="sections/header.tpl"}
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h4><sup>{$_c['currency_code']}</sup>
                    {number_format($iday,0,$_c['dec_point'],$_c['thousands_sep'])}</h4>
                <p>{Lang::T('Income Today')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{$_url}reports/by-date" class="small-box-footer">{Lang::T('View Reports')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h4><sup>{$_c['currency_code']}</sup>
                    {number_format($imonth,0,$_c['dec_point'],$_c['thousands_sep'])}</h4>
                <p>{Lang::T('Income This Month')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{$_url}reports/by-period" class="small-box-footer">{Lang::T('View Reports')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h4>{$u_act}/{$u_all}</h4>
                <p>{Lang::T('Users Active')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="index.php?_route=prepaid/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h4>{$c_all}</h4>
                <p>{Lang::T('Total Users')}</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{$_url}customers/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-light-blue">
            <div class="inner">
                <h4>{$hotspotUsers}</h4>
                <p>{Lang::T('Hotspot Online Users')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-wifi"></i>
            </div>
            <a href="index.php?_route=prepaid/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h4>{$pppoeUsers}</h4>
                <p>{Lang::T('PPPoE Online Users')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-network"></i>
            </div>
            <a href="index.php?_route=prepaid/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h4>{$staticUsers}</h4>
                <p>{Lang::T('Static Online Users')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-android-wifi"></i>
            </div>
            <a href="index.php?_route=prepaid/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h4>{$totalOnlineUsers}</h4>
                <p>{Lang::T('Total Online Users')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-people"></i>
            </div>
            <a href="index.php?_route=prepaid/list" class="small-box-footer">{Lang::T('View All')} <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-7">
   <!-- solid sales graph -->
    {if $_c['hide_mrc'] != 'yes'}
        <div class="box box-solid ">
            <div class="box-header">
                <i class="fa fa-th"></i>

                <h3 class="box-title">{Lang::T('Monthly Registered Customers')}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <a href="{$_url}settings/app#hide_dashboard_content" class="btn bg-teal btn-sm"><i
                            class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="box-body border-radius-none">
                <canvas class="chart" id="chart" style="height: 250px;"></canvas>
            </div>
        </div>
    {/if}

    <!-- solid sales graph -->
    {if $_c['hide_tms'] != 'yes'}
        <div class="box box-solid ">
            <div class="box-header">
                <i class="fa fa-inbox"></i>

                <h3 class="box-title">{Lang::T('Total Monthly Sales')}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <a href="{$_url}settings/app#hide_dashboard_content" class="btn bg-teal btn-sm"><i
                            class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="box-body border-radius-none">
                <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
            </div>
                </div>
                   {/if} 
 <div class="data-usage">
  <div class="row">
    <div class="col-md-12">
      <!-- Section title goes here -->
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h4>Today's Data Usage</h4>
        </div>
        <div class="card-body d-flex flex-column">
          <div class="chart-container flex-grow-1">
            <canvas id="todayUsageChart"></canvas>
          </div>
          <div class="usage-info">
            <p><strong>Upload:</strong> {$todayUpload|convert_bytes}</p>
            <p><strong>Download:</strong> {$todayDownload|convert_bytes}</p>
            <p><strong>Total:</strong> {($todayUpload + $todayDownload)|convert_bytes}</p>
            <p><strong>Date:</strong> {$smarty.now|date_format:"%Y-%m-%d"}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4>Weekly Data Usage</h4>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="weeklyUsageChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  .data-usage {
    padding: 20px;
  }
.section-title {
font-size: 24px;
font-weight: bold;
margin-bottom: 20px;
}
.card {
border-radius: 5px;
box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
margin-bottom: 20px;
height: 100%;
}
.card-header {
background-color: #f8f9fa;
border-bottom: 1px solid #dee2e6;
padding: 10px;
}
.card-header h4 {
margin: 0;
}
.card-body {
padding: 20px;
}
.chart-container {
margin-bottom: 20px;
}
.usage-info p {
margin-bottom: 5px;
}
</style>
 {if $_c['disable_voucher'] != 'yes' && $stocks['unused']>0 || $stocks['used']>0}
        {if $_c['hide_vs'] != 'yes'}
            <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">Vouchers Stock</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Plan Name')}</th>
                                <th>unused</th>
                                <th>used</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $plans as $stok}
                                <tr>
                                    <td>{$stok['name_plan']}</td>
                                    <td>{$stok['unused']}</td>
                                    <td>{$stok['used']}</td>
                                </tr>
                            </tbody>
                        {/foreach}
                        <tr>
                            <td>Total</td>
                            <td>{$stocks['unused']}</td>
                            <td>{$stocks['used']}</td>
                        </tr>
                    </table>
                </div>
            </div>
        {/if}
    {/if}
 {if $_c['disable_voucher'] != 'yes' && $stocks['unused']>0 || $stocks['used']>0}
        {if $_c['hide_vs'] != 'yes'}
            <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">Vouchers Stock</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Plan Name')}</th>
                                <th>unused</th>
                                <th>used</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $plans as $stok}
                                <tr>
                                    <td>{$stok['name_plan']}</td>
                                    <td>{$stok['unused']}</td>
                                    <td>{$stok['used']}</td>
                                </tr>
                            </tbody>
                        {/foreach}
                        <tr>
                            <td>Total</td>
                            <td>{$stocks['unused']}</td>
                            <td>{$stocks['used']}</td>
                        </tr>
                    </table>
                </div>
            </div>
        {/if}
    {/if}
    <div class="box box-solid">
    <div class="box-header">
        <i class="fa fa-line-chart"></i>
        <h3 class="box-title">{Lang::T('Customers Growth')}</h3>
        <!-- Add any additional header content or tools -->
    </div>
    <div class="box-body border-radius-none">
        <canvas class="chart" id="customersGrowthChart" style="height: 250px;"></canvas>
    </div>
</div>
<div class="row">
<div class="col-md-6">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-star"></i> Best Selling Packages Per Month</h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Sales</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $bestSellingPackages as $package}
                        <tr>
                            <td>{$package.name_plan}</td>
                            <td>{$currencyCode} {$package.formattedPrice}</td>
                            <td>{$package.sales}</td>
                            <td>{$currencyCode} {$package.formattedRevenue}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-wifi"></i> Transactions per Router</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Router</th>
                                <th>Transactions</th>
                                <th>Percentage</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
<tbody>
    {foreach $transactionsPerRouter as $router}
    <tr>
        <td>{$router.router_name}</td>
        <td>{$router.transactions}</td>
        <td>{$router.percentage}%</td>
        <td>{$currencyCode} {$router.formattedAmount}</td>
    </tr>
    {/foreach}
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{if $_c['hide_uet'] != 'yes'}
        <div class="panel panel-warning mb20 panel-hovered project-stats table-responsive">
                  <div class="panel-heading">{Lang::T('Users Expiring Today')}</div>
            <div class="table-responsive">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                             <th>{Lang::T('Username')}</th>
                             <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $expire as $expired}
                            <tr>
                                <td><a href="{$_url}customers/viewu/{$expired['username']}">{$expired['username']}</a></td>
                                <td>{Lang::dateAndTimeFormat($expired['recharged_on'],$expired['recharged_time'])}
                                </td>
                                <td>{Lang::dateAndTimeFormat($expired['expiration'],$expired['time'])}
                                </td>
                            </tr>
                        </tbody>
                    {/foreach}
                </table>
            </div>
            &nbsp; {$paginator['contents']}
        </div>
    {/if}
</div>


<div class="col-md-5">
    {if $_c['hide_pg'] != 'yes'}
        <div class="panel panel-success panel-hovered mb20 activities">
            <div class="panel-heading">{Lang::T('Payment Gateway')}: {$_c['payment_gateway']}</div>
        </div>
    {/if}
    {if $_c['hide_aui'] != 'yes'}
        <div class="panel panel-info panel-hovered mb20 activities">
            <div class="panel-heading">{Lang::T('All Users Insights')}</div>
            <div class="panel-body">
                <canvas id="userRechargesChart"></canvas>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Monthly Data Usage</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyUsageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-money"></i> Last 5 Transactions</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>username</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $lastTransactions as $transaction}
                                <tr>
                                    <td>{$transaction.username}</td>
                                    <td>{$transaction.price}</td>
                                    <td>{$transaction.recharged_on}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> Users by Service Type</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Service Type</th>
                                <th>Users</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $serviceTypes as $serviceType}
                                <tr>
                                    <td>{$serviceType.service_type}</td>
                                    <td>{$serviceType.count}</td>
                                    <td>{$serviceType.percentage}%</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-download"></i> Top 5 Data Users</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $topDownloaders as $downloader}
                                <tr>
                                    <td>{$downloader.username}</td>
                                    <td>{$downloader.download|convert_bytes}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{if $_c['hide_al'] != 'yes'}
<div class="row">
<div class="col-md-12">
<div class="panel panel-info panel-hovered mb20 activities">
<div class="panel-heading"><a href="{$_url}logs">{Lang::T('Activity Log')}</a></div>
<div class="panel-body">
<ul class="list-unstyled">
{foreach $dlog as $dlogs}
<li class="primary">
<span class="point"></span>
<span class="time small text-muted">{Lang::timeElapsed($dlogs['date'],true)}</span>
<p>{$dlogs['description']}</p>
</li>
{/foreach}
</ul>
</div>
</div>
</div>
</div>
{/if}


























































<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>

<script type="text/javascript">
 document.addEventListener("DOMContentLoaded", function() {
    var monthlyRegistered = JSON.parse('{$monthlyRegistered|json_encode}');
    var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var labels = monthNames;

    // Calculate the cumulative values
    var cumulativeValues = [];
    var previousValue = 0;
    monthlyRegistered.forEach(function(item) {
        previousValue += item.count;
        cumulativeValues.push(previousValue);
    });
// Fill the remaining months with the last cumulative value
var lastValue = cumulativeValues[cumulativeValues.length - 1];
for (var i = cumulativeValues.length; i < monthNames.length; i++) {
    cumulativeValues.push(lastValue);
}
    var data = cumulativeValues;

    var ctx = document.getElementById('customersGrowthChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Customers Growth',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    });
});
</script>

<script type="text/javascript">
    {if $_c['hide_mrc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var counts = JSON.parse('{/literal}{$monthlyRegistered|json_encode}{literal}');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = counts.find(count => count.date === i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.count : 0);
                }

                var ctx = document.getElementById('chart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Registered Members',
                            data: data,
                            backgroundColor: 'rgba(0, 0, 255, 0.5)',
                            borderColor: 'rgba(0, 0, 255, 0.7)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });
        {/literal}
    {/if}
    {if $_c['hide_tmc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var monthlySales = JSON.parse('{/literal}{$monthlySales|json_encode}{literal}');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = findMonthData(monthlySales, i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.totalSales : 0);
                }

                var ctx = document.getElementById('salesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Sales',
                            data: data,
                            backgroundColor: 'rgba(2, 10, 242)', // Customize the background color
                            borderColor: 'rgba(255, 99, 132, 1)', // Customize the border color
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });

            function findMonthData(monthlySales, month) {
                for (var i = 0; i < monthlySales.length; i++) {
                    if (monthlySales[i].month === month) {
                        return monthlySales[i];
                    }
                }
                return null;
            }
        {/literal}
    {/if}
    {if $_c['hide_aui'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                // Get the data from PHP and assign it to JavaScript variables
                var u_act = '{/literal}{$u_act}{literal}';
                var c_all = '{/literal}{$c_all}{literal}';
                var u_all = '{/literal}{$u_all}{literal}';
                //lets calculate the inactive users as reported
                var expired = u_all - u_act;
                var inactive = c_all - u_all;
                // Create the chart data
                var data = {
                    labels: ['Active Users', 'Expired Users', 'Inactive Users'],
                    datasets: [{
                        label: 'User Recharges',
                        data: [parseInt(u_act), parseInt(expired), parseInt(inactive)],
                        backgroundColor: ['rgba(4, 191, 13)', 'rgba(191, 35, 4)', 'rgba(0, 0, 255, 0.5'],
                        borderColor: ['rgba(0, 255, 0, 1)', 'rgba(255, 99, 132, 1)', 'rgba(0, 0, 255, 0.7'],
                        borderWidth: 1
                    }]
                };

                // Create chart options
                var options = {
                    responsive: true,
                    aspectRatio: 1,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15
                            }
                        }
                    }
                };

                // Get the canvas element and create the chart
                var ctx = document.getElementById('userRechargesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: options
                });
            });
        {/literal}
    {/if}
</script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        $.getJSON("./version.json?" + Math.random(), function(data) {
            var localVersion = data.version;
            $('#version').html('Version: ' + localVersion);
            $.getJSON(
                "https://raw.githubuserc/master/version.json?" +
                Math
                .random(),
                function(data) {
                    var latestVersion = data.version;
                    if (localVersion !== latestVersion) {
                        $('#version').html('Latest Version: ' + latestVersion);
                    }
                });
        });

    });
</script>
























<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Today's Usage Chart
    var todayUsageCtx = document.getElementById('todayUsageChart').getContext('2d');
    var todayUsageChart = new Chart(todayUsageCtx, {
        type: 'doughnut',
        data: {
            labels: ['Upload', 'Download'],
            datasets: [{
                data: [{$todayUpload}, {$todayDownload}],
                backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Today\'s Data Usage'
                }
            },
            cutout: '50%',
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    // Weekly Usage Chart
    var weeklyUsageCtx = document.getElementById('weeklyUsageChart').getContext('2d');
    var weeklyUsageChart = new Chart(weeklyUsageCtx, {
        type: 'bar',
        data: {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            datasets: [{
                label: 'Upload',
                data: [
                    {$mondayUpload},
                    {$tuesdayUpload},
                    {$wednesdayUpload},
                    {$thursdayUpload},
                    {$fridayUpload},
                    {$saturdayUpload},
                    {$sundayUpload}
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Download',
                data: [
                    {$mondayDownload},
                    {$tuesdayDownload},
                    {$wednesdayDownload},
                    {$thursdayDownload},
                    {$fridayDownload},
                    {$saturdayDownload},
                    {$sundayDownload}
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Weekly Data Usage'
                }
            },
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function(value) {
                            return formatBytes(value);
                        }
                    }
                }
            },
            tooltips: {
                callbacks: {
                    label: function(context) {
                        var label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += formatBytes(context.parsed.y);
                        return label;
                    }
                }
            }
        }
    });

    // Monthly Usage Chart
    var monthlyUsageCtx = document.getElementById('monthlyUsageChart').getContext('2d');
    var monthlyUsageChart = new Chart(monthlyUsageCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Upload',
                data: [
                    {$januaryUpload},
                    {$februaryUpload},
                    {$marchUpload},
                    {$aprilUpload},
                    {$mayUpload},
                    {$juneUpload},
                    {$julyUpload},
                    {$augustUpload},
                    {$septemberUpload},
                    {$octoberUpload},
                    {$novemberUpload},
                    {$decemberUpload}
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Download',
                data: [
                    {$januaryDownload},
                    {$februaryDownload},
                    {$marchDownload},
                    {$aprilDownload},
                    {$mayDownload},
                    {$juneDownload},
                    {$julyDownload},
                    {$augustDownload},
                    {$septemberDownload},
                    {$octoberDownload},
                    {$novemberDownload},
                    {$decemberDownload}
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Monthly Data Usage'
                }
            },
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function(value) {
                            return formatBytes(value);
                        }
                    }
                }
            },
            tooltips: {
                callbacks: {
                    label: function(context) {
                        var label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += formatBytes(context.parsed.y);
                        return label;
                    }
                }
            }
        }
    });

    // Format bytes to a readable format
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>

{include file="sections/footer.tpl"}