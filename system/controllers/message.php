<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', Lang::T('Send Message'));
$ui->assign('_system_menu', 'message');

$action = $routes['1'];
$ui->assign('_admin', $admin);

if (empty($action)) {
    $action = 'send';
}

switch ($action) {
    case 'send':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        $select2_customer = <<<EOT
<script>
document.addEventListener("DOMContentLoaded", function(event) {
    $('#personSelect').select2({
        theme: "bootstrap",
        ajax: {
            url: function(params) {
                if(params.term != undefined){
                    return './index.php?_route=autoload/customer_select2&s='+params.term;
                }else{
                    return './index.php?_route=autoload/customer_select2';
                }
            }
        }
    });
});
</script>
EOT;
        if (isset($routes['2']) && !empty($routes['2'])) {
            $ui->assign('cust', ORM::for_table('tbl_customers')->find_one($routes['2']));
        }
        $id = $routes['2'];
        $ui->assign('id', $id);
        $ui->assign('xfooter', $select2_customer);
        $ui->display('message.tpl');
        break;

        

    case 'send-post':
        // Check user permissions
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        // Get form data
        $id_customer = $_POST['id_customer'];
        $message = $_POST['message'];
        $via = $_POST['via'];

        // Check if fields are empty
        if ($id_customer == '' or $message == '' or $via == '') {
            r2(U . 'message/send', 'e', Lang::T('All field is required'));
        } else {
            // Get customer details from the database
            $c = ORM::for_table('tbl_customers')->find_one($id_customer);

            // Replace placeholders in the message with actual values
            $message = str_replace('[[name]]', $c['fullname'], $message);
            $message = str_replace('[[user_name]]', $c['username'], $message);
            $message = str_replace('[[phone]]', $c['phonenumber'], $message);
            $message = str_replace('[[company_name]]', $config['CompanyName'], $message);


            //Send the message
            if ($via == 'sms' || $via == 'both') {
                $smsSent = Message::sendSMS($c['phonenumber'], $message);
            }

            if ($via == 'wa' || $via == 'both') {
                $waSent = Message::sendWhatsapp($c['phonenumber'], $message);
            }

            if (isset($smsSent) || isset($waSent)) {
                r2(U . 'message/send', 's', Lang::T('Message Sent Successfully'));
            } else {
                r2(U . 'message/send', 'e', Lang::T('Failed to send message'));
            }
        }
        break;

    case 'send_bulk':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        // Get form data
        $group = $_POST['group'];
        $message = $_POST['message'];
        $via = $_POST['via'];
        $test = isset($_POST['test']) && $_POST['test'] === 'on' ? 'yes' : 'no';
        $batch = $_POST['batch'];
        $delay = $_POST['delay'];

        // Initialize counters
        $totalSMSSent = 0;
        $totalSMSFailed = 0;
        $totalWhatsappSent = 0;
        $totalWhatsappFailed = 0;
        $batchStatus = [];

        if (_req('send') == 'now') {
            // Check if fields are empty
            if ($group == '' || $message == '' || $via == '') {
                r2(U . 'message/send_bulk', 'e', Lang::T('All fields are required'));
            } else {
                // Get customer details from the database based on the selected group
                if ($group == 'all') {
                    $customers = ORM::for_table('tbl_customers')->find_many()->as_array();
                } elseif ($group == 'new') {
                    // Get customers created just a month ago
                    $customers = ORM::for_table('tbl_customers')->where_raw("DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)")->find_many()->as_array();
                } elseif ($group == 'expired') {
                    // Get expired user recharges where status is 'off'
                    $expired = ORM::for_table('tbl_user_recharges')->where('status', 'off')->find_many();
                    $customer_ids = [];
                    foreach ($expired as $recharge) {
                        $customer_ids[] = $recharge->customer_id;
                    }
                    $customers = ORM::for_table('tbl_customers')->where_in('id', $customer_ids)->find_many()->as_array();
                } elseif ($group == 'active') {
                    // Get active user recharges where status is 'on'
                    $active = ORM::for_table('tbl_user_recharges')->where('status', 'on')->find_many();
                    $customer_ids = [];
                    foreach ($active as $recharge) {
                        $customer_ids[] = $recharge->customer_id;
                    }
                    $customers = ORM::for_table('tbl_customers')->where_in('id', $customer_ids)->find_many()->as_array();
                }

                // Set the batch size
                $batchSize = $batch;

                // Calculate the number of batches
                $totalCustomers = count($customers);
                $totalBatches = ceil($totalCustomers / $batchSize);

                // Loop through batches
                for ($batchIndex = 0; $batchIndex < $totalBatches; $batchIndex++) {
                    // Get the starting and ending index for the current batch
                    $start = $batchIndex * $batchSize;
                    $end = min(($batchIndex + 1) * $batchSize, $totalCustomers);
                    $batchCustomers = array_slice($customers, $start, $end - $start);

                    // Loop through customers in the current batch and send messages
                    foreach ($batchCustomers as $customer) {
                        // Create a copy of the original message for each customer and save it as currentMessage
                        $currentMessage = $message;
                        $currentMessage = str_replace('[[name]]', $customer['fullname'], $currentMessage);
                        $currentMessage = str_replace('[[user_name]]', $customer['username'], $currentMessage);
                        $currentMessage = str_replace('[[phone]]', $customer['phonenumber'], $currentMessage);
                        $currentMessage = str_replace('[[company_name]]', $config['CompanyName'], $currentMessage);

                        // Send the message based on the selected method
                        if ($test === 'yes') {
                            // Only for testing, do not send messages to customers
                            $batchStatus[] = [
                                'name' => $customer['fullname'],
                                'phone' => $customer['phonenumber'],
                                'message' => $currentMessage,
                                'status' => 'Test Mode - Message not sent'
                            ];
                        } else {
                            // Send the actual messages
                            if ($via == 'sms' || $via == 'both') {
                                $smsSent = Message::sendSMS($customer['phonenumber'], $currentMessage);
                                if ($smsSent) {
                                    $totalSMSSent++;
                                    $batchStatus[] = [
                                        'name' => $customer['fullname'],
                                        'phone' => $customer['phonenumber'],
                                        'message' => $currentMessage,
                                        'status' => 'SMS Message Sent'
                                    ];
                                } else {
                                    $totalSMSFailed++;
                                    $batchStatus[] = [
                                        'name' => $customer['fullname'],
                                        'phone' => $customer['phonenumber'],
                                        'message' => $currentMessage,
                                        'status' => 'SMS Message Failed'
                                    ];
                                }
                            }

                            if ($via == 'wa' || $via == 'both') {
                                $waSent = Message::sendWhatsapp($customer['phonenumber'], $currentMessage);
                                if ($waSent) {
                                    $totalWhatsappSent++;
                                    $batchStatus[] = [
                                        'name' => $customer['fullname'],
                                        'phone' => $customer['phonenumber'],
                                        'message' => $currentMessage,
                                        'status' => 'WhatsApp Message Sent'
                                    ];
                                } else {
                                    $totalWhatsappFailed++;
                                    $batchStatus[] = [
                                        'name' => $customer['fullname'],
                                        'phone' => $customer['phonenumber'],
                                        'message' => $currentMessage,
                                        'status' => 'WhatsApp Message Failed'
                                    ];
                                }
                            }
                        }
                    }

                    // Introduce a delay between each batch
                    if ($batchIndex < $totalBatches - 1) {
                        sleep($delay);
                    }
                }
            }
        }
        $ui->assign('batchStatus', $batchStatus);
        $ui->assign('totalSMSSent', $totalSMSSent);
        $ui->assign('totalSMSFailed', $totalSMSFailed);
        $ui->assign('totalWhatsappSent', $totalWhatsappSent);
        $ui->assign('totalWhatsappFailed', $totalWhatsappFailed);
        $ui->display('message-bulk.tpl');
        break;

        case 'schedule':
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
                _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
            }
    
            // Retrieve customers
            $ui->assign('customers', ORM::for_table('tbl_customers')->find_many());
    
            // Assign the scheduling form script
            $ui->assign('xfooter', $select2_customer);
            $ui->display('schedule.tpl');
            break;
    
            case 'schedule-post':
                if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
                    _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
                }
            
                // Get form data
                $group = $_POST['group'];
                $message = $_POST['message'];
                $via = $_POST['via'];
                $schedule_time = $_POST['schedule_time'];
                $batch = $_POST['batch'];
                $delay = $_POST['delay'];
            
                // Debugging: Log the received form data
                error_log("Form Data: Group = $group, Message = $message, Via = $via, Schedule Time = $schedule_time, Batch = $batch, Delay = $delay");
            
                // Check if fields are empty
                if ($group == '' || $message == '' || $via == '' || $schedule_time == '') {
                    r2(U . 'message/schedule', 'e', Lang::T('All fields are required'));
                } else {
                    // Save the schedule in the database
                    $schedule = ORM::for_table('tbl_scheduled_messages')->create();
                    $schedule->group = $group;
                    $schedule->message = $message;
                    $schedule->via = $via;
                    $schedule->schedule_time = $schedule_time;
                    $schedule->batch = $batch;
                    $schedule->delay = $delay;
                    $schedule->status = 'pending';
                    $schedule->save();
            
                    // Debugging: Check if the schedule was saved
                    if ($schedule->id()) {
                        error_log("Schedule Saved: ID = " . $schedule->id());
                    } else {
                        error_log("Failed to save schedule.");
                    }
            
                    r2(U . 'message/schedule', 's', Lang::T('Message Scheduled Successfully'));
                }
                break;
            

        case 'specific':
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
                _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
            }
            
            // Retrieve routers
            $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
            $ui->assign('routers', $routers);
            
            $ui->display('router-specific.tpl');
            break;
        case 'specific-post':
            // Check user permissions
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
                _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
            }
        
            // Get form data
            $routerId = $_POST['router'];
            $message = $_POST['message'];
            $via = $_POST['via'];
            $test = isset($_POST['test']) && $_POST['test'] === 'on' ? 'yes' : 'no';
            $batch = $_POST['batch'];
            $delay = $_POST['delay'];
        
            // Initialize counters
            $totalSMSSent = 0;
            $totalSMSFailed = 0;
            $totalWhatsappSent = 0;
            $totalWhatsappFailed = 0;
            $batchStatus = [];
        
            // Retrieve customers associated with the selected router
            $customers = ORM::for_table('tbl_customers')
                ->where('router_id', $routerId)
                ->find_many()
                ->as_array();
        
            // Set the batch size
            $batchSize = $batch;
        
            // Calculate the number of batches
            $totalCustomers = count($customers);
            $totalBatches = ceil($totalCustomers / $batchSize);
        
            // Loop through batches
            for ($batchIndex = 0; $batchIndex < $totalBatches; $batchIndex++) {
                // Get the starting and ending index for the current batch
                $start = $batchIndex * $batchSize;
                $end = min(($batchIndex + 1) * $batchSize, $totalCustomers);
                $batchCustomers = array_slice($customers, $start, $end - $start);
        
                // Loop through customers in the current batch and send messages
                foreach ($batchCustomers as $customer) {
                    // Create a copy of the original message for each customer and save it as currentMessage
                    $currentMessage = $message;
                    $currentMessage = str_replace('[[name]]', $customer['fullname'], $currentMessage);
                    $currentMessage = str_replace('[[user_name]]', $customer['username'], $currentMessage);
                    $currentMessage = str_replace('[[phone]]', $customer['phonenumber'], $currentMessage);
                    $currentMessage = str_replace('[[company_name]]', $config['CompanyName'], $currentMessage);
        
                    // Send the message based on the selected method
                    if ($test === 'yes') {
                        // Only for testing, do not send messages to customers
                        $batchStatus[] = [
                            'name' => $customer['fullname'],
                            'phone' => $customer['phonenumber'],
                            'message' => $currentMessage,
                            'status' => 'Test Mode - Message not sent'
                        ];
                    } else {
                        // Send the actual messages
                        if ($via == 'sms' || $via == 'both') {
                            $smsSent = Message::sendSMS($customer['phonenumber'], $currentMessage);
                            if ($smsSent) {
                                $totalSMSSent++;
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'SMS Message Sent'
                                ];
                            } else {
                                $totalSMSFailed++;
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'SMS Message Failed'
                                ];
                            }
                        }
        
                        if ($via == 'wa' || $via == 'both') {
                            $waSent = Message::sendWhatsapp($customer['phonenumber'], $currentMessage);
                            if ($waSent) {
                                $totalWhatsappSent++;
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'WhatsApp Message Sent'
                                ];
                            } else {
                                $totalWhatsappFailed++;
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'WhatsApp Message Failed'
                                ];
                            }
                        }
                    }
                }
        
                // Introduce a delay between each batch
                if ($batchIndex < $totalBatches - 1) {
                    sleep($delay);
                }
            }
        
            $ui->assign('batchStatus', $batchStatus);
            $ui->assign('totalSMSSent', $totalSMSSent);
            $ui->assign('totalSMSFailed', $totalSMSFailed);
            $ui->assign('totalWhatsappSent', $totalWhatsappSent);
            $ui->assign('totalWhatsappFailed', $totalWhatsappFailed);
            $ui->display('router-specific.tpl');
            break;

    default:
        r2(U . 'message/send_sms', 'e', 'action not defined');
}