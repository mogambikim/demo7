{include file="sections/user-header.tpl"}
<!-- user-orderHistory -->
<div class=" space-y-5">
  <div class="card">
    <header class=" card-header noborder">
      <h4 class="card-title">{Lang::T('Order History')} </h4>
    </header>
    <div class="card-body px-6 pb-6">
      <div class="overflow-x-auto -mx-6 dashcode-data-table">
        <span class=" col-span-8  hidden"></span>
        <span class="  col-span-4 hidden"></span>
        <div class="inline-block min-w-full align-middle">
          <div class="overflow-hidden ">
            <table id="datatable" class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700 ">
              <thead class=" bg-slate-200 dark:bg-slate-700">
                <tr>
                  <th scope="col" class=" table-th "> {Lang::T('Plan Name')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Gateway')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Routers')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Type')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Plan Price')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Created On')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Expires On')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Date Done')} </th>
                  <th scope="col" class=" table-th "> {Lang::T('Method')} </th>
                  <th scope="col" class=" table-th "> Action </th>
                </tr>
              </thead> {foreach $d as $ds} <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                <tr>
                  <td class="table-td">{$ds['plan_name']}</td>
                  <td class="table-td ">{$ds['gateway']}</td>
                  <td class="table-td ">{$ds['routers']}</td>
                  <td class="table-td ">{$ds['payment_channel']}</td>
                  <td class="table-td ">
                    <div>{Lang::moneyFormat($ds['price'])}</div>
                  </td>
                  <td class="table-td ">
                    <div> {date("{$_c['date_format']} H:i", strtotime($ds['created_date']))} </div>
                  </td>
                  <td class="table-td ">
                    <div> {date("{$_c['date_format']} H:i", strtotime($ds['expired_date']))} </div>
                  </td>
                  <td class="table-td ">
                    <div> {if $ds['status']!=1}{date("{$_c['date_format']} H:i", strtotime($ds['paid_date']))}{/if} </div>
                  </td>
                  <td class="table-td "> {if $ds['status']==1} <div class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-warning-500
                              bg-warning-500">{Lang::T('UNPAID')}</div> {elseif $ds['status']==2} <div class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-success-500
                            bg-success-500">{Lang::T('Paid')}</div> {elseif $ds['status']==3} <div class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-secondary-500
                              bg-secondary-500">{Lang::T('FAILED')}</div> {elseif $ds['status']==4} <div class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-danger-500
                              bg-danger-500">{Lang::T('CANCELED')}</div> {elseif $ds['status']==5} <div class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-primary-500
                              bg-primary-500">{Lang::T('UNKNOWN')}</div> {/if} </td>
                  <td class="table-td ">
                    <div class="flex space-x-3 rtl:space-x-reverse">
                      <a href="{$_url}order/view/{$ds['id']}">
                        <button class="action-btn" type="button">
                          <iconify-icon icon="heroicons:eye"></iconify-icon>
                        </button>
                      </a>
                    </div>
                  </td>
                </tr> {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> {$paginator['contents']}
</div> {include file="sections/user-footer.tpl"}
