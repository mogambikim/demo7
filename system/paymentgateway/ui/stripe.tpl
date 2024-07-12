{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/stripe">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Stripe Payment Gateway</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Publishable Key (Public)</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="stripe_api_key" name="stripe_api_key"
                                value="{$_c['stripe_api_key']}">
                            <a href="https://dashboard.stripe.com/apikeys" target="_blank"
                                class="help-block">Get your Stripe Publishable Key from the Stripe Dashboard</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Secret Key (Private)</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="stripe_secret_key" name="stripe_secret_key"
                                value="{$_c['stripe_secret_key']}">
                            <a href="https://dashboard.stripe.com/apikeys" target="_blank"
                                class="help-block">Get your Stripe Secret Key from the Stripe Dashboard</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Currency</label>
                        <div class="col-md-6">
                            <select class="form-control" name="stripe_currency">
                                {foreach $currency as $cur}
                                    <option value="{$cur['id']}"
                                    {if $cur['id'] == $_c['stripe_currency']}selected{/if}
                                    >{$cur['id']} - {$cur['name']}</option>
                                {/foreach}
                            </select>
                            <small class="form-text text-muted">Select the currency for transactions (e.g., USD, EUR).</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary" type="submit">{Lang::T('Save Changes')}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}
