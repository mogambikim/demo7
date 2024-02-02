{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/kopokopo" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Kopo Kopo Payment Gateway Settings</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Client ID / Application Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="kopokopo_app_key" name="kopokopo_app_key" placeholder="*************************" value="{$_c['kopokopo_app_key']}">
                            <small class="form-text text-muted">Production - Live: <a href="https://app.kopokopo.com/oauth/applications" target="_blank">https://app.kopokopo.com/oauth/applications</a></small><br>
                            <small class="form-text text-muted">Sandbox - Testing: <a href="https://sandbox.kopokopo.com/oauth/applications" target="_blank">https://sandbox.kopokopo.com/oauth/applications</a></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Client Secret / Application Secret</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="kopokopo_app_secret" name="kopokopo_app_secret" placeholder="**************************" value="{$_c['kopokopo_app_secret']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Application API Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="kopokopo_api_key" name="kopokopo_api_key" placeholder="******************************" maxlength="" value="{$_c['kopokopo_api_key']}">
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-md-2 control-label">Till Number</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="kopokopo_till_number" name="kopokopo_till_number" placeholder="K000000" maxlength="7" value="{$_c['kopokopo_till_number']}">
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-md-2 control-label">Kopo Kopo Environment</label>
                        <div class="col-md-6">
                          <select class="form-control" name="kopokopo_env" id="kopokopo_env">
                            <option value="sandbox" {if $_c['kopokopo_env'] == 'sandbox'}selected{/if}>SandBox or Testing</option>
                            <option value="live" {if $_c['kopokopo_env'] == 'live'}selected{/if}>Live or Production</option>
                          </select>
                            <small class="form-text text-muted"><font color="red"><b>Sandbox</b></font> is for testing purpose, please switch to <font color="green"><b>Live</b></font> in production.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">Url Notification</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control" onclick="this.select()" value="{$_url}callback/kopokopo">
                            <p class="help-block">CallBack URL</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">{$_L['Save']}</button>
                        </div>
                    </div>
                        <pre>/ip hotspot walled-garden
                   add dst-host=kopokopo.com
                   add dst-host=*.kopokopo.com</pre>
                </div>
            </div>

        </div>
    </div>
</form>
{include file="sections/footer.tpl"}
