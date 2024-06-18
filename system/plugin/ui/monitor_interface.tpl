{include file="sections/header.tpl"}

<div class="container mt-5">
  <div class="card">
    <div class="card-header">
    </div>
    <div class="card-body">
      <div class="form-group">
        <label for="interface">Interface</label>
      </div>
      <div class="table-responsive mt-4">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>
                <select name="interface" id="interface" class="form-control custom-select" onchange="updateTrafficValues()">
                    {foreach $interfaces as $interface}
                        <option value="{$interface}">{$interface}</option>
                    {/foreach}
                </select>
              </th>
              <th id="tabletx"><strong>TX:</strong> 0 B</th>
              <th id="tablerx"><strong>RX:</strong> 0 B</th>
            </tr>
          </thead>
        </table>
      </div>
      <div id="chart" class="mt-3" style="width: auto; height: 500px;"></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  var chart;
  var chartData = {
    txData: [],
    rxData: []
  };

function createChart() {
  var options = {
    chart: {
      height: 350,
      type: 'area',
      animations: {
        enabled: true,
        easing: 'linear',
        speed: 200,
        animateGradually: {
          enabled: true,
          delay: 150
        },
        dynamicAnimation: {
          enabled: true,
          speed: 200
        }
      },
      events: {
        mounted: function(chartContext, config) {
          // Initially load data and set up refresh interval
          updateTrafficValues();
          setInterval(updateTrafficValues, 3000);
        }
      }
    },
    stroke: {
      curve: 'smooth'
    },
    series: [{
      name: 'Upload',
      data: chartData.txData
    }, {
      name: 'Download',
      data: chartData.rxData
    }],
    xaxis: {
      type: 'datetime',
      labels: {
        formatter: function(value) {
          return new Date(value).toLocaleTimeString();
        }
      }
    },
    yaxis: {
      title: {
        text: 'Lalu Lintas Langsung'
      },
      labels: {
        formatter: function(value) {
          return formatBytes(value);
        }
      }
    },
    tooltip: {
      x: {
        format: 'HH:mm:ss'
      },
      y: {
        formatter: function(value) {
          return formatBytes(value) + 'ps';
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function(value) {
        return formatBytes(value);
      }
    }
  };
  chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();
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
  var interface = $('#interface').val();
  $('#selectedInterface').text(interface);
  $.ajax({
    url: '{$_url}plugin/monitor_traffic/{$router}',
    dataType: 'json',
    data: {
      interface: interface
    },
    success: function(data) {
      var timestamp = new Date().getTime();
      var txData = data.rows.tx;
      var rxData = data.rows.rx;
      if (txData.length > 0 && rxData.length > 0) {
        var TX = parseInt(txData[0]);
        var RX = parseInt(rxData[0]);
        chartData.txData.push({ x: timestamp, y: TX });
        chartData.rxData.push({ x: timestamp, y: RX });
        var maxDataPoints = 10;
        if (chartData.txData.length > maxDataPoints) {
          chartData.txData.shift();
          chartData.rxData.shift();
        }
        chart.updateSeries([{
          name: 'Upload',
          data: chartData.txData
        }, {
          name: 'Download',
          data: chartData.rxData
        }]);
        document.getElementById("tabletx").textContent = formatBytes(TX);
        document.getElementById("tablerx").textContent = formatBytes(RX);
      } else {
        document.getElementById("tabletx").textContent = "0";
        document.getElementById("tablerx").textContent = "0";
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.error("Status: " + textStatus + " request: " + XMLHttpRequest);
      console.error("Error: " + errorThrown);
    }
  });
}

createChart(); // Create the chart on page load
</script>

<script>
  window.addEventListener('DOMContentLoaded', function() {
    var portalLink = "https://github.com/kevindoni";
    $('#version').html('Interface Monitor | Ver: 1.0 | by: <a href="' + portalLink + '">Kevin Doni</a>');
  });
</script>

{include file="sections/footer.tpl"}