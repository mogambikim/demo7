{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('Wireless Settings')}</span>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                    {Lang::T('Need Help?')}
                </button>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="index.php?_route=routers/wireless">
                    <input type="hidden" name="action" value="select_router">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Select Router')}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="router_id" onchange="this.form.submit()">
                                <option value="">{Lang::T('Select a Router')}</option>
                                {foreach $routers as $router}
                                    <option value="{$router.id}" {if $selected_router && $router.id == $selected_router.id}selected{/if}>{$router.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </form>
                {if $selected_router}
                <form class="form-horizontal" method="post" action="index.php?_route=routers/wireless">
                    <input type="hidden" name="router_id" value="{$selected_router.id}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Current SSID')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" value="{$current_ssid}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('New SSID')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="ssid" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('New Password')}</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button type="submit" class="btn btn-primary">{Lang::T('Update')}</button>
                            <a href="{$_url}routers/list" class="btn btn-default">{Lang::T('Cancel')}</a>
                        </div>
                    </div>
                </form>
                {/if}
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
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tQNY_TfIIQE?si=pu14iOtkGNa3sO59" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
