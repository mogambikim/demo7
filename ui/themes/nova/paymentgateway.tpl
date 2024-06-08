{include file="sections/header.tpl"}
<div class="row">
    <div class="col-sm-12">
            <div class="panel panel-info panel-hovered">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{Lang::T('Payment Gateway')}</span>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help?')}
                    </button>
                </div>
            <div class="panel-body row">
                {foreach $pgs as $pg}
                    <div class="col-sm-4 mb20">
                        <a href="{$_url}paymentgateway/{$pg}"
                        class="btn btn-block btn-{if $pg==$_c['payment_gateway']}success{else}default{/if}">{ucwords($pg)}</a>
                    </div>
                {/foreach}
            </div>
            <div class="panel-footer">
                <form method="post">
                <div class="form-group row">
                    <label class="col-md-2 control-label">Payment Gateway</label>
                    <div class="col-md-8">
                        <select name="payment_gateway" id="payment_gateway" class="form-control">
                            <option value="none">None</option>
                            {foreach $pgs as $pg}
                                <option value="{$pg}" {if $_c['payment_gateway'] eq {$pg}}selected="selected" {/if}>{ucwords($pg)}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-2">
 <button class="btn btn-success"
                               type="submit">{Lang::T('Save Changes')}</button>
                    </div>
                </div>
                </div>
            </div>
    </div>
</div>

<div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="tutorialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tutorialModalLabel">Tutorial Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/M91aZf1wrEw?si=f3cxhNtD6wDbMBwz" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}