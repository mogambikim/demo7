{include file="sections/user-header.tpl"}
<!-- user-orderPlan -->
{if $_c['radius_enable']}
  <!-- Check if user's service type is PPPoE and if there are PPPoE plans available -->
  {if $_user['service_type'] == 'PPPoE' && Lang::arrayCount($radius_pppoe) > 0}
  <div class="space-y-5">
    <div class="card">
      <header class="card-header">
        <div class="card-title">
          {if $_c['radius_plan'] == ''}Radius Plan{else}{$_c['radius_plan']}{/if} | {if $_c['pppoe_plan'] == ''}PPPoE Plan{else}{$_c['pppoe_plan']}{/if}
        </div>
      </header>
      <div class="card-body p-6">
        <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5">
          {foreach $radius_pppoe as $plan}
          <!-- PPPoE Plan Display Logic Here -->
          {/foreach}
        </div>
      </div>
    </div>
  </div>









    <div class="card-body p-6">


<div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5"> {foreach $radius_hotspot as $plan} <div class="price-table bg-opacity-[0.16] dark:bg-opacity-[0.36] rounded-[6px] p-6 text-slate-900 dark:text-white relative
                  overflow-hidden z-[1] bg-warning-500">
          <div class="overlay absolute right-0 top-0 w-full h-full z-[-1]">
            <img src="{$_theme}/assets/images/all-img/big-shap2.png" alt="" class="ml-auto block">
          </div>




          <div class="text-sm font-medium bg-slate-900 dark:bg-slate-900 text-white py-2 text-center absolute ltr:-right-[43px]
                      rtl:-left-[43px] top-6 px-10 transform ltr:rotate-[45deg] rtl:-rotate-45"> {if $_c['radius_plan']==''}Radius Plan{else}{$_c['radius_plan']}{/if} </div>
          <header class="mb-6">
            <h4 class="text-xl mb-5">{$plan['name_plan']}</h4>
            <div class="space-x-4 relative flex items-center mb-5 rtl:space-x-reverse">
              <span class="text-[32px] leading-10 font-medium"> {Lang::moneyFormat($plan['price'])} </span>
              <span class="text-xs text-warning-500 font-medium px-3 py-1 rounded-full inline-block bg-white uppercase h-auto">Save 20%</span>
            </div>
            <p class="text-slate-500 dark:text-slate-300 text-sm"> {Lang::T('Validity')} : {$plan['validity']} {$plan['validity_unit']} </p>
          </header>
          <div class="price-body space-y-8">
            <p class="text-sm leading-5 text-slate-600 dark:text-slate-300">
            <table class="table table-bordered table-striped">
              <tbody>
                <tr>
                  <td>Service Type:&nbsp; </td>
                  <td>{$plan['type']}</td>
                </tr>
                <tr>
                  <td>Include:&nbsp; </td>
                  <td> 24/7 Support</td>
                </tr>
                <tr>
                  <td>Include:&nbsp; </td>
                  <td>Speed Burst</td>
                </tr>
              </tbody>
            </table>
            </p>
            <div>
              <a href="{$_url}order/buy/radius/{$plan['id']}" onclick="return confirm('{Lang::T('Buy this? your active package will be overwrite')}')">
                <button class="btn-outline-dark dark:border-slate-400 w-full btn"> Order Now</button>
              </a>
            </div> {if $_c['enable_balance'] == 'yes' && $_user['balance']>=$plan['price']} <div>
              <a href="{$_url}order/pay/radius/{$plan['id']}" onclick="return confirm('{Lang::T('Pay this with Balance? your active package will be overwrite')}')">
                <button class="btn-outline-dark dark:border-slate-400 w-full btn"> {Lang::T('Pay With Balance')}</button>
              </a>
            </div> {/if}
          </div>
        </div> {/foreach} </div>
    </div>
  </div>
</div> {/if}{/if}

