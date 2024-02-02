{include file="sections/user-header.tpl"}
<!-- user-orderView -->

<style>
    body {
      font-family: Arial, sans-serif;
    }
    
    input[type="text"] {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      box-sizing: border-box;
      border: 1px solid #3498db;
      border-radius: 5px;
    }
    
    
    
    
    
    
    
    
       .prtm-loader-wrap {
    position: fixed;
    text-align: center;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, .85);
    z-index: 99999
}

.prtm-loader-wrap .showbox {
    position: absolute;
    top: 45%;
    bottom: 0;
    left: 0;
    right: 0
}

.prtm-loader-wrap .loader {
    position: relative;
    margin: 0 auto;
    width: 100px
}

.loader h4 {
    color: rgb(190, 192, 194);
    margin-left: -50px;
    margin-top: 25px;
    white-space: nowrap;
}

.prtm-loader-wrap .loader:before {
    content: '';
    display: block;
    padding-top: 100%
}

.prtm-loader-wrap .circular {
    -webkit-animation: rotate 2s linear infinite;
    -o-animation: rotate 2s linear infinite;
    animation: rotate 2s linear infinite;
    height: 100%;
    -webkit-transform-origin: center center;
    -ms-transform-origin: center center;
    -o-transform-origin: center center;
    transform-origin: center center;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto
}

.prtm-loader-wrap .path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    -webkit-animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
    -o-animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
    animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
    stroke-linecap: round
}

@-webkit-keyframes rotate {
    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg)
    }
}

@-o-keyframes rotate {
    100% {
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg)
    }
}

@keyframes rotate {
    100% {
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg)
    }
}

@-webkit-keyframes dash {
    0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0
    }

    50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px
    }

    100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px
    }
}

@-o-keyframes dash {
    0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0
    }

    50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px
    }

    100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px
    }
}

@keyframes dash {
    0% {
        stroke-dasharray: 1, 200;
        stroke-dashoffset: 0
    }

    50% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -35px
    }

    100% {
        stroke-dasharray: 89, 200;
        stroke-dashoffset: -124px
    }
}

@-webkit-keyframes color {

    0%,
    100% {
        stroke: #d24636
    }

    40% {
        stroke: #d24636
    }

    66% {
        stroke: #d24636
    }

    80%,
    90% {
        stroke: #d24636
    }
}

@-o-keyframes color {

    0%,
    100% {
        stroke: #d24636
    }

    40% {
        stroke: #d24636
    }

    66% {
        stroke: #d24636
    }

    80%,
    90% {
        stroke: #d24636
    }
}

@keyframes color {

    0%,
    100% {
        stroke: #d24636
    }

    40% {
        stroke: #d24636
    }

    66% {
        stroke: #d24636
    }

    80%,
    90% {
        stroke: #d24636
    }
}
    
   
    
    
    
    
    
    
    
    
    
    
  </style>
 
 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" defer integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" defer integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>






