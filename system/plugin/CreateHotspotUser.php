<?php


function Alloworigins(){



    if(isset($_GET['type'])){





        CreateHostspotUser();

    //    echo json_encode(['status' => 'error', 'code' => 1, 'message' => 'cheked']);

     //   exit();

    }



}



function CreateHostspotUser(){


   

   

   

    // Parse JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Extract data from JSON input
    $phone = isset($input['phone_number']) ? $input['phone_number'] : '';
    $planId = isset($input['plan_id']) ? $input['plan_id'] : '';
    $routerId = isset($input['router_id']) ? $input['router_id'] : '';

    // Your POST request processing code here...

    // Output response
    header('Content-Type: application/json'); // Ensure JSON content type header
    //echo json_encode(['status' => 'error', 'code' => 405, 'message' => 'phone is ' .$phoneNumber]);


    $phone = (substr($phone, 0,1) == '+') ? str_replace('+', '', $phone) : $phone;
    $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
    $phone = (substr($phone, 0,1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
    $phone = (substr($phone, 0,1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
    $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
    $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;


 if(strlen($phone) !==12){


    echo json_encode(['status' => 'error', 'code' => 1, 'message' => 'Phone number is invalid please confirm']);

    exit();
 }

if(strlen($phone) == 12 && !empty($planId) && !empty($routerId)){

    
$PlanExist = ORM::for_table('tbl_plans')->where('id', $planId)->count() > 0;

$RouterExist = ORM::for_table('tbl_routers')->where('id', $routerId)->count() > 0;

if(!$PlanExist && !$RouterExist){


    echo json_encode(["status" => "error", "message" => "Unable to precoess your request, please refresh the page"]);
    exit();

}


    
$Userexist = ORM::for_table('tbl_customers')->where('username', $phone)->count() > 0;




if($Userexist){

    InitiateStkpush($phone,$planId,$routerId);

    exit();

}


$defpass='1234';
$defaddr='FreeispRadius';
$defmail = $phone. '@gmail.com';

$createUser = ORM::for_table('tbl_customers')->create();
$createUser->username = $phone;
$createUser->password = $defpass;
$createUser->fullname = $phone;
$createUser->phonenumber = $phone;
$createUser->pppoe_password = $defpass;
$createUser->address = $defaddr;
$createUser->email = $defmail;
$createUser->service_type = 'Hotspot';



if($createUser->save()){

   



    InitiateStkpush($phone,$planId,$routerId);


// we do the stk push here okay




















    exit();

}else{

    echo json_encode(["status" => "error", "message" => "There was a system error when registering user, please contact support"]);
    exit();
}

   
    






   
}




   
}

function InitiateStkpush($phone,$planId,$routerId){

   



    $gateway = ORM::for_table('tbl_appconfig')
    ->where('setting', 'payment_gateway')
    ->find_one();

    $gateway = ($gateway) ? $gateway->value : null;

       if($gateway=="MpesatillStk"){

        $url=(U. "plugin/initiatetillstk");

       }elseif($gateway=="BankStkPush"){


        $url=(U. "plugin/initiatebankstk");


       }
     


       $Planname = ORM::for_table('tbl_plans')
       ->where('id', $planId)
       ->order_by_desc('id')
       ->find_one();

    $price=$Planname->price;
 $Planname=$Planname->name_plan;



 $Checkorders = ORM::for_table('tbl_payment_gateway')
 ->where('username', $phone)
 ->where('status', 1)
  ->order_by_desc('id')
 ->find_many();


if($Checkorders){


foreach ($Checkorders as $Dorder){


$Dorder->delete();


}


}








     $rname='Hotspot';

    



        $d = ORM::for_table('tbl_payment_gateway')->create();
        $d->username = $phone;
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



        echo json_encode(["status" => "success", "message" => "Registration complete,Please enter Mpesa Pin to activate the package"]);


      
     


        SendSTKcred($phone,$url);




  
   






    }
    
    function SendSTKcred($phone, $url) {
        // Do not echo any output here
        $link = $url;
        // what post fields?
        $fields = array(
            'username' => $phone,
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
