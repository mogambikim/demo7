<?php


function Alloworigins()
{



    if (isset($_GET['type']) == 'grant') {


        $type = $_GET['type'];


        if ($type == "verify") {

            VerifyHotspot();

            exit();

        } elseif ($type == "grant") {


            CreateHostspotUser();

            exit();

        }



        exit();

    }






}


function VerifyHotspot()
{


    $phone = $_POST['phone_number'];


    $user = ORM::for_table('tbl_payment_gateway')
        ->where('username', $phone)
        ->order_by_desc('id')
        ->find_one();

    if ($user) {


        $status = $user->status;
        $mpesacode = $user->gateway_trx_id;

        $res = $user->pg_paid_response;



       

        if ($status == 2 && !empty($mpesacode)) {

            $data = array(
                "Resultcode" => "3",
                "phone" => $phone,
                "tyhK" => "1234",
                "Message" => "We have received your transation under the mpesa Transaction $mpesacode,Please don't leave this page as we are redirecting you",
                "Status" => "success"
            );

            echo json_encode($data);

            exit();

        }

        if($res=="Not enough balance"){
            
         
            $data = array(
           "Resultcode" => "2",
           "Message1" => "Insuficient Balance for the transaction",
           "Status" => "danger",
           "Redirect" => "Insuficient balance"
     
       );
       
   echo    $message = json_encode($data);
         
            exit();
            
            
        }



        if($res=="Wrong Mpesa pin"){
            
         
            $data = array(
           "Resultcode" => "2",
           "Message" => " You entered Wrong Mpesa pin, please resubmit",
           "Status" => "danger",
           "Redirect" => "Wrong Mpesa pin"
     
       );
       
   echo    $message = json_encode($data);
         
            exit();
            
            
            
            
            
        }

        if($status == 4){

            $data = array(
                "Resultcode" => "2",
                "Message" => "You cancelled the transation, you can enter phone number again to activate",
                "Status" => "info",
                "Redirect" => "Transaction Cancelled"

            );

            echo $message = json_encode($data);

            exit();

        }


        if (empty($mpesacode)) {


            $data = array(
                "Resultcode" => "1",
                "Message" => "A payment pop up has been sent to $phone, Please enter pin to continue(Please do not leave  or reload the page untill redirected)",
                "Status" => "primary"

            );

            echo $message = json_encode($data);

            exit();
        }




    }













}



