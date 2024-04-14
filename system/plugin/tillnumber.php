<?php

function tillnumber()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'KopoKopo Payments');
    $ui->assign('_system_menu', 'reports');
    //$admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $action = $routes['1'];

    if ($admin['user_type'] != 'Admin' and $admin['user_type'] != 'Sales') {
    r2(U . "dashboard", 'e', Lang::T('You do not have permission to access this page'));
    }


    $ui->display('manualtill.tpl');
}