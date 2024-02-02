{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/paypal">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">PayPal Payment Gateway</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Client ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id"
                                value="{$_c['paypal_client_id']}">
                            <a href="https://developer.paypal.com/dashboard/applications/live" target="_blank"
                                class="help-block">https://developer.paypal.com/dashboard/applications/live</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Verification Token</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="paypal_secret_key" name="paypal_secret_key"
                                value="{$_c['paypal_secret_key']}">
                            <a href="https://developer.paypal.com/dashboard/applications/live" target="_blank"
                                class="help-block">https://developer.paypal.com/dashboard/applications/live</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Currency</label>
                        <div class="col-md-6">
                            <select class="form-control" name="paypal_currency">
                                {foreach $currency as $cur}
                                    <option value="{$cur['id']}"
                                    {if $cur['id'] == $_c['paypal_currency']}selected{/if}
                                    >{$cur['id']} - {$cur['name']}</option>
                                {/foreach}
                            </select>
                            <small class="form-text text-muted">* This currency does not support decimals. If you pass a decimal amount, an error occurs.<br>
                            ** This currency is supported as a payment currency and a currency balance for in-country PayPal
                            accounts only. If the receiver of funds is not from Brazil, then PayPal converts funds into the
                            primary holding currency of the account with the applicable currency conversion rate. The currency
                            conversion rate includes PayPal's applicable spread or fee.<br>
                            *** This currency is supported as a payment currency and a currency balance for in-country PayPal
                            accounts only.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light"
                                type="submit">{$_L['Save']}</button>
                        </div>
                    </div>
                    <pre>/ip hotspot walled-garden
add dst-host=paypal.com
add dst-host=*.paypal.com</pre>
                    <small class="form-text text-muted">Set Telegram Bot to get any error and
                        notification</small>
                </div>
            </div>

        </div>
    </div>
</form>
{include file="sections/footer.tpl"}