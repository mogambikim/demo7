{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-4 col-md-4">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <img class="profile-user-img img-responsive img-circle"
                    src="https://robohash.org/{$d['id']}?set=set3&size=100x100&bgset=bg1"
                    onerror="this.src='{$UPLOAD_PATH}/user.default.jpg'" alt="avatar">

                <h3 class="profile-username text-center">{$d['fullname']}</h3>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{Lang::T('Username')}</b> <span class="pull-right">{$d['username']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Phone Number')}</b> <span class="pull-right">{$d['phonenumber']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Email')}</b> <span class="pull-right">{$d['email']}</span>
                    </li>
                     <!-- Add the IP Address display here -->
                    <li class="list-group-item">
                        <b>IP Address</b> <span class="pull-right">{$d['ip_address']}</span>
                    </li>
                </ul>
                <p class="text-muted">{Lang::nl2br($d['address'])}</p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{Lang::T('Password')}</b> <input type="password" value="{$d['password']}"
                            style=" border: 0px; text-align: right;" class="pull-right"
                            onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                            onclick="this.select()">
                    </li>
                    {if $d['pppoe_password'] != ''}
                        <li class="list-group-item">
                            <b>PPPOE {Lang::T('Password')}</b> <input type="password" value="{$d['pppoe_password']}"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    {/if}
                    <!--Customers Attributes view start -->
                    {if $customFields}
                        {foreach $customFields as $customField}
                            <li class="list-group-item">
                                <b>{$customField.field_name}</b> <span class="pull-right">
                                    {if strpos($customField.field_value, ':0') === false}
                                        {$customField.field_value}
                                    {else}
                                        <b>{Lang::T('Paid')}</b>
                                    {/if}
                                </span>

                            </li>
                        {/foreach}
                    {/if}
                    <!--Customers Attributes view end -->
                    <li class="list-group-item">
                        <b>{Lang::T('Service Type')}</b> <span class="pull-right">{Lang::T($d['service_type'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Balance')}</b> <span class="pull-right">{Lang::moneyFormat($d['balance'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Auto Renewal')}</b> <span class="pull-right">
                            {if $d['auto_renewal']}yes{else}no{/if}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Created On')}</b> <span class="pull-right">{Lang::dateTimeFormat($d['created_at'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Last Login')}</b> <span class="pull-right">{Lang::dateTimeFormat($d['last_login'])}</span>
                    </li>
{if $d['coordinates']}
<li class="list-group-item">
    <b>{Lang::T('Coordinates')}</b>
    <span class="pull-right">
        <i class="glyphicon glyphicon-road"></i>
        <a style="color: black;" href="https://www.google.com/maps/dir//{$d['coordinates']}/" target="_blank">Get Directions</a>
    </span>
    <div style="height: 100px; overflow: hidden;">
        <div id="map" style="width: 100%; height: 100%;"></div>
    </div>
</li>
{/if}
                <div class="row">
                    <div class="col-xs-4">
                        <a href="{$_url}customers/delete/{$d['id']}" id="{$d['id']}"
                           class="btn btn-danger btn-block btn-sm" onclick="return confirm('{Lang::T('Delete')}?')">
                           <span class="fa fa-trash"></span>
                        </a>
                    </div>
                    <div class="col-xs-8">
                        <a href="{$_url}customers/edit/{$d['id']}"
                           class="btn btn-warning btn-sm btn-block">{Lang::T('Edit')}</a>
                    </div>
                </div>
            </div>
        </div>
        {if $package}
            <div class="box box-{if $package['status']=='on'}success{else}danger{/if}">
                <div class="box-body box-profile">
                    <h4 class="text-center">{$package['type']} - {$package['namebp']}</h4>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            {Lang::T('Active')} <span class="pull-right">{if $package['status']=='on'}yes{else}no{/if}</span>
                        </li>
                        <li class="list-group-item">
                            {Lang::T('Created On')} <span class="pull-right">{Lang::dateAndTimeFormat($package['recharged_on'],$package['recharged_time'])}</span>
                        </li>
                        <li class="list-group-item">
                            {Lang::T('Expires On')} <span class="pull-right">{Lang::dateAndTimeFormat($package['expiration'], $package['time'])}</span>
                        </li>
                        <li class="list-group-item">
                            {$package['routers']} <span class="pull-right">{$package['method']}</span>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="col-xs-4">
                            <a href="{$_url}customers/deactivate/{$d['id']}" id="{$d['id']}"
                                class="btn btn-danger btn-block btn-sm"
                                onclick="return confirm('This will deactivate Customer Plan, and make it expired')">{Lang::T('Deactivate')}</a>
                        </div>
                        <div class="col-xs-4">
                            <a href="{$_url}customers/recharge/{$d['id']}"
                                onclick="return confirm('This will extend Customer plan, same as recharge')"
                                class="btn btn-success btn-sm btn-block">{Lang::T('Recharge')}</a>
                        </div>
                        <div class="col-xs-4">
                            <a href="{$_url}customers/sync/{$d['id']}"
                                onclick="return confirm('This will sync Customer to Mikrotik?')"
                                class="btn btn-primary btn-sm btn-block">{Lang::T('Sync')}</a>
                        </div>
                    </div>
                </div>
            </div>
        {else}
            <a href="{$_url}prepaid/recharge/{$d['id']}" class="btn btn-success btn-sm btn-block mt-1">{Lang::T('Recharge')}</a><br>
        {/if}
        <div class="row">
            <div class="col-xs-4">
                <a href="{$_url}customers/list" class="btn btn-primary btn-sm btn-block">{Lang::T('Back')}</a>
            </div>
            <div class="col-xs-4">
                <a href="{$_url}customers/sync/{$d['id']}"
                    onclick="return confirm('This will sync Customer to Mikrotik?')"
                    class="btn btn-info btn-sm btn-block">{Lang::T('Sync')}</a>
            </div>
            <div class="col-xs-4">
                <a href="{$_url}message/send/{$d['id']}" class="btn btn-success btn-sm btn-block">{Lang::T('Send Message')}</a>
            </div>
        </div>
    </div>
    
    <div class="col-sm-8 col-md-8">
