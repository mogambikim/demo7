{include file="sections/user-header.tpl"}
<!-- BEGIN: Company Table -->
<div class="space-y-5">
  <!-- BEGIN: BreadCrumb -->
  <div class="flex justify-between flex-wrap items-center mb-6">
    <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4 mb-1 sm:mb-0">{Lang::T('Your Account Information')}</h4>
    <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center rtl:space-x-reverse"> {if $_c['disable_voucher'] != 'yes'} <button class="btn inline-flex justify-center btn-outline-success rounded-[25px] btn-sm m-1 active" data-bs-toggle="modal" data-bs-target="#voucher_modal">
        <span class="flex items-center">
          <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:credit-card"></iconify-icon>
          <span>Redeem Voucher</span>
        </span>
      </button> {/if} {if $_c['enable_balance'] == 'yes' && $_c['allow_balance_transfer'] == 'yes'} <button class="btn inline-flex justify-center btn-outline-warning rounded-[25px] btn-sm m-1 active" data-bs-toggle="modal" data-bs-target="#transfer_modal">
        <span class="flex items-center">
          <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="basil:telegram-outline"></iconify-icon>
          <span>{Lang::T("Transfer Balance")}</span>
        </span>
      </button> {/if} {if $_c['enable_balance'] == 'yes' && $_c['allow_balance_transfer'] == 'yes'} <button class="btn inline-flex justify-center btn-outline-primary rounded-[25px] btn-sm m-1 active" data-bs-toggle="modal" data-bs-target="#plan_modal">
        <span class="flex items-center">
          <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons-outline:user-plus"></iconify-icon>
          <span>{Lang::T("Recharge a friend")}</span>
        </span>
      </button> {/if}
    </div>
  </div>
