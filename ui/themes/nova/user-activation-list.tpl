{include file="sections/user-header.tpl"}
<!-- user-activation-list -->
<div class="card">
  <header class=" card-header noborder">
    <h4 class="card-title">{Lang::T('List Activated Voucher')} </h4>
  </header>
  <div class="card-body px-6 pb-6">
    <div class="overflow-x-auto -mx-6">
      <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden ">
          <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
            <thead class="bg-slate-200 dark:bg-slate-700">
              <tr>
                <th scope="col" class=" table-th "> {Lang::T('Username')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Plan Name')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Plan Price')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Type')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Created On')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Expires On')} </th>
                <th scope="col" class=" table-th "> {Lang::T('Method')} </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700"> {foreach $d as $ds} <tr class="hover:bg-slate-200 dark:hover:bg-slate-700">
                <td class="table-td">{$ds['username']}</td>
                <td class="table-td">{$ds['plan_name']}</td>
                <td class="table-td ">{Lang::moneyFormat($ds['price'])}</td>
                <td class="table-td ">{$ds['type']}</td>
                <td class="table-td ">{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                <td class="table-td ">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                <td class="table-td ">{$ds['method']}</td>
              </tr> {/foreach} </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<br> {$paginator['contents']} {include file="sections/user-footer.tpl"}