function CreateHostspotUser()
{






// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Log the received data to the error log
error_log('Received data: ' . json_encode($input));

// Extract data from JSON input
$phone = isset($input['phone_number']) ? $input['phone_number'] : '';
$planId = isset($input['plan_id']) ? $input['plan_id'] : '';
$routerId = isset($input['router_id']) ? $input['router_id'] : '';

// Retrieve the MAC address from the login page
$macAddress = isset($input['mac_address']) ? $input['mac_address'] : '';

// Log the MAC address to the error log
error_log('MAC Address: ' . $macAddress);

// Create the username using the MAC address
$username = $phone . '-' . $macAddress;

// Log the extracted data to the error log
error_log('Extracted data:');
error_log('Phone: ' . $phone);
error_log('Plan ID: ' . $planId);
error_log('Router ID: ' . $routerId);
error_log('Username: ' . $username);

// Your POST request processing code here...
header('Content-Type: application/json'); // Ensure JSON content type header
// echo json_encode(['status' => 'error', 'code' => 405, 'message' => 'phone is ' .$phoneNumber]);

    $phone = (substr($phone, 0, 1) == '+') ? str_replace('+', '', $phone) : $phone;
    $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
    $phone = (substr($phone, 0, 1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
    $phone = (substr($phone, 0, 1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
    $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
    $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;

    if (strlen($phone) !== 12) {
        echo json_encode(['status' => 'error', 'code' => 1, 'message' => 'Phone number is invalid please confirm']);
        exit();
    }

    if (strlen($phone) == 12 && !empty($planId) && !empty($routerId)) {
        $PlanExist = ORM::for_table('tbl_plans')->where('id', $planId)->count() > 0;
        $RouterExist = ORM::for_table('tbl_routers')->where('id', $routerId)->count() > 0;

        if (!$PlanExist && !$RouterExist) {
            echo json_encode(["status" => "error", "message" => "Unable to process your request, please refresh the page"]);
            exit();
        }


        $Userexist = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
        if ($Userexist) {
            // Update the router ID for the existing user
            $Userexist->router_id = $routerId;
            $Userexist->save();
        
            InitiateStkpush($phone,$username, $planId, $routerId);
            exit();
        }

        $defpass = '1234';
        $defaddr = 'FreeispRadius';
        $defmail = $phone . '@gmail.com';
        $router = $routerId;

        $createUser = ORM::for_table('tbl_customers')->create();
        $createUser->username = $username; // Use $username instead of $phone
        $createUser->password = $defpass;
        $createUser->fullname = $phone;
        $createUser->phonenumber = $phone;
        $createUser->pppoe_password = $defpass;
        $createUser->address = $defaddr;
        $createUser->email = $defmail;
        $createUser->service_type = 'Hotspot';
        $createUser->router_id = $router;

        if ($createUser->save()) {
            InitiateStkpush($phone, $username, $planId, $routerId);
            // we do the stk push here okay
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "There was a system error when registering user, please contact support"]);
            exit();
        }
    }
}




function InitiateStkpush($phone, $username, $planId, $routerId)
{
    $gateway = ORM::for_table('tbl_appconfig')
        ->where('setting', 'payment_gateway')
        ->find_one();

    $gateway = ($gateway) ? $gateway->value : null;

    if ($gateway == "MpesatillStk") {
        $url = (U . "plugin/initiatetillstk");
    } elseif ($gateway == "BankStkPush") {
        $url = (U . "plugin/initiatebankstk");
    } elseif ($gateway == "MpesaPaybill") {
        $url = (U . "plugin/initiatePaybillStk");
    }

    $Planname = ORM::for_table('tbl_plans')
        ->where('id', $planId)
        ->order_by_desc('id')
        ->find_one();

    $Findrouter = ORM::for_table('tbl_routers')
        ->where('id', $routerId)
        ->order_by_desc('id')
        ->find_one();

    $rname = $Findrouter->name;
    $price = $Planname->price;
    $Planname = $Planname->name_plan;

    $Checkorders = ORM::for_table('tbl_payment_gateway')
        ->where('username', $username)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_many();

    if ($Checkorders) {
        foreach ($Checkorders as $Dorder) {
            $Dorder->delete();
        }
    }

    $d = ORM::for_table('tbl_payment_gateway')->create();
    $d->username = $username;
    $d->gateway = $gateway;
    $d->plan_id = $planId;
    $d->plan_name = $Planname;
    $d->routers_id = $routerId;
    $d->routers = $rname;
    $d->price = $price;
    $d->payment_method = $gateway;
    $d->payment_channel = $gateway;
    $d->created_date = date('Y-m-d H:i:s');
    $d->paid_date = date('Y-m-d H:i:s');
    $d->expired_date = date('Y-m-d H:i:s');
    $d->pg_url_payment = $url;
    $d->status = 1;
    $d->save();

    echo json_encode(["status" => "success", "phone" => $phone, "message" => "Registration complete,Please enter Mpesa Pin to activate the package"]);

SendSTKcred($phone, $username, $url);



}
function SendSTKcred($phone, $username, $url)
{


    // Do not echo any output here
    $link = $url;
    // what post fields?
    $fields = array(
        'username' => $username,
        'phone' => $phone,
        'channel' => 'Yes',

    );

    // build the urlencoded data
    $postvars = http_build_query($fields);
    // open connection
    // open connection
    $ch = curl_init();

    // set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

    // execute post
    $result = curl_exec($ch);

    // Handle errors or process the result as needed
}



// Call the function
Alloworigins();

?>