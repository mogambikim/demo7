{include file="sections/header.tpl"}

		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="panel panel-primary panel-hovered panel-stacked mb30">
					<div class="panel-heading">{$_L['Edit_Bandwidth']}</div>
						<div class="panel-body">
						
                <form class="form-horizontal" method="post" role="form" action="{$_url}bandwidth/edit-post" >
				<input type="hidden" name="id" value="{$d['id']}">
                    <div class="form-group">
						<label class="col-md-2 control-label">{$_L['BW_Name']}</label>
						<div class="col-md-6">
							<input type="text" class="form-control" id="name" name="name" value="{$d['name_bw']}">
						</div>
                    </div>
                    <div class="form-group">
						<label class="col-md-2 control-label">{$_L['Rate_Download']}</label>
						<div class="col-md-4">
							<input type="text" class="form-control" id="rate_down" name="rate_down" value="{$d['rate_down']}">
						</div>
						<div class="col-md-2">
							<select class="form-control" id="rate_down_unit" name="rate_down_unit">
								<option value="Kbps" {if $d['rate_down_unit'] eq 'Kbps'}selected="selected" {/if}>Kbps</option>
                                <option value="Mbps" {if $d['rate_down_unit'] eq 'Mbps'}selected="selected" {/if}>Mbps</option>
							</select>
						</div>
                    </div>
                    <div class="form-group">
						<label class="col-md-2 control-label">{$_L['Rate_Upload']}</label>
						<div class="col-md-4">
							<input type="text" class="form-control" id="rate_up" name="rate_up" value="{$d['rate_up']}">
						</div>
						<div class="col-md-2">
							<select class="form-control" id="rate_up_unit" name="rate_up_unit">
								<option value="Kbps" {if $d['rate_up_unit'] eq 'Kbps'}selected="selected" {/if}>Kbps</option>
                                <option value="Mbps" {if $d['rate_up_unit'] eq 'Mbps'}selected="selected" {/if}>Mbps</option>
							</select>
						</div>
                    </div>
					
		
					<!-- ...existing form fields... -->

					<div class="form-group">
    <label class="col-md-2 control-label">Enable Burst Options</label>
    <div class="col-md-6">
        <input type="checkbox" id="enable_burst" name="enable_burst">
    </div>
</div>
<div id="burst_options" style="display: none;">



<div class="form-group">
    <label class="col-md-2 control-label">Burst Limit for Upload(Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_limit_for_upload" name="burst_limit_for_upload">
    </div>
	<div class="col-md-2">
							<select class="form-control" id="burst_limit_for_upload_unit" name="burst_limit_for_upload_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label">Burst Limit For Download (Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_limit_for_download" name="burst_limit_for_download">
    </div>

	<div class="col-md-2">
							<select class="form-control" id="burst_limit_for_download_unit" name="burst_limit_for_download_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label">Burst Threshold For Upload (Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_threshold_for_upload" name="burst_threshold_for_upload">
    </div>
	<div class="col-md-2">
							<select class="form-control" id="burst_threshold_for_upload_unit" name="burst_threshold_for_upload_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label">Burst Threshold for Download (Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_threshold_for_download" name="burst_threshold_for_download">
    </div>
	<div class="col-md-2">
							<select class="form-control" id="burst_threshold_for_download_unit" name="burst_threshold_for_download_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">Burst Time for Upload(Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_time_for_upload" name="burst_time_for_upload">
    </div>

	<div class="col-md-2">
							<select class="form-control" id="nul1l_unit" name="null_1unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label">Burst Time for Download (Kbps)</label>
    <div class="col-md-4">
        <input type="text" class="form-control" id="burst_time_for_download" name="burst_time_for_download">
    </div>

	<div class="col-md-2">
							<select class="form-control" id="null_unit" name="null_unit">
								<option value="Kbps">Kbps</option>
								<option value="Mbps">Mbps</option>
							</select>
						</div>
</div>
</div>

<!-- Repeat similar structure for Burst Limit, Burst Threshold, and Burst Time for both Upload and Download -->

<!-- ...rest of the form... -->

					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn btn-primary waves-effect waves-light" type="submit">{$_L['Submit']}</button>
							Or <a href="{$_url}bandwidth/list">{$_L['Cancel']}</a>
						</div>
					</div>
                </form>
				
					</div>
				</div>
			</div>
		</div>

		<script>
    document.getElementById('enable_burst').addEventListener('change', function() {
        var burstSection = document.getElementById('burst_options');
        if(this.checked) {
            burstSection.style.display = 'block';
        } else {
            burstSection.style.display = 'none';
        }
    });
</script>








{include file="sections/footer.tpl"}
