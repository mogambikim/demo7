<?php

/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/


 _admin();
 $ui->assign('_title', Lang::T('Customer'));
 $ui->assign('_system_menu', 'customers');
 $action = $routes['1'];
 $ui->assign('_admin', $admin);
 
 if(empty($action)){
     $action = 'list';
 }

 $leafletpickerHeader = <<<EOT
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
EOT;
function smarty_modifier_convert_bytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unit = 0;

    while ($bytes >= 1024 && $unit < 4) {
        $bytes /= 1024;
        $unit++;
    }

    return round($bytes, 2) . ' ' . $units[$unit];
}
 
 switch ($action) {
    case 'csv':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }
    
        $cs = ORM::for_table('tbl_customers')
            ->select('tbl_customers.id', 'id')
            ->select('tbl_customers.username', 'username')
            ->select('tbl_customers.password', 'password')
            ->select('tbl_customers.pppoe_password', 'pppoe_password')
            ->select('fullname')
            ->select('address')
            ->select('phonenumber')
            ->select('email')
            ->select('balance')
            ->select('service_type')
            ->select('ip_address')
            ->order_by_asc('tbl_customers.id')
            ->find_array();
    
        $h = false;
        set_time_limit(-1);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-type: text/csv");
        header('Content-Disposition: attachment;filename="freeispradius_customers_' . date('Y-m-d_H_i') . '.csv"');
        header('Content-Transfer-Encoding: binary');
    
        $headers = [
            'id',
            'username',
            'password',
            'pppoe_password',
            'fullname',
            'address',
            'phonenumber',
            'email',
            'balance',
            'service_type',
            'ip_address',
        ];
    
        if (!$h) {
            echo '"' . implode('","', $headers) . "\"\n";
            $h = true;
        }
    
        foreach ($cs as $c) {
            $row = [
                $c['id'],
                $c['username'],
                $c['password'],
                $c['pppoe_password'],
                $c['fullname'],
                $c['address'],
                $c['phonenumber'],
                $c['email'],
                $c['balance'],
                $c['service_type'],
                $c['ip_address'],
            ];
            echo '"' . implode('","', $row) . "\"\n";
        }
        break;
        case 'csv-prepaid':
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
                _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
            }
        
            $cs = ORM::for_table('tbl_customers')
                ->select('tbl_customers.id', 'id')
                ->select('tbl_customers.username', 'username')
                ->select('tbl_customers.password', 'password')
                ->select('tbl_customers.pppoe_password', 'pppoe_password')
                ->select('fullname')
                ->select('address')
                ->select('phonenumber')
                ->select('email')
                ->select('balance')
                ->select('service_type')
                ->select('ip_address')
                ->select('namebp')
                ->select('routers')
                ->select('status')
                ->select('method', 'Payment')
                ->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                ->order_by_asc('tbl_customers.id')
                ->find_array();
        
            $h = false;
            set_time_limit(-1);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-type: text/csv");
            header('Content-Disposition: attachment;filename="freeispradius_active_customers_' . date('Y-m-d_H_i') . '.csv"');
            header('Content-Transfer-Encoding: binary');
        
            $headers = [
                'id',
                'username',
                'password',
                'pppoe_password',
                'fullname',
                'address',
                'phonenumber',
                'email',
                'balance',
                'service_type',
                'ip_address',
                'namebp',
                'routers',
                'status',
                'Payment'
            ];
        
            if (!$h) {
                echo '"' . implode('","', $headers) . "\"\n";
                $h = true;
            }
        
            foreach ($cs as $c) {
                $row = [
                    $c['id'],
                    $c['username'],
                    $c['password'],
                    $c['pppoe_password'],
                    $c['fullname'],
                    $c['address'],
                    $c['phonenumber'],
                    $c['email'],
                    $c['balance'],
                    $c['service_type'],
                    $c['ip_address'],
                    $c['namebp'],
                    $c['routers'],
                    $c['status'],
                    $c['Payment']
                ];
                echo '"' . implode('","', $row) . "\"\n";
            }
            break;
            case 'list':
                $search = _post('search');
                run_hook('list_customers');
            
                if ($search != '') {
                    $queryBuilder = ORM::for_table('tbl_customers')
                        ->select('tbl_customers.*')
                        ->select('tbl_routers.name', 'router_name')
                        ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                        ->where_raw("tbl_customers.username LIKE ? OR tbl_customers.fullname LIKE ? OR tbl_customers.phonenumber LIKE ? OR tbl_customers.email LIKE ? OR tbl_customers.ip_address LIKE ? OR tbl_customers.service_type LIKE ?", array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
            
                    $paginator = Paginator::build($queryBuilder, [
                        'username' => '%' . $search . '%',
                        'fullname' => '%' . $search . '%',
                        'phonenumber' => '%' . $search . '%',
                        'email' => '%' . $search . '%',
                        'ip_address' => '%' . $search . '%',
                        'service_type' => '%' . $search . '%'
                    ], $search);
            
                    $d = $queryBuilder->offset($paginator['startpoint'])
                        ->limit($paginator['limit'])
                        ->order_by_asc('tbl_customers.username')
                        ->find_many();
                } else {
                    $queryBuilder = ORM::for_table('tbl_customers')
                        ->select('tbl_customers.*')
                        ->select('tbl_routers.name', 'router_name')
                        ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'));
            
                    $paginator = Paginator::build($queryBuilder);
            
                    $d = $queryBuilder->offset($paginator['startpoint'])
                        ->limit($paginator['limit'])
                        ->order_by_desc('tbl_customers.id')
                        ->find_many();
                }
            
                $ui->assign('search', htmlspecialchars($search));
                $ui->assign('d', $d);
                $ui->assign('paginator', $paginator);
                $ui->display('customers.tpl');
                break;
            
            

                case 'active_users':
                    $search = _post('search');
                    run_hook('list_customers');
                
                    $queryBuilder = ORM::for_table('tbl_customers')
                        ->select('tbl_customers.*')
                        ->select('tbl_routers.name', 'router_name')
                        ->join('tbl_user_recharges', 'tbl_customers.id = tbl_user_recharges.customer_id')
                        ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                        ->where('tbl_user_recharges.status', 'on')
                        ->group_by('tbl_customers.id');
                
                    if ($search != '') {
                        $queryBuilder->where_raw("tbl_customers.username LIKE ? OR tbl_customers.fullname LIKE ? OR tbl_customers.phonenumber LIKE ? OR tbl_customers.email LIKE ? OR tbl_customers.ip_address LIKE ?", 
                                                 array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                    }
                
                    $paginator = Paginator::build($queryBuilder, [
                        'username' => '%' . $search . '%',
                        'fullname' => '%' . $search . '%',
                        'phonenumber' => '%' . $search . '%',
                        'email' => '%' . $search . '%',
                        'ip_address' => '%' . $search . '%'
                    ], $search);
                
                    $d = $queryBuilder->offset($paginator['startpoint'])
                        ->limit($paginator['limit'])
                        ->order_by_asc('tbl_customers.username')
                        ->find_many();
                
                    $ui->assign('search', htmlspecialchars($search));
                    $ui->assign('d', $d);
                    $ui->assign('paginator', $paginator);
                    $ui->display('customers_active_users.tpl');
                    break;
                
                
                

                    case 'expired_users':
                        $search = _post('search');
                        run_hook('list_customers');
                    
                        $queryBuilder = ORM::for_table('tbl_customers')
                            ->select('tbl_customers.*')
                            ->select('tbl_routers.name', 'router_name')
                            ->join('tbl_user_recharges', 'tbl_customers.id = tbl_user_recharges.customer_id')
                            ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                            ->where('tbl_user_recharges.status', 'off')
                            ->group_by('tbl_customers.id');
                    
                        if ($search != '') {
                            $queryBuilder->where_raw("tbl_customers.username LIKE ? OR tbl_customers.fullname LIKE ? OR tbl_customers.phonenumber LIKE ? OR tbl_customers.email LIKE ? OR tbl_customers.ip_address LIKE ?", 
                                                     array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                        }
                    
                        $paginator = Paginator::build($queryBuilder, [
                            'username' => '%' . $search . '%',
                            'fullname' => '%' . $search . '%',
                            'phonenumber' => '%' . $search . '%',
                            'email' => '%' . $search . '%',
                            'ip_address' => '%' . $search . '%'
                        ], $search);
                    
                        $d = $queryBuilder->offset($paginator['startpoint'])
                            ->limit($paginator['limit'])
                            ->order_by_asc('tbl_customers.username')
                            ->find_many();
                    
                        $ui->assign('search', htmlspecialchars($search));
                        $ui->assign('d', $d);
                        $ui->assign('paginator', $paginator);
                        $ui->display('customers_expired_users.tpl');
                        break;
                    
                    
                
                        
                        case 'hotspot_users':
                            $search = _post('search');
                            run_hook('list_customers');
                        
                            $queryBuilder = ORM::for_table('tbl_customers')
                                ->select('tbl_customers.*')
                                ->select('tbl_routers.name', 'router_name')
                                ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                                ->where('tbl_customers.service_type', 'Hotspot');
                        
                            if ($search != '') {
                                $queryBuilder->where_raw("`tbl_customers`.`username` LIKE ? OR `tbl_customers`.`fullname` LIKE ? OR `tbl_customers`.`phonenumber` LIKE ? OR `tbl_customers`.`email` LIKE ? OR `tbl_customers`.`ip_address` LIKE ?", 
                                                          array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                            }
                        
                            $paginator = Paginator::build($queryBuilder);
                        
                            $d = $queryBuilder->offset($paginator['startpoint'])
                                ->limit($paginator['limit'])
                                ->order_by_desc('tbl_customers.id')
                                ->find_many();
                        
                            $ui->assign('search', htmlspecialchars($search));
                            $ui->assign('d', $d);
                            $ui->assign('paginator', $paginator);
                            $ui->display('customers_hotspot_users.tpl');
                            break;
                        
                            case 'pppoe_users':
                                $search = _post('search');
                                run_hook('list_customers');
                            
                                $queryBuilder = ORM::for_table('tbl_customers')
                                    ->select('tbl_customers.*')
                                    ->select('tbl_routers.name', 'router_name')
                                    ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                                    ->where('tbl_customers.service_type', 'PPPoE');
                            
                                if ($search != '') {
                                    $queryBuilder->where_raw("`tbl_customers`.`username` LIKE ? OR `tbl_customers`.`fullname` LIKE ? OR `tbl_customers`.`phonenumber` LIKE ? OR `tbl_customers`.`email` LIKE ? OR `tbl_customers`.`ip_address` LIKE ?", 
                                                              array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                                }
                            
                                $paginator = Paginator::build($queryBuilder);
                            
                                $d = $queryBuilder->offset($paginator['startpoint'])
                                    ->limit($paginator['limit'])
                                    ->order_by_desc('tbl_customers.id')
                                    ->find_many();
                            
                                $ui->assign('search', htmlspecialchars($search));
                                $ui->assign('d', $d);
                                $ui->assign('paginator', $paginator);
                                $ui->display('customers_pppoe_users.tpl');
                                break;

                                case 'static_users':
                                    $search = _post('search');
                                    run_hook('list_customers');
                                
                                    $queryBuilder = ORM::for_table('tbl_customers')
                                        ->select('tbl_customers.*')
                                        ->select('tbl_routers.name', 'router_name')
                                        ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                                        ->where('tbl_customers.service_type', 'Static');
                                
                                    if ($search != '') {
                                        $queryBuilder->where_raw("`tbl_customers`.`username` LIKE ? OR `tbl_customers`.`fullname` LIKE ? OR `tbl_customers`.`phonenumber` LIKE ? OR `tbl_customers`.`email` LIKE ? OR `tbl_customers`.`ip_address` LIKE ?", 
                                                                  array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                                    }
                                
                                    $paginator = Paginator::build($queryBuilder);
                                
                                    $d = $queryBuilder->offset($paginator['startpoint'])
                                        ->limit($paginator['limit'])
                                        ->order_by_desc('tbl_customers.id')
                                        ->find_many();
                                
                                    $ui->assign('search', htmlspecialchars($search));
                                    $ui->assign('d', $d);
                                    $ui->assign('paginator', $paginator);
                                    $ui->display('customers_static_users.tpl');
                                    break;
                                

                            
                                    case 'new_users':
                                        $search = _post('search');
                                        run_hook('list_customers');
                                    
                                        $queryBuilder = ORM::for_table('tbl_customers')
                                            ->select('tbl_customers.*')
                                            ->select('tbl_routers.name', 'router_name')
                                            ->left_outer_join('tbl_routers', array('tbl_customers.router_id', '=', 'tbl_routers.id'))
                                            ->where_raw('MONTH(tbl_customers.created_at) = MONTH(CURRENT_DATE())')
                                            ->where_raw('YEAR(tbl_customers.created_at) = YEAR(CURRENT_DATE())');
                                    
                                        if ($search != '') {
                                            $queryBuilder->where_raw("`tbl_customers`.`username` LIKE ? OR `tbl_customers`.`fullname` LIKE ? OR `tbl_customers`.`phonenumber` LIKE ? OR `tbl_customers`.`email` LIKE ? OR `tbl_customers`.`ip_address` LIKE ?", 
                                                                      array('%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%'));
                                        }
                                    
                                        $paginator = Paginator::build($queryBuilder);
                                    
                                        $d = $queryBuilder->offset($paginator['startpoint'])
                                            ->limit($paginator['limit'])
                                            ->order_by_desc('tbl_customers.id')
                                            ->find_many();
                                    
                                        $ui->assign('search', htmlspecialchars($search));
                                        $ui->assign('d', $d);
                                        $ui->assign('paginator', $paginator);
                                        $ui->display('customers_new_users.tpl');
                                        break;
                                    
            
        case 'edit-balance':
            $customer_id = $routes['2'];
            $customer = ORM::for_table('tbl_customers')->find_one($customer_id);
        
            if ($customer) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $balance = $_POST['balance'];
                    $previous_balance = $customer->balance; // Store the previous balance
        
                    $customer->set('balance', $balance);
                    $customer->save();
        
                    // Generate a unique transaction reference
                    $transaction_ref = 'AdminEdit' . mt_rand(1000, 9999); // Using a random 4-digit number
                    // Alternatively, you can use a sequence number:
                    // $last_transaction = ORM::for_table('tbl_transactions')->order_by_desc('id')->find_one();
                    // $transaction_ref = 'AdminEdit' . ($last_transaction ? $last_transaction->id + 1 : 1);
        
                    // Log the transaction
                    $transaction = ORM::for_table('tbl_transactions')->create();
                    $transaction->invoice = $transaction_ref;
                    $transaction->username = $customer['username'];
                    $transaction->plan_name = 'Manual Balance Edit';
                    $transaction->price = $balance - $previous_balance; // Calculate the balance change
                    $transaction->recharged_on = date('Y-m-d');
                    $transaction->recharged_time = date('H:i:s');
                    $transaction->expiration = date('Y-m-d');
                    $transaction->time = date('H:i:s');
                    $transaction->method = 'Balance Edit - ' . $admin['fullname'];
                    $transaction->routers = 'balance';
                    $transaction->type = 'Balance';
                    $transaction->admin_id = $admin['id'];
                    $transaction->save();
        
                    // Redirect back to the customer list page after updating the balance
                    r2(U . 'customers/list', 's', 'Balance updated successfully');
                } else {
                    // Display the edit balance form
                    $ui->assign('customer', $customer);
                    $ui->display('customers-edit-balance.tpl');
                }
            } else {
                r2(U . 'customers/list', 'e', 'Customer not found');
            }
            break;

            
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
                _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
            }
            $cs = ORM::for_table('tbl_customers')
            ->select('tbl_customers.id', 'id')
            ->select('tbl_customers.username', 'username')
                ->select('fullname')
                ->select('phonenumber')
                ->select('email')
                ->select('balance')
                ->select('namebp')
                ->select('routers')
                ->select('status')
                ->select('method', 'Payment')
                ->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                ->order_by_asc('tbl_customers.id')->find_array();
            $h = false;
            set_time_limit(-1);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-type: text/csv");
            header('Content-Disposition: attachment;filename="freeispradius_customers_' . date('Y-m-d_H_i') . '.csv"');
            header('Content-Transfer-Encoding: binary');
            foreach ($cs as $c) {
                $ks = [];
                $vs = [];
                foreach ($c as $k => $v) {
                    $ks[] = $k;
                    $vs[] = $v;
                }
                if (!$h) {
                    echo '"' . implode('";"', $ks) . "\"\n";
                    $h = true;
                }
             echo '"' . implode('";"', $vs) . "\"\n";
            }
            break;

    case 'add':
		if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
        }    
        $ui->assign('xheader', $leafletpickerHeader);
                run_hook('view_add_customer'); #HOOK
                $routers = ORM::for_table('tbl_routers')->find_many();
                $ui->assign('routers', $routers); // Pass routers to the template
                $ui->display('customers-add.tpl');
                break;
            
    case 'recharge':
		if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
        }        
        $id_customer  = $routes['2'];
        $b = ORM::for_table('tbl_user_recharges')->where('customer_id', $id_customer)->find_one();
        if ($b) {
            if (Package::rechargeUser($id_customer, $b['routers'], $b['plan_id'], "Recharge", $admin['fullname'])) {
                r2(U . 'customers/view/' . $id_customer, 's', 'Success Recharge Customer');
            } else {
                r2(U . 'customers/view/' . $id_customer, 'e', 'Customer plan is inactive');
            }
        }
        r2(U . 'customers/view/' . $id_customer, 'e', 'Cannot find active plan');
    case 'deactivate':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
            _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
        }
        $id_customer  = $routes['2'];
        $b = ORM::for_table('tbl_user_recharges')->where('customer_id', $id_customer)->find_one();
        if ($b) {
            $p = ORM::for_table('tbl_plans')->where('id', $b['plan_id'])->where('enabled', '1')->find_one();
            if ($p) {
                if ($p['is_radius']) {
                    Radius::customerDeactivate($b['username']);
                } else {
                    $mikrotik = Mikrotik::info($b['routers']);
                    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                    if ($b['type'] == 'Hotspot') {
                        Mikrotik::removeHotspotUser($client, $b['username']);
                        Mikrotik::removeHotspotActiveUser($client, $b['username']);
                    } else if ($b['type'] == 'PPPOE') {
                        Mikrotik::removePpoeUser($client, $b['username']);
                        Mikrotik::removePpoeActive($client, $b['username']);
                    }else if ($b['type'] == 'Static') {
            
                        Mikrotik::removeStaticUser($client, $b['username']);
                    }
                }
                $b->status = 'off';
                $b->expiration = date('Y-m-d');
                $b->time = date('H:i:s');
                $b->save();
                Message::sendTelegram('Admin ' . $admin['username'] . ' Deactivate ' . $b['namebp'] . ' for u' . $b['username']);
                r2(U . 'customers/view/' . $id_customer, 's', 'Success deactivate customer to Mikrotik');
            }
        }
        r2(U . 'customers/view/' . $id_customer, 'e', 'Cannot find active plan');
        break;
    case 'sync':
        $id_customer  = $routes['2'];
        $b = ORM::for_table('tbl_user_recharges')->where('customer_id', $id_customer)->where('status', 'on')->find_one();
        if ($b) {
            $c = ORM::for_table('tbl_customers')->find_one($id_customer);
            $p = ORM::for_table('tbl_plans')->where('id', $b['plan_id'])->where('enabled', '1')->find_one();
            if ($p) {
                if ($p['is_radius']) {
                    Radius::customerAddPlan($c, $p, $p['expiration'].' '.$p['time']);
                    r2(U . 'customers/view/' . $id_customer, 's', 'Success sync customer to Radius');
                } else {
                    $mikrotik = Mikrotik::info($b['routers']);
                    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                    if ($b['type'] == 'Hotspot') {
                        Mikrotik::addHotspotUser($client, $p, $c);
                    } else if ($b['type'] == 'PPPOE') {
                        Mikrotik::addPpoeUser($client, $p, $c);
                    }
                    r2(U . 'customers/view/' . $id_customer, 's', 'Success sync customer to Mikrotik');
                }
            } else {
                r2(U . 'customers/view/' . $id_customer, 'e', 'Customer plan is inactive');
            }
        }
        r2(U . 'customers/view/' . $id_customer, 'e', 'Cannot find active plan');
        break;
        case 'viewu':
            $customer = ORM::for_table('tbl_customers')->where('username', $routes['2'])->find_one();
        case 'view':
            $id = $routes['2'];
            run_hook('view_customer'); #HOOK
            if (!$customer) {
                $customer = ORM::for_table('tbl_customers')->find_one($id);
            }
            if ($customer) {
                // Fetch the Customers Attributes values from the tbl_customer_custom_fields table
                $customFields = ORM::for_table('tbl_customers_fields')
                    ->where('customer_id', $customer['id'])
                    ->find_many();
        
                $v = $routes['3'];
                if (empty($v) || $v == 'order') {
                    $v = 'order';
                    $paginator = Paginator::build(ORM::for_table('tbl_payment_gateway'), ['username' => $customer['username']]);
                    $order = ORM::for_table('tbl_payment_gateway')
                        ->where('username', $customer['username'])
                        ->offset($paginator['startpoint'])
                        ->limit($paginator['limit'])
                        ->order_by_desc('id')
                        ->find_many();
                    $ui->assign('paginator', $paginator);
                    $ui->assign('order', $order);
                    $ui->assign('ip_address', $customer['ip_address']);
                } else if ($v == 'activation') {
                    $paginator = Paginator::build(ORM::for_table('tbl_transactions'), ['username' => $customer['username']]);
                    $activation = ORM::for_table('tbl_transactions')
                        ->where('username', $customer['username'])
                        ->offset($paginator['startpoint'])
                        ->limit($paginator['limit'])
                        ->order_by_desc('id')
                        ->find_many();
                    $ui->assign('paginator', $paginator);
                    $ui->assign('activation', $activation);
                } else if ($v == 'traffic') {
                    $v = 'traffic';
                    $routers = User::_billing($customer['id']);
                    if ($routers) {
                        foreach ($routers as $row) {
                            $userRouters = $row->routers;
                            $mikrotik = Mikrotik::info($userRouters);
                            $router = $mikrotik['id'];
                        }
                    }
                    $ui->assign('traffic', $traffic);
                    $ui->assign('router', $router);
                }           if ($v == 'data-usage') {
                    // Fetch today's data usage for the customer
                    $todayUsage = ORM::for_table('tbl_daily_data_usage')
                        ->where('customer_id', $customer['id'])
                        ->where_raw('DATE(date) = DATE(NOW())')
                        ->find_one();
        
                    if ($todayUsage) {
                        $ui->assign('todayUsage', $todayUsage);
                        $ui->assign('hasTodayUsage', true);
                    } else {
                        $ui->assign('hasTodayUsage', false);
                    }
        
                    // Fetch weekly data usage for the customer
                    $weeklyUsage = ORM::for_table('tbl_weekly_data_usage')
                        ->where('customer_id', $customer['id'])
                        ->order_by_desc('week_start_date')
                        ->limit(1)
                        ->find_one();
        
                    if ($weeklyUsage) {
                        $ui->assign('weeklyUsage', $weeklyUsage);
                        $ui->assign('hasWeeklyUsage', true);
                    } else {
                        $ui->assign('hasWeeklyUsage', false);
                    }
        
                    // Fetch monthly data usage for the customer
                    $monthlyUsage = ORM::for_table('tbl_monthly_data_usage')
                        ->where('customer_id', $customer['id'])
                        ->find_one();
        
                    if ($monthlyUsage) {
                        $ui->assign('monthlyUsage', $monthlyUsage);
                        $ui->assign('hasMonthlyUsage', true);
                    } else {
                        $ui->assign('hasMonthlyUsage', false);
                    }
                }
                
        
                $package = ORM::for_table('tbl_user_recharges')->where('username', $customer['username'])->find_one();
                $ui->assign('package', $package);
                $ui->assign('v', $v);
                $ui->assign('d', $customer);
                $ui->assign('customFields', $customFields);
                $ui->assign('xheader', $leafletpickerHeader);
                $ui->display('customers-view.tpl');
            } else {
                r2(U . 'customers/list', 'e', Lang::T('Account Not Found'));
            }
            break;
        
        case 'edit':
            if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent'])) {
                _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
            }
            
            $id = $routes['2'];
            run_hook('edit_customer'); #HOOK
            
            $d = ORM::for_table('tbl_customers')->find_one($id);
            $customFields = ORM::for_table('tbl_customers_fields')
                ->where('customer_id', $id)
                ->find_many();
            
            if ($d) {
                // Fetch the list of routers from the database
                $routers = ORM::for_table('tbl_routers')->find_many();
                
                $ui->assign('d', $d);
                $ui->assign('customFields', $customFields);
                $ui->assign('xheader', $leafletpickerHeader);
                
                // Assign the list of routers to the template
                $ui->assign('routers', $routers);
                
                // Check if the form is submitted
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $balance = $_POST['balance'];
                    $customer = ORM::for_table('tbl_customers')->find_one($id);
                    
                    if ($customer) {
                        $customer->set('balance', $balance);
                        $customer->save();
                        
                        // Handle success case
                        _alert(Lang::T('Balance updated successfully'), 'success', $_url . 'customers/list');
                    } else {
                        // Handle failure case
                        _alert(Lang::T('Failed to update balance'), 'danger');
                    }
                }
                
                $ui->display('customers-edit.tpl');
            } else {
                r2(U . 'customers/list', 'e', Lang::T('Account Not Found'));
            }
            break;
    case 'delete':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
            _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
        }
        $id  = $routes['2'];
        run_hook('delete_customer'); #HOOK
        $d = ORM::for_table('tbl_customers')->find_one($id);
        if ($d) {
            // Delete the associated Customers Attributes records from tbl_customer_custom_fields table
            ORM::for_table('tbl_customers_fields')->where('customer_id', $id)->delete_many();
            $c = ORM::for_table('tbl_user_recharges')->where('username', $d['username'])->find_one();
            if ($c) {
                $p = ORM::for_table('tbl_plans')->find_one($c['plan_id']);
                if ($p['is_radius']) {
                    Radius::customerDelete($d['username']);
                } else {
                    $mikrotik = Mikrotik::info($c['routers']);
                    if ($c['type'] == 'Hotspot') {
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        Mikrotik::removeHotspotUser($client, $d['username']);
                        Mikrotik::removeHotspotActiveUser($client, $d['username']);
                    } elseif ($c['type'] == 'PPPOE') {
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        Mikrotik::removePpoeUser($client, $d['username']);
                        Mikrotik::removePpoeActive($client, $d['username']);
                    } elseif ($c['type'] == 'Static') {
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                      
                        Mikrotik::removeStaticUser($client, $d['username']);
                    }
                    try {
                        $d->delete();
                    } catch (Exception $e) {
                    } catch (Throwable $e) {
                    }
                    try {
                        $c->delete();
                    } catch (Exception $e) {
                    }
                }
            } else {
                try {
                    $d->delete();
                } catch (Exception $e) {
                } catch (Throwable $e) {
                }
                try {
                    if($c) $c->delete();
                } catch (Exception $e) {
                } catch (Throwable $e) {
                }
            }
            _log('[' . $admin['username'] . ']: Customer ' . $deletedUsername . ' deleted successfully', $admin['user_type'], $admin['id']);
            r2(U . 'customers/list', 's', Lang::T('User deleted Successfully'));
        }
        break;

        case 'add-post':
            $username = _post('username');
            $fullname = _post('fullname');
            $password = _post('password');
            $pppoe_password = _post('pppoe_password');
            $email = _post('email');
            $address = _post('address');
            $phonenumber = _post('phonenumber');
            $service_type = _post('service_type');
            $coordinates = _post('coordinates');
            $custom_field_names = (array) $_POST['custom_field_name'];
            $custom_field_values = (array) $_POST['custom_field_value'];
            $ip_address = _post('ip_address');
            $router_id = _post('router_id');
            
            if ($router_id == '') {
                $msg = 'A router must be chosen.' . '<br>';
                r2(U . 'customers/add', 'e', $msg);
                break;
            }
            
            run_hook('add_customer'); #HOOK
            
            $msg = '';
            if (Validator::Length($username, 35, 2) == false) {
                $msg .= 'Username should be between 3 to 55 characters' . '<br>';
            }
            if (Validator::Length($fullname, 36, 2) == false) {
                $msg .= 'Full Name should be between 3 to 25 characters' . '<br>';
            }
            if (!Validator::Length($password, 36, 2)) {
                $msg .= 'Password should be between 3 to 35 characters' . '<br>';
            }
        
            $d = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
            if ($d) {
                $msg .= Lang::T('Account already exist') . '<br>';
            }
        
            if ($msg == '') {
                $d = ORM::for_table('tbl_customers')->create();
                $d->username = Lang::phoneFormat($username);
                $d->password = $password;
                $d->pppoe_password = $pppoe_password;
                $d->email = $email;
                $d->fullname = $fullname;
                $d->address = $address;
                $d->created_by = $admin['id'];
                $d->phonenumber = Lang::phoneFormat($phonenumber);
                $d->service_type = $service_type;
                $d->coordinates = $coordinates;
                $d->ip_address = $ip_address;
                $d->router_id = $router_id;
                $d->save();

                _log('[' . $admin['username'] . ']: Customer ' . $d->username . ' created successfully', $admin['user_type'], $admin['id']);
                
                // Retrieve the customer ID of the newly created customer
                $customerId = $d->id();
                // Save Customers Attributes details
                if (!empty($custom_field_names) && !empty($custom_field_values)) {
                    $totalFields = min(count($custom_field_names), count($custom_field_values));
                    for ($i = 0; $i < $totalFields; $i++) {
                        $name = $custom_field_names[$i];
                        $value = $custom_field_values[$i];
        
                        if (!empty($name)) {
                            $customField = ORM::for_table('tbl_customers_fields')->create();
                            $customField->customer_id = $customerId;
                            $customField->field_name = $name;
                            $customField->field_value = $value;
                            $customField->save();
                        }
                    }
                }
        
                // After saving the new customer
                // Load the notifications.json file
                $notifications = json_decode(file_get_contents($UPLOAD_PATH . DIRECTORY_SEPARATOR . 'notifications.json'), true);
                
                if (isset($notifications['account_created_sms'])) {
                    // Prepare the message text
                    $message = $notifications['account_created_sms'];
                    $message = str_replace('[[name]]', $d->fullname, $message);
                    $message = str_replace('[[user_name]]', $d->username, $message);
                    $message = str_replace('[[user_password]]', $d->password, $message);
                    // Send the SMS
                    Message::sendAccountCreateNotification($d->phonenumber, $d->fullname, $d->username, $d->password, $message, $config['user_notification_expired']);
                }
                
                r2(U . 'customers/list', 's', Lang::T('Account Created Successfully'));
            } else {
                r2(U . 'customers/add', 'e', $msg);
            }
            break;
        

    case 'edit-post':
        $username = Lang::phoneFormat(_post('username'));
        $fullname = _post('fullname');
        $password = _post('password');
        $pppoe_password = _post('pppoe_password');
        $email = _post('email');
        $address = _post('address');
        $phonenumber = Lang::phoneFormat(_post('phonenumber'));
        $service_type = _post('service_type');
        $coordinates = _post('coordinates');
        $ip_address = _post('ip_address');
        $router_id = _post('router_id');
        if ($router_id == '') {
            $router_id = NULL; // Set router_id to NULL if no selection was made
        }
        run_hook('edit_customer'); #HOOK
        $msg = '';
        if (Validator::Length($username, 35, 2) == false) {
            $msg .= 'Username should be between 3 to 15 characters' . '<br>';
        }
        if (Validator::Length($fullname, 36, 1) == false) {
            $msg .= 'Full Name should be between 2 to 25 characters' . '<br>';
        }
        if ($password != '') {
            if (!Validator::Length($password, 36, 2)) {
                $msg .= 'Password should be between 3 to 15 characters' . '<br>';
            }
        }

        $id = _post('id');
        $d = ORM::for_table('tbl_customers')->find_one($id);
        //lets find user Customers Attributes using id
        $customFields = ORM::for_table('tbl_customers_fields')
         ->where('customer_id', $id)
         ->find_many();       
        if (!$d) {
            $msg .= Lang::T('Data Not Found') . '<br>';
        }

        $oldusername = $d['username'];
        $oldPppoePassword =  $d['password'];
        $oldPassPassword =  $d['pppoe_password'];
        $userDiff = false;
        $pppoeDiff = false;
        $passDiff = false;
        if ($oldusername != $username) {
            $c = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
            if ($c) {
                $msg .= Lang::T('Account already exist') . '<br>';
            }
            $userDiff = true;
        }
        if ($oldPppoePassword != $pppoe_password) {
            $pppoeDiff = true;
        }
        if ($password != '' && $oldPassPassword != $password) {
            $passDiff = true;
        }

        if ($msg == '') {
            if ($userDiff) {
                $d->username = $username;
            }
            if ($password != '') {
                $d->password = $password;
            }
            $d->pppoe_password = $pppoe_password;
            $d->fullname = $fullname;
            $d->email = $email;
            $d->address = $address;
            $d->phonenumber = $phonenumber;
            $d->service_type = $service_type;
            $d->coordinates = $coordinates;
            $d->ip_address = $ip_address;
            $d->router_id = $router_id; // Update router_id in the customer record
            $d->save();
            _log('[' . $admin['username'] . ']: Customer ' . $d->username . ' edited successfully', $admin['user_type'], $admin['id']);

             // Update Customers Attributes values in tbl_customers_fields table
             foreach ($customFields as $customField) {
                $fieldName = $customField['field_name'];
                if (isset($_POST['custom_fields'][$fieldName])) {
                    $customFieldValue = $_POST['custom_fields'][$fieldName];
                    $customField->set('field_value', $customFieldValue);
                    $customField->save();
                }
            }

            // Add new Customers Attributess
            if (isset($_POST['custom_field_name']) && isset($_POST['custom_field_value'])) {
                $newCustomFieldNames = $_POST['custom_field_name'];
                $newCustomFieldValues = $_POST['custom_field_value'];

                // Check if the number of field names and values match
                if (count($newCustomFieldNames) == count($newCustomFieldValues)) {
                    $numNewFields = count($newCustomFieldNames);

                    for ($i = 0; $i < $numNewFields; $i++) {
                        $fieldName = $newCustomFieldNames[$i];
                        $fieldValue = $newCustomFieldValues[$i];

                        // Insert the new Customers Attributes
                        $newCustomField = ORM::for_table('tbl_customers_fields')->create();
                        $newCustomField->set('customer_id', $id);
                        $newCustomField->set('field_name', $fieldName);
                        $newCustomField->set('field_value', $fieldValue);
                        $newCustomField->save();
                    }
                }
            }

             // Delete Customers Attributess
             if (isset($_POST['delete_custom_fields'])) {
                $fieldsToDelete = $_POST['delete_custom_fields'];
                foreach ($fieldsToDelete as $fieldName) {
                    // Delete the Customers Attributes with the given field name
                    ORM::for_table('tbl_customers_fields')
                        ->where('field_name', $fieldName)
                        ->where('customer_id', $id)
                        ->delete_many();
                }
            }
           

            if ($userDiff || $pppoeDiff || $passDiff) {
                $c = ORM::for_table('tbl_user_recharges')->where('username', ($userDiff) ? $oldusername : $username)->find_one();
                if ($c) {
                    $c->username = $username;
                    $c->save();
                    $p = ORM::for_table('tbl_plans')->find_one($c['plan_id']);
                    if ($p['is_radius']) {
                        if($userDiff){
                            Radius::customerChangeUsername($oldusername, $username);
                        }
                              Radius::customerAddPlan($d, $p, $p['expiration'] . ' ' . $p['time']);
                    } else {
                        $mikrotik = Mikrotik::info($c['routers']);
                        if ($c['type'] == 'Hotspot') {
                            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                            Mikrotik::setHotspotUser($client, $c['username'], $password);
                            Mikrotik::removeHotspotActiveUser($client, $d['username']);
                        } elseif ($c['type'] == 'PPPoE') {
                            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                            if (!empty($d['pppoe_password'])) {
                                Mikrotik::setPpoeUser($client, $c['username'], $d['pppoe_password']);
                            } else {
                                Mikrotik::setPpoeUser($client, $c['username'], $password);
                            }
                            Mikrotik::removePpoeActive($client, $d['username']);
                        } elseif ($c['type'] == 'Static') {
                            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);



                        }
                    }
                }
            }
            r2(U . 'customers/list', 's', 'User Updated Successfully');
        } else {
            r2(U . 'customers/edit/' . $id, 'e', $msg);
        }
        break;

    default:
        r2(U . 'customers/list', 'e', 'action not defined');
}
