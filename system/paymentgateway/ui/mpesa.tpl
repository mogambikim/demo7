{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/MpesaPaybill" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">M-Pesa</div>
                <div class="panel-body">
                    <div class="form-group">
                        
                        <div class="col-md-6">
                            <input type="hidden" class="form-control" id="mpesa_consumer_key" name="mpesa_consumer_key" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_consumer_key']}">
                           
                        </div>
                    </div>
                    <div class="form-group">
                        
                        <div class="col-md-6">
                            <input type="hidden" class="form-control" id="mpesa_consumer_secret" name="mpesa_consumer_secret" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_consumer_secret']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Your Paybill</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_paybill" name="mpesa_paybill" placeholder="xxxxxxx" maxlength="7" value="{$_c['mpesa_paybill']}">
                        </div>
                    </div>
					<div class="form-group">
                       
                        <div class="col-md-6">
                            <input type="hidden" class="form-control" id="mpesa_pass_key" name="mpesa_pass_key" placeholder="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919" maxlength="" value="{$_c['mpesa_pass_key']}">
                        </div>
                    </div>
					

                    <div class="form-group">
                       
                        <div class="col-md-6">
                            <input type="hidden" readonly class="form-control" onclick="this.select()" value="{$_url}callback/mpesa">
                           
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary" type="submit">{Lang::T('Save Changes')}</button>
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
