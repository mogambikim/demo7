{include file="sections/user-header.tpl"}
<!-- user-profile -->
<div class="grid xl:grid-cols-2 grid-cols-1 gap-6">
  <div class="card xl:col-span-2">
    <div class="card-body flex flex-col p-6">
      <header class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5 -mx-6 px-6">
        <div class="flex-1">
          <div class="card-title text-slate-900 dark:text-white">{$_L['Edit_User']}</div>
        </div>
      </header>
      <div class="card-text h-full ">
        <form class="space-y-4" method="post" role="form" action="{$_url}accounts/edit-profile-post">
          <input type="hidden" name="id" value="{$d['id']}">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Username']}</label>
              <input type="text" class="form-control" name="username" id="username" readonly value="{$d['username']}" placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']}{/if} {$_L['Phone_Number']}">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Full_Name']}</label>
              <input type="" class="form-control" id="fullname" name="fullname" value="{$d['fullname']}">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Phone_Number']}</label>
              <input type="text" class="form-control" name="phonenumber" id="phonenumber" value="{$d['phonenumber']}" placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']}{/if} {$_L['Phone_Number']}">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{Lang::T('Email')}</label>
              <input type="email" class="form-control" id="email" name="email" value="{$d['email']}">
            </div>
            <div class="input-area relative">
              <label for="largeInput" class="form-label">{$_L['Address']}</label>
              <textarea name="address" id="address" class="form-control">{$d['address']}</textarea>
            </div>
          </div>
          <button type="submit" class="btn inline-flex justify-center btn-primary">{$_L['Save']}</button>&nbsp; <a class="btn inline-flex justify-center btn-dark" href="{$_url}home">{$_L['Cancel']}</a>
        </form>
      </div>
    </div>
  </div>
</div> {include file="sections/user-footer.tpl"}
