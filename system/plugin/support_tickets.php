<?php
//session_start();
// Check if the Support_tickets and Support_tickets_replies tables exist
$db = ORM::getDb();
$tableExists = false;
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('tbl_support_tickets', $tables) && in_array('tbl_support_tickets_replies', $tables)) {
    $tableExists = true;
}


if (!$tableExists) {
    // Create the Support_tickets table if it doesn't exist
    if (!in_array('tbl_support_tickets', $tables)) {
        try {
            $db->exec("
        CREATE TABLE `tbl_support_tickets` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `ticket_id` CHAR(36) NOT NULL,
          `title` varchar(255) NOT NULL,
          `message` text NOT NULL,
          `userid` int(11) NOT NULL,
          `report` text,
          `issue` text,
          `custom` text,
          `department` text NOT NULL,
          `priority` text NOT NULL,
          `created` datetime NOT NULL DEFAULT current_timestamp(),
          `created_by` text NOT NULL,
          `updated_by` text NULL,
          `last_updated` datetime NOT NULL DEFAULT current_timestamp(),
          `status` enum('open','closed','in_progress','resolved') NOT NULL DEFAULT 'open',
          `read_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Unread, 1 = Read',
          `delete_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Delete',
          `attachment_id` varchar(255) DEFAULT NULL,
          `attachment_path` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unique_ticket_id` (`ticket_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        } catch (PDOException $e) {
            // Handle the exception or log the error message
            echo "Error creating tbl_support_tickets table: " . $e->getMessage();
        }
    }

    // Create the Support_tickets_replies table if it doesn't exist
    if (!in_array('tbl_support_tickets_replies', $tables)) {
        try {
            $db->exec("
            CREATE TABLE `tbl_support_tickets_replies` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `ticket_id` CHAR(36) NOT NULL,
              `reply_message` text NOT NULL,
              `userid` text NOT NULL,
              `reply_by` text NOT NULL,
              `admin_name` text NOT NULL,
              `delete_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Delete',
              `created` datetime NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`),
              FOREIGN KEY (`ticket_id`) REFERENCES `tbl_support_tickets` (`ticket_id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        } catch (PDOException $e) {
            // Handle the exception or log the error message
            echo "Error creating tbl_support_tickets_replies table: " . $e->getMessage();
        }
    }
}

$newTicket = ORM::for_table('tbl_support_tickets')
    ->where('read_flag', 0)
    ->where('delete_flag', 0)
    ->count();

if ($newTicket) {
    //if is new ticket submitted there should be notification on dashboard
    register_menu(" Support Ticket", true, "support_tickets", 'AFTER_REPORTS', 'fa fa-envelope-o', $newTicket, "red");
} else {
    register_menu(" Support Ticket", true, "support_tickets", 'AFTER_REPORTS', 'fa fa-envelope-o');
}
$configFile = 'system/plugin/support_tickets_config.json';
$defaultConfig = array(
    'enable' => 'enable',
    'ucp' => 'enable',
    'notification' => 'enable',
    'type' => 'both',
    'admin' => 'enable'
);

if (!file_exists($configFile)) {
    $configContents = json_encode($defaultConfig, JSON_PRETTY_PRINT);
    file_put_contents($configFile, $configContents);
}

$settings = json_decode(file_get_contents($configFile), true);

// Check if the 'ucp' setting is set to 'enable'
if ($settings['ucp'] === 'enable') {
    register_menu("Support Ticket", false, "support_tickets_clients", '', '', "", "");
}
function support_tickets()
{
    global $ui, $config, $routes;
    _admin();
    $ui->assign('_title', 'Support Ticket');
    $ui->assign('_system_menu', '');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    if ($admin['user_type'] != 'SuperAdmin' && $admin['user_type'] != 'Admin' && $admin['user_type'] != 'Sales') {
        r2(U . "dashboard", 'e', Lang::T("You Do Not Have Access"));
    }

    $configFile = 'system/plugin/support_tickets_config.json';
    $settings = json_decode(file_get_contents($configFile), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate the CSRF token
        $csrfToken = $_POST['csrf_token'];
        if (!support_tickets_validate_csrf_token($csrfToken)) {
            r2(U . 'plugin/support_tickets', 'e', Lang::T("Invalid CSRF token"));
            return;
        }

        // Access form data
        $id_customer = $_POST['id_customer'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $priority = $_POST['priority'];
        $department = $_POST['department'];
        //$report = $_POST['report'];
        //$issue = $_POST['issue'];
        //$custom = $_POST['custom'];
        $created_by = $_POST['created_by'];
        $attachment = $_FILES['attachment'];
        //lets find specific customer using ID
        $c = ORM::for_table('tbl_customers')->where('id', $id_customer)->find_one();



        // Generate a UUID with "#" prefix
        $ticketId = support_tickets_generateTicketId();

        // Initialize attachment variables
        $attachmentId = null;
        $attachmentPath = null;

        // Process the attachment (if provided)
        if (!empty($attachment) && $attachment['error'] === UPLOAD_ERR_OK) {
            // Generate a unique ID for the attachment
            $attachmentId = uniqid();

            // Get the file extension
            $extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);

            // Construct the unique filename
            $attachmentFilename = $attachmentId . '.' . $extension;
            //uoload folder
            $uploadedFolder = 'system/uploads/ticket_attachment/';
            //create folder if not exists
            if (!file_exists($uploadedFolder)) {
                mkdir($uploadedFolder);
            }
            // Move the uploaded file to a desired location with the unique filename
            $uploadedFilePath = $uploadedFolder . $attachmentFilename;
            move_uploaded_file($attachment['tmp_name'], $uploadedFilePath);
            $attachmentPath = $uploadedFilePath;
        }

        // Insert the form data into the tbl_support_tickets table ORM
        $d = ORM::for_table('tbl_support_tickets')->create();
        $d->set('ticket_id', $ticketId);
        $d->set('title', $subject);
        $d->set('message', $message);
        $d->set('priority', $priority);
        $d->set('userid', $id_customer);
        //$d->set('report', $report);
        //$d->set('issue', $issue);
        //$d->set('custom', $custom);
        $d->set('department', $department);
        $d->set('created_by', $created_by);
        $d->set('attachment_id', $attachmentId);
        $d->set('attachment_path', $attachmentPath);
        $d->save();

        $phonenumber = $c['phonenumber'];
        $fullname = $c['fullname'];
        // let translated the message using the Lang::T function
        $message = Lang::T("Hi") . " " . $fullname . ", \n" . Lang::T("you have a new ticket number") . " " . $ticketId . " " . Lang::T("open by admin. Please check your app. \n Thank you.");

        if ($settings['notification'] === 'enable') {
            // send sms or notification to user
            if ($settings['type'] === 'sms') {
                Message::sendSMS($phonenumber, $message);
            } elseif ($settings['type'] === 'whatsapp') {
                Message::sendWhatsapp($phonenumber, $message);
            } elseif ($settings['type'] === 'both') {
                Message::sendSMS($phonenumber, $message);
                Message::sendWhatsapp($phonenumber, $message);
            }
        }
        // Redirect to the Support Ticket page
        r2(U . 'plugin/support_tickets', 's', Lang::T("Ticket Submitted Successfully"));
        // ... Add your code here to redirect the user after the form submission ...
    }

    $c = ORM::for_table('tbl_customers')->find_many();
    $customerData = [];

    foreach ($c as $customer) {
        $customerData[] = [
            'id' => $customer->id,
            'name' => $customer->fullname,
            'info' => Lang::T("Username") . ": " . $customer->username .  " - "  . Lang::T("Full Name") . ": " . $customer->fullname . " - "  . Lang::T("Email") . ": " . $customer->email . " - "  . Lang::T("Phone") . ": " . $customer->phonenumber . " - "  . Lang::T("Service Type") . ": " . $customer->service_type,
        ];
    }

    // Define the number of tickets per page and the current page number
    $perPage = 5; // Number of tickets per page
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number

    // Calculate the offset for the database query
    $offset = ($page - 1) * $perPage;

    $supportTickets = ORM::for_table('tbl_support_tickets')
        ->order_by_desc('created')
        ->where('delete_flag', '0')
        ->limit($perPage)
        ->offset($offset)
        ->find_many();

    foreach ($supportTickets as $ticket) {
        $ticket->formattedCreated = date('D, d M Y h:i A', strtotime($ticket->created));
        $ticket->formattedLastUpdated = date('D, d M Y h:i A', strtotime($ticket->last_updated));
    }

    // Fetch the total count of tickets
    $totalActiveTickets = ORM::for_table('tbl_support_tickets')
        ->where('delete_flag', 0)
        ->count();

    // Calculate the total number of pages
    $totalPages = ceil($totalActiveTickets / $perPage);


    // Execute the query and fetch the count
    $unreadTicket = ORM::for_table('tbl_support_tickets')
        ->where('read_flag', 0)
        ->where('delete_flag', 0)
        ->count();

    $inProgressTicketCount = ORM::for_table('tbl_support_tickets')
        ->where('status', 'in_progress')
        ->where('delete_flag', 0)
        ->count();
    $openTicketCount = ORM::for_table('tbl_support_tickets')
        ->where('status', 'open')
        ->where('delete_flag', 0)
        ->count();
    $resolvedTicketCount = ORM::for_table('tbl_support_tickets')
        ->where('status', 'resolved')
        ->where('delete_flag', 0)
        ->count();
    $closedTicketCount = ORM::for_table('tbl_support_tickets')
        ->where('status', 'closed')
        ->where('delete_flag', 0)
        ->count();

    $highPriorityCount = ORM::for_table('tbl_support_tickets')
        ->where('priority', 'High')
        ->where('delete_flag', 0)
        ->count();

    $mediumPriorityCount = ORM::for_table('tbl_support_tickets')
        ->where('priority', 'Medium')
        ->where('delete_flag', 0)
        ->count();

    $lowPriorityCount = ORM::for_table('tbl_support_tickets')
        ->where('priority', 'Low')
        ->where('delete_flag', 0)
        ->count();

    $trashTicketCount = ORM::for_table('tbl_support_tickets')
        ->where('delete_flag', 1)
        ->count();

    if ($admin['user_type'] === 'SuperAdmin' || $admin['user_type'] === 'Admin') {
        $buttonSettings = '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#settings"><i class="fa fa-cog" aria-hidden="true"></i></button>    ';
    }

    $csrfToken = support_tickets_generate_csrf_token();
    $ui->assign('csrfToken', $csrfToken);
    $ui->assign('settings', $settings);
    $ui->assign('buttonSettings', $buttonSettings);
    $ui->assign('highPriorityCount', $highPriorityCount);
    $ui->assign('mediumPriorityCount', $mediumPriorityCount);
    $ui->assign('lowPriorityCount', $lowPriorityCount);
    $ui->assign('inProgressTicketCount', $inProgressTicketCount);
    $ui->assign('resolvedTicketCount', $resolvedTicketCount);
    $ui->assign('closedTicketCount', $closedTicketCount);
    $ui->assign('trashTicketCount', $trashTicketCount);
    $ui->assign('openTicketCount', $openTicketCount);
    $ui->assign('newTicketCount', $unreadTicket);
    $ui->assign('sortedTickets', $supportTickets);
    $ui->assign('currentPage', $page);
    $ui->assign('totalPages', $totalPages);
    $ui->assign('totalActiveTickets', $totalActiveTickets);
    $ui->assign('customers', $customerData);
    $ui->display('support_tickets.tpl');
}

// Generate a UUID with "#" prefix
function support_tickets_generateTicketId()
{
    if (function_exists('com_create_guid') === true) {
        $uuid = trim(com_create_guid(), '{}');
    } else {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // Truncate or generate a random 9-digit number
    $randomNumber = sprintf('%09d', mt_rand(0, 999999999));

    $ticketId = substr($randomNumber, 0, 9);
    return $ticketId;
}


function support_tickets_clients()
{
    global $ui, $routes;
    _auth();
    $ui->assign('_title', 'Support Ticket');
    $ui->assign('_system_menu', 'support_tickets_clients');
    $user = User::_info();
    $ui->assign('_user', $user);

    $configFile = 'system/plugin/support_tickets_config.json';
    $settings = json_decode(file_get_contents($configFile), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate the CSRF token
        $csrfToken = $_POST['csrf_token'];
        if (!support_tickets_validate_csrf_token($csrfToken)) {
            r2(U . 'plugin/support_tickets_clients', 'e', Lang::T("Invalid CSRF token"));
            return;
        }

        // Access form data
        $id_customer = $_POST['id_customer'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $priority = $_POST['priority'];
        //$report = $_POST['report'];
        //$issue = $_POST['issue'];
        //$custom = $_POST['custom'];
        $department = $_POST['department'];
        $created_by = $_POST['created_by'];
        $attachment = $_FILES['attachment'];

        // Generate a UUID with "#" prefix
        $ticketId = support_tickets_generateTicketId();

        // Initialize attachment variables
        $attachmentId = null;
        $attachmentPath = null;

        // Process the attachment (if provided)
        if (!empty($attachment) && $attachment['error'] === UPLOAD_ERR_OK) {
            // Generate a unique ID for the attachment
            $attachmentId = uniqid();

            // Get the file extension
            $extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);

            // Construct the unique filename
            $attachmentFilename = $attachmentId . '.' . $extension;

            // Move the uploaded file to a desired location with the unique filename
            $uploadedFilePath = 'system/uploads/ticket_attachment/' . $attachmentFilename;
            move_uploaded_file($attachment['tmp_name'], $uploadedFilePath);
            $attachmentPath = $uploadedFilePath;
        }

        // Insert the form data into the tbl_support_tickets table using ORM
        $d = ORM::for_table('tbl_support_tickets')->create();
        $d->set('ticket_id', $ticketId);
        $d->set('title', $subject);
        $d->set('message', $message);
        $d->set('priority', $priority);
        $d->set('userid', $id_customer);
        //$d->set('report', $report);
        //$d->set('issue', $issue);
        //$d->set('custom', $custom);
        $d->set('department', $department);
        $d->set('created_by', $created_by);
        $d->set('attachment_id', $attachmentId);
        $d->set('attachment_path', $attachmentPath);
        $d->save();

        //lets find specific customer using ID
        $c = ORM::for_table('tbl_customers')->where('id', $id_customer)->find_one();
        $fullname = $c['fullname'];
        //send Notification to admin using the Lang::T function
        $message = Lang::T("Hi Admin") . ", \n" . $fullname . " " . Lang::T("Have submitted a new Ticket with ticket number") . " [" . $ticketId . "]\n" . Lang::T("Subject") . ": [" . $subject . "]\n" . Lang::T("Kindly Login To Dashboard.");
        if ($settings['admin'] === 'enable') {
            Message::sendTelegram($message);
        }
        // Redirect the user to the support tickets page
        r2(U . 'plugin/support_tickets_clients', 's', "Ticket Submitted Successfully");
        // ... Add your code here to redirect the user after the form submission ...
    }

    // Define the number of tickets per page and the current page number
    $perPage = 5; // Number of tickets per page
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number

    // Calculate the offset for the database query
    $offset = ($page - 1) * $perPage;




    $id = $_SESSION['uid'];
    // Retrieve the tickets for the current page with sorting and filtering by user ID
    $supportTickets = ORM::for_table('tbl_support_tickets')
        ->where('userid', $id) // Assuming you have the user ID stored in the $userId variable
        ->where('delete_flag', 0) // Add the condition for delete_flag = 0
        ->order_by_desc('created')
        ->limit($perPage)
        ->offset($offset)
        ->find_many();

    foreach ($supportTickets as $ticket) {
        $ticket->formattedCreated = date('D, d M Y h:i A', strtotime($ticket->created));
        $ticket->formattedLastUpdated = date('D, d M Y h:i A', strtotime($ticket->last_updated));
    }


    // Fetch the total count of tickets
    $totalTickets = ORM::for_table('tbl_support_tickets')
        ->where('userid', $id)
        ->where('delete_flag', 0)
        ->count();

    // Calculate the total number of pages
    $totalPages = ceil($totalTickets / $perPage);

    $data = ORM::for_table('tbl_support_tickets_replies')
        ->order_by_asc('created')
        ->where('delete_flag', 0)
        ->find_many();

    $csrfToken = support_tickets_generate_csrf_token();
    $ui->assign('csrfToken', $csrfToken);
    $ui->assign('replies', $data);
    $ui->assign('sortedTickets', $supportTickets);
    $ui->assign('currentPage', $page);
    $ui->assign('totalPages', $totalPages);
    $ui->assign('totalTickets', $totalTickets);
    $ui->display('support_tickets_clients.tpl');
}


function support_tickets_update_status()
{
    global $ui;
    _admin();
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    if ($admin['user_type'] != 'SuperAdmin' && $admin['user_type'] != 'Admin' && $admin['user_type'] != 'Sales') {
        r2(U . "dashboard", 'e', Lang::T("You Do Not Have Access"));
    }

    // Retrieve the ticket ID, new status, and CSRF token from the URL parameters
    $ticketId = $_GET['ticketId'];
    $newStatus = $_GET['newStatus'];
    $updatedBy = $_GET['updatedBy'];
    $csrfToken = $_GET['csrf_token'];
    $delete = $_GET['delete'];

    // Validate the CSRF token
    if (!support_tickets_validate_csrf_token($csrfToken)) {
        r2(U . 'plugin/support_tickets', 'e', Lang::T("Invalid CSRF token"));
        return;
    }

    $configFile = 'system/plugin/support_tickets_config.json';
    $settings = json_decode(file_get_contents($configFile), true);
    // Update the ticket status in your database using Idiorm
    // Example: Update the ticket status in a "tbl_support_tickets" table
    $result = ORM::for_table('tbl_support_tickets')
        ->where('ticket_id', $ticketId)
        ->find_one();
    $id_customer = $result->userid;
    $currentStatus = $result->status;
    //lets find specific customer using ID
    $c = ORM::for_table('tbl_customers')->where('id', $id_customer)->find_one();
    $phonenumber = $c['phonenumber'];
    $fullname = $c['fullname'];

    if ($result) {
        $result->set('status', $newStatus)
            ->set('updated_by', $updatedBy)
            ->set('last_updated', date('Y-m-d H:i:s'));

        if ($delete === 'trash') {
            $result->set('delete_flag', 1);
        }

        $result->save();

        // let translated the message using the Lang::T function
        $message = Lang::T("Hi") . " " . $fullname . ", \n" . Lang::T("your ticket number") . " " . $ticketId . " " . Lang::T("was change from ") . $currentStatus .  Lang::T(" to ") . $newStatus . "\n" . Lang::T("please check your app for remarks.\nThank you");

        if ($settings['notification'] === 'enable') {
            // send sms or notification to user
            if ($settings['type'] === 'sms') {
                Message::sendSMS($phonenumber, $message);
            } elseif ($settings['type'] === 'whatsapp') {
                Message::sendWhatsapp($phonenumber, $message);
            } elseif ($settings['type'] === 'both') {
                Message::sendSMS($phonenumber, $message);
                Message::sendWhatsapp($phonenumber, $message);
            }
        }

        r2(U . 'plugin/support_tickets', 's', Lang::T("Ticket Status Successfully Changed"));
    } else {
        r2(U . 'plugin/support_tickets_clients', 's', Lang::T("Ticket not found"));
    }
}

function support_tickets_admin_reply()
{
    // Retrieve the form data
    $ticketId = $_POST['ticketId'];
    $userId = $_POST['userId'];
    $reply = $_POST['reply'];
    $reply_by = $_POST['reply_by'];
    $admin_name = $_POST['admin_name'];

    // Retrieve the CSRF token from the request
    $csrfToken = $_POST['csrf_token'];

    // Validate the CSRF token
    if (!support_tickets_validate_csrf_token($csrfToken)) {
        r2(U . 'plugin/support_tickets', 'e', Lang::T("Invalid CSRF token"));
        return;
    }

    // Check if the ticket exists
    $ticketExists = ORM::for_table('tbl_support_tickets')->where('ticket_id', $ticketId)->count();

    if ($ticketExists) {
        // Insert the ticket reply into the database
        $replyData = [
            'ticket_id' => $ticketId,
            'reply_message' => $reply,
            'userid' => $userId,
            'reply_by' => $reply_by,
            'admin_name' => $admin_name,
            'created' => date('Y-m-d H:i:s')
        ];

        $ticketReply = ORM::for_table('tbl_support_tickets_replies')->create($replyData);
        $result = $ticketReply->save();

        if ($result) {
            r2(U . 'plugin/support_tickets_view/' . $ticketId, 's', Lang::T("Ticket reply submitted successfully"));
        } else {
            r2(U . 'plugin/support_tickets_view/' . $ticketId, 'e', Lang::T("Failed to submit ticket reply"));
        }
    } else {
        r2(U . 'plugin/support_tickets', 'e', Lang::T("Invalid ticket ID") . ":" . $ticketId);
    }
}

function support_tickets_clients_reply()
{
    // Retrieve the form data
    $ticketId = $_POST['ticketId'];
    $userId = $_POST['userId'];
    $reply = $_POST['reply'];
    $reply_by = $_POST['reply_by'];
    $admin_name = $_POST['admin_name'];

    // Retrieve the CSRF token from the request
    $csrfToken = $_POST['csrf_token'];

    // Validate the CSRF token
    if (!support_tickets_validate_csrf_token($csrfToken)) {
        r2(U . 'plugin/support_tickets_clients_view/'.$ticketId, 'e', Lang::T("Invalid CSRF token"));
        return;
    }

    // Check if the ticket exists
    $ticketExists = ORM::for_table('tbl_support_tickets')->where('ticket_id', $ticketId)->count();

    if ($ticketExists) {
        // Insert the ticket reply into the database
        $replyData = [
            'ticket_id' => $ticketId,
            'reply_message' => $reply,
            'userid' => $userId,
            'reply_by' => $reply_by,
            'admin_name' => $admin_name,
            'created' => date('Y-m-d H:i:s')
        ];


        $ticketReply = ORM::for_table('tbl_support_tickets_replies')->create($replyData);
        $result = $ticketReply->save();

        if ($result) {
            r2(U . 'plugin/support_tickets_clients_view/' . $ticketId, 's', Lang::T("Ticket reply submitted successfully"));
        } else {
            r2(U . 'plugin/support_tickets_clients_view/' . $ticketId, 'e', Lang::T("Failed to submit ticket reply"));
        }
    } else {
        r2(U . 'plugin/support_tickets_clients', 'e', Lang::T("Invalid ticket ID") . ":" . $ticketId);
    }
}

// Generate CSRF token
function support_tickets_generate_csrf_token()
{
    $tokenExpiration = time() + 3600; // Token expiry set to 1 hour
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token_expiration'] < time()) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expiration'] = $tokenExpiration;
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function support_tickets_validate_csrf_token($token)
{
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token || $_SESSION['csrf_token_expiration'] < time()) {
        // Invalid or expired CSRF token
        return false;
    }

    // Reset the CSRF token after successful validation to prevent reuse
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiration'] = time() + 3600; // Set new expiration time

    return true;
}


function support_tickets_view()
{
    global $ui, $routes;

    _admin();
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_title', 'Support Ticket Details');
    $ui->assign('_system_menu', '');
    $ticketId = $routes['2'];


    if ($admin['user_type'] != 'SuperAdmin' && $admin['user_type'] != 'Admin' && $admin['user_type'] != 'Sales') {
        r2(U . "dashboard", 'e', Lang::T("You Do Not Have Access"));
    }

    // Retrieve the ticket ID from the AJAX request
    // $ticketId = $_GET['ticketId'];
    // $ticketId = "244904520";

    // Find the ticket by ID
    $ticket = ORM::for_table('tbl_support_tickets')->where('ticket_id', $ticketId)->find_one();

    // Check if the ticket exists
    if (!$ticket) {
        r2(U . 'plugin/support_tickets', 'e', Lang::T("Ticket Not Found ") . ":" . $ticketId);
        return;
    } else {
        $ticket->set('read_flag', 1);
        $ticket->save();
    }

    $data = ORM::for_table('tbl_support_tickets_replies')
        ->order_by_asc('created')
        ->where('delete_flag', 0)
        ->find_many();

    $c = ORM::for_table('tbl_customers')->find_many();
    $customerData = [];

    foreach ($c as $customer) {
        $customerData[] = [
            'id' => $customer->id,
            'name' => $customer->fullname,
            'info' => Lang::T("Username") . ": " . $customer->username .  " - "  . Lang::T("Full Name") . ": " . $customer->fullname . " - "  . Lang::T("Email") . ": " . $customer->email . " - "  . Lang::T("Phone") . ": " . $customer->phonenumber . " - "  . Lang::T("Service Type") . ": " . $customer->service_type,
            'phone' => $customer->phonenumber,
            'email' => $customer->email,
            'type' => $customer->service_type,
            'balance' => $customer->balance
        ];
    }

    $csrfToken = support_tickets_generate_csrf_token();
    $ui->assign('customers', $customerData);
    $ui->assign('csrfToken', $csrfToken);
    $ui->assign('ticket', $ticket);
    $ui->assign('replies', $data);
    $ui->display('support_tickets_view.tpl');
}


function support_tickets_settings()
{
    $configFile = 'system/plugin/support_tickets_config.json';
    $settings = json_decode(file_get_contents($configFile), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the form values and update the settings
        $enable = $_POST['enable'];
        $ucp = $_POST['ucp'];
        $notification = $_POST['notification'];
        $type = $_POST['type'];
        $admin = $_POST['admin'];
        // Validate the CSRF token
        $csrfToken = $_POST['csrf_token'];
        if (!support_tickets_validate_csrf_token($csrfToken)) {
            r2(U . 'plugin/support_tickets', 'e', Lang::T("Invalid CSRF token"));
            return;
        }

        // Update the post value and name in the settings array
        $settings['enable'] = $enable;
        $settings['ucp'] = $ucp;
        $settings['notification'] = $notification;
        $settings['type'] = $type;
        $settings['admin'] = $admin;


        // Save the updated settings to the JSON file
        file_put_contents($configFile, json_encode($settings));

        // Redirect or display a success message
        r2(U . "plugin/support_tickets", 's', Lang::T("Settings Saved"));
    }
}

function support_tickets_clients_view()
{
    global $ui, $routes;
    _auth();
    $ui->assign('_title', 'Support Ticket');
    $ui->assign('_system_menu', 'Support Ticket');
    $user = User::_info();
    $ui->assign('_user', $user);
    $ticketId = $routes['2'];

    //$ticket = ORM::for_table('tbl_support_tickets')->find_one($ticketId);
    $id = $_SESSION['uid'];
    // Retrieve the tickets for the current page with sorting and filtering by user ID
    $supportTickets = ORM::for_table('tbl_support_tickets')
        ->where('userid', $id) // Assuming you have the user ID stored in the $userId variable
        ->where('delete_flag', 0) // Add the condition for delete_flag = 0
        ->order_by_desc('created')
        ->find_many();

        $tickets = ORM::for_table('tbl_support_tickets')
        ->where('userid', $id) // Assuming you have the user ID stored in the $userId variable
        ->where('delete_flag', 0) // Add the condition for delete_flag = 0
        ->where('ticket_id', $ticketId)
        ->order_by_desc('created')
        ->find_one();

    foreach ($supportTickets as $ticket) {
        $ticket->formattedCreated = date('D, d M Y h:i A', strtotime($ticket->created));
        $ticket->formattedLastUpdated = date('D, d M Y h:i A', strtotime($ticket->last_updated));
    }

    $data = ORM::for_table('tbl_support_tickets_replies')
        ->order_by_asc('created')
        ->where('delete_flag', 0)
        ->find_many();

    $csrfToken = support_tickets_generate_csrf_token();
    $ui->assign('csrfToken', $csrfToken);
    $ui->assign('replies', $data);
    $ui->assign('tickets', $tickets);
    $ui->assign('ticket', $ticket);
    $ui->assign('sortedTickets', $supportTickets);
    $ui->assign('xheader', '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">');
    $ui->display('support_tickets_clients_view.tpl');
}