{include file="sections/header.tpl"}

<h3 align=""><u>Server Status and Information</u>:</h3>
    <style>
        /* CSS styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <table>

		{foreach $systemInfo as $key => $value}
        <tr>
            <th>{$key}</th>
            <th>{$value}</th>
        </tr>
        {/foreach}
		<tr>
            <th>Memory:</th>
			<th>
			<p>Total Memory: {$memory_usage.total} MB</p>
            <p>Free Memory: {$memory_usage.free} MB</p>
            <p>Used Memory: {$memory_usage.used} MB</p>
			<p>Memory Usage: {$memory_usage.used_percentage}%</p>
			</th>
        </tr>
		<tr>
            <th>Storage:</th>
			<th>
			<p>Total: {$disk_usage['total']}</p>
            <p>Total: {$disk_usage['total']}</p>
            <p>Free: {$disk_usage['free']}</p>
           <p>Usage Percentage: {$disk_usage['used_percentage']}</p>
			</th>
        </tr>
    </table>
<hr>
<br>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
               <div class="btn-group pull-right">
				<form action="{$_url}plugin/system_info" method="post">
                   <input type="hidden" name="reload" value="true">
                    <button type="submit" class="btn btn-primary btn-xs" title="Reload FreeRadius Server"
                        onclick="return confirm('Are you sure you want to Reload FreeRadius Server?')"><span
                            class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Reload FreeRADIUS</button>
                </form>
               </div>
                Service Status:
            </div>
            <div class="panel-body">
                <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                </div>
                <div class="table-responsive">
                    <table>
                        <tbody>
                         {foreach $serviceTable.rows as $row}
                            <tr>
                              <th>{$row.0}</th>
                              <th>{$row.1}</th>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{if $output != ''} <div class="panel panel-primary panel-hovered panel-stacked mb30">
        <div class="panel-heading">Results</div>
        <div class="panel-body">
          <pre>
		  {if $returnCode === 0}
            <p>Freeradius service reload successfully!</p>
            {else}
            <p>Freeradius service reload failed. Return code: {$returnCode} : {$output} </p>
      	  {/if}
		  </pre>
        </div>
      </div>
    </div> {/if}

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var portalLink = "https://github.com/focuslinkstech";
            $('#version').html('System Info Plugin by: <a href="' + portalLink + '">Focuslinks Tech</a>');
        });
    </script>

{include file="sections/footer.tpl"}
