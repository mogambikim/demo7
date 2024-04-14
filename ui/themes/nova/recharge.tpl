{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
             <div class="panel-heading">{Lang::T('Recharge Account')}</div>
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
            <select id="server" name="server" class="form-control select2" >
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
                        <label class="col-md-2 control-label">{Lang::T('Using')}</label>
                        <div class="col-md-6">
                            <select name="using" class="form-control">
                                <option value="default">{Lang::T('Default')}</option>
                                {if $_c['enable_balance'] eq 'yes'}
                                    <option value="balance">{Lang::T('Customer Balance')}</option>
                                {/if}
                                <option value="zero">{$_c['currency_code']} 0</option>
                            </select>
                        </div>
                    </div>

    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
									<button class="btn btn-success" type="submit">{Lang::T('Recharge Now')}</button>
       Or <a href="{$_url}customers/list">{Lang::T('Cancel')}</a>
        </div>
    </div>
</form></div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
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
});
</script>


            
          
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Correctly wrapped JavaScript code
    {literal}
    // Initialize Select2 elements
    $('.select2').select2({theme: "bootstrap"});
    
    $('#personSelect').change(function(){
      var customerId = $(this).val();
      
       fetch('{/literal}{$_url}{literal}plugin/findme&router='+customerId)
            .then(response => response.json())
            .then(data => {
                  
                // Assuming the response contains an object with router details
                // Clear existing options in the router dropdown
                // var routerSelect = document.getElementById('server');
                // routerSelect.innerHTML = ''; // Remove existing options
                $('#server').empty();
                // Add an option for the router returned by the AJAX request
                if(data.router_id && data.router_name) {
                    console.log(data.router_name);
                    // var option = new Option(data.router_name, data.router_id, true, true);
                    // routerSelect.add(option);
                    
                    $('#server').append($('<option>', {
                        value: data.router_id,
                        text: data.router_name
                    }));
                    
                } else {
                    // Handle case where no router is found or customer has no specific router
                    routerSelect.add(new Option('No router available', '', true, true));
                }
                      $('#server').trigger('change.select2');
                      $('#server').val(data.router_id);
                // Refresh the Select2 dropdown to display the new option
                // $('#server').select2({theme: "bootstrap"});
            })
            .catch(error => {
                console.error('Error fetching router data:', error);
            });
    });
    // });
    // Event listener for customer selection change
    // document.getElementById('personSelect').addEventListener('change', function() {
        
    //     console.log('dfdfdf');
        
    //     var customerId = this.value; // Get the selected customer ID
 
    //     // Make an AJAX request to fetch the router for the selected customer
    //     fetch('{/literal}{$_url}{literal}fetch-router-for-customer?id_customer=' + encodeURIComponent(customerId))
    //         .then(response => response.json())
    //         .then(data => {
                
    //             // Assuming the response contains an object with router details
    //             // Clear existing options in the router dropdown
    //             var routerSelect = document.getElementById('server');
    //             routerSelect.innerHTML = ''; // Remove existing options
                
    //             // Add an option for the router returned by the AJAX request
    //             if(data.router_id && data.router_name) {
    //                 var option = new Option(data.router_name, data.router_id, true, true);
    //                 routerSelect.add(option);
    //             } else {
    //                 // Handle case where no router is found or customer has no specific router
    //                 routerSelect.add(new Option('No router available', '', true, true));
    //             }

    //             // Refresh the Select2 dropdown to display the new option
    //             $('#server').select2({theme: "bootstrap"});
    //         })
    //         .catch(error => {
    //             console.error('Error fetching router data:', error);
    //         });
    // });
    {/literal}
});
</script>



// <script>
// // Disable user interaction with the select element
// document.getElementById('server').addEventListener('mousedown', function(event) {
//     event.preventDefault();
// });
// </script>

{include file="sections/footer.tpl"}