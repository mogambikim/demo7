<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{$_title} - {$_c['CompanyName']}</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />

    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">

    <link rel="stylesheet" href="ui/ui/fonts/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
    <link rel="stylesheet" href="ui/ui/styles/select2.min.css" />
    <link rel="stylesheet" href="ui/ui/styles/select2-bootstrap.min.css" />
        <link rel="stylesheet" href="ui/ui/styles/sweetalert2.min.css" />
    <link rel="stylesheet" href="ui/ui/styles/plugins/pace.css" />        
    <script src="ui/ui/scripts/sweetalert2.all.min.js"></script>
    <style>
    .chart-container {
    position: relative;
    height: 400px;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1;
}

.overlay .fa-refresh {
    font-size: 24px;
}
        ::-moz-selection {
            /* Code for Firefox */
            color: red;
            background: yellow;
        }
        ::selection {
            color: red;
            background: yellow;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            margin-top: 0px !important;
        }
        @media (min-width: 768px) {
            .outer {
                height: 200px
                    /* Or whatever */
            }
        }
        th:first-child,
        td:first-child {
            position: sticky;
            left: 0px;
            background-color: #f9f9f9;
        }
        .text1line {
            display: block;
            /* or inline-block */
            text-overflow: ellipsis;
            word-wrap: break-word;
            overflow: hidden;
            max-height: 1em;
            line-height: 1em;
        }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f2f2f2;
    }

    .chatbot-container {
      position: fixed;
      right: 20px;
      bottom: 20px;
      width: 350px;
      height: 500px;
      border: none;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      display: none;
      transition: height 0.3s ease, width 0.3s ease, bottom 0.3s ease;
      overflow: hidden;
      z-index: 9999; /* Add this line */
    }

     .chatbot-container.expanded {
      width: 100%;
      height: 100%;
      right: 0;
      bottom: 0;
      border-radius: 0;
      z-index: 9999; /* Add this line */
    }
