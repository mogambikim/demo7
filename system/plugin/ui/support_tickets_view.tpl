{include file="sections/header.tpl"}
<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<section class="content-header">
  <h1>
    <div class="btn-group">
      <a href="{$_url}plugin/support_tickets" class="btn btn-success">{Lang::T('BACK')} </a>
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
<div class="modal fade" id="create">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">{Lang::T('Create New Ticket')}</h4>
      </div>
      <div class="box-body">
        <div class="tab-pane">
          <form action="{$_url}plugin/support_tickets" method="post" enctype="multipart/form-data" class="form-horizontal">
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
              <label for="inputName" class="col-sm-2 control-label">{Lang::T('Subject')}:</label>

              <div class="col-sm-10">
                <input class="form-control" name="subject" placeholder="{Lang::T('Subject')}" required>
              </div>
             </div>
            <div class="form-group">
              <label for="inputExperience" class="col-sm-2 control-label">{Lang::T('Message')}:</label>

              <div class="col-sm-10">
                <textarea name="message" class="form-control" placeholder="{Lang::T('Message')}" required></textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="inputSkills" class="col-sm-2 control-label">{Lang::T('Priority')}:</label>

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
                  <input type="radio" name="report" value="landline" onclick="showSubcategories('landline')">  {Lang::T('Landline')}
                </label>
                <label class="radio-inline">
                  <input type="radio" name="report" value="iptv" onclick="showSubcategories('iptv')">{Lang::T('IPTV')} 
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
              <label class="col-sm-2 control-label"> {Lang::T('Issue')}:</label>
              <div class="col-sm-10">
                <select class="form-control" name="issue" id="subcategorySelect" required>
                  <option value="">{Lang::T('Select One')}</option>
                </select>
                <input type="text" class="form-control" name="custom" placeholder="{Lang::T('Please specify')}" id="customSubcategoryInput" style="display: none;">
              </div>
            </div>
             -->
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
              <label for="inputSkills" class="col-sm-2 control-label">{Lang::T('Attachment')}:</label>
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
          <button class="btn btn-primary btn-block margin-bottom" data-toggle="modal" data-target="#create"> {Lang::T('Create Ticket')}</button> <br>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">{Lang::T('Customer Details')}</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus">
                  </i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                {foreach $customers as $customer}
                  {if $customer.id == $ticket.userid}
                <li class="">
                  <a href="#">
                    {Lang::T('Name')}: {$customer.name}
                  </a>
                </li>
                <li class="">
                  <a href="#">
                    {Lang::T('Email')}: {$customer.email}
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Active Service')}: {$customer.service}
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Service Type')}: {$customer.type}
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Balance')}: {$customer.balance}
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Phone')}: {$customer.phone}
                  </a>
                </li>
                {/if}
              {/foreach}
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">{Lang::T('Ticket Details')}</h3>
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
                    {Lang::T('Priority')}: <span
                     class="label  pull-right {if $ticket.priority == Low}label-success {elseif $ticket.priority == Medium}label-primary {elseif $ticket.priority == High}label-danger {/if}">{$ticket.priority}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Status')}: <span class="label pull-right {if $ticket.status == open}label-danger {elseif $ticket.status == in_progress}label-primary {elseif $ticket.status == resolved}label-success {elseif $ticket.status == closed}label-default{/if} ">{$ticket.status}</span>
                  </a>
                </li>
                <li>
                  <a href="#">
                    {Lang::T('Department')}: <span class="label label-primary pull-right">{$ticket.department}</span>
                  </a>
                </li>
                {if $ticket.report}
                <li>
                  <a href="#">
                    {Lang::T('Report')}: <span class="label label-primary pull-right">{$ticket.report}</span>
                  </a>
                </li>
                {/if}
                {if $ticket.issue}
                <li>
                  <a href="#">
                    {Lang::T('Issue')}: <span class="label label-primary pull-right">{$ticket.issue}</span>
                  </a>
                </li>
                {/if}
                {if $ticket.custom}
                <li>
                  <a href="#">
                   <pre class="">{$ticket.custom}</pre>
                  </a>
                </li>
                {/if}
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
              <h3 class="box-title">{Lang::T('Ticket')}: [{$ticket.ticket_id}] </h3>

              <div class="box-tools pull-right">
                <a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Previous"><i
                    class="fa fa-chevron-left"></i></a>
                <a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Next"><i
                    class="fa fa-chevron-right"></i></a>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-read-info">
                <h3>{$ticket.title}</h3>
                <h5>{Lang::T('From')}: {foreach $customers as $customer}
                  {if $customer.id == $ticket.userid}
                      <a href="{$_url}customers/view/{$customer.id}" data-toggle="tooltip"
                      title="{$customer.info}">{$customer.name}</a> {/if}
                    {/foreach}
                  <span class="mailbox-read-time pull-right">{$ticket.created}</span>
                </h5>
              </div>
              <!-- /.mailbox-read-info -->
              <div class="mailbox-controls with-border text-center">
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body"
                    title="Delete">
                    <i class="fa fa-trash-o"></i></button>
                  <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body"
                    title="Reply">
                    <i class="fa fa-reply"></i></button>
                  <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body"
                    title="Forward">
                    <i class="fa fa-share"></i></button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" title="Print">
                  <i class="fa fa-print"></i></button>
              </div>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <p>{$ticket.message}</p>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <ul class="mailbox-attachments clearfix"> {if $ticket.attachment_id && $ticket.attachment_path} {assign
                var="extension" value=pathinfo($ticket.attachment_path, PATHINFO_EXTENSION)} {if
                $extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension ==
                'gif'}
                <li>
                  <span class="mailbox-attachment-icon has-img"><img src="{$ticket.attachment_path}"
                      alt="Attachment"></span>

                  <div class="mailbox-attachment-info">
                    <a href="{$ticket.attachment_path}" class="mailbox-attachment-name"><i class="fa fa-camera"></i>{if
                      $ticket.attachment_id} {assign var="extension"
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
                      {else} {Lang::T('Undefined')}
                      {/if}</a>
                    <span class="mailbox-attachment-size">
                      1.9 MB
                      <a href="{$ticket.attachment_path}" class="btn btn-default btn-xs pull-right"><i
                          class="fa fa-cloud-download"></i></a>
                    </span>
                  </div>
                </li>
                {else}
                <li>
                  <span class="mailbox-attachment-icon"><i class="fa fa-file-pdf-o"></i></span>

                  <div class="mailbox-attachment-info">
                    <a href="{$ticket.attachment_path}" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                      Sep2014-report.pdf</a>
                    <span class="mailbox-attachment-size">
                      1,245 KB
                      <a href="{$ticket.attachment_path}" class="btn btn-default btn-xs pull-right"><i
                          class="fa fa-cloud-download"></i></a>
                    </span>
                  </div>
                </li>
                {/if}
                {else} <li>{Lang::T('No uploaded attachments')}</li>
                {/if}
              </ul>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="chat-messages-container">
              <!-- Conversations are loaded here -->
              <div class="direct-chat-messages">
                <!-- Message. Default to the left -->
                <!-- /.direct-chat-msg -->
                <!-- Message to the right --> {foreach $replies as $reply} {if $reply.ticket_id ==
                $ticket.ticket_id} {if $reply.reply_by == 'Admin'} <div class="direct-chat-msg right">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">{$reply.admin_name}</span>
                    <span class="direct-chat-timestamp pull-left">{Lang::timeElapsed($reply.created,true)}</span>
                  </div>
                  <img src="https://robohash.org/{$reply.userid}?set=set3&size=100x100&bgset=bg1"
                    onerror="this.src='system/uploads/admin.default.png'" class="direct-chat-img" alt="Avatar">
                  <div class="direct-chat-text"> {$reply.reply_message} </div>
                </div> {/if} {if $reply.reply_by == 'User'} <div class="direct-chat-msg">
                  <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left"> {foreach $customers as $customer} {if
                      $customer.id == $reply.userid} <a href="{$_url}customers/view/{$customer.id}"
                        data-toggle="tooltip" title="{$customer.info}">{$customer.name}</a> {/if}
                      {/foreach} </span>
                    <span class="direct-chat-timestamp pull-right">{Lang::timeElapsed($reply.created,true)}</span>
                  </div>
                  <img src="https://robohash.org/{$reply.userid}?set=set3&size=100x100&bgset=bg1"
                    onerror="this.src='system/uploads/admin.default.png'" class="direct-chat-img" alt="Avatar">
                  <div class="direct-chat-text"> {$reply.reply_message} </div>
                </div> {/if}
                {/if}
                {/foreach}
              </div>
              <div class="box-footer">
                <form action="{$_url}plugin/support_tickets_admin_reply" method="post" enctype="multipart/form-data">
                  <div class="direct-chat-info clearfix">
                    <input type="hidden" name="ticketId" value="{$ticket.ticket_id}">
                    <input type="hidden" name="userId" value="{$_admin['id']}">
                    <input type="hidden" name="reply_by" value="Admin">
                    <input type="hidden" name="admin_name" value="{$_admin['fullname']}">
                    <input type="hidden" name="csrf_token" value="{$csrfToken}">
                  </div>
                  <div class="input-group">
                    <input type="text" name="reply" placeholder="Type Message ..." class="form-control" required>
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-success btn-flat">{Lang::T('Send')}</button>
                    </span>
                  </div>
                </form>
              </div>
              <!--/.direct-chat-messages-->
            </div>
            <!-- /.direct-chat-pane -->
            <!-- /.box-footer-->
          </div>
          <!-- /.box-footer -->
          <div class="box-footer">
            <div class="pull-right">
              <button type="button" class="btn btn-default btn-reply"><i class="fa fa-reply"></i> {Lang::T('Reply')}</button>
              <button type="button" class="btn btn-success btn-reply dropdown-toggle" data-toggle="dropdown">
                {Lang::T('Update Status')} <span class="caret">
                </span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu" role="menu">
                <li>
                  <a
                    href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=in_progress&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">
                    {Lang::T('In Progress')}</a>
                </li>
                <li>
                  <a
                    href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=resolved&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}"> {Lang::T('Resolved')}</a>
                </li>
                <li>
                  <a
                    href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=closed&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}">{Lang::T('Closed')}</a>
                </li>
              </ul>
            </div>
            <a href="{$_url}plugin/support_tickets_update_status&ticketId={$ticket.ticket_id}&newStatus=closed&delete=trash&updatedBy={$_admin['fullname']}&csrf_token={$csrfToken}"><button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> {Lang::T('Delete')}</button></a>
            <button type="button" class="btn btn-default"><i class="fa fa-print"></i> {Lang::T('Print')}</button>
          </div>
          <!-- /.box-footer -->
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
  var table = document.querySelector('.table');
  table.addEventListener('click', function (e) {
    if (e.target.classList.contains('ticket-link')) {
      e.preventDefault();
      var ticketId = e.target.getAttribute('data-ticket-id');
      var messageRow = document.getElementById('messageRow_' + ticketId);
      // Hide other message rows
      var messageRows = document.querySelectorAll('.message-row');
      messageRows.forEach(function (row) {
        if (row !== messageRow) {
          row.style.display = 'none';
        }
      });
      // Toggle display of clicked message row
      if (messageRow.style.display === 'none') {
        messageRow.style.display = 'table-row';
      } else {
        messageRow.style.display = 'none';
      }
    }
    if (e.target.classList.contains('status-change-btn')) {
      var ticketId = e.target.getAttribute('data-ticket-id');
      var status = prompt('Enter new status:');
      if (status !== null) {
        // Update the status in your backend or perform any further actions
        console.log('New status for ticket ' + ticketId + ': ' + status);
      }
    }
  });
  // Hide all message rows by default
  var messageRows = document.querySelectorAll('.message-row');
  messageRows.forEach(function (row) {
    row.style.display = 'none';
  });
  const searchInput = document.getElementById('searchTickets');
  const ticketTable = document.getElementById('ticketTable');
  const ticketRows = ticketTable.getElementsByTagName('tr');
  searchInput.addEventListener('input', function () {
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
  searchInput.addEventListener('keyup', function (event) {
    if (event.keyCode === 8 && searchInput.value === '') {
      location.reload();
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var chatMessagesContainer = document.getElementById('chat-messages-container');
    var replyButton = document.querySelector('.btn-reply');

    chatMessagesContainer.style.display = 'none'; // Hide the chat messages container by default

    replyButton.addEventListener('click', function () {
      chatMessagesContainer.style.display = 'block'; // Show the chat messages container when the button is clicked
    });
  });
</script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    var portalLink = "https://freeispradius.com";
    $('#version').html('Support Ticket | Ver: 1.5.3 | by: <a href="' + portalLink + '">FreeIspRadius</a>');
  });
</script> {include file="sections/footer.tpl"}
