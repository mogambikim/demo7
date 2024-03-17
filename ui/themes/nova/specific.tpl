{include file="sections/header.tpl"}

<style>
    .black-option, .select2-selection__choice, .select2-results__option {
        color: black !important;
    }
</style>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading">Send Bulk messages</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}settings/specific-post">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Send to</label>
                        <div class="col-md-6">
                            <label><input type="radio" id="All" name="type" value="All" checked>  All users</label>
                            <label><input type="radio" id="Spec" name="type" value="Spec">  Specific users</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{$_L['Routers']}</label>
                        <div class="col-md-6">
                            <select id="server" name="server" class="form-control select2" >
                                <option value=''>Select routers</option>
                                {foreach $r as $router}
                                    <option value='{$router->id}'>{$router->name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" id="selectAccountGroup">
                        <label class="col-md-2 control-label">Select User(s)</label>
                        <div class="col-md-6">
                            <select id="personSelect" class="form-control select2" name="id_customer[]" multiple style="width: 100%" data-placeholder="{$_L['Select_Customer']}...">
                              
                                     <!-- Filter users based on the selected router ID -->
                                        <option data-router="" value="" class="user-option black-option"></option>

                                   
                            </select>
                        </div>
                    </div>


                     <div class="form-group">
                        <label class="col-md-2 control-label">Message</label>
                        <div class="col-md-6">
                            <select id="type" name="msgtype" class="form-control select2">
                                <option class="plan-option" data-type="" value='downtime_alert'>Downtime alert</option>
                                 <option class="plan-option" data-type="" value='discount_alert'>Discount/Offer message</option>
                                 <option class="plan-option" data-type="" value='custom_message'>Custom Message</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success waves-effect waves-light" type="submit">Send Now</button>
                            Or <a href="{$_url}settings/specific">{$_L['Cancel']}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



{include file="sections/footer.tpl"}

<script>




$(document).ready(function() {
    // Hide Select Account group by default
    $('#selectAccountGroup').hide();
    
    // Show/hide Select Account group based on radio button selection
    $('input[type="radio"]').change(function() {
        var selectedType = $(this).val();
        if (selectedType === "All") {
            $('#selectAccountGroup').hide();
        } else {
            $('#selectAccountGroup').show();
        }
    });
    
    // Initialize Select2
    $('#personSelect').select2(); // Ensure Select2 is initialized

    // Filter users based on selected router
    $('#server').change(function() {
        var selectedRouterId = $(this).val();
        $.ajax({
            url: '{$_url}plugin/finduser&router=' + selectedRouterId,
            type: "GET",
            data: { router_id: selectedRouterId },
            success: function(response){
                // Parse the JSON response
                var data = JSON.parse(response);
                
                // Clear existing options
                $('#personSelect').empty(); 
                
                // Append options for each user
                data.forEach(function(user) {
                    var usernameWithEmail = user.username + ' - ' + user.email;
                    // Append option with black text color directly
                    $('#personSelect').append($('<option>', {
                        value: user.id, // Assuming you want username as value
                        text: usernameWithEmail, // Combine username and email
                        class: 'black-option' // Apply black text color
                    }));
                });
                
                // Trigger the change event of Select2 to refresh the dropdown
                $('#personSelect').trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.log('failed');
                // Handle the error here
            }
        });
    });
});







</script>