<div class="card ring-1 {if $trx['status']==1}ring-warning-500{elseif $trx['status']==2}ring-success-500{elseif $trx['status']==3}ring-danger-500{elseif $trx['status']==4}ring-danger-500{else}ring-primary-500{/if}">
  <div class="card-body">
    <div class="card-text h-full">
      <header class="border-b px-4 pt-4 pb-3 flex items-center {if $trx['status']==1}border-warning-500{elseif $trx['status']==2}border-success-500{elseif $trx['status']==3}border-danger-500{elseif $trx['status']==4}border-danger-500{else}border-primary-500{/if}">
        <iconify-icon class="text-3xl inline-block ltr:mr-2 rtl:ml-2 {if $trx['status']==1}text-warning-500{elseif $trx['status']==2}text-success-500{elseif $trx['status']==3}text-danger-500{elseif $trx['status']==4}text-danger-500{else}text-primary-500{/if}" icon="{if $trx['status']==1}ic:round-warning-amber{elseif $trx['status']==2}ph:circle-wavy-check{elseif $trx['status']==3}ph:info{elseif $trx['status']==4}material-symbols:dangerous-outline-rounded{else}fluent:settings-28-regular{/if}"></iconify-icon>
        <h3 class="card-title mb-0 text-info-500">Transaction #{$trx['id']}</h3>
      </header>
      <div class="py-3 px-5"> {if $trx['routers']!='balance'} <h5 class="card-subtitle">{$router['name']} | {$router['description']} </h5> {/if} <p class="card-text mt-3">
        <ul class="divide-y divide-slate-100 dark:divide-slate-700"> {if $trx['pg_url_payment']=='balance'} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Type')}</span>
              <span>{$trx['plan_name']}</span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Paid Date')}</span>
              <span>{date($_c['date_format'], strtotime($trx['paid_date']))} {date('H:i', strtotime($trx['paid_date']))} </span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between"> {if $trx['plan_name'] == 'Receive Balance'} <span>{Lang::T('From')}</span> {else} <span>{Lang::T('To')}</span> {/if} <span>{$trx['gateway']}</span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{$_L['Balance']}</span>
              <span>{Lang::moneyFormat($trx['price'])}</span>
            </div>
          </li> {else} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Status')}</span>
              <span>{if $trx['status']==1}{Lang::T('UNPAID')}{elseif $trx['status']==2}{Lang::T('PAID')}{elseif $trx['status']==3}{Lang::T('FAILED')}{elseif $trx['status']==4}{Lang::T('CANCELED')}{else}{Lang::T('UNKNOWN')}{/if}</span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Expired')}</span>
              <span>{date($_c['date_format'], strtotime($trx['expired_date']))} {date('H:i', strtotime($trx['expired_date']))}</span>
            </div>
          </li> {if $trx['status']==2} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Paid Date')}</span>
              <span>{date($_c['date_format'], strtotime($trx['paid_date']))} {date('H:i', strtotime($trx['paid_date']))} </span>
            </div>
          </li> {/if} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{$_L['Plan_Name']}</span>
              <span>{$plan['name_plan']}</span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{$_L['Plan_Price']}</span>
              <span>{Lang::moneyFormat($plan['price'])}</span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Type')}</span>
              <span>{$plan['type']}</span>
            </div>
          </li> {if $plan['type']!='Balance'} {if $plan['type'] eq 'Hotspot'} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Plan_Type')}</span>
              <span>{Lang::T($plan['typebp'])}</span>
            </div>
          </li> {if $plan['typebp'] eq 'Limited'} {if $plan['limit_type'] eq 'Time_Limit' or $plan['limit_type'] eq 'Both_Limit'} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Time_Limit')}</span>
              <span>{$ds['time_limit']} {$ds['time_unit']}</span>
            </div>
          </li> {/if} {if $plan['limit_type'] eq 'Data_Limit' or $plan['limit_type'] eq 'Both_Limit'} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{Lang::T('Data_Limit')}</span>
              <span>{$ds['data_limit']} {$ds['data_unit']}</span>
            </div>
          </li> {/if} {/if} {/if} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{$_L['Plan_Validity']}</span>
              <span>{$plan['validity']} {$plan['validity_unit']}  </span>
            </div>
          </li>
          <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>{$_L['Bandwidth_Plans']}</span>
              <span>{$bandw['name_bw']} <br>{$bandw['rate_down']}{$bandw['rate_down_unit']}/{$bandw['rate_up']}{$bandw['rate_up_unit']} </span>
            </div>
          </li> {/if} {/if} <br> {if $trx['status']==1} <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
            <div class="flex justify-between">
              <span>
                <a href="{$_url}order/view/{$trx['id']}/cancel" onclick="return confirm('{Lang::T('Cancel it?')}')">
                  <button class="btn btn-sm flex justify-center btn-danger"> {Lang::T('Cancel')} </button>
                </a>
              </span>
             <!-- <span>
                <a href="{$_url}order/view/{$trx['id']}" <button class="btn btn-sm flex justify-center btn-info">{Lang::T('Check for Payment')} </button>
                </a>
              </span>
              <span>
              
               <!--   <a href="{$trx['pg_url_payment']}" {if $trx['gateway']=='midtrans' } target="_blank" {/if} <button class="btn btn-sm flex justify-center btn-success ml-auto ">{Lang::T('PAY NOW')}</button>-->
                </a>
              </span>
            </div>    
          </li> 
          
          
            <center>
                  
            <div class="styled-container">
                <form method="post" action="{$trx['pg_url_payment']}"    id="loginForm">
                    
                    
                    <div id="message"></div><br>
                           <div id="data-container"></div><br>
                    
                    
                    
                    
                    <strong>Enter your mpesa phone number then click Pay now to use the Mpesa express</strong>
  <p>Phone Number</p>
  <input type="hidden" name="username" value="{$_user['username']}">
  <input type="text" name="phone" placeholder="0712345678" required>
  
  
  <button type="submit" class="btn btn-sm flex justify-center btn-success"  id="loginBtn" >
                  PAY NOW 
                </button
  
