{include file="sections/user-header.tpl"}
<!-- user-activation -->
<div class="grid grid-cols-12 gap-6">
  <div class="lg:col-span-4 col-span-12">
    <div class="card h-full">
      <div class="card">
        <div class="card-body flex flex-col p-6">
          <header class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5 -mx-6 px-6">
            <div class="flex-1">
              <div class="card-title text-slate-900 dark:text-white">{$_L['Voucher_Activation']}</div>
            </div>
          </header>
          <div class="card-text h-full">
            <form class="space-y-4" method="post" role="form" action="{$_url}voucher/activation-post">
              <div class="input-area">
                <label for="" class="form-label">{$_L['Code_Voucher']}</label>
                <input id="code" name="code" type="text" class="form-control" required placeholder="{$_L['Enter_Voucher_Code']}">
              </div>
              <div class="flex items-center justify-end p-6 space-x-2 border-t border-slate-200 rounded-b dark:border-slate-600">
                <a href="{$_url}home">
                  <button type="button" class="btn  btn-outline-primary rounded-[25px]">{$_L['Cancel']}</button>
                </a>
                <button type="submit" class="btn btn-outline-success rounded-[25px]">{$_L['Recharge']}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="lg:col-span-8 col-span-12">
    <div class="card ">
      <header class="card-header">
        <h4 class="card-title">{$_L['Order_Voucher']} </h4>
      </header>
      <div class="card-body">
        <div class="card-body p-6">
          <p class="text-base text-slate-600 dark:text-slate-400 leading-6"> {include file="$_path/../pages/Order_Voucher.html"} </p>
        </div>
      </div>
    </div>
  </div>
</div> {include file="sections/user-footer.tpl"}
