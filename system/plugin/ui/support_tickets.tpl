{include file="sections/header.tpl"}
<style>tr.unread-ticket {
  border: 1px solid red;
}</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<section class="content-header">
  <h1>
    <div class="btn-group">
      {$buttonSettings}
    </div>
  </h1>
  <ol class="breadcrumb">
    <li>
      <a href="#">
        <i class="fa fa-dashboard">
        </i> {Lang::T('Dashboard')}</a>
    </li>
    <li class="active">{Lang::T('Support Ticket')}</li>
  </ol>
</section>

<!-- support ticket settings modal start -->

<div class="modal fade" id="settings">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">{Lang::T('Support Ticket Settings')}</h4>
      </div>
      <div class="box-body">
        <div class="tab-pane">
          <form action="{$_url}plugin/support_tickets_settings" method="post" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="{$csrfToken}">
            <div class="form-group">
              <label for="inputEmail" class="col-sm-2 control-label">{Lang::T('Enable In UCP')}:</label>

              <div class="col-sm-10">
                <select name="ucp" id="ucp" class="form-control">
                  <option value="enable" {if {$settings.ucp}=='enable' }selected="selected" {/if}> {Lang::T('Enable')}
                  <option value="disable" {if {$settings.ucp}=='disable' }selected="selected" {/if}> {Lang::T('Disable')}
                  </option>
              </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputName" class="col-sm-2 control-label">{Lang::T('Notifications')}:</label>

              <div class="col-sm-10">
                <select name="notification" id="notification" class="form-control">
                  <option value="enable" {if {$settings.enable}=='enable' }selected="selected" {/if}> {Lang::T('Enable')}
                  <option value="disable" {if {$settings.enable}=='disable' }selected="selected" {/if}> {Lang::T('Disable')}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputExperience" class="col-sm-2 control-label">{Lang::T('Notify Type')}:</label>

              <div class="col-sm-10">
                <select name="type" id="type" class="form-control">
                  <option value="sms" {if {$settings.type}=='sms' }selected="selected" {/if}> {Lang::T('SMS')}
                  <option value="whatsapp" {if {$settings.type}=='whatsapp' }selected="selected" {/if}> {Lang::T('WhatsApp')}
                  <option value="both" {if {$settings.type}=='both' }selected="selected" {/if}> {Lang::T('SMS and WhatsApp')}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputSkills" class="col-sm-2 control-label"> {Lang::T('Notify Admin')}:</label>

              <div class="col-sm-10">
                <select name="admin" id="admin" class="form-control">
                  <option value="enable" {if {$settings.admin}=='enable' }selected="selected" {/if}> {Lang::T('Enable')}
                  <option value="disable" {if {$settings.admin}=='disable' }selected="selected" {/if}> {Lang::T('Disable')}
                  </option>
                </select>
              </div>
            </div>
            <div class="box-footer">
              <div class="pull-right">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-telegram">
                  </i> {Lang::T('Save')} </button>
              </div>
              <button type="button" data-dismiss="modal" class="btn btn-danger">
                <i class="">
                </i>{Lang::T('Cancel')}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- support ticket settings modal end -->



