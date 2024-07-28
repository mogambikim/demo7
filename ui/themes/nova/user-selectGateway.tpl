{include file="sections/user-header.tpl"}

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-info panel-hovered">
            <div class="panel-heading">{Lang::T('Available Payment Gateway')}</div>
            <div class="panel-footer">
                <form method="post" action="{$_url}order/pay_now">
                    <div class="form-group row">

                        <div class="col-md-8">
                            <select name="gateway" id="gateway" class="form-control">
                                {foreach $pgs as $pg}
                                <option value="{$pg}">{ucwords($pg)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <!-- Add hidden fields for router_id and plan_id -->
                    <input type="hidden" name="router_id" value="{$route2}">
                    <input type="hidden" name="plan_id" value="{$route3}">
            </div>
            <div class="panel-body">
                <center><b>{Lang::T('Package Details')}</b></center>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{Lang::T('Plan Name')}</b> <span class="pull-right">{$plan.name_plan}</span>
                    </li>
                    {if $plan.is_radius or $plan.routers}

                    {/if}

                    <li class="list-group-item">
                        <b>{Lang::T('Plan Price')}</b> <span class="pull-right">{Lang::moneyFormat($plan.price)}</span>
                    </li>
                    {if $plan.validity}
                    <li class="list-group-item">
                        <b>{Lang::T('Plan Validity')}</b> <span class="pull-right">{$plan.validity} {$plan.validity_unit}</span>
                    </li>
                    {/if}
                </ul>
                <center><b>{Lang::T('Summary')}</b></center>
                <ul class="list-group list-group-unbordered">
                    {if $tax}
                    <li class="list-group-item">
                        <b>{Lang::T('Tax')}</b> <span class="pull-right">{Lang::moneyFormat($tax)}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Total')}</b> <small>({Lang::T('Plan Price')} + {Lang::T('Tax')})</small><span class="pull-right"
                            style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace;">{Lang::moneyFormat($plan.price+$tax)}</span>
                    </li>
                    {else}
                    <li class="list-group-item">
                        <b>{Lang::T('Total')}</b> <span class="pull-right"
                            style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace;">
                          {Lang::moneyFormat($plan.price)}</span>
                    </li>
                    {/if}
                </ul>
                <center>
                    <button type="submit" class="btn btn-primary">{Lang::T('Pay Now')}</button><br>
                    <a class="btn btn-link" href="{$_url}home">{Lang::T('Cancel')}</a>
                </center>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/user-footer.tpl"}
