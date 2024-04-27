{include file="sections/user-header.tpl"}
<div class="flex flex-wrap justify-between items-center mb-4">
  <h4
      class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4 mb-1 sm:mb-0">
      <div class="relative">
        <input type="text" class="form-control !pr-12" placeholder="{Lang::T('Search Tickets...')}">
        <button class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l border-l-slate-200 dark:border-l-slate-700 flex items-center justify-center">
          <iconify-icon icon="heroicons-solid:search"></iconify-icon>
        </button>
      </div>
  </h4>
  <div class="flex space-x-4 justify-end items-center rtl:space-x-reverse">
      <button class="btn inline-flex justify-center btn-dark dark:bg-slate-800 m-1" data-bs-toggle="modal"
          data-bs-target="#newEmailModal">
          <span class="flex items-center">
              <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
              <span>{Lang::T('Submit Ticket')}</span>
          </span>
      </button>
  </div>
</div>
<div class="card">
  <header class=" card-header noborder">
    <h4 class="card-title">{Lang::T('Your Ticket History')}
    </h4>
  </header>
  <div class="card-body px-6 pb-6">
    <div class="overflow-x-auto -mx-6">
      <span class=" col-span-8  hidden"></span>
      <span class="  col-span-4 hidden"></span>
      <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden ">
          <table id="ticketTable" class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
            <thead class="bg-slate-200 dark:bg-slate-700">
              <tr>

                <th scope="col" class=" table-th ">
                  {Lang::T('Ticket ID')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Opened By')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Subject')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Department')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Priority')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Status')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Attachment')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Created Date')}
                </th>

                <th scope="col" class=" table-th ">
                  {Lang::T('Last Updated')}
                </th>

              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
              {foreach from=$sortedTickets item=ticket} <tr>
                <td class="table-td"><a href="{$_url}plugin/support_tickets_clients_view/{$ticket.ticket_id}"
                    class="ticket-link" data-toggle="tooltip" title="{$ticket.message}"> {$ticket.ticket_id} </a></td>
                <td class="table-td"> {if $ticket.created_by == $_user['fullname']} {Lang::T('Me')} {else}
                  {$ticket.created_by} {/if} </td>
                <td class="table-td">{$ticket.title}</td>
                <td class="table-td">{$ticket.department}</td>
                <td class="table-td">
                  <div
                    class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-{if $ticket.priority == Low}success{elseif $ticket.priority == Medium}primary{elseif $ticket.priority == High}danger{/if}-500
                     bg-{if $ticket.priority == Low}success{elseif $ticket.priority == Medium}primary{elseif $ticket.priority == High}danger{/if}-500">
                    {$ticket.priority}
                  </div>
                </td>
                <td class="table-td">
                  <div
                    class="inline-block px-3 min-w-[90px] text-center mx-auto py-1 rounded-[999px] bg-opacity-25 text-{if $ticket.status == open}danger{elseif $ticket.status == in_progress}primary{elseif $ticket.status == resolved}success{elseif $ticket.status == closed}secondary{/if}-500
                     bg-{if $ticket.status == open}danger{elseif $ticket.status == in_progress}primary{elseif $ticket.status == resolved}success{elseif $ticket.status == closed}secondary{/if}-500">
                    {$ticket.status}
                  </div>
                </td>
                <td class="table-td">
                  <div> {if $ticket.attachment_id} {assign var="extension"
                    value=pathinfo($ticket.attachment_path, PATHINFO_EXTENSION)} {assign var="attachmentType"
                    value=""} {if $extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension ==
                    'gif'} {assign var="attachmentType" value="Image"} {elseif $extension == 'pdf'} {assign
                    var="attachmentType" value="PDF"} {elseif $extension == 'doc' || $extension == 'docx'} {assign
                    var="attachmentType" value="Word Document"} {elseif $extension == 'xls' || $extension == 'xlsx'}
                    {assign var="attachmentType" value="Excel Spreadsheet"} {elseif $extension == 'ppt' || $extension
                    == 'pptx'} {assign var="attachmentType" value="PowerPoint Presentation"} {else} {assign
                    var="attachmentType" value="File"} {/if} {$attachmentType} {else} {Lang::T('None')} {/if} </div>
                </td>
                <td class="table-td">
                  <div>{$ticket.formattedCreated}</div>
                </td>
                <td class="table-td">
                  <div>{$ticket.formattedLastUpdated}</div>
                </td>
              </tr> {/foreach}
            </tbody>
          </table>

          <div class="card col-span-1 md:col-span-2 rounded-md bg-white dark:bg-slate-800 lg:h-full shadow-base">
            <div class="card-body flex flex-col p-6">
              <div class="card-text h-full flex flex-wrap items-center justify-between">
                <div class="flex items-center space-x-2 mb-2 sm:mb-0">
                  {Lang::T('Total Tickets')}: &nbsp; <b>{$totalTickets}</b>
                </div>
                <div>
                  <ul class="list-none">
                    {if
                    $totalPages > 1} {if $currentPage > 1}
                    <li class="inline-block">
                      <a href="{$_url}plugin/support_tickets_clients&page={$currentPage - 1}" class="flex items-center justify-center w-6 h-6 text-slate-600 mr-5 ml-5 text-sm font-Inter font-medium transition-all
                              duration-300 relative dark:text-white">
                        Previous
                      </a>
                    </li>{/if}
                    {foreach range(1,
                    $totalPages) as $pageNumber}<li class="inline-block"> {if $pageNumber == $currentPage}
                      <a href="#" class="flex items-center justify-center w-6 h-6 bg-slate-100 text-slate-800
                                    dark:text-white rounded mx-[2px] sm:mx-1 hover:bg-black-500 hover:text-white text-sm font-Inter font-medium transition-all
                                    duration-300 p-active">
                        {$pageNumber}</a>

                      {else}

                      <a href="{$_url}plugin/support_tickets_clients&page={$pageNumber}" class="flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-700 dark:hover:bg-black-500 text-slate-800
                                    dark:text-white rounded mx-[2px] sm:mx-1 hover:bg-black-500 hover:text-white text-sm font-Inter font-medium transition-all
                                    duration-300 ">
                        {$pageNumber}</a>
                      {/if}
                    </li>
                    {/foreach}
                    {if $currentPage < $totalPages} <li class="inline-block">
                      <a href="{$_url}plugin/support_tickets_clients&page={$currentPage + 1}" class="flex items-center justify-center w-6 h-6 text-slate-600 ml-3 text-sm font-Inter font-medium transition-all
                              duration-300 relative dark:text-white">
                        Next
                      </a>
                      </li>
                      {/if} {/if}
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- BEGIN: Modal Content -->