</div>
<div class="space-y-5">
<!--  <div class="py-[18px] px-6 font-normal font-Inter text-sm rounded-md bg-danger-500 text-white dark:bg-danger-500 dark:text-slate-300">
    <div class="flex items-start space-x-3 rtl:space-x-reverse">
      <div class="flex-1"> An error occured while connection to the network. </div>
    </div>
  </div> -->
  <div class="grid grid-cols-12 gap-5 mb-5">
    <div class="2xl:col-span-3 lg:col-span-4 col-span-12">
      <div class="bg-no-repeat bg-cover bg-center p-5 rounded-[6px] relative" style="background-image: url({$_theme}/assets/images/all-img/widget-bg-2.png)">
        <div class="max-w-[180px]">
          <h4 class="text-xl font-medium text-white mb-2">
            <span class="block font-normal" id="greeting"></span>
            <span class="block" id="fullnameSpan">{$_user['fullname']}</span>
          </h4>
          <p class="text-sm text-white font-normal"> Welcome to {$_c['CompanyName']} </p>
        </div>
      </div>
    </div>
    <div class="2xl:col-span-9 lg:col-span-8 col-span-12">
      <div class="grid md:grid-cols-3 grid-cols-1 gap-4">
        <!-- BEGIN: Group Chart -->
        <div class="bg-no-repeat bg-cover bg-center px-5 py-8 rounded-[6px] relative flex items-center" style="background-image: url({$_theme}/assets/images/all-img/widget-bg-6.png)">
          <div class="flex-1">
            <div class="max-w-[180px]">
              <h4 class="text-2xl font-medium text-white mb-2">
                <span class="block text-sm">Current Balance</span>
                <span class="block">{if $_c['enable_balance'] == 'yes'} {Lang::moneyFormat($_user['balance'])} {else} N/A {/if}</span>
              </h4>
            </div>
          </div> {if $_c['enable_balance'] == 'yes'} <div class="flex-none"> {if $_user['auto_renewal'] == 1} <button class="btn-success bg-white btn-sm btn">
              <a class="label label-success pull-right" href="{$_url}home&renewal=0" onclick="return confirm('{Lang::T('Disable auto renewal?')}')">{Lang::T('Auto Renewal On')}</a>
            </button> {else} <button class="btn-danger bg-white btn-sm btn">
              <a class="label label-danger pull-right" href="{$_url}home&renewal=1" onclick="return confirm('{Lang::T('Enable auto renewal?')}')">{Lang::T('Auto Renewal Off')}</a>
            </button> {/if} </div> {else} <div class="flex-none">
            <button class="btn inline-flex justify-center btn-sm btn-danger cursor-not-allowed light" disabled="disabled">{Lang::T('Auto Renewal Off')}</button>
          </div> {/if}
        </div>
         {if $_bills}
        <div class="bg-no-repeat bg-cover bg-center px-5 py-8 rounded-[6px] relative flex items-center" style="background-image: url({$_theme}/assets/images/all-img/widget-bg-6.png)">
          <div class="flex-1">
            <div class="max-w-[180px]">
              <h4 class="text-2xl font-medium text-white mb-2">
                <span class="block text-sm">Account Status</span>
                <span class="block">
                  {assign var=isActiveFlag value=false}
                  {foreach $_bills as $_bill}
                    {if $_bill.status eq 'on'}
                   {assign var=isActiveFlag value=true}
                   {/if}
                 {/foreach}
                 {if $isActiveFlag}
               <font color="green">Active</font>
                 {else}
               <font color="red">Inactive</font>
              {/if}</span>
              </h4>
            </div>
          </div>
          <div class="flex-none">
            <button onClick="window.location.reload();" class="btn-primary bg-white btn-sm btn">Refresh</button>
          </div>
        </div>

        <div class="bg-no-repeat bg-cover bg-center px-5 py-8 rounded-[6px] relative flex items-center" style="background-image: url({$_theme}/assets/images/all-img/widget-bg-6.png)">
          <div class="flex-1">
            <div class="max-w-[180px]">
             <h4 class="text-sm font-medium text-white mb-2">
    <span class="block text-sm">
        {if $_bills}
            {foreach $_bills as $_bill}
                {if $_bill['status'] == 'on'}
                    {$_bill['namebp']} &nbsp;
                    <span class="badge bg-primary-500 text-small text-white capitalize">
                        <a class="flex-none" href="{$_url}home&deactivate={$_bill['id']}"
                            onclick="return confirm('{Lang::T('Deactivate')}?')">{Lang::T('Deactivate')}</a>
                    </span>
                    <br>
                {/if}
            {/foreach}
        {else}
            <span class="block text-sm">{Lang::T('Plan Name')}</span><span class="block">{Lang::T('Buy Package')}</span>
        {/if}
    </span>