.chatbot-preferences,
.chatbot-feedback {
  padding: 15px;
  background-color: #f2f2f2;
  border-top: 1px solid #ccc;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.chatbot-preferences.hidden,
.chatbot-feedback.hidden {
  transform: translateY(100%);
  opacity: 0;
}
.chatbot-preferences input[type="text"],
.chatbot-feedback input[type="text"] {
  width: 100%;
  padding: 10px;
  border: none;
  border  font-size: 16px;
  background-color: #fff;
  margin-bottom: 10px;
}

.chatbot-preferences button,
.chatbot-feedback button {
  padding: 10px 20px;
  background-color: #fa7070;
  color: #fff;
  border: none;
  border-radius: 20px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.chatbot-preferences button:hover,
.chatbot-feedback button:hover {
  background-color: #e86060;
}

.success-message {
  display: none;
  background-color: #4caf50;
  color: #fff;
  padding: 10px;
  border-radius: 20px;
  font-size: 14px;
  margin-top: 10px;
  text-align: center;
}

    .chatbot-header {
      background-color: #fa7070;
      color: #fff;
      padding: 15px;
      font-size: 20px;
      font-weight: bold;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .chatbot-header:hover {
      background-color: #e86060;
    }

.chatbot-messages {
  height: calc(100% - 120px);
  overflow-y: auto;
  padding: 20px;
  background-color: #f9f9f9;
  display: flex;
  flex-direction: column-reverse;
  transition: height 0.3s ease;
  margin-bottom: 60px; /* Adjust the margin at the bottom */
}
    .user-message {
      text-align: right;
    }

    .user-message span {
      display: inline-block;
      background-color: #fa7070;
      color: #fff;
      padding: 10px 15px;
      border-radius: 20px;
      max-width: 70%;
      word-wrap: break-word;
    }

    .bot-message {
      text-align: left;
    }

    .bot-message span {
      display: inline-block;
      background-color: #e2f7ff;
      color: #333;
      padding: 10px 15px;
      border-radius: 20px;
      max-width: 70%;
      word-wrap: break-word;
    }

    .chatbot-input {
  position: absolute;
  right: 0;
  bottom: 60px; /* Adjust the bottom position */
  width: 100%;
  display: flex;
  align-items: center;
  padding: 15px;
  background-color: #fff;
  border-top: 1px solid #ccc;
  box-sizing: border-box;
}

    .chatbot-input input[type="text"] {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      background-color: #f2f2f2;
    }

    .chatbot-input button {
      margin-left: 10px;
      padding: 10px 20px;
      background-color: #fa7070;
      color: #fff;
      border: none;
      border-radius: 20px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .chatbot-input button:hover {
      background-color: #e86060;
    }

.chatbot-label {
  position: fixed;
  right: 20px;
  bottom: 20px; /* Adjust the bottom position */
  background-color: #fa7070;
  color: #fff;
  padding: 12px 25px;
  border-radius: 20px;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  z-index: 9998;
}

    .chatbot-label:hover {
      transform: scale(1.05);
    }

.expand-button {
  
  top: 70px; /* Adjust this value as needed */
  right: 380px; /* Adjust this value as needed */
  background-color: #fa7070;
  color: #fff;
  padding: 8px 12px;
  border: none;
  border-radius: 50%;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  z-index: 10000; /* Add a higher z-index value */
}

.expand-button:hover {
  background-color: #e86060;
}

    .typing-indicator {
      display: flex;
      align-items: center;
      margin-top: 10px;
    }

.chatbot-messages.expanded {
  height: calc(100% - 180px); /* Adjust the height when expanded */
}

    .typing-indicator span {
      display: inline-block;
      width: 8px;
      height: 8px;
      margin-right: 5px;
      background-color: #fa7070;
      border-radius: 50%;
      animation: typing 1s infinite;
    }

    .typing-indicator span:nth-child(2) {
      animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes typing {
      0% {
        transform: scale(1);
        opacity: 1;
      }
      50% {
        transform: scale(1.2);
        opacity: 0.6;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

        .loading {
          pointer-events: none;
          opacity: 0.7;
        }
    
        .loading::after {
          content: "";
          display: inline-block;
          width: 16px;
          height: 16px;
          vertical-align: middle;
          margin-left: 10px;
          border: 2px solid #fff;
          border-top-color: transparent;
          border-radius: 50%;
          animation: spin 0.8s infinite linear;
        }
    
        @keyframes spin {
          0% {
            transform: rotate(0deg);
          }
    
          100% {
            transform: rotate(360deg);
          }
        }


    </style>

    {if isset($xheader)}
        {$xheader}
    {/if}

</head>

<body class="hold-transition modern-skin-dark sidebar-mini {if $_kolaps}sidebar-collapse{/if}">
    <div class="wrapper">

        <header class="main-header">
            <a href="{$_url}dashboard" class="logo">
                <span class="logo-mini"><b>I</b>Sp</span>
               <span class="logo-lg">{$_c['CompanyName']}</span>
            </a>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" onclick="return setKolaps()">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">

                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="https://robohash.org/{$_admin['id']}?set=set3&size=100x100&bgset=bg1"
                                    onerror="this.src='{$UPLOAD_PATH}/admin.default.png'" class="user-image"
                                    alt="Avatar">
                                <span class="hidden-xs">{$_admin['fullname']}</span>
                            </a>
                            <ul class="dropdown-menu">
                               <li class="user-header">
    <img src="https://robohash.org/{$_admin['id']}?set=set3&size=100x100&bgset=bg1" onerror="this.src='{$UPLOAD_PATH}/admin.default.png'" class="img-circle" alt="Avatar">
    <p>
        {$_admin['fullname']}
                                        <small>{Lang::T($_admin['user_type'])}</small>
    </p>
</li>
<li class="user-body">
    <div class="row">
                                        <div class="col-xs-7 text-center text-sm">
                                            <a href="{$_url}settings/change-password"><i class="ion ion-settings"></i>
                                               {Lang::T('Change Password')}</a>
                                        </div>
                                        <div class="col-xs-5 text-center text-sm">
                                            <a href="{$_url}settings/users-view/{$_admin['id']}">
                                                <i class="ion ion-person"></i> {Lang::T('My Account')}</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="{$_url}logout" class="btn btn-default btn-flat"><i
                                               class="ion ion-power"></i> {Lang::T('Logout')}</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li {if $_system_menu eq 'dashboard'}class="active" {/if}>
                <a href="{$_url}dashboard">
                    <i class="ion ion-monitor"></i>
                    <span>{Lang::T('Dashboard')}</span>
                </a>
            </li>
            {$_MENU_AFTER_DASHBOARD}
            {if !in_array($_admin['user_type'],['Report'])}
                <li class="{if $_system_menu eq 'customers' || $_system_menu eq 'map'}active menu-open{/if} treeview">
                    <a href="#">
                        <i class="ion ion-android-contacts"></i>
                        <span>{Lang::T('Customers')}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li {if $_routes[1] eq 'add'}class="active" {/if}>
                            <a href="{$_url}customers/add">
                                <i class="fa fa-user-plus"></i> {Lang::T('Add New User')}
                            </a>
                        </li>
                        <li {if $_routes[1] eq 'list'}class="active" {/if}>
                            <a href="{$_url}customers/list">
                                <i class="fa fa-users"></i> {Lang::T('Users')}
                            </a>
                        </li>
                        <li {if $_system_menu eq 'map'}class="active" {/if}>
                            <a href="{$_url}map/customer">
                                <i class="ion ion-ios-location"></i> {Lang::T('User\'s Location')}
                            </a>
                        </li>
                        {$_MENU_CUSTOMERS}
                    </ul>
                </li>
                {$_MENU_AFTER_CUSTOMERS}
                <li class="{if $_system_menu eq 'prepaid'}active{/if} treeview">
                    <a href="#">
                        <i class="fa fa-ticket"></i> <span>{Lang::T('Activation')}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li {if $_routes[1] eq 'list'}class="active" {/if}>
                            <a href="{$_url}prepaid/list">{Lang::T('Active Users')}</a>
                        </li>
                        {if $_c['disable_voucher'] != 'yes'}
                            <li {if $_routes[1] eq 'voucher'}class="active" {/if}>
                                <a href="{$_url}prepaid/voucher">{Lang::T('Vouchers')}</a>
                            </li>
                            <li {if $_routes[1] eq 'refill'}class="active" {/if}>
                                <a href="{$_url}prepaid/refill">{Lang::T('Redeem Voucher')}</a>
                            </li>
                        {/if}
                        <li {if $_routes[1] eq 'recharge'}class="active" {/if}>
                            <a href="{$_url}prepaid/recharge">{Lang::T('Activate User')}</a>
                        </li>
                        {$_MENU_PREPAID}
                    </ul>
                </li>
            {/if}
            {$_MENU_AFTER_PREPAID}
            {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                <li class="{if $_system_menu eq 'services'}active{/if} treeview">
                    <a href="#">
                        <i class="ion ion-cube"></i> <span>{Lang::T('Packages/Plans')}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li {if $_routes[1] eq 'hotspot'}class="active" {/if}>
                            <a href="{$_url}services/hotspot">{Lang::T('Hotspot Plans')}</a>
                        </li>
                        <li {if $_routes[1] eq 'pppoe'}class="active" {/if}>
                            <a href="{$_url}services/pppoe">{Lang::T('PPPOE Plans')}</a>
                        </li>
                        <li {if $_routes[1] eq 'static'}class="active" {/if}>
                            <a href="{$_url}services/static">{Lang::T('Static ip plans')}</a>
                        </li>
                        <li {if $_routes[1] eq 'list'}class="active" {/if}>
                            <a href="{$_url}bandwidth/list">{Lang::T('Bandwidth Plans')}</a>
                        </li>
                        <li {if $_routes[1] eq 'balance'}class="active" {/if}>
                            <a href="{$_url}services/balance">{Lang::T('Balance Plans')}</a>
                        </li>
                        {$_MENU_SERVICES}
                    </ul>
                </li>
            {/if}
            {$_MENU_AFTER_SERVICES}
            <li class="{if $_system_menu eq 'reports'}active{/if} treeview">
                <a href="#">
                    <i class="ion ion-clipboard"></i> <span>{Lang::T('Reports')}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li {if $_routes[1] eq 'daily-report'}class="active" {/if}>
                        <a href="{$_url}reports/daily-report">{Lang::T('Daily Reports')}</a>
                    </li>
                    <li {if $_routes[1] eq 'by-period'}class="active" {/if}>
                        <a href="{$_url}reports/by-period">{Lang::T('Period Reports')}</a>
                    </li>
                    <li {if $_routes[1] eq 'activation'}class="active" {/if}>
                        <a href="{$_url}reports/activation">{Lang::T('Activation History')}</a>
                    </li>
                    {$_MENU_REPORTS}
                </ul>
            </li>
            {$_MENU_AFTER_REPORTS}
            <li class="{if $_system_menu eq 'message'}active{/if} treeview">
                <a href="#">
                    <i class="ion ion-android-chat"></i> <span>{Lang::T('Send Message')}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li {if $_routes[1] eq 'send'}class="active" {/if}>
                        <a href="{$_url}message/send">{Lang::T('Single User')}</a>
                    </li>
                    <li {if $_routes[1] eq 'send_bulk'}class="active" {/if}>
                        <a href="{$_url}message/send_bulk">{Lang::T('Bulk Send')}</a>
                    </li>
                    {$_MENU_MESSAGE}
                </ul>
            </li>
            {$_MENU_AFTER_MESSAGE}
            {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                <li class="{if $_system_menu eq 'network'}active{/if} treeview">
                    <a href="#">
                        <i class="ion ion-network"></i> <span>{Lang::T('Network')}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li {if $_routes[0] eq 'routers' and $_routes[1] eq 'list'}class="active" {/if}>
                            <a href="{$_url}routers/list">{Lang::T('Routers')}</a>
                        </li>
                        <li {if $_routes[0] eq 'pool' and $_routes[1] eq 'list'}class="active" {/if}>
                            <a href="{$_url}pool/list">{Lang::T('IP Pool')}</a>
                        </li>
                        {$_MENU_NETWORK}
                    </ul>
                </li>
                {$_MENU_AFTER_NETWORKS}
                {if $_c['radius_enable']}
                    <li class="{if $_system_menu eq 'radius'}active{/if} treeview">
                        <a href="#">
                            <i class="fa fa-database"></i> <span>{Lang::T('Radius')}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li {if $_routes[0] eq 'radius' and $_routes[1] eq 'nas-list'}class="active" {/if}>
                                <a href="{$_url}radius/nas-list">{Lang::T('Radius NAS')}</a>
                            </li>
                            {$_MENU_RADIUS}
                        </ul>
                    </li>
                {/if}
                {$_MENU_AFTER_RADIUS}
                <li class="{if $_system_menu eq 'pages'}active{/if} treeview">
                    <a href="#">
                        <i class="ion ion-document"></i> <span>{Lang::T("Static Pages")}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li {if $_routes[1] eq 'Order_Voucher'}class="active" {/if}>
                            <a href="{$_url}pages/Order_Voucher">{Lang::T('Order Voucher')}</a>
                        </li>
                        <li {if $_routes[1] eq 'Voucher'}class="active" {/if}>
                            <a href="{$_url}pages/Voucher">{Lang::T('Voucher')} Template</a>
                        </li>
                        <li {if $_routes[1] eq 'Announcement'}class="active" {/if}>
                            <a href="{$_url}pages/Announcement">{Lang::T('Announcement')}</a>
                        </li>
                        <li {if $_routes[1] eq 'Registration_Info'}class="active" {/if}>
                            <a href="{$_url}pages/Registration_Info">{Lang::T('Registration Info')}</a>
                        </li>
                        <li {if $_routes[1] eq 'Privacy_Policy'}class="active" {/if}>
                            <a href="{$_url}pages/Privacy_Policy">Privacy Policy</a>
                        </li>
                        <li {if $_routes[1] eq 'Terms_and_Conditions'}class="active" {/if}>
                            <a href="{$_url}pages/Terms_and_Conditions">Terms and Conditions</a>
                        </li>
                        {$_MENU_PAGES}
                    </ul>
                </li>
            {/if}
            {$_MENU_AFTER_PAGES}
            <li class="{if $_system_menu eq 'settings' || $_system_menu eq 'paymentgateway'}active{/if} treeview">
                <a href="#">
                    <i class="ion ion-gear-a"></i> <span>{Lang::T('Settings')}</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li {if $_routes[1] eq 'app'}class="active" {/if}>
                            <a href="{$_url}settings/app">{Lang::T('General Settings')}</a>
                        </li>
                        <li {if $_routes[1] eq 'localisation'}class="active" {/if}>
                            <a href="{$_url}settings/localisation">{Lang::T('Localisation')}</a>
                        </li>
                        <li {if $_routes[1] eq 'notifications'}class="active" {/if}>
                            <a href="{$_url}settings/notifications">{Lang::T('User Notification')}</a>
                        </li>
                    {/if}
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin','Agent'])}
                        <li {if $_routes[1] eq 'users'}class="active" {/if}>
                            <a href="{$_url}settings/users">{Lang::T('Administrator Users')}</a>
                        </li>
                    {/if}
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li {if $_routes[1] eq 'dbstatus'}class="active" {/if}>
                            <a href="{$_url}settings/dbstatus">{Lang::T('Backup/Restore')}</a>
                        </li>
                        <li {if $_system_menu eq 'paymentgateway'}class="active" {/if}>
                            <a href="{$_url}paymentgateway">
                                <span class="text">{Lang::T('Payment Gateway')}</span>
                            </a>
                        </li>
                        {$_MENU_SETTINGS}
                    {/if}
                </ul>
            </li>
            <li class="{if $_system_menu eq 'Router Monitor'}active{/if}">
                <a href="{$_url}plugin/mikrotik_ui">
                    <i class="ion ion-wifi"></i>
                    <span class="text">{Lang::T('Router Monitor')}</span>
                </a>
            </li>
{$_MENU_AFTER_SETTINGS}
<li class="{if $_system_menu eq 'logs'}active{/if} treeview">
    <a href="#">
        <i class="ion ion-clock"></i> <span>{Lang::T('Logs')}</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li {if $_routes[1] eq 'list'}class="active" {/if}>
            <a href="{$_url}logs/freeispradius">FreeIspRadius</a>
        </li>
        {if $_c['radius_enable']}
            <li {if $_routes[1] eq 'radius'}class="active" {/if}>
                <a href="{$_url}logs/radius">Radius</a>
            </li>
        {/if}
    </ul>
    {$_MENU_LOGS}
</li>
{$_MENU_AFTER_LOGS}
<li {if $_system_menu eq 'community'}class="active" {/if}>
    <a href="{$_url}community">
        <i class="ion ion-chatboxes"></i>
        <span class="text">{Lang::T('Community')}</span>
    </a>
</li>
{$_MENU_AFTER_COMMUNITY}

</ul>
</section>
</aside>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    {$_title}
                </h1>
            </section>

            <section class="content">
{if isset($notify)}
                    <script>
                        // Display SweetAlert toast notification
                        Swal.fire({
                            icon: '{if $notify_t == "s"}success{else}error{/if}',
                            title: '{$notify}',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    </script>
{/if}