<br> {foreach $routers as $router} <div class=" space-y-5">
  <div class="card">
    <header class="card-header">
      <div class="card-title">{$router['name']} | {if $router['description'] != ''} {$router['description']} {/if} {$_user['service_type'] } </div>
    </header>
    <div class="card-body p-6"> {if $_user['service_type'] == 'Hotspot'} <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5"> {foreach $plans_hotspot as $plan} {if $router['name'] eq $plan['routers']} <div class="price-table bg-opacity-[0.16] dark:bg-opacity-[0.36] rounded-[6px] p-6 text-slate-900 dark:text-white relative
                  overflow-hidden z-[1] bg-primary-500">
          <div class="overlay absolute right-0 top-0 w-full h-full z-[-1]">
            <img src="{$_theme}/assets/images/all-img/big-shap3.png" alt="" class="ml-auto block">
          </div>
          <div class="text-sm font-medium bg-slate-900 dark:bg-slate-900 text-white py-2 text-center absolute ltr:-right-[43px]
                      rtl:-left-[43px] top-6 px-10 transform ltr:rotate-[45deg] rtl:-rotate-45"> {Lang::T('Hotspot Plan')} </div>
          <header class="mb-6">
            <h4 class="text-xl mb-5">{$plan['name_plan']}</h4>
            <div class="space-x-4 relative flex items-center mb-5 rtl:space-x-reverse">
              <span class="text-[32px] leading-10 font-medium"> {Lang::moneyFormat($plan['price'])} </span>
              <span class="text-xs text-warning-500 font-medium px-3 py-1 rounded-full inline-block bg-white uppercase h-auto">Save 20%</span>
            </div>
            <p class="text-slate-500 dark:text-slate-300 text-sm"> {Lang::T('Validity')} : {$plan['validity']} {$plan['validity_unit']} </p>
          </header>
          <div class="price-body space-y-8">
            <p class="text-sm leading-5 text-slate-600 dark:text-slate-300">
            <table class="table table-bordered table-striped">
              <tbody>
                <tr>
                  <td>Service Type:&nbsp; </td>
                  <td>{$plan['type']}</td>
                </tr>
                <tr>
                  <td>Include:&nbsp; </td>
                  <td> 24/7 Support</td>
                </tr>
                <tr>
                  <td>Include:&nbsp; </td>
                  <td>Speed Burst</td>
                </tr>
              </tbody>
            </table>
            </p>
            <div>
              <a href="{$_url}order/buy/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Buy this? your active package will be overwrite')}')">
                <button class="btn-outline-dark dark:border-slate-400 w-full btn"> Order Now</button>
              </a>
            </div> {if $_c['enable_balance'] == 'yes' && $_user['balance']>=$plan['price']} <div>
              <a href="{$_url}order/pay/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Pay this with Balance? your active package will be overwrite')}')">
                <button class="btn-outline-dark dark:border-slate-400 w-full btn"> {Lang::T('Pay With Balance')}</button>
              </a>
            </div> {/if}
          </div>
        </div> {/if} {/foreach} </div>
    </div>
  </div> {/if} 
  
  <!--static start-->
  
    {if $_user['service_type'] == 'Static'}
    <!-- Add your Static service type logic here -->
    <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5"> 
    {foreach $plans_static as $plan}
  
                <div class="col col-md-4">
                    <div class="box box- box-primary">
    <div class="price-table rounded-[6px] shadow-base dark:bg-slate-800 p-6 text-slate-900 dark:text-white relative
                      overflow-hidden z-[1] bg-slate-900">
      <div class="overlay absolute right-0 top-0 w-full h-full z-[-1]">
        <img src="" alt="" class="ml-auto block">
      </div>
      <div class="text-sm font-medium bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-300 py-2 text-center absolute
                          ltr:-right-[43px] rtl:-left-[43px] top-6 px-10 transform ltr:rotate-[45deg] rtl:-rotate-45"> {Lang::T('Static Plan')} </div>
      <header class="mb-6">
        <h4 class="text-xl mb-5  text-slate-100  "> {$plan['name_plan']} </h4>
        <div class="space-x-4 relative flex items-center mb-5 rtl:space-x-reverse  text-slate-100  ">
          <span class="text-[32px] leading-10 font-medium"> {Lang::moneyFormat($plan['price'])} </span>
          <span class="text-xs bg-warning-50 text-warning-500 font-medium px-2 py-1 rounded-full inline-block dark:bg-slate-700 uppercase
                            h-auto"> Save 20%</span>
        </div>
        <p class="text-sm leading-5  text-slate-100"> {$plan['validity']} {$plan['validity_unit']} </p>
      </header>
      <div class="price-body space-y-8">
        <table class=" text-sm leading-5  text-slate-100">
          <tbody>
            <tr>
              <td>Service Type:&nbsp; </td>
              <td>{$plan['type']}</td>
            </tr>
            <tr>
              <td>Include:&nbsp; </td>
              <td> 24/7 Support</td>
            </tr>
            <tr>
              <td>Include: &nbsp; </td>
              <td>Speed Burst</td>
            </tr>
          </tbody>
        </table>
        <div>
          <a href="{$_url}order/buy/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Buy this? your active package will be overwrite')}')">
            <button class="w-full btn bt text-slate-100 border-slate-300 border "> Order Now </button>
          </a>
        </div> {if $_c['enable_balance'] == 'yes' && $_user['balance']>=$plan['price']} <div>
          <a href="{$_url}order/pay/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Pay this with Balance? your active package will be overwrite')}')">
            <button class="w-full btn  text-slate-100 border-slate-300 border "> {Lang::T('Pay With Balance')} </button>
          </a>
        </div> {/if}
      </div>
    </div>  </div> </div>

    
    
    {/foreach}
  {/if}
  
      
  <!--static end -->
  
  
  
 {if $_user['service_type'] == 'PPPoE' && count($plans_pppoe) > 0}
 <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5"> {foreach $plans_pppoe as $plan} {if $router['name'] eq $plan['routers']} <div class="price-table rounded-[6px] shadow-base dark:bg-slate-800 p-6 text-slate-900 dark:text-white relative
                      overflow-hidden z-[1] bg-slate-900">
      <div class="overlay absolute right-0 top-0 w-full h-full z-[-1]">
        <img src="" alt="" class="ml-auto block">
      </div>
      <div class="text-sm font-medium bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-300 py-2 text-center absolute
                          ltr:-right-[43px] rtl:-left-[43px] top-6 px-10 transform ltr:rotate-[45deg] rtl:-rotate-45"> {Lang::T('PPPOE Plan')} </div>
      <header class="mb-6">
        <h4 class="text-xl mb-5  text-slate-100  "> {$plan['name_plan']} </h4>
        <div class="space-x-4 relative flex items-center mb-5 rtl:space-x-reverse  text-slate-100  ">
          <span class="text-[32px] leading-10 font-medium"> {Lang::moneyFormat($plan['price'])} </span>
          <span class="text-xs bg-warning-50 text-warning-500 font-medium px-2 py-1 rounded-full inline-block dark:bg-slate-700 uppercase
                            h-auto"> Save 20%</span>
        </div>
        <p class="text-sm leading-5  text-slate-100"> {$plan['validity']} {$plan['validity_unit']} </p>
      </header>
      <div class="price-body space-y-8">
        <table class=" text-sm leading-5  text-slate-100">
          <tbody>
            <tr>
              <td>Service Type:&nbsp; </td>
              <td>{$plan['type']}</td>
            </tr>
            <tr>
              <td>Include:&nbsp; </td>
              <td> 24/7 Support</td>
            </tr>
            <tr>
              <td>Include: &nbsp; </td>
              <td>Speed Burst</td>
            </tr>
          </tbody>
        </table>
        <div>
          <a href="{$_url}order/buy/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Buy this? your active package will be overwrite')}')">
            <button class="w-full btn bt text-slate-100 border-slate-300 border "> Order Now </button>
          </a>
        </div> {if $_c['enable_balance'] == 'yes' && $_user['balance']>=$plan['price']} <div>
          <a href="{$_url}order/pay/{$router['id']}/{$plan['id']}" onclick="return confirm('{Lang::T('Pay this with Balance? your active package will be overwrite')}')">
            <button class="w-full btn  text-slate-100 border-slate-300 border "> {Lang::T('Pay With Balance')} </button>
          </a>
        </div> {/if}
      </div>
    </div>  {/if} {/foreach}</div>
</div><br>  {/if} {/foreach}

{include file="sections/user-footer.tpl"}
