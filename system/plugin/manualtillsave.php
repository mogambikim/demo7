<?php

function manualtillsave()
{
    global $admin, $_L;

    // Debugging: Dump the contents of $admin
// var_dump($admin['username'] );

    global $config;

    $text = _post('manualtext');
    $show = _post('show');

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'tillmanualtext')->find_one();
    if ($d) {
        $d->value = $text;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'tillmanualtext';
        $d->value = $text;
        $d->save();
    }

    $d = ORM::for_table('tbl_appconfig')->where('setting', 'tillmanualshow')->find_one();
    if ($d) {
        $d->value = $show;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'tillmanualshow';
        $d->value = $show;
        $d->save();
    }

    // Log the information
  // _log('[' . $admin['username'] . ']: Manual till setting ' . Lang::T('Settings Saved Successfully'), 'Admin', $admin['id']);

   r2(U . 'plugin/tillnumber', 's', Lang::T('Settings Saved Successfully'));
}
