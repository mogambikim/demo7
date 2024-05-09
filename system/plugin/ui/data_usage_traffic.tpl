{include file="sections/user-header.tpl"}
<style>
    .table-container {
      flex: 0 0 100%;
      padding: 10px;
    }
  
    table {
      width: 100%;
      border-collapse: collapse;
    }
  
    thead {
      background-color: #f5f5f5;
    }
  
    th,
    td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
  
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  
    tr:hover {
      background-color: #f5f5f5;
    }
  </style>

<div class="container">
    <div class="graph-container">
        <canvas id="trafficFlow"></canvas>
    </div>
</div>


<div style="overflow-x:auto;" class="table-container">
    <table>
      <thead>
        <tr>
          <th>Username</th>
          <th>Address</th>
          <th>Uptime</th>
          <th>Download</th>
          <th>Upload</th>
        </tr>
      </thead>
      <tbody>
        {foreach $userTable as $user}
        <tr>
          <td>{$user.username}</td>
          <td>{$user.address}</td>
          <td>{$user.uptime}</td>
          <td id="tabletx"></td>
          <td id="tablerx"></td>
        </tr>
        {/foreach}
      </tbody>
    </table>
  </div>
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
      var username = '{$user.username}'; // Replace with the actual username
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
  {include file="sections/user-footer.tpl"}