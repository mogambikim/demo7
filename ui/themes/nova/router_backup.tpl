{include file="sections/header.tpl"}

<style>
.table th, .table td {
    vertical-align: middle !important;
}

.btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}

.thead-dark th {
    background-color: #343a40;
    color: #000; /* Change to black */
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075);
}

.accordion .card {
    margin-bottom: 1rem;
}

.accordion .card-header {
    cursor: pointer;
    background-color: #007bff;
    color: white;
}

.accordion .card-header h5 {
    margin-bottom: 0;
}

.accordion .card-header .btn {
    width: 100%;
    text-align: left;
    color: white;
}

.accordion .card-header .btn:hover {
    text-decoration: none;
}

.card-body {
    background-color: #f8f9fa;
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('Router Backups')}</span>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                    {Lang::T('Need Help?')}
                </button>
            </div>
            <div class="panel-body">
                <div id="accordion" class="accordion">
                    {foreach $routers as $router}
                    <div class="card">
                        <div class="card-header" id="heading{$router.id}">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{$router.id}" aria-expanded="true" aria-controls="collapse{$router.id}">
                                    {$router.name}
                                </button>
                            </h5>
                        </div>

                        <div id="collapse{$router.id}" class="collapse" aria-labelledby="heading{$router.id}" data-parent="#accordion">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="color: black;">{Lang::T('Backup Date')}</th>
                                                <th style="color: black;">{Lang::T('Actions')}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$backups item=backup}
                                                {if $backup.router_id == $router.id}
                                                <tr>
                                                    <td>{$backup.backup_date}</td>
                                                    <td>
                                                        <a href="{$_url}router_backups/download-backup?id={$backup.id}" class="btn btn-info btn-sm">
                                                            <i class="fa fa-download"></i> {Lang::T('Download')}
                                                        </a>
                                                        <a href="{$_url}router_backups/restore-backup?id={$backup.id}" class="btn btn-success btn-sm">
                                                            <i class="fa fa-undo"></i> {Lang::T('Restore')}
                                                        </a>
                                                        <a href="{$_url}router_backups/delete-backup?id={$backup.id}" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i> {Lang::T('Delete')}
                                                        </a>
                                                    </td>
                                                </tr>
                                                {/if}
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="tutorialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tutorialModalLabel">{Lang::T('Tutorial Video')}</h5>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{Lang::T('Close')}</button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