</div></form>
            </center>
           <div class="prtm-loader-wrap d-none">
        <div class="showbox">
            <div class="loader">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle> 
                    <span class="mt-3 text-primary font-weight-bold fs-18">LOADING.....</span>
                </svg>
            </div>
  </div></div>

           <p></p><br>
           {if   $_c['tillmanualshow'] =='Show'  }
            <center><div class='alert alert-info'><strong>{$_c['tillmanualtext']}<strong><h6><br>Make sure you use {$_user['phonenumber']} to pay (You can change your number on your profile)</h6></strong><br></center>
          
          {/if}
          
          
         
          
          
          
          
          {/if}
        </ul>
        </p>
      </div>
      
      </div>
    </div>
  </div>
  
  
  <script>
      
      
      
      
      
   // Check if the page has been reloaded
if (!sessionStorage.getItem('pageReloaded')) {
    // Call refreshData independently only if the page has not been reloaded
    refreshData();
}

function refreshData() {
    var redirectFlag = false; // Flag to track if redirection has occurred
    var refreshInterval; // Variable to store the interval

    function refreshDataInternal() {
        $.ajax({
            url: "{$_url}plugin/stkverify&username={$_user['username']}", // Path to your PHP script
            method: "GET",
            dataType: "json",
            success: function(data) {
                var resultCode = data.Resultcode;
                var message = data.Message;
                var status = data.Status;
                var redirect = data.Redirect;

                if (resultCode === "0" && !redirectFlag) {
                    // Check if Resultcode is 0 and refresh if true, but only if not refreshed before
                    redirectFlag = true; // Set the flag to true to prevent further refreshes
                    window.location.reload();

                    // Set a flag in sessionStorage indicating the page has been reloaded
                    sessionStorage.setItem('pageReloaded', 'true');

                    // Clear the interval to stop refreshing
                    clearInterval(refreshInterval);
                }

             
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
            }
        });
    }

    // Set interval to refresh data every 1 second and store it in the refreshInterval variable
    refreshInterval = setInterval(refreshDataInternal, 1000); // Refresh every 1 second
}

      
      
      
      
      
  </script>
  
<script>
    $(document).ready(function() {
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            $('.prtm-loader-wrap').removeClass('d-none');
            $.ajax({
                type: "POST",
                url: e.target.action,
                data: $(this).serialize(),
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },
                success: function(response) {
                    $('#message').html(response);
                    $('.prtm-loader-wrap').addClass('d-none');
                    
                    // Start the refresh code only when the submit script has returned data
                    startRefresh();
                }
            });
        });
    });

    function startRefresh() {
        var redirectFlag = false; // Flag to track if redirection has occurred
        var refreshInterval; // Variable to store the interval

        function refreshData() {
            $.ajax({
                url: "{$_url}plugin/stkverify&username={$_user['username']}", // Path to your PHP script
                method: "GET",
                dataType: "json",
                success: function(data) {
                    var resultCode = data.Resultcode;
                    var message = data.Message;
                    var message1 = data.Message1;
                    var status = data.Status;
                    var redirect = data.Redirect;

                 
                   if (resultCode === "2" && !redirectFlag) {
                        // Check if Resultcode is 0 and redirect if true, but only if not redirected before
                        redirectFlag = true; // Set the flag to true to prevent further redirections
                        
                        
                          setTimeout(function () {
       window.location.href = "index.php?_route=home";
    }, 3000); // 3000 milliseconds (3 seconds)

                        // Clear the interval to stop refreshing
                        clearInterval(refreshInterval);
                    }

                    if (resultCode === "0" && !redirectFlag) {
                        // Check if Resultcode is 0 and redirect if true, but only if not redirected before
                        redirectFlag = true; // Set the flag to true to prevent further redirections
                        
                        
                          setTimeout(function () {
        window.location.reload();
    }, 3000); // 3000 milliseconds (3 seconds)

                        // Clear the interval to stop refreshing
                        clearInterval(refreshInterval);
                    }

                    $('#data-container').html("<div class='alert alert-" + status + "'>"
                        + " <i class='fa fa-ban-circle'></i><strong>" + message1 + " </br></strong>" +  message + "  </div>");
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error: " + errorThrown);
                }
            });
        }


        

        refreshData();

        // Set interval to refresh data every 1 second and store it in the refreshInterval variable
        refreshInterval = setInterval(refreshData, 1000); // Refresh every 1 second
    }
</script>
  
  
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>  
  
  
  
  
  
  </div> {include file="sections/user-footer.tpl"}
