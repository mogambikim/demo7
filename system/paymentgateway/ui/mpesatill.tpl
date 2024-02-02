{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/MpesatillStk" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">M-Pesa</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Consumer Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_consumer_key" name="mpesa_consumer_key" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_consumer_key']}">
                            <small class="form-text text-muted"><a href="https://developer.safaricom.co.ke/MyApps" target="_blank">https://developer.safaricom.co.ke/MyApps</a></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Consumer Secret</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_consumer_secret" name="mpesa_consumer_secret" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_till_consumer_secret']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Business Shortcode(Store number/H.O)</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_business_code" name="mpesa_business_code" placeholder="xxxxxxx" maxlength="7" value="{$_c['mpesa_till_shortcode_code']}">
                        </div>
                    </div>
                      <div class="form-group">
                        <label class="col-md-2 control-label">Business Shortcode(Till number)</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_business_code" name="mpesa_till" placeholder="xxxxxxx" maxlength="7" value="{$_c['mpesa_till_partyb']}">
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-md-2 control-label">Pass Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_pass_key" name="mpesa_pass_key" placeholder="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919" maxlength="" value="{$_c['mpesa_pass_key']}">
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-md-2 control-label">M-Pesa Environment</label>
                        <div class="col-md-6">
                            <select class="form-control" name="mpesa_env">
                                {foreach $env as $environment}
                                    <option value="{$environment['id']}"
                                    {if $environment['id'] == $_c['mpesa_env']}selected{/if}
                                    >{$environment['id']} - {$environment['name']}</option>
                                {/foreach}
                            </select>
                            <small class="form-text text-muted"><font color="red"><b>Sandbox</b></font> is for testing purpose, please switch to <font color="green"><b>Live</b></font> in production.</small>
                        </div>
                    </div>

                   
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">{$_L['Save']}</button>
                        </div>
                    </div>
                        <pre>/ip hotspot walled-garden
                   add dst-host=safaricom.co.ke
                   add dst-host=*.safaricom.co.ke</pre>
                </div>
            </div>

        </div>
    </div>
</form>
{include file="sections/footer.tpl"}
