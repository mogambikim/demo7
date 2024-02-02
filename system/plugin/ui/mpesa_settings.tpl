{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="">
    {if $message}
        <div class="alert alert-success">
            {$message}
        </div>
    {/if}
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Mpesa Settings</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mpesa Settings Consumer Key</label>
                        <div class="col-md-6">
                            <input type="text" id="mpesa_settings_consumer_key" name="mpesa_settings_consumer_key" value="{$mpesa_settings_consumer_key}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mpesa Settings Consumer Secret</label>
                        <div class="col-md-6">
                            <input type="text" id="mpesa_settings_consumer_secret" name="mpesa_settings_consumer_secret" value="{$mpesa_settings_consumer_secret}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mpesa Settings Business Code</label>
                        <div class="col-md-6">
                            <input type="text" id="mpesa_settings_business_code" name="mpesa_settings_business_code" value="{$mpesa_settings_business_code}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mpesa Settings Pass Key</label>
                        <div class="col-md-6">
                            <input type="text" id="mpesa_settings_pass_key" name="mpesa_settings_pass_key" value="{$mpesa_settings_pass_key}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Mpesa Hashback API Key</label>
                        <div class="col-md-6">
                            <input type="text" id="mpesa_hashback_api" name="mpesa_hashback_api" value="{$mpesa_hashback_api}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-2">
                            <small class="form-text text-muted">
                                Register for Hashback's API keys <a href="https://www.hashback.co.ke/" target="_blank">here</a>.
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success waves-effect waves-light" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}