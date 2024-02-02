<?php
/**
 
 *
 **/
register_menu("Kopokopo Payments", true, "kopokopo_payments", 'REPORTS', '');

//require 'system/vendor/autoload.php';
use Kopokopo\SDK\K2;


function kopokopo_payments()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'KopoKopo Payments');
    $ui->assign('_system_menu', 'reports');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $action = $routes['1'];

    if ($admin['user_type'] != 'Admin' and $admin['user_type'] != 'Sales') {
    r2(U . "dashboard", 'e', $_L['Do_Not_Access']);
    }


    $ui->display('kopokopo_payment.tpl');
}

// Example decryption function for AES-256-CBC encrypted data
function kopokopo_decryptData($encryptedData, $key, $cipher = 'AES-256-CBC') {
    $data = base64_decode($encryptedData);
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivLength);
    $hmac = substr($data, $ivLength, 32);
    $encrypted = substr($data, $ivLength + 32);
    $decrypted = openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $calculatedHmac = hash_hmac('sha256', $encrypted, $key, true);
    if (hash_equals($hmac, $calculatedHmac)) {
        return $decrypted;
    }
    return false;
}

function kopokopo_payments_validate()
{
     global $config, $ui, $routes;
     _auth();
     $user = User::_info();
     $trx = ORM::for_table('tbl_payment_gateway')
     ->where('username', $user['username'])
     ->where('status', 1)
     ->find_one();
     if (!$trx) {
         return;
     }

    // Check if 'data' parameter is present in the request
    if (isset($_GET['data'])) {
        $encryptedData = $_GET['data'];

        // Decrypt the data
        //$decryptedData = kopokopo_decryptData($encryptedData, $key);

        if ($encryptedData !== false) {
            // JSON data decryption successful
            $jsonData = json_decode($encryptedData, true);

            // Use the decrypted JSON data for further processing
            // For example, access the values:
            $firstName = $jsonData['first_name'];
            $phoneNumber = $jsonData['phone_number'];
            $price = $jsonData['price'];

        } else {
            // Failed to decrypt JSON data
            echo json_encode(['status' => 'error', 'message' => 'Failed to decrypt data']);
        }
    } else {
        // 'data' parameter not found in the request
      echo json_encode(['status' => 'error', 'message' => 'Data parameter not found']);
    }


    //select evironment
    $environment = $config['kopokopo_env'];
    $kopokopo_app_key = $config['kopokopo_app_key'];
    $kopokopo_app_secret = $config['kopokopo_app_secret'];
    $kopokopo_api_key = $config['kopokopo_api_key'];
    $kopokopo_till_number = $config['kopokopo_till_number'];
    $CallBackURL = U . 'callback/kopokopo';
    //lets check the environment selected
    if ($environment == "live") {
        $baseUrl = 'https://api.kopokopo.com';
    } elseif ($environment == "sandbox") {
        $baseUrl = 'https://sandbox.kopokopo.com';
    } else {
        return json_encode(["Message" => "invalid application status"]);
    };
      //echo "\n";
    //  echo $environment;
     //echo $kopokopo_till_number;

    //lets get access token from kopokopo

    $options = [
    'clientId' => $kopokopo_app_key,
    'clientSecret' =>  $kopokopo_app_secret,
    'apiKey' => $kopokopo_api_key,
    'baseUrl' => $baseUrl,
     ];

    $K2 = new K2($options);
    // Get one of the services
    $tokens = $K2->TokenService();

    // Use the service
    $result = $tokens->getToken();
    //var_dump($result); // Add this line to inspect the structure of the $result array

    if ($result) {
        $data = $result['data'];
        // Access the 'accessToken' and 'expiresIn' elements of the $data array
        //echo "\n";
        //echo "My access token is: " . $data['accessToken'];
        //echo "\n";
        //echo "It expires in: " . $data['expiresIn'];
    };

    $stk = $K2->StkService();
    $response = $stk->initiateIncomingPayment([
      'paymentChannel' => 'M-PESA STK Push',
      'tillNumber' => $kopokopo_till_number,
      'firstName' => $user['fullname'],
      'lastName' => $user['username'],
      'phoneNumber' => '+'. $user['phonenumber'],
      'amount' => $trx['price'],
      'currency' => 'KES',
      'email' => (empty($user['email'])) ? $user['username'] . '@' . $_SERVER['HTTP_HOST'] : $user['email'],
      'callbackUrl' => $CallBackURL,
      'metadata' => [
                 'price' => $trx['price'],
                 'username' => $user['username'],
                 'trxid' => $trx['id'],
                 'Plan_Name' => $trx['plan_name']
             ],
      'accessToken' => $data['accessToken'],
    ]);

    //var_dump($response);

    if ($response['status'] == 'success') {
        //$location = $response['location'];
        echo "Please check your phone to process payment";
        //$ui->display('kopokopo_success.tpl');
    }

    $stk = $K2->StkService();

    $options = [
        'location' => $response['location'],
        'accessToken' => $data['accessToken'],
    ];

    $jsonData = null;
    while (!$jsonData) {
        $jsonData = $stk->getStatus($options);

        // Add a waiting time of 1 second before checking again
        sleep(1);
    }

    if ($jsonData) {
      // Access the individual values from the captured data
       $status = $jsonData['status'];
       $id = $jsonData['data']['id'];
       $resourceStatus = $jsonData['data']['resourceStatus'];
       $origination_time = $jsonData['data']['originationTime'];
       $system = $jsonData['data']['system'];
       $eventType = $jsonData['data']['eventType'];
       $linkSelf = $jsonData['data']['linkSelf'];
       //echo "resourceStatus is: " . $resourceStatus;
       //$status = $jsonData['data']['attributes']['status'];

      if ($status === 'success' && $resourceStatus === 'Received' && $trx['status'] != 2) {
        if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'], 'KopoKopo')) {
          r2(U . "order/view/" . $trx['id'], 'd', Lang::T("Failed to activate your Package, try again later."));
      }

      _log('[' . $eventType . ']: KopoKopo Payment Reports: ' . " \n Payment Status: " . $status. " \n Payment Confirmation: " .$resourceStatus. " \n API Response:\n" . json_encode($jsonData));
      sendTelegram("KopoKopo Payment Reports: \n Payment Status: " . $status. " \n Payment Confirmation: " .$resourceStatus. " \n API Response:\n" . json_encode($jsonData));
      $d = ORM::for_table('tbl_payment_gateway')
          ->where('username', $user['username'])
          ->where('status', 1)
          ->find_one();
      $d->payment_method = 'KopoKopo';
      $d->payment_channel = $system;
      $d->pg_paid_response = json_encode($jsonData);
      $d->pg_request = $linkSelf;
      $d->paid_date = date('Y-m-d h:i:s A', strtotime($origination_time));
      $d->status = 2;
      $d->save();
      r2(U . 'order/view/'.$trx['id']);
      exit();
    } elseif ($status !== 'success' && $resourceStatus !== 'Received') {
      // Handle error case
      _log('[' . $eventType . ']: KopoKopo ' . "Payment Status: " . $status . " API Response:\n" . $formattedJson);
      sendTelegram("KopoKopo payment report - HTTP Status: " . $status . " API Response:\n" . $formattedJson . " Username:\n" . $user['username']);
      $d->status = 4;
      $d->save();
      exit();
    } else {
      $d->status = 1;
      $d->save();
      exit();
    }
  };

}
