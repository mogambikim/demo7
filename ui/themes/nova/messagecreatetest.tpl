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
            <div class="panel-heading">Send Bulk Messages</div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#bulkSend">{Lang::T('Bulk Send')}</a></li>
                    <li><a data-toggle="tab" href="#routerSpecific">{Lang::T('Router Specific')}</a></li>
                </ul>

                <div class="tab-content">
                    <div id="bulkSend" class="tab-pane fade in active">
                        <form class="form-horizontal" method="post" role="form" id="bulkMessageForm" action="">
                            <!-- Bulk Send form content goes here -->
                            <div class="form-group">
                                <label class="col-md-2 control-label">{Lang::T('Group')}</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="group" id="group">
                                        <option value="all" selected>{Lang::T('All Customers')}</option>
                                        <option value="new">{Lang::T('New Customers')}</option>
                                        <option value="expired">{Lang::T('Expired Customers')}</option>
                                        <option value="active">{Lang::T('Active Customers')}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Rest of the Bulk Send form fields -->
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-success" type="submit" name="send" value="now">
                                        {Lang::T('Send Message')}
                                    </button>
                                    <a href="{$_url}dashboard" class="btn btn-default">{Lang::T('Cancel')}</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="routerSpecific" class="tab-pane fade">
                        <form class="form-horizontal" method="post" role="form" action="{$_url}settings/specific-post">
                            <!-- Router Specific form content goes here -->
                            <div class="form-group">
                                <label class="col-md-2 control-label">{Lang::T('Send to')}</label>
                                <div class="col-md-6">
                                    <label><input type="radio" id="All" name="type" value="All" checked>  {Lang::T('All users')}</label>
                                    <label><input type="radio" id="Spec" name="type" value="Spec">  {Lang::T('Specific users')}</label>
                                </div>
                            </div>
                            <!-- Rest of the Router Specific form fields -->
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-success" type="submit">{Lang::T('Send Now')}</button>
                                    {Lang::T('Or')} <a href="{$_url}settings/specific">{Lang::T('Cancel')}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{if $batchStatus}
<p>
    <span class="label label-success">{Lang::T('Total SMS Sent')}: {$totalSMSSent}</span>
    <span class="label label-danger">{Lang::T('Total SMS Failed')}: {$totalSMSFailed}</span>
    <span class="label label-success">{Lang::T('Total WhatsApp Sent')}: {$totalWhatsappSent}</span>
    <span class="label label-danger">{Lang::T('Total WhatsApp Failed')}: {$totalWhatsappFailed}</span>
</p>
{/if}
<div class="box">
    <!-- Message Results table -->
    <div class="box-header">
        <h3 class="box-title">{Lang::T('Message Results')}</h3>
    </div>
    <div class="box-body">
        <table id="messageResultsTable" class="table table-bordered table-striped table-condensed">
            <!-- Message Results table content -->
            <thead>
                <tr>
                    <th>{Lang::T('Name')}</th>
                    <th>{Lang::T('Phone')}</th>
                    <th>{Lang::T('Message')}</th>
                    <th>{Lang::T('Status')}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $batchStatus as $customer}
                <tr>
                    <td>{$customer.name}</td>
                    <td>{$customer.phone}</td>
                    <td>{$customer.message}</td>
                    <td>{$customer.status}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    var $j = jQuery.noConflict();

    $j(document).ready(function () {
        $j('#messageResultsTable').DataTable();

        // Router Specific form script
        $j('#selectAccountGroup').hide();
        
        $j('input[type="radio"]').change(function() {
            var selectedType = $j(this).val();
            if (selectedType === "All") {
                $j('#selectAccountGroup').hide();
            } else {
                $j('#selectAccountGroup').show();
            }
        });
        
        $j('#personSelect').select2();

        $j('#server').change(function() {
            var selectedRouterId = $j(this).val();
            $j.ajax({
                url: '{$_url}plugin/finduser&router=' + selectedRouterId,
                type: "GET",
                data: { router_id: selectedRouterId },
                success: function(response){
                    var data = JSON.parse(response);
                    $j('#personSelect').empty(); 
                    data.forEach(function(user) {
                        var usernameWithEmail = user.username + ' - ' + user.email;
                        $j('#personSelect').append($j('<option>', {
                            value: user.id,
                            text: usernameWithEmail,
                            class: 'black-option'
                        }));
                    });
                    $j('#personSelect').trigger('change.select2');
                },
                error: function(xhr, status, error) {
                    console.log('failed');
                }
            });
        });
    });
</script>

{include file="sections/footer.tpl"}