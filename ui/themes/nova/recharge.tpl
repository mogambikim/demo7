{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>{Lang::T('Recharge Account')}</span>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                    {Lang::T('Need Help? Watch Guide Here')}
                </button>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}prepaid/recharge-post">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Select Account')}</label>
                        <div class="col-md-6">
                            <select {if $cust}{else} id="personSelect" {/if} class="form-control select2" name="id_customer" style="width: 100%" data-placeholder="{Lang::T('Select Customer')}...">
                                {if $cust}
                                <option value="{$cust['id']}">{$cust['username']} &bull; {$cust['fullname']} &bull; {$cust['email']}</option>
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Type')}</label>
                        <div class="col-md-6">
                            <label><input type="radio" id="Hot" name="type" value="Hotspot"> {Lang::T('Hotspot Plans')}</label>
                            <label><input type="radio" id="POE" name="type" value="PPPOE"> {Lang::T('PPPoE Plans')}</label>
                            <label><input type="radio" id="Static" name="type" value="Static"> {Lang::T('Static Ip Plans')}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Routers')}</label>
                        <div class="col-md-6">
                            <select id="server" name="server" class="form-control select2">
                                {foreach $r as $router}
                                {if $router->id == $cust['router_id']}
                                <option value='{$router->id}' selected>{$router->name}</option>
                                {/if}
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Service Plan')}</label>
                        <div class="col-md-6">
                            <select id="plan" name="plan" class="form-control select2">
                                {foreach $r as $router}
                                {$router->id}
                                {if $router->id == $cust['router_id']}
                                {foreach $p as $plan}
                                {if $plan->routers == $router->name}
                                <option class="plan-option" data-type="{$plan->type}" value='{$plan->id}'>{$plan->name_plan}</option>
                                {/if}
                                {/foreach}
                                {/if}
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit">{Lang::T('Recharge Now')}</button>
                            Or <a href="{$_url}customers/list">{Lang::T('Cancel')}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tutorial Modal -->
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

{literal}
<script>
$(document).ready(function() {
    $('.select2').select2({theme: 'bootstrap'});

    // Store original options
    var originalOptions = $('#plan').html();
    
    $('input[type="radio"]').change(function() {
        var selectedType = $(this).val();
        
        // Restore original options
        $('#plan').html(originalOptions);
        
        // Filter options based on selected type
        $('#plan option').each(function() {
            if ($(this).data('type') !== selectedType) {
                $(this).remove();
            }
        });
    });
    
    $('#personSelect').change(function(){
        var customerId = $(this).val();
        
        fetch('{$_url}plugin/findme&router='+customerId)
            .then(response => response.json())
            .then(data => {
                $('#server').empty();
                if(data.router_id && data.router_name) {
                    $('#server').append($('<option>', {
                        value: data.router_id,
                        text: data.router_name
                    }));
                } else {
                    $('#server').append($('<option>', {
                        value: '',
                        text: 'No router available'
                    }));
                }
                $('#server').trigger('change.select2');
                $('#server').val(data.router_id);
            })
            .catch(error => {
                console.error('Error fetching router data:', error);
            });
    });
});
</script>
{/literal}

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