<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto"
    id="newEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog relative w-auto pointer-events-none">
        <div class="modal-content border-none shadow-lg relative flex flex-col lg:w-[576px] w-full pointer-events-auto bg-white
bg-clip-padding rounded-md outline-none text-current">
            <div class="relative bg-white rounded-lg shadow dark:bg-slate-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-5 border-b rounded-t dark:border-slate-600 bg-slate-900 dark:bg-slate-700">
                    <h3 class="text-base font-medium text-white dark:text-white capitalize">
                        {Lang::T('Create New Ticket')}
                    </h3>
                    <button type="button" class="text-slate-400 bg-transparent hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center
  dark:hover:bg-slate-600 dark:hover:text-white" data-bs-dismiss="modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="#ffffff" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
      11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-4">
                    <form action="" method="post" enctype="multipart/form-data"
                        class="flex flex-col space-y-3">
                        <input type="hidden" class="form-control" name="created_by" value="{$_user['fullname']}">
                        <input type="hidden" class="form-control" name="id_customer" value="{$_user['id']}">
                        <input type="hidden" name="csrf_token" value="{$csrfToken}">
                        <div class="input-area">
                            <label for="subject" class="form-label">{Lang::T('Subject')}:</label>
                            <input class="form-control" name="subject" placeholder="{Lang::T('Subject')}" required>
                        </div>

                        <div class="input-area">
                            <label for="priority" class="form-label">{Lang::T('Priority')}:</label>
                            <select class="form-control" name="priority">
                                <option value="Low">{Lang::T('Low')}</option>
                                <option value="Medium">{Lang::T('Medium')}</option>
                                <option value="High">{Lang::T('High')}</option>
                            </select>
                        </div>
                        <div class="input-area">
                            <label for="department" class="form-label">{Lang::T('Department')}:</label>
                            <select class="form-control" name="department">
                                <option value="Sales Team">{Lang::T('Sales Team')}</option>
                                <option value="Technical Team">{Lang::T('Technical Team')}</option>
                                <option value="Support Team">{Lang::T('Support Team')}</option>
                            </select>
                        </div>
                        <div class="multiFilePreview">
                            <label for="attachment" class="form-label">{Lang::T('Attachment')}:</label>
                            <label>
                                <input type="file" class=" w-full hidden" name="attachment"
                                    accept=".jpg, .gif, .jpeg, .png, .pdf, .txt, .doc, .xlsx, .xls, .ppt, .pptx">
                                <span class="w-full h-[40px] file-control flex items-center custom-class">
                                    <span class="flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
                                        <span class="text-slate-400">{Lang::T('Choose a file')}</span>
                                    </span>
                                    <span
                                        class="file-name flex-none cursor-pointer border-l px-4 border-slate-200 dark:border-slate-700 h-full inline-flex items-center bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-sm rounded-tr rounded-br font-normal">Browse</span>
                                </span>
                            </label>
                            <div id="file-preview"></div>
                        </div>
                        <div class="input-area">
                            <label for="message" class="form-label">{Lang::T('Message')}:</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="{Lang::T('Message')}"
                                required></textarea>
                        </div>
                        <div class="flex items-center justify-end rounded-b dark:border-slate-600">
                            <button type="submit"
                                class="btn inline-flex justify-center text-white bg-black-500">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/user-footer.tpl"}