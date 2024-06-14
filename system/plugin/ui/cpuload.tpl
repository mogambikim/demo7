{include file="sections/header.tpl"}
<div class="container mt-4">
  <div class="col-sm-12 col-md-12">
    <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/cpuload_ui">
      <ul class="nav nav-tabs mb-4">
        {foreach $routers as $r}
        <li class="nav-item">
          <a class="nav-link {if $r['id'] == $router}active{/if}" href="{$_url}plugin/cpuload_ui/{$r['id']}">{$r['name']}</a>
        </li>
        {/foreach}
      </ul>
    </form>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="table-responsive" api-get-text="{$_url}plugin/monitoring/{$router}"></div>
      </div>
    </div>
    <div class="card-header-bg-danger text-white card mt-4">
      <div class="card-header text-white"><i class="fas fa-bolt"> Performance</i></div>
<div class="card-body">
    <div class="mb-3">
        <label><i class="fas fa-microchip"></i> CPU Load</label>
        <div class="progress">
            <div id="cpu-load-bar" class="progress-bar bg-info" role="progressbar" style="width: 0%;">0%</div>
        </div>
    </div>
    <div class="mb-3">
        <label><i class="fas fa-thermometer-half"></i> Temperature</label>
        <div class="progress">
            <div id="temperature-bar" class="progress-bar bg-danger" role="progressbar" style="width: 0%;">0°C</div>
        </div>
    </div>
    <div class="mb-3">
        <label><i class="fas fa-bolt"></i> Voltage</label>
        <div class="progress">
            <div id="voltage-bar" class="progress-bar bg-warning" role="progressbar" style="width: 0%;">0 V</div>
        </div>
    </div>
    <div class="mb-3">
        <label><i class="fas fa-clock"></i> Current Time</label>
        <div class="progress">
            <div id="current-time-bar" class="progress-bar bg-dark" role="progressbar" style="width: 100%;">0</div>
        </div>
    </div>
</div>

    </div>
  </div>
</div>
{include file="sections/footer.tpl"}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function() {
    function updateProgressBars() {
      $.ajax({
        url: '{$_url}plugin/get_monitoring_data/{$router}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          $('#cpu-load-bar').css('width', data.cpu_load + '%').text(data.cpu_load + '%');
          $('#temperature-bar').css('width', data.temperature + '%').text(data.temperature + '°C');
          $('#voltage-bar').css('width', data.voltage + '%').text(data.voltage + ' V');
          $('#disk-usage-bar').css('width', data.write_sect_total_percentage + '%').text(data.write_sect_total_percentage + '%');
          $('#disk-usage-since-reboot-bar').css('width', data.write_sect_since_reboot_percentage + '%').text(data.write_sect_since_reboot_percentage + '%');
          $('#current-time-bar').text(data.current_time);
        },
        error: function(xhr, status, error) {
          console.error('Error fetching monitoring data:', error);
          // Handle error case, if needed
        }
      });
    }

    updateProgressBars();
    setInterval(updateProgressBars, 1000); // Refresh every 1 second
  });
</script>