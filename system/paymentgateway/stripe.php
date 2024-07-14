<?php


function stripe_save_config()
{
    global $admin, $_L;
    $stripe_api_key = _post('stripe_api_key');
    $stripe_secret_key = _post('stripe_secret_key');
    $stripe_currency = _post('stripe_currency');

    $settings = [
        'stripe_secret_key' => $stripe_secret_key,
        'stripe_api_key' => $stripe_api_key,
        'stripe_currency' => $stripe_currency
    ];

    foreach ($settings as $key => $value) {
        $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
        if ($d) {
            $d->value = $value;
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = $key;
            $d->value = $value;
            $d->save();
        }
    }

    _log('[' . $admin['username'] . ']: Stripe ' . Lang::T('Settings Saved Successfully'), 'Admin', $admin['id']);
    r2(U . 'paymentgateway/stripe', 's', Lang::T('Settings Saved Successfully'));
}

function stripe_show_config()
{
    global $ui;
    $currency = json_decode(file_get_contents('system/paymentgateway/stripe_currency.json'), true);
    $stripe_secret_key = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_secret_key')->find_one()->value;
    $stripe_api_key = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_api_key')->find_one()->value;
    $stripe_currency = ORM::for_table('tbl_appconfig')->where('setting', 'stripe_currency')->find_one()->value;

    $ui->assign('_title', 'Stripe - Payment Gateway');
    $ui->assign('currency', $currency);
    $ui->assign('stripe_api_key', $stripe_api_key);
    $ui->assign('stripe_secret_key', $stripe_secret_key);
    $ui->assign('stripe_currency', $stripe_currency);
    $ui->display('stripe.tpl');
}
?>