<div class="modal fade" id="create">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"> {Lang::T('Create New Ticket')}</h4>
      </div>
      <div class="box-body">
        <div class="tab-pane">
          <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" class="form-control" name="created_by" value="{$_admin['fullname']}">
            <input type="hidden" name="csrf_token" value="{$csrfToken}">
            <div class="form-group">
              <label for="inputEmail" class="col-sm-2 control-label">{Lang::T('Customer')}:</label>

              <div class="col-sm-10">
                <select {if $customers}{else}id="personSelect" {/if} class="form-control select2" name="id_customer"
                  style="width: 100%" data-placeholder="{$_L['Select_Customer']}..."> {foreach $customers as $customer}
                  <option value="{$customer.id}">{$customer.name}</option> {/foreach}
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputName" class="col-sm-2 control-label"> {Lang::T('Subject')}:</label>

              <div class="col-sm-10">
                <input class="form-control" name="subject" placeholder="{Lang::T('Subject')}" required>
              </div>
            </div>
            <div class="form-group">
              <label for="inputExperience" class="col-sm-2 control-label"> {Lang::T('Message')}:</label>

              <div class="col-sm-10">
                <textarea name="message" class="form-control" placeholder="{Lang::T('Message')}" required></textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="inputSkills" class="col-sm-2 control-label"> {Lang::T('Priority')}:</label>

              <div class="col-sm-10">
                <select class="form-control" name="priority">
                  <option value="Low">{Lang::T('Low')}</option>
                  <option value="Medium">{Lang::T('Medium')}</option>
                  <option value="High">{Lang::T('High')}</option>
                </select>
              </div>
            </div>
            <!-- <div class="form-group">
              <label class="col-sm-2 control-label">{Lang::T('Report')}:</label>
              <div class="col-sm-10">
                <label class="radio-inline">
                  <input type="radio" name="report" value="internet" onclick="showSubcategories('internet')"> {Lang::T('Internet')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="change_speed" onclick="showSubcategories('change_speed')"> {Lang::T('Change Speed')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="landline" onclick="showSubcategories('landline')"> {Lang::T('Landline')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="iptv" onclick="showSubcategories('iptv')"> {Lang::T('IPTV')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="bills" onclick="showSubcategories('bills')"> {Lang::T('Bills')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="others" onclick="showSubcategories('others')"> {Lang::T('Others')}
                </label>
              </div>
            </div>
            
            <div class="form-group" id="subcategorySection" style="display: none;">
              <label class="col-sm-2 control-label">{Lang::T('Issue')}:</label>
              <div class="col-sm-10">
                <select class="form-control" name="issue" id="subcategorySelect" required>
                  <option value="">{Lang::T('Select One')}</option>
                </select>
                <input type="text" class="form-control" name="custom" placeholder="{Lang::T('Please specify')}" id="customSubcategoryInput" style="display: none;">
              </div>
            </div> -->
            
            <div class="form-group">
              <label for="inputSkills" class="col-sm-2 control-label">{Lang::T('Department')}:</label>

              <div class="col-sm-10">
                <select class="form-control" name="department">
                  <option value="Sales Team">{Lang::T('Sales Team')}</option>
                  <option value="Technical Team">{Lang::T('Technical Team')}</option>
                  <option value="Support Team">{Lang::T('Support Team')}</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="inputSkills" class="col-sm-2 control-label"> {Lang::T('Attachment')}:</label>
              <div class="col-sm-10">
                <input type="file" name="attachment">
                <span>{Lang::T('File Max Size. 2MB')}</span>
              </div>
            </div>
            <div class="box-footer">
              <div class="pull-right">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-telegram">
                  </i> {Lang::T('Create Ticket')}</button>
              </div>
              <button type="button" data-dismiss="modal" class="btn btn-danger">
                <i class="">
                </i> {Lang::T('Cancel')}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="row">
    <div class="col">
      <div class="row">
        <div class="col-md-3">
          <button class="btn btn-primary btn-block margin-bottom" data-toggle="modal" data-target="#create"> {Lang::T('Create
            Ticket')}</button> <br>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">{Lang::T('Support Ticket')}</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus">
                  </i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li class="">
                  <a href="#">
                    <i class="ion-email-unread">
                    </i>{Lang::T('Unread Ticket')} <span class="label label-danger pull-right">{$newTicketCount}</span>
                  </a>
                </li>
                <li class="">
                  <a href="#">
                    <i class="fa fa-envelope">
                    </i> {Lang::T('Open')} <span class="label label-danger pull-right">{$openTicketCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-calendar">
                    </i> {Lang::T('In Progress')} <span class="label label-primary pull-right">{$inProgressTicketCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-thumbs-up">
                    </i> {Lang::T('Resolved')} <span class="label label-success pull-right">{$resolvedTicketCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-close">
                    </i> {Lang::T('Closed')} <span class="label label-default pull-right">{$closedTicketCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-trash">
                    </i> {Lang::T('Trash')}<span class="label label-warning pull-right">{$trashTicketCount}</span>
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">{Lang::T('Priority')}</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus">
                  </i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li>
                  <a href="#">
                    <i class="fa fa-circle-o text-red">
                    </i> {Lang::T('HIGH')} <span class="label label-danger pull-right">{$highPriorityCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-circle-o text-yellow">
                    </i> {Lang::T('MEDIUM')} <span class="label label-warning pull-right">{$mediumPriorityCount}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-circle-o text-light-blue">
                    </i> {Lang::T('LOW')} <span class="label label-primary pull-right">{$lowPriorityCount}</span>
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{Lang::T('Tickets List')}</h3>
              <div class="box-tools pull-right">
                <div class="has-feedback">
                  <div class="form-group">
                    <input type="text" class="form-control input-sm" id="searchTickets" placeholder="{Lang::T('Search Tickets')}">
                  </div>
                  <span class="glyphicon glyphicon-search form-control-feedback">
                  </span>
                </div>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                  <i class="fa fa-square-o">
                  </i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fa fa-trash-o">
                    </i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fa fa-reply">
                    </i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fa fa-share">
                    </i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fa fa-refresh">
                  </i>
                </button>
                <div class="pull-right">{Lang::T('Total Active Tickets')}: <b>{$totalActiveTickets}</b> &nbsp; &nbsp; {if $totalPages > 1} {if
                    $currentPage > 1} <a href="{$_url}plugin/support_tickets&page={$currentPage - 1}"
                      class="btn btn-default btn-sm">
                      <i class="fa fa-chevron-left">
                      </i>
                    </a> {/if}
                      {foreach range(1, $totalPages) as $pageNumber}
                        {if $pageNumber == $currentPage} <span
                          class="btn btn-primary btn-sm">{$pageNumber}</span> {else} <a
                            href="{$_url}plugin/support_tickets&page={$pageNumber}"
                            class="btn btn-default btn-sm">{$pageNumber}</a> {/if} {/foreach} {if $currentPage <$totalPages} <a
                            href="{$_url}plugin/support_tickets&page={$currentPage + 1}" class="btn btn-default btn-sm">
                            <i class="fa fa-chevron-right">
                            </i>
                        </a> {/if}
                      {/if}
                      <!-- /.btn-group -->
                    </div>
                    <!-- /.pull-right -->
                  </div>
                  <div class="table-responsive mailbox-messages">
                    <table id="ticketTable" class="table table-hover table-striped">
                      <tbody> {foreach from=$sortedTickets item=ticket}
                          <tr {if $ticket.read_flag == 0}class="unread-ticket" {/if}>
                            <td style="padding: 0px">
                              <table class="table table-bordered" style="margin: 0px">
                                <tr>
                                  <td>
                                    <input type="checkbox"><a href="{$_url}plugin/support_tickets_view/{$ticket.ticket_id}" class="ticket-link" data-toggle="tooltip"
                                      title="{$ticket.message}"> {$ticket.ticket_id} </a>
                                  </td>
                                  <td class="mailbox-subject" colspan="4"><b>{$ticket.title}</b></td>
                                  <td class="mailbox-name">{$ticket.created_by}</td>
                                  <td class="mailbox">
                                    <span
                                      class="label {if $ticket.priority == Low}label-success {elseif $ticket.priority == Medium}label-primary {elseif $ticket.priority == High}label-danger {/if}">{$ticket.priority}</span>
                                    <span
                                      class="label {if $ticket.status == open}label-danger {elseif $ticket.status == in_progress}label-primary {elseif $ticket.status == resolved}label-success {elseif $ticket.status == closed}label-default{/if} ">{$ticket.status}</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="mailbox-subject">{$ticket.department}</td>
                                  <td class="mailbox"> {foreach $customers as $customer}
                                    {if $customer.id == $ticket.userid}
                                        <a href="{$_url}customers/view/{$customer.id}" data-toggle="tooltip"
                                        title="{$customer.info}">{$customer.name}</a> {/if}
                                      {/foreach}
                                  </td>
                                  <td class="mailbox-attachment"> {if $ticket.attachment_id} {assign var="extension"
                                      value=pathinfo($ticket.attachment_path, PATHINFO_EXTENSION)} {assign var="attachmentType"
                                      value=""} {if $extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' ||
                                      $extension
                                    == 'gif'} {assign var="attachmentType" value="Image"} {elseif $extension == 'pdf'} {assign
                                    var="attachmentType" value="PDF"} {elseif $extension == 'doc' || $extension == 'docx'}
                                      {assign
                                      var="attachmentType" value="Word Document"} {elseif $extension == 'xls' || $extension ==
                                      'xlsx'}
                                      {assign var="attachmentType" value="Excel Spreadsheet"} {elseif $extension == 'ppt' ||
                                      $extension == 'pptx'} {assign var="attachmentType" value="PowerPoint Presentation"}
                                    {else}
                                    {assign var="attachmentType" value="File"}
                                    {/if} {$attachmentType}
                                  {else} {Lang::T('None')}
                                  {/if} </td>
                                <td class="mailbox-date">{$ticket.formattedCreated}</td>
                                <td class="mailbox-date">{$ticket.formattedLastUpdated}</td>
                                <td class="mailbox-date">{$ticket.updated_by}</td>
                                <td>
                                  <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                      data-toggle="dropdown">
                                      {Lang::T('Update Status')} <span class="caret">
                                      </span>
                                      <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                      <li>
                                        <a
                                          href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=in_progress&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">{Lang::T('In Progress')}</a>
                                      </li>
                                      <li>
                                        <a
                                          href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=resolved&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">{Lang::T('Resolved')}</a>
                                      </li>
                                      <li>
                                        <a
                                          href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=closed&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">{Lang::T('Closed')}</a>
                                      </li>
                                      <li>
                                        <a
                                          href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=closed&delete=trash&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">{Lang::T('Trash')}</a>
                                      </li>
                                    </ul>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                     {/foreach}

                  </tbody>
                </table>
              </div>
              <!-- /.mail-box-messages -->
            </div>
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
</section>

<!-- <script>
  function showSubcategories(category) {
    var subcategorySection = document.getElementById('subcategorySection');
    var subcategorySelect = document.getElementById('subcategorySelect');
    var customSubcategoryInput = document.getElementById('customSubcategoryInput');

    if (category === 'internet') {
      subcategorySection.style.display = 'block';
      subcategorySelect.innerHTML = `
        <option value="">Select One</option>
        <option value="no_internet">No Internet</option>
        <option value="slow_browsing">Slow Browsing</option>
        <option value="intermittent">Intermittent</option>
      `;
      customSubcategoryInput.style.display = 'none';
    } else if (category === 'change_speed') {
      subcategorySection.style.display = 'block';
      subcategorySelect.innerHTML = `
        <option value="">Select One</option>
        <option value="upgrade">Upgrade</option>
        <option value="downgrade">Downgrade</option>
      `;
      customSubcategoryInput.style.display = 'none';
    } else if (category === 'others') {
      subcategorySection.style.display = 'block';
      subcategorySelect.innerHTML = `
        <option value="custom">Other</option>
      `;
      customSubcategoryInput.style.display = 'block';
    } else {
      subcategorySection.style.display = 'none';
      subcategorySelect.innerHTML = `<option value="">Select One</option>`;
      customSubcategoryInput.style.display = 'none';
    }
  }
</script> -->
<script>
  // Attach click event listener to the table

  // Hide all message rows by default
  var messageRows = document.querySelectorAll('.message-row');
  messageRows.forEach(function(row) {
    row.style.display = 'none';
  });
  const searchInput = document.getElementById('searchTickets');
  const ticketTable = document.getElementById('ticketTable');
  const ticketRows = ticketTable.getElementsByTagName('tr');
  searchInput.addEventListener('input', function() {
    const searchQuery = searchInput.value.toLowerCase();
    for (let i = 1; i < ticketRows.length; i++) {
      const ticketRow = ticketRows[i];
      const ticketData = ticketRow.getElementsByTagName('td');
      let hasMatch = false;
      for (let j = 0; j < ticketData.length; j++) {
        const ticketCell = ticketData[j];
        if (ticketCell.textContent.toLowerCase().includes(searchQuery)) {
          hasMatch = true;
          break;
        }
      }
      if (hasMatch || searchQuery === '') {
        ticketRow.style.display = '';
      } else {
        ticketRow.style.display = 'none';
      }
    }
  });
  // Reload the page when the search input is cleared
  searchInput.addEventListener('keyup', function(event) {
    if (event.keyCode === 8 && searchInput.value === '') {
      location.reload();
    }
  });
</script>
<script>
  window.addEventListener('DOMContentLoaded', function() {
    var portalLink = "https://freeispradius.com";
    $('#version').html('Support Ticket | Ver: 1.5.3 | by: <a href="' + portalLink + '">FreeIspRadius</a>');
  });
</script> {include file="sections/footer.tpl"}
