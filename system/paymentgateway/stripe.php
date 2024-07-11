<?php

function stripe_validate_config()
{
    global $config;
    if (empty($config['stripe_api_key']) || empty($config['stripe_secret_key'])) {
        sendTelegram("Stripe payment gateway not configured");
        r2(U . 'order/package', 'w', "Admin has not yet setup Stripe payment gateway, please tell admin");
    }
}

function stripe_show_config()
{
    global $ui;
    $ui->assign('_title', 'Stripe - Payment Gateway');
    $ui->assign('currency', json_decode(file_get_contents('system/paymentgateway/stripe_currency.json'), true));
    $ui->display('stripe.tpl');
}

function stripe_save_config()
{
    global $admin, $_L;
    $stripe_api_key = _post('stripe_api_key');
    $stripe_secret_key = _post('stripe_secret_key');
    $stripe_currency = _post('stripe_currency');
    
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_secret_key')->find_one();
    if ($d) {
        $d->value = $stripe_secret_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'stripe_secret_key';
        $d->value = $stripe_secret_key;
        $d->save();
    }

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_api_key')->find_one();
    if ($d) {
        $d->value = $stripe_api_key;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'stripe_api_key';
        $d->value = $stripe_api_key;
        $d->save();
    }

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_currency')->find_one();
    if ($d) {
        $d->value = $stripe_currency;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'stripe_currency';
        $d->value = $stripe_currency;
        $d->save();
    }

    _log('[' . $admin['username'] . ']: Stripe ' . Lang::T('Settings Saved Successfully'), 'Admin', $admin['id']);
    r2(U . 'paymentgateway/stripe', 's', Lang::T('Settings Saved Successfully'));
}

function stripe_create_transaction($trx, $user)
{
    global $config;
    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => $config['stripe_currency'],
                'product_data' => [
                    'name' => $trx['description'],
                ],
                'unit_amount' => $trx['price'] * 100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => U . "order/view/" . $trx['id'] . '/check',
        'cancel_url' => U . "order/view/" . $trx['id'],
    ]);
    
    if (!$session['id']) {
        sendTelegram("stripe_create_transaction FAILED: \n\n" . json_encode($session, JSON_PRETTY_PRINT));
        r2(U . 'order/package', 'e', "Failed to create Stripe transaction.");
    }

    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    
    $d->gateway_trx_id = $session['id'];
    $d->pg_url_payment = $session['url'];
    $d->pg_request = json_encode($session);
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+ 6 HOUR"));
    $d->save();
    
    header('Location: ' . $session['url']);
    exit();
}

function stripe_payment_notification()
{
    // Not yet implemented
    die('OK');
}

function stripe_get_status($trx, $user)
{
    global $config;
    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    $session = \Stripe\Checkout\Session::retrieve($trx['gateway_trx_id']);
    
    if ($session['payment_status'] == 'paid' && $trx['status'] != 2) {
        if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'], 'Stripe')) {
            r2(U . "order/view/" . $trx['id'], 'd', "Failed to activate your Package, try again later.");
        }
        
        $trx->pg_paid_response = json_encode($session);
        $trx->payment_method = 'STRIPE';
        $trx->payment_channel = 'stripe';
        $trx->paid_date = date('Y-m-d H:i:s', strtotime($session['created']));
        $trx->status = 2;
        $trx->save();
        
        r2(U . "order/view/" . $trx['id'], 's', "Transaction has been paid.");
    } else if ($session['status'] == 'expired') {
        $trx->pg_paid_response = json_encode($session);
        $trx->status = 3;
        $trx->save();
        r2(U . "order/view/" . $trx['id'], 'd', "Transaction expired.");
    } else {
        sendTelegram("stripe_get_status: unknown result\n\n" . json_encode($session, JSON_PRETTY_PRINT));
        r2(U . "order/view/" . $trx['id'], 'w', "Transaction status :" . $session['status']);
    }
}

function stripe_get_server()
{
    // Not needed for Stripe as it uses a single API endpoint
}
