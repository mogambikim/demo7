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
                <div class="panel-heading">KopoKopo Settings</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Client ID</label>
                        <div class="col-md-6">
                            <input type="text" id="kopokopo_client_id" name="kopokopo_client_id" value="{$kopokopo_client_id}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Client Secret</label>
                        <div class="col-md-6">
                            <input type="text" id="kopokopo_client_secret" name="kopokopo_client_secret" value="{$kopokopo_client_secret}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">API Key</label>
                        <div class="col-md-6">
                            <input type="text" id="kopokopo_api_key" name="kopokopo_api_key" value="{$kopokopo_api_key}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Till Number</label>
                        <div class="col-md-6">
                            <input type="text" id="kopokopo_till_number" name="kopokopo_till_number" value="{$kopokopo_till_number}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Webhook</label>
                        <div class="col-md-6">
                            <input type="text" id="kopokopo_webhook" name="kopokopo_webhook" value="{$kopokopo_webhook}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Hashback API Key</label>
                        <div class="col-md-6">
                            <input type="text" id="hashback_api" name="hashback_api" value="{$hashback_api}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-2">
                            <small class="form-text text-muted">
                                Register for Hashback API keys <a href="https://www.hashback.co.ke/" target="_blank">here</a>.
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}