{include file="sections/header.tpl"}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<style>
    .black-option, .select2-selection__choice, .select2-results__option {
        color: black !important;
    }
</style>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('Manage SMS Groups')}</span>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                    {Lang::T('Need Help?')}
                </button>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" id="createGroupForm" action="{U}message/sms_groups_post">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Group Name')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="group_name" id="group_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit">
                                {Lang::T('Create Group')}
                            </button>
                            <a href="{$_url}dashboard" class="btn btn-default">{Lang::T('Cancel')}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading">
                <span>{Lang::T('Existing Groups')}</span>
            </div>
            <div class="panel-body">
                <table id="groupsTable" class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>{Lang::T('Group Name')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$groups item=group}
                        <tr>
                            <td>{$group.group_name}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('Send Group Message')}</span>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" id="sendGroupMessageForm" action="{U}message/send_group_message">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Group')}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="group_id" id="group_id" required>
                                {foreach from=$groups item=group}
                                <option value="{$group.id}">{$group.group_name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Send Via')}</label>
                        <div class="col-md-6">
                            <select class="form-control" name="via" id="via">
                                <option value="sms" selected>{Lang::T('SMS')}</option>
                                <option value="wa">{Lang::T('WhatsApp')}</option>
                                <option value="both">{Lang::T('SMS and WhatsApp')}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Message')}</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="message" name="message" placeholder="{Lang::T('Compose your message...')}" rows="5" required></textarea>
                        </div>
                        <p class="help-block col-md-4">
                            {Lang::T('Use placeholders:')}<br>
                            <b>[[name]]</b> - {Lang::T('Customer Name')}<br>
                            <b>[[user_name]]</b> - {Lang::T('Customer Username')}<br>
                            <b>[[phone]]</b> - {Lang::T('Customer Phone')}<br>
                            <b>[[company_name]]</b> - {Lang::T('Your Company Name')}
                        </p>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit">
                                {Lang::T('Send Message')}
                            </button>
                            <a href="{$_url}dashboard" class="btn btn-default">{Lang::T('Cancel')}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    var $j = jQuery.noConflict();

    $j(document).ready(function () {
        $j('#groupsTable').DataTable();
    });
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