<ul class="nav nav-tabs">
    <li role="presentation" {if $v=='order' }class="active" {/if}><a href="{$_url}customers/view/{$d['id']}/order">30 {Lang::T('Order History')}</a></li>
    <li role="presentation" {if $v=='activation' }class="active" {/if}><a href="{$_url}customers/view/{$d['id']}/activation">30 {Lang::T('Activation History')}</a></li>
                    <li role="presentation" {if $v=='traffic' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/traffic">{Lang::T('Traffic Monitor')}</a></li>
                            <li role="presentation" {if $v=='data-usage' }class="active" {/if}><a href="{$_url}customers/view/{$d['id']}/data-usage">{Lang::T('Data Usage')}</a></li>
</ul>
        </ul>
        <div class="table-responsive" style="background-color: white;">
            <table id="datatable" class="table table-bordered table-striped">













{if $v == 'data-usage'}
    <div class="data-usage">
        <h1>Data Usage for {$d.username}</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Today's Data Usage</h3>
                    </div>
                    <div class="panel-body">
                        {if $hasTodayUsage}
                            <div class="chart-container">
                                <canvas id="todayUsageChart"></canvas>
                            </div>
                            <div class="usage-info">
                                <p><strong>Upload:</strong> {$todayUsage.upload|convert_bytes}</p>
                                <p><strong>Download:</strong> {$todayUsage.download|convert_bytes}</p>
                                <p><strong>Total:</strong> {($todayUsage.upload + $todayUsage.download)|convert_bytes}</p>
                            </div>
                        {else}
                            <p class="text-center">No data usage found for today.</p>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Weekly Data Usage</h3>
                    </div>
                    <div class="panel-body">
                        {if $hasWeeklyUsage}
                            <div class="chart-container">
                                <canvas id="weeklyUsageChart"></canvas>
                            </div>
                        {else}
                            <p class="text-center">No weekly data usage found.</p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Monthly Data Usage</h3>
                    </div>
                    <div class="panel-body">
                        {if $hasMonthlyUsage}
                            <div class="chart-container">
                                <canvas id="monthlyUsageChart"></canvas>
                            </div>
                        {else}
                            <p class="text-center">No monthly data usage found.</p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Today's Usage Chart
        {if $hasTodayUsage}
            var todayUsageCtx = document.getElementById('todayUsageChart').getContext('2d');
            var todayUsageChart = new Chart(todayUsageCtx, {
                type: 'pie',
                data: {
                    labels: ['Upload', 'Download'],
                    datasets: [{
                        data: [{$todayUsage.upload}, {$todayUsage.download}],
                        backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 192, 0.6)']
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
                    }
                }
            });
        {/if}

        // Weekly Usage Chart
        {if $hasWeeklyUsage}
            var weeklyUsageCtx = document.getElementById('weeklyUsageChart').getContext('2d');
            var weeklyUsageChart = new Chart(weeklyUsageCtx, {
                type: 'bar',
                data: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    datasets: [{
                        label: 'Upload',
                        data: [
                            {$weeklyUsage.monday_upload},
                            {$weeklyUsage.tuesday_upload},
                            {$weeklyUsage.wednesday_upload},
                            {$weeklyUsage.thursday_upload},
                            {$weeklyUsage.friday_upload},
                            {$weeklyUsage.saturday_upload},
                            {$weeklyUsage.sunday_upload}
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }, {
                        label: 'Download',
                        data: [
                            {$weeklyUsage.monday_download},
                            {$weeklyUsage.tuesday_download},
                            {$weeklyUsage.wednesday_download},
                            {$weeklyUsage.thursday_download},
                            {$weeklyUsage.friday_download},
                            {$weeklyUsage.saturday_download},
                            {$weeklyUsage.sunday_download}
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatBytes(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
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
                }
            });
        {/if}

        // Monthly Usage Chart
        {if $hasMonthlyUsage}
            var monthlyUsageCtx = document.getElementById('monthlyUsageChart').getContext('2d');
            var monthlyUsageChart = new Chart(monthlyUsageCtx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                        label: 'Upload',
                        data: [
                            {$monthlyUsage.january_upload},
                            {$monthlyUsage.february_upload},
                            {$monthlyUsage.march_upload},
                            {$monthlyUsage.april_upload},
                            {$monthlyUsage.may_upload},
                            {$monthlyUsage.june_upload},
                            {$monthlyUsage.july_upload},
                            {$monthlyUsage.august_upload},
                            {$monthlyUsage.september_upload},
                            {$monthlyUsage.october_upload},
                            {$monthlyUsage.november_upload},
                            {$monthlyUsage.december_upload}
                        ],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true
                    }, {
                        label: 'Download',
                        data: [
                            {$monthlyUsage.january_download},
                            {$monthlyUsage.february_download},
                            {$monthlyUsage.march_download},
                            {$monthlyUsage.april_download},
                            {$monthlyUsage.may_download},
                            {$monthlyUsage.june_download},
                            {$monthlyUsage.july_download},
                            {$monthlyUsage.august_download},
                            {$monthlyUsage.september_download},
                            {$monthlyUsage.october_download},
                            {$monthlyUsage.november_download},
                            {$monthlyUsage.december_download}
                        ],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return formatBytes(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
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
                }
            });
        {/if}

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
{/if}























{if $v == 'traffic'}
<div class="tab-pane">
    <div class="box-body">
        <h4 class="text-center">Traffic Monitor</h4>
        <div class="chart">
            <canvas id="trafficFlow" width="800" height="400"></canvas>
        </div>
    </div>
</div>
{/if}


                {if Lang::arrayCount($activation)}
                    <thead>
                        <tr>
                            <th>{Lang::T('Invoice')}</th>
                            <th>{Lang::T('Username')}</th>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $activation as $ds}
                            <tr onclick="window.location.href = '{$_url}prepaid/view/{$ds['id']}'" style="cursor:pointer;">
                                <td>{$ds['invoice']}</td>
                                <td>{$ds['username']}</td>
                                <td>{$ds['plan_name']}</td>
                                <td>{Lang::moneyFormat($ds['price'])}</td>
                                <td>{$ds['type']}</td>
                                <td class="text-success">{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}
                                </td>
                                <td class="text-danger">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                <td>{$ds['method']}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                {/if}
                {if Lang::arrayCount($order)}
                    <thead>
                        <tr>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Gateway')}</th>
                            <th>{Lang::T('Routers')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Date Done')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $order as $ds}
                            <tr>
                                <td>{$ds['plan_name']}</td>
                                <td>{$ds['gateway']}</td>
                                <td>{$ds['routers']}</td>
                                <td>{$ds['payment_channel']}</td>
                                <td>{Lang::moneyFormat($ds['price'])}</td>
                                <td class="text-primary">{Lang::dateTimeFormat($ds['created_date'])}</td>
                                <td class="text-danger">{Lang::dateTimeFormat($ds['expired_date'])}</td>
                                <td class="text-success">{if $ds['status']!=1}{Lang::dateTimeFormat($ds['paid_date'])}{/if}</td>
                                <td>
                                    {if $ds['status']==1}{Lang::T('UNPAID')}
                                    {elseif $ds['status']==2}{Lang::T('PAID')}
                                    {elseif $ds['status']==3}{$_L['FAILED']}
                                    {elseif $ds['status']==4}{Lang::T('CANCELED')}
                                    {elseif $ds['status']==5}{Lang::T('UNKNOWN')}
                                    {/if}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                {/if}
                {if $v == traffic}
                <div style="overflow-x:auto;" class="tab-pane">
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="trafficFlow" width="400" height="200"></canvas>
                        </div>
                    </div>
                  </div>
                {/if}
            </table>
        </div>
        {include file="pagination.tpl"}
    </div>
</div>

{if $d['coordinates']}
{literal}
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    function setupMap(lat, lon) {
        var map = L.map('map').setView([lat, lon], 17);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        
        // Disable zoom on scroll
        map.scrollWheelZoom.disable();
    }
    window.onload = function() {
        {/literal}setupMap({$d['coordinates']});{literal}
    }
</script>



{/literal}
{/if}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Global variables for the chart and data
    var chart;
    var chartData = {
        labels: [],
        txData: [],
        rxData: []
    };

    // Function to create and update the chart
    function createChart() {
        var ctx = document.getElementById('trafficFlow').getContext('2d');
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'TX',
                    data: chartData.txData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 0,
                    tension: 0.4,
                    fill: 'start' // Use 'start' to fill the area from the starting point
                }, {
                    label: 'RX',
                    data: chartData.rxData,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 0,
                    tension: 0.4,
                    fill: 'start' // Use 'start' to fill the area from the starting point
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Live Traffic'
                        },
                        ticks: {
                            callback: function (value) {
                                return formatBytes(value); // Format the tick values using formatBytes()
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                var label = context.dataset.label || '';
                                var value = context.parsed.y || 0;
                                return label + ': ' + formatBytes(value) + 'ps';
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0, // Set the point radius to 0 to remove the dots
                        hoverRadius: 0 // Set the hover point radius to 0 to remove the dots
                    },
                    line: {
                        tension: 0 // Set the line tension to 0 to remove the curve
                    }
                }
            }
        });
    }

    function formatBytes(bytes) {
        if (bytes === 0) {
            return '0 B';
        }
        var k = 1024;
        var sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        var formattedValue = parseFloat((bytes / Math.pow(k, i)).toFixed(2));
        return formattedValue + ' ' + sizes[i];
    }
    function updateTrafficValues() {
      // Get the username and router values
      var username = '{$d['username']}'; // Replace with the actual username
      var router = '{$router}'; // Replace with the actual router
  
      // Create the AJAX request
      $.ajax({
        url: '{$_url}plugin/data_usage_monitor_traffic', // Replace with the actual PHP file path
        type: 'GET',
        dataType: 'json',
        data: {
          router: router,
          username: username
        },
        success: function(data) {
              var labels = data.labels;
              var txData = data.rows.tx;
              var rxData = data.rows.rx;
              if (txData.length > 0 && rxData.length > 0) {
                var TX = parseInt(txData[0]);
                var RX = parseInt(rxData[0]);
                // Update chart data
                chartData.labels.push(labels[0]);
                chartData.txData.push(TX);
                chartData.rxData.push(RX);
                // Limit the number of data points to display (e.g., show the last 10 entries)
                var maxDataPoints = 10;
                if (chartData.labels.length > maxDataPoints) {
                  chartData.labels.shift();
                  chartData.txData.shift();
                  chartData.rxData.shift();
                }
                // Update the chart with the new data
                chart.update();
                // Update the table values
                document.getElementById("tabletx").textContent = formatBytes(TX);
                document.getElementById("tablerx").textContent = formatBytes(RX);
              } else {
                document.getElementById("tabletx").textContent = "0";
                document.getElementById("tablerx").textContent = "0";
              }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          console.error("Status: " + textStatus + " request: " + XMLHttpRequest);
          console.error("Error: " + errorThrown);
        }
      });
    }
   // Call createChart() to initialize the chart
   createChart();

// Example usage:
// updateTrafficValues();
// Update the traffic values every 1 seconds
setInterval(updateTrafficValues, 1000);
  
  </script>

