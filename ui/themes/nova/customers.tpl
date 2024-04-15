{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                <div class="btn-group pull-right">
                    <a class="btn btn-primary btn-xs" title="save" href="{$_url}customers/csv" onclick="return confirm('This will export to CSV?')"><span class="glyphicon glyphicon-download" aria-hidden="true"></span> CSV</a>
                </div>
                {/if}
                {Lang::T('Manage Contact')}
            </div>
            <ul class="nav nav-tabs nav-justified">
                <li class="{if $filter == 'all'}active{/if}">
                    <a href="{$_url}customers/list/?filter=all" class="bg-primary">
                        <i class="fa fa-users"></i> {Lang::T('All Users')}
                    </a>
                </li>
                <li class="{if $filter == 'active'}active{/if}">
                    <a href="{$_url}customers/list/?filter=active" class="bg-success">
                        <i class="fa fa-check-circle"></i> {Lang::T('Active Users')}
                    </a>
                </li>
                <li class="{if $filter == 'expired'}active{/if}">
                    <a href="{$_url}customers/list/?filter=expired" class="bg-danger">
                        <i class="fa fa-times-circle"></i> {Lang::T('Expired Users')}
                    </a>
                </li>
                <li class="{if $filter == 'hotspot'}active{/if}">
                    <a href="{$_url}customers/list/?filter=hotspot" class="bg-warning">
                        <i class="fa fa-wifi"></i> {Lang::T('Hotspot Users')}
                    </a>
                </li>
                <li class="{if $filter == 'static'}active{/if}">
                    <a href="{$_url}customers/list/?filter=static" class="bg-info">
                        <i class="fa fa-desktop"></i> {Lang::T('Static Users')}
                    </a>
                </li>
                <li class="{if $filter == 'pppoe'}active{/if}">
                    <a href="{$_url}customers/list/?filter=pppoe" class="bg-purple">
                        <i class="fa fa-exchange"></i> {Lang::T('PPPoE Users')}
                    </a>
                </li>
                <li class="{if $filter == 'new'}active{/if}">
                    <a href="{$_url}customers/list/?filter=new" class="bg-pink">
                        <i class="fa fa-plus-circle"></i> {Lang::T('New Users')}
                    </a>
                </li>
            </ul>
            <div class="panel-body">
                <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                    <div class="col-md-8">
                        <form id="site-search" method="post" action="{$_url}customers/list/">
                            <div class="input-group">
                                <input type="text" name="search" value="{$search}" class="form-control" placeholder="{Lang::T('Search')}...">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="submit"><span class="fa fa-search"></span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <a href="{$_url}customers/add" class="btn btn-primary btn-block"><i class="ion ion-android-add"> </i> {Lang::T('Add New Contact')}</a>
                    </div>&nbsp;
                </div>
                <div class="table-responsive table_mobile">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Full Name')}</th>
                                <th>{Lang::T('Balance')}</th>
                                <th>{Lang::T('Phone Number')}</th>
                                <th>{Lang::T('Email')}</th>
                                <th>{Lang::T('Package')}</th>
                                <th>{Lang::T('Service Type')}</th>
                                <th>{Lang::T('Created On')}</th>
                                <th>{Lang::T('IP Address')}</th>
                                <th>Router</th>
                                <th>{Lang::T('Manage')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                            <tr>
                                <td onclick="window.location.href = '{$_url}customers/view/{$ds['id']}'" style="cursor:pointer;">{$ds['username']}</td>
                                <td onclick="window.location.href = '{$_url}customers/view/{$ds['id']}'" style="cursor: pointer;">{$ds['fullname']}</td>
<td>
    {Lang::moneyFormat($ds['balance'])}
    <a href="{$_url}customers/edit-balance/{$ds['id']}" class="btn btn-primary btn-xs">Edit</a>
</td>
                                <td>{$ds['phonenumber']}</td>
                                <td>{$ds['email']}</td>
                                <td align="center" api-get-text="{$_url}autoload/customer_is_active/{$ds['id']}">
                                    <span class="label label-default">&bull;</span>
                                </td>
                                <td>{$ds['service_type']}</td>
                                <td>{Lang::dateTimeFormat($ds['created_at'])}</td>
                                <td>{$ds['ip_address']}</td>
                                <td>{$ds['router_name']}</td>
                                <td align="center">
                                    <a href="{$_url}customers/view/{$ds['id']}" id="{$ds['id']}" style="margin: 0px;" class="btn btn-success btn-xs">&nbsp;&nbsp;{Lang::T('View')}&nbsp;&nbsp;</a>
                                    <a href="{$_url}prepaid/recharge/{$ds['id']}" id="{$ds['id']}" style="margin: 0px;" class="btn btn-primary btn-xs">{Lang::T('Recharge')}</a>
                                    <a href="{$_url}customers/edit/{$ds['id']}" id="{$ds['id']}" style="margin: 0px;" class="btn btn-warning btn-xs">{Lang::T('Edit')}</a>
                                    <a href="{$_url}customers/delete/{$ds['id']}" id="{$ds['id']}" style="margin: 0px;" class="btn btn-danger btn-xs" onclick="return confirm('{Lang::T('Delete')}?')">{Lang::T('Delete')}</a>
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

<div class="modal fade" id="editBalanceModal" tabindex="-1" role="dialog" aria-labelledby="editBalanceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editBalanceModalLabel">{Lang::T('Edit Balance')}</h4>
            </div>
            <div class="modal-body">
                <form id="editBalanceForm" method="post" action="{$_url}customers/edit-balance">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="form-group">
                        <label for="balance">{Lang::T('Balance')}</label>
                        <input type="text" class="form-control" id="balance" name="balance" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{Lang::T('Save Changes')}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{literal}
<script>
$(document).ready(function() {
    $('.edit-balance').click(function() {
        console.log('Edit balance click event triggered');
        var customerId = $(this).data('customer-id');
        var balance = $(this).data('balance');
        console.log('Customer ID:', customerId);
        console.log('Current Balance:', balance);
        $('#customer_id').val(customerId);
        $('#balance').val(balance);
        $('#editBalanceModal').modal('show');
    });

    $('#editBalanceForm').submit(function(e) {
        e.preventDefault();
        console.log('Form submission event triggered');
        var customerId = $('#customer_id').val();
        var balance = $('#balance').val();
        console.log('Customer ID:', customerId);
        console.log('New Balance:', balance);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: {
                customer_id: customerId,
                balance: balance
            },
            success: function(response) {
                console.log('AJAX success response:', response);
                // Handle success response
                $('#editBalanceModal').modal('hide');
                location.reload(); // Optionally reload the page
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', error);
                // Handle error response
            }
        });
    });
});
</script>
{/literal}


{include file="sections/footer.tpl"}