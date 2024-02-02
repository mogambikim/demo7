{include file="sections/user-header.tpl"}
<!-- user-change-password -->

<div class="grid xl:grid-cols-2 grid-cols-1 gap-6">
  <div class="card xl:col-span-2">
    <div class="card-body flex flex-col p-6">
      <header class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5 -mx-6 px-6">
        <div class="flex-1">
          <div class="card-title text-slate-900 dark:text-white">{$_L['Change_Password']}</div>
        </div>
      </header>
      <div class="card-text h-full ">
        <form class="space-y-4" method="post" role="form" action="{$_url}accounts/change-password-post">
          <input type="hidden" name="id" value="{$d['id']}">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Current_Password']}</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['New_Password']}</label>
              <input type="password" class="form-control" id="npass" name="npass">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Confirm_New_Password']}</label>
              <input type="password" class="form-control" id="cnpass" name="cnpass">
            </div>
          </div>
          <button type="submit" class="btn inline-flex justify-center btn-primary">{$_L['Save']}</button>&nbsp; <a class="btn inline-flex justify-center btn-dark" href="{$_url}home">{$_L['Cancel']}</a>
        </form>
      </div>
    </div>
  </div>
</div>

{include file="sections/user-footer.tpl"}
