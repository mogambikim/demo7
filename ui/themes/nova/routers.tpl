{include file="sections/header.tpl"}
<!-- routers -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
                <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{Lang::T('Routers')}</span>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help?')}
                    </button>
                </div>
            <div class="panel-body">
                <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                    <div class="col-md-8">
                        <form id="site-search" method="post" action="{$_url}routers/list/">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-search"></span>
                                </div>
                                <input type="text" name="name" class="form-control" placeholder="{Lang::T('Search by Name')}...">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="submit">{Lang::T('Search')}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <a href="{$_url}routers/add" class="btn btn-primary btn-block waves-effect"><i class="ion ion-android-add"> </i> {Lang::T('New Router')}</a>
                    </div>&nbsp;
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{Lang::T('Router Name')}</th>
                                <th>{Lang::T('IP Address')}</th>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Description')}</th>
                                <th>{Lang::T('Status')}</th>
                                <th>{Lang::T('State')}</th>
                                <th>{Lang::T('Uptime')}</th>
                                <th>{Lang::T('Model')}</th>
                                <th>{Lang::T('Last Seen')}</th>
                                <th>{Lang::T('Reboot')}</th>
                                <th>{Lang::T('Manage')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $routers as $router}
                            <tr {if $router['enabled'] != 1}class="danger" title="disabled"{/if}>
                                <td>{$router['id']}</td>
                                <td>{$router['name']}</td>
                                <td>{$router['ip_address']}</td>
                                <td>{$router['username']}</td>
                                <td>{$router['description']}</td>
                                <td>{if $router['enabled'] == 1}Enabled{else}Disabled{/if}</td>
                                <td><span class="label label-{$router['pingClass']}">{$router['pingStatus']}</span></td>
                                <td>{$router['uptime']}</td>
                                <td>{$router['model']}</td>
                                <td>
                                    {if $router['pingStatus'] == 'Online'}
                                        <span class="label label-success">Currently Online</span>
                                    {else}
                                        {Lang::dateAndTimeFormat($router['last_seen'], '')}
                                    {/if}
                                </td>
                                <td><a href="{$_url}routers/reboot/{$router['id']}" class="btn btn-warning btn-xs">Reboot</a></td>
                                <td>
                                    <a href="{$_url}routers/edit/{$router['id']}" class="btn btn-info btn-xs">{Lang::T('Edit')}</a>
                                    <a href="{$_url}routers/delete/{$router['id']}" id="{$router['id']}" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {$paginator['contents']}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href="#collapseInstructions" class="text-primary">
                        <i class="fa fa-plus-circle"></i> {Lang::T('Enable Reboot Functionality')}
                    </a>
                </h4>
            </div>
            <div id="collapseInstructions" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> {Lang::T('To enable the router reboot functionality, please follow the instructions below:')}
                    </div>
                    <ol>
                        <li>{Lang::T('Copy the code snippet from the box below.')}</li>
                        <li>{Lang::T('Log in to your MikroTik router\'s terminal.')}</li>
                        <li>{Lang::T('Paste the code into the terminal and press Enter.')}</li>
                        <li>{Lang::T('The reboot functionality will be enabled on your router.')}</li>
                    </ol>
                    <div class="well">
                        <pre><code>/file print file=reboot.txt
/file set reboot.txt contents="0"
# Create the "reboot" script
/system script add name="reboot" source="/file set reboot.txt contents=\"1\""
# Create the "watch-reboot" scheduler
/system scheduler add name="watch-reboot" interval=1m on-event=":local needReboot [/file get reboot.txt contents]; :if (\$needReboot != \"0\") do={ /file set \"reboot.txt\" contents=\"0\"; /system reboot; }"</code></pre>
                        <button class="btn btn-primary btn-block" onclick="copyCode()"><i class="fa fa-copy"></i> {Lang::T('Copy Code')}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function copyCode() {
    var code = document.querySelector("pre code");
    var range = document.createRange();
    range.selectNode(code);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand("copy");
    window.getSelection().removeAllRanges();
    alert("{Lang::T('Code copied to clipboard!')}");
}
</script>

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
