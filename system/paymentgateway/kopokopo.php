<?php
//require 'system/vendor/autoload.php';
//use Kopokopo\SDK\K2;
/**
 
 *
 **/
function kopokopo_validate_config()
{
    global $config;
    if (empty($config['kopokopo_app_key']) || empty($config['kopokopo_app_secret']) || empty($config['kopokopo_api_key'])) {
        sendTelegram("Kopo Kopo payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup Kopo Kopo payment gateway, please tell admin"));
    }
}

function kopokopo_show_config()
{
    global $ui, $config;
    //$ui->assign('env', json_decode(file_get_contents('system/paymentgateway/kopokopo_env.json'), true));
    $ui->assign('_title', 'Kopo Kopo - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('kopokopo.tpl');
}

function kopokopo_save_config()
{
    global $admin, $_L;
    $kopokopo_app_key = _post('kopokopo_app_key');
    $kopokopo_app_secret = _post('kopokopo_app_secret');
    $kopokopo_api_key = _post('kopokopo_api_key');
    $kopokopo_till_number = _post('kopokopo_till_number');
    $kopokopo_env = _post('kopokopo_env');
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'kopokopo_app_key')->find_one();
    if ($d) {
        $d->value = $kopokopo_app_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'kopokopo_app_key';
        $d->value = $kopokopo_app_key;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'kopokopo_app_secret')->find_one();
    if ($d) {
        $d->value = $kopokopo_app_secret;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'kopokopo_app_secret';
        $d->value = $kopokopo_app_secret;
        $d->save();
    }

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'kopokopo_api_key')->find_one();
    if ($d) {
        $d->value = $kopokopo_api_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'kopokopo_api_key';
        $d->value = $kopokopo_api_key;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'kopokopo_till_number')->find_one();
    if ($d) {
        $d->value = $kopokopo_till_number;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'kopokopo_till_number';
        $d->value = $kopokopo_till_number;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'kopokopo_env')->find_one();
    if ($d) {
        $d->value = $kopokopo_env;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'kopokopo_env';
        $d->value = $kopokopo_env;
        $d->save();
    }

    _log('[' . $admin['username'] . ']: Kopo Kopo ' . Lang::T('Settings Saved Successfully'), 'Admin', $admin['id']);

    r2(U . 'paymentgateway/kopokopo', 's', Lang::T('Settings Saved Successfully'));
}


function kopokopo_create_transaction($trx, $user)
{
    global $config, $routes;


    // Example encryption function using AES-256-CBC algorithm
    function kopokopo_encryptData($data, $key, $cipher = 'AES-256-CBC') {
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $encrypted, $key, true);
        return base64_encode($iv . $hmac . $encrypted);
    }


    //  lets create Transaction
    $data = [
        'first_name' => $user['fullname'],
        'phone_number' => $user['phonenumber'],
        'price' => $trx['price'],
    ];

    $encodedData = json_encode($data);

      //$app_url = 'http://localhost/freeispradius/';
    $url = U . 'plugin/kopokopo_payments_validate&data=' . urlencode($encodedData);

    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    $d->gateway_trx_id = '';
    $d->pg_url_payment = $url;
    $d->pg_request = '';
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));

    die();
}

function kopokopo_get_status($trx, $user)
{
  // not needed but if required, will be implement later
}


function kopokopo_payment_notification()
{
	// not needed but if required, will be implement later
}