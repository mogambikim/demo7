{include file="sections/user-header.tpl"}
<!-- user-orderPlan --> {if $_c['enable_balance'] == 'yes'} <div class=" space-y-5">
  <div class="card">
    <header class="card-header">
      <div class="card-title">{Lang::T('Balance Plans')}</div>
    </header>
    <div class="card-body p-6">
      <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5"> {foreach $plans_balance as $plan} <div class="price-table bg-opacity-[0.16] dark:bg-opacity-[0.36] rounded-[6px] p-6 text-slate-900 dark:text-white relative
                overflow-hidden z-[1] bg-info-500">
          <div class="overlay absolute right-0 top-0 w-full h-full z-[-1]">
            <img src="{$_theme}/assets/images/all-img/big-shap2.png" alt="" class="ml-auto block">
          </div>
          <div class="text-sm font-medium bg-slate-900 dark:bg-slate-900 text-white py-2 text-center absolute ltr:-right-[43px]
                    rtl:-left-[43px] top-6 px-10 transform ltr:rotate-[45deg] rtl:-rotate-45"> {Lang::T('Balance Plans')} </div>
          <header class="mb-6">
            <h4 class="text-xl mb-5">{$plan['name_plan']}</h4>
            <div class="space-x-4 relative flex items-center mb-5 rtl:space-x-reverse">
              <span class="text-[32px] leading-10 font-medium">{Lang::moneyFormat($plan['price'])}</span>
              <span class="text-xs text-warning-500 font-medium px-3 py-1 rounded-full inline-block bg-white uppercase h-auto">Save 20%</span>
            </div>
            <p class="text-slate-500 dark:text-slate-300 text-sm"></p>
          </header>
          <div class="price-body space-y-8">
            <p class="text-sm leading-5 text-slate-600 dark:text-slate-300"></p>
            <div>
              <a href="{$_url}order/buy/0/{$plan['id']}" onclick="return confirm('{Lang::T('Buy Balance?')}')">
                <button class="btn-outline-dark dark:border-slate-400 w-full btn"> Order Now</button>
              </a>
            </div>
          </div>
        </div> {/foreach} </div>
    </div>
  </div>
</div>
{/if}
{include file="sections/user-footer.tpl"}