</h4>

            </div>
          </div>
        </div>
        {/if}
        <!-- END: Group Chart -->
      </div>
    </div>
  </div>
  <div class=" space-y-5">
    <div class="grid grid-cols-12 gap-5">
      <div class="lg:col-span-8 col-span-12 space-y-5">
        {if $unpaid }
        <div class="card p-6">
          <div class="grid xl:grid-cols-4 lg:grid-cols-2 col-span-1 gap-3"> <div class="lg:col-span-4 col-span-12 space-y-5">
              <div class="card">
                <header class="card-header">
                  <h4 class="card-title"> {Lang::T('Unpaid Order')} </h4>
                  <div>
                    <!-- BEGIN: Card Dropdown -->
                    <div class="relative">
                      <div class="dropdown relative"></div>
                    </div>
                    <!-- END: Card Droopdown -->
                  </div>
                </header>
                <div class="card-body p-6">
                  <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                    <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                      <div class="flex justify-between">
                        <span>{Lang::T('expired')}</span>
                        <span>{Lang::dateTimeFormat($unpaid['expired_date'])}</span>
                      </div>
                    </li>
                    <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                      <div class="flex justify-between">
                        <span>{Lang::T('Plan Name')}</span>
                        <span>{$unpaid['plan_name']}</span>
                      </div>
                    </li>
                    <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                      <div class="flex justify-between">
                        <span>{Lang::T('Plan Price')}</span>
                        <span>{$unpaid['price']}</span>
                      </div>
                    </li>
                    <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                      <div class="flex justify-between">
                        <span>{Lang::T('Routers')}</span>
                        <span>{$unpaid['routers']}</span>
                      </div>
                    </li>
                    <br>
                    <li class="first:text-xs text-sm first:text-slate-600 text-slate-600 dark:text-slate-300 py-2 first:uppercase">
                      <div class="flex justify-between">
                        <span>
                          <a href="{$_url}order/view/{$unpaid['id']}/cancel" onclick="return confirm('{Lang::T('Cancel the order?')}')">
                            <button class="btn btn-sm flex justify-center btn-danger"> {Lang::T('Cancel')} </button>
                          </a>
                        </span>
                        <span>
                          <a href="{$_url}order/view/{$unpaid['id']}">
                            <button class="btn btn-sm flex justify-center btn-success ml-auto ">{Lang::T('PAY NOW')}</button>
                          </a>
                        </span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div> </div>
        </div>
        {/if}
        <div class="card">
          <header class=" card-header">
            <h4 class="card-title">Data Usage | Coming Soon  </h4>
          </header>
          <div class="card-body px-6 pb-6">
            <div id="areaSpaline"></div>
          </div>
        </div>
      </div>
      <div class="lg:col-span-4 col-span-12 space-y-5">
        <div class="lg:col-span-4 col-span-12 space-y-5">
          <div class="card">
            <header class="card-header">
              <h4 class="card-title"> {Lang::T('Announcement')} </h4>
              <div></div>
            </header>
            <div class="card-body p-6">
              <p class="text-sm font-Inter text-slate-600 dark:text-slate-300">{include file="$_path/../pages/Announcement.html"}</p>
            </div>
          </div>
          <div class="card">
            <header class="card-header">
              <h4 class="card-title"> Account Overview </h4>
              <div>
                <!-- BEGIN: Card Dropdown -->
                <div class="relative">
                  <div class="dropdown relative"></div>
                </div>
                <!-- END: Card Droopdown -->
              </div>
            </header>
            <div class="card-body p-6">
              <div class="legend-ring3">
                <div id="">
                  <div class="card-body px-6 pb-6">
                    <div class="overflow-x-auto ">
                      <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                          <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                            <tbody class="bg-white dark:bg-slate-800 ">
                              <tr>
                                <td class="table-td "> {Lang::T('Username')}&nbsp;: <br> {$_user['username']} </td>
                                <td class="table-td "> {Lang::T('Password')}&nbsp;: <br>
                                  <input type="password" value="{$_user['password']}" style="width:100%; border: 0px; color: red;" onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'" onclick="this.select()">
                                </td>
                                <td class="table-td  "> {Lang::T('Balance')}&nbsp;: <br> {if $_c['enable_balance'] == 'yes'} {Lang::moneyFormat($_user['balance'])} {else} N/A {/if} </td>
                              </tr>
                               {if $_bills}
                              <tr>
                                <td class="table-td ">{Lang::T('Plan Name')}&nbsp;: <br>{foreach $_bills as $_bill}
                                    {$_bill['namebp']} &nbsp;
                                    {if $_bill['status'] == 'on'}
                                            <a class="flex-none" href="{$_url}home&deactivate={$_bill['id']}"
                                                onclick="return confirm('{Lang::T('Deactivate')}?')"><font color="red">{Lang::T('Deactivate')}</font></a>
                                        </span>
                                    {else}

                                            <a class="flex-none" href="{$_url}order/package"><font color="red">{Lang::T('expired')}</font></a>

                                    {/if}<br>
                                {/foreach}</td>
                                <td class="table-td ">{Lang::T('Created On')} <br> {if $_bill['time'] ne ''}{Lang::dateAndTimeFormat($_bill['recharged_on'],$_bill['recharged_time'])} {/if} </td>
                                <td class="table-td  "> {Lang::T('Expires On')}&nbsp;: <br> {if $_bill['time'] ne ''}{Lang::dateAndTimeFormat($_bill['expiration'],$_bill['time'])}{/if} </td>
                              </tr>
                              <tr>
                                <td class="table-td "> {Lang::T('Current IP')}&nbsp;: <br> {if $nux_ip} <br> {$nux_ip} {else} N/A {/if} </td>
                                <td class="table-td "> {Lang::T('Current MAC')}&nbsp;: <br> {if $nux_mac} <br> {$nux_mac} {else} N/A {/if} </td>
                                <td id="login_status_{$_bill['id']}" class="table-td ">{Lang::T('Login Status')} <br> {if $_bill['type'] == 'Hotspot' && $_bill['status'] == 'on'} <img src="{$_theme}/assets/images/loading.gif">
                                </td> {/if}
                              </tr>
                              {/if}
                              <tr>
                                <td class="table-td "> {Lang::T('Service Type')}&nbsp;: <br> {if $_user.service_type == 'Hotspot'}
                                     Hotspot
                                  {elseif $_user.service_type == 'PPPoE'}
                                     PPPoE
                                      {elseif $_user.service_type == 'Static'}
                                     Static
                                    {elseif $_user.service_type == 'Others' || $_user.service_type == null}
                                  Others
                                 {/if}
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="transfer_modal" tabindex="-1" aria-labelledby="transfer_modal" aria-hidden="true">
  <div class="modal-dialog relative w-auto pointer-events-none">
    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding
                    rounded-md outline-none text-current">
      <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
          <h3 class="text-xl font-medium text-white dark:text-white capitalize"> {Lang::T("Transfer Balance")}&nbsp;| {Lang::moneyFormat($_user['balance'])} </h3>
          <button type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                                dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                        11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div>
          <form method="post" onsubmit="return askConfirm()" role="form" action="{$_url}home">
            <div class="p-6 space-y-6">
              <div class="input-group">
                <label for="username" class="text-sm font-Inter font-normal text-slate-900 block"></label>
                <div class="relative">
                  <input type="text" id="username" name="username" required autocomplete="on" placeholder="input the receiver username" class="text-sm font-Inter font-normal text-slate-600 block w-full py-3 px-4 focus:!outline-none focus:!ring-0 border !border-slate-400 rounded-md mt-2">
                </div>
                <div class="input-group">
                  <label for="balance" class="text-sm font-Inter font-normal text-slate-900 block"></label>
                  <div class="relative">
                    <input type="number" id="balance" name="balance" required placeholder="input the required amount" autocomplete="off" class="text-sm font-Inter font-normal text-slate-600 block w-full py-3 px-4 pr-9 focus:!outline-none  focus:!ring-0 border !border-slate-400 rounded-md mt-2">
                  </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                  <button type="button" data-bs-dismiss="modal" class="btn btn-outline-primary rounded-[25px]">Cancel</button>
                  <button class="btn btn-outline-success rounded-[25px]" id="sendBtn" type="submit" name="send" onclick="return confirm('{Lang::T(" Are You Sure?")}')" value="balance">Transfer</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="voucher_modal" tabindex="-1" aria-labelledby="voucher_modal" aria-hidden="true">
  <div class="modal-dialog relative w-auto pointer-events-none">
    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding
                    rounded-md outline-none text-current">
      <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
          <h3 class="text-xl font-medium text-white dark:text-white capitalize"> {Lang::T('Voucher Activation')} </h3>
          <button type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                                dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                        11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div>
          <form method="post" role="form" class="" action="{$_url}voucher/activation-post">
            <div class="p-6 space-y-6">
              <div class="input-group">
                <label for="voucher" class="text-sm font-Inter font-normal text-slate-900 block">
                  <h6>{Lang::T('Code Voucher')}</h6>
                </label>
                <div class="relative">
                  <input type="text" id="code" name="code" required placeholder="{Lang::T('Enter voucher code here')}" class="text-sm font-Inter font-normal text-slate-600 block w-full py-3 px-4 focus:!outline-none focus:!ring-0 border
                              !border-slate-400 rounded-md mt-2">
                </div>
              </div>
              <!-- Modal footer -->
              <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                <button type="button" data-bs-dismiss="modal" class="btn  btn-outline-primary rounded-[25px]">Cancel</button>
                <button type="submit" class="btn btn-outline-success rounded-[25px]">{Lang::T('Recharge')}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto" id="plan_modal" tabindex="-1" aria-labelledby="plan_modal" aria-hidden="true">
  <div class="modal-dialog relative w-auto pointer-events-none">
    <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding
                  rounded-md outline-none text-current">
      <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-black-500">
          <h3 class="text-xl font-medium text-white dark:text-white capitalize"> {Lang::T("Recharge a friend")} </h3>
          <button type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
                              dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
            <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
                                      11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <div>
          <form method="post" role="form" action="{$_url}home">
            <div class="p-6 space-y-6">
              <div class="input-group">
                <label for="voucher" class="text-sm font-Inter font-normal text-slate-900 block">
                  <h6>{Lang::T('Username')}</h6>
                </label>
                <div class="relative">
                  <input type="text" id="username" name="username" required placeholder="input the receiver username" class="text-sm font-Inter font-normal text-slate-600 block w-full py-3 px-4 focus:!outline-none focus:!ring-0 border
                          !border-slate-400 rounded-md mt-2">
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                  <button type="button" data-bs-dismiss="modal" class="btn btn-outline-primary rounded-[25px]">Cancel</button>
                  <button class="btn btn-outline-success rounded-[25px]" id="sendBtn" type="submit" name="send" onclick="return confirm('{Lang::T(" Are You Sure?")}')" value="plan">{Lang::T('Recharge')}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
{foreach $_bills as $_bill}
    {if $_bill['type'] == 'Hotspot' && $_bill['status'] == 'on'}
        <script>
            setTimeout(() => {
                $.ajax({
                    url: "index.php?_route=autoload_user/isLogin/{$_bill['id']}",
                    cache: false,
                    success: function(msg) {
                        $("#login_status_{$_bill['id']}").html(msg);
                    }
                });
            }, 2000);
        </script>
    {/if}
{/foreach}
<script>
    var greeting;
    var time = new Date().getHours();
    if (time < 12) {
      greeting = "Good Morning,";
    } else if (time < 18) {
      greeting = "Good Afternoon,";
    } else if (time < 24) {
      greeting = "Good Evening,"
    } else {
      greeting = "Welcome";
    }
    document.getElementById("greeting").innerHTML = greeting;
  </script>
  <script>
    function askConfirm() {
      if (confirm('{Lang::T('
          Send your balance ? ')}')) {
        setTimeout(() => {
          document.getElementById('sendBtn').setAttribute('disabled', '');
        }, 1000);
        return true;
      }
      return false;
    }
  </script>
  <script>
    var fullnameSpan = document.getElementById("fullnameSpan");
    var maxlength = 12;
    var content = fullnameSpan.innerHTML;
    if (content.length > maxlength) {
        fullnameSpan.innerHTML = content.substring(0, maxlength) + "...";
    }
</script>
 {include file="sections/user-footer.tpl"}

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Set an interval to check for the button every 2 seconds
    var checkInterval = setInterval(function() {
        var button = $('.btn-danger').filter(function() {
            return $(this).text() === 'Not Online, Login now?';
        });
        if (button.length) {
            button[0].click(); // Click the button
            clearInterval(checkInterval); // Stop checking after the button is clicked
        }
    }, 200); // Check every 2 seconds
});
</script>

