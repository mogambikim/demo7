<?php
/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/

_admin();
$ui->assign('_title', $_L['Static_Plans']);
$ui->assign('_system_menu', 'services');

$action = $routes['1'];
$admin = Admin::_info();
$ui->assign('_admin', $admin);

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
	r2(U."dashboard",'e',$_L['Do_Not_Access']);
}

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';


switch ($action) {
	case 'lists':
	    $ui->assign('_title', $_L['Static_IP_Plans']);
	    $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/c/static-ip.js"></script>');

	    $name = _post('name');
	    if ($name != '') {
	        $paginator = Paginator::build(ORM::for_table('tbl_plans'), ['name_plan' => '%' . $name . '%', 'type' => 'static'], $name);
	        $d = ORM::for_table('tbl_bandwidth')->join('tbl_plans', array('tbl_bandwidth.id', '=', 'tbl_plans.id_bw'))->where('tbl_plans.type', 'static')->where_like('tbl_plans.name_plan', '%' . $name . '%')->offset($paginator['startpoint'])->limit($paginator['limit'])->find_many();
	    } else {
	        $paginator = Paginator::build(ORM::for_table('tbl_plans'), ['type' => 'static'], $name);
	        $d = ORM::for_table('tbl_bandwidth')->join('tbl_plans', array('tbl_bandwidth.id', '=', 'tbl_plans.id_bw'))->where('tbl_plans.type', 'static')->offset($paginator['startpoint'])->limit($paginator['limit'])->find_many();
	    }

	    $ui->assign('d', $d);
	    $ui->assign('paginator', $paginator);
	    run_hook('view_list_static'); #HOOK
	    $ui->display('static.tpl');

	    break;





	    case 'add':
	        $ui->assign('_title', $_L['Static_IP_Plans']);
	        $d = ORM::for_table('tbl_bandwidth')->find_many();
	        $ui->assign('d', $d);
	        $r = ORM::for_table('tbl_routers')->find_many();
	        $ui->assign('r', $r);
	        run_hook('view_add_static'); // Update the hook for static IP
	        $ui->display('static-add.tpl'); // Ensure this template exists for adding static IP plans
	        break;

	    case 'edit':
	        $ui->assign('_title', $_L['Static_IP_Plans']);
	        $id = $routes['2'];
	        $d = ORM::for_table('tbl_plans')->find_one($id);
	        if ($d) {
	            $ui->assign('d', $d);
	            $p = ORM::for_table('tbl_pool')->where('routers', ($d['is_radius']) ? 'radius' : $d['routers'])->find_many();
	            $ui->assign('p', $p);
	            $b = ORM::for_table('tbl_bandwidth')->find_many();
	            $ui->assign('b', $b);
	            $r = ORM::for_table('tbl_routers')->find_many();
	            $ui->assign('r', $r);
	            run_hook('view_edit_static'); // Update the hook for editing static IP
	            $ui->display('static-edit.tpl'); // Ensure this template exists for editing static IP plans
	        } else {
	            r2(U . 'services/static', 'e', $_L['Account_Not_Found']);
	        }
	        break;

	        case 'delete':
	            $id = $routes['2'];

	            $d = ORM::for_table('tbl_plans')->find_one($id);
	            if ($d) {
	                run_hook('delete_static'); // Update the hook for static IP deletion
	                // You can add any specific logic here if needed for static IP plans
	                if ($d['is_radius']) {
	                    Radius::planDelete($d['id']);
	                } else {

	                try {
	                    $mikrotik = Mikrotik::info($d['routers']);
	                    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
	                   //below the are saying remove pppoe plan check on that
	                   //thios has been checked and rectified
	                    Mikrotik::removeStaticPlan($client, $d['name_plan']);
	                } catch (Exception $e) {
	                    //ignore exception, it means router has already deleted
	                }
	            }
	            $d->delete();


	                r2(U . 'static/lists', 's', $_L['Delete_Successfully']);
	            } else {
	                r2(U . 'static/lists', 'e', $_L['Account_Not_Found']);
	            }
	            break;

	            case 'add-post':
	                $name = _post('name_plan');
	                $radius = _post('radius');
	                $id_bw = _post('id_bw');

	                $price = _post('price');
	                $validity = _post('validity');
	                $validity_unit = _post('validity_unit');
	                $routers = _post('routers');
	                $pool = _post('pool_name');
	                $pool_expired = _post('pool_expired');
	                $enabled = _post('enabled');
									$allow_purchase = _post('allow_purchase');


	                $msg = '';
	                if (Validator::UnsignedNumber($validity) == false) {
	                    $msg .= 'The validity must be a number' . '<br>';
	                }
	                if (Validator::UnsignedNumber($price) == false) {
	                    $msg .= 'The price must be a number' . '<br>';
	                }
	                if ($name == '' or $id_bw == '' or $price == '' or $validity == '') {
	                    $msg .= $_L['All_field_is_required'] . '<br>';
	                }
	                if ($routers == '') {
	                    $msg .= $_L['All_field_is_required'] . '<br>';
	                }

	                $d = ORM::for_table('tbl_plans')->where('name_plan', $name)->find_one();
	                if ($d) {
	                    $msg .= $_L['Plan_already_exist'] . '<br>';
	                }
	                run_hook('add_static'); // Update the hook for static IP

	                if ($msg == '') {
	                    $b = ORM::for_table('tbl_bandwidth')->where('id', $id_bw)->find_one();
	                    if ($b['rate_down_unit'] == 'Kbps') {
	                        $unitdown = 'K';
	                        $raddown = '000';
	                    } else {
	                        $unitdown = 'M';
	                        $raddown = '000000';
	                    }
	                    if ($b['rate_up_unit'] == 'Kbps') {
	                        $unitup = 'K';
	                        $radup = '000';
	                    } else {
	                        $unitup = 'M';
	                        $radup = '000000';
	                    }
	                   // Your existing code to construct the basic rate limit string
	                    $rate = $b['rate_up'] . $unitup . "/" . $b['rate_down'] . $unitdown;

	                    // Append burst limit parameters if they are set and not zero
	                    if (!empty($b['burst_limit_for_upload']) && !empty($b['burst_limit_for_download'])) {
	                        $burstLimitUpload = $b['burst_limit_for_upload'] . $unitup;
	                        $burstLimitDownload = $b['burst_limit_for_download'] . $unitdown;
	                        $rate .= " $burstLimitUpload/$burstLimitDownload";
	                    }

	                    // Append burst threshold parameters if they are set and not zero
	                    if (!empty($b['burst_threshold_for_upload']) && !empty($b['burst_threshold_for_download'])) {
	                        $burstThresholdUpload = $b['burst_threshold_for_upload'] . $unitup;
	                        $burstThresholdDownload = $b['burst_threshold_for_download'] . $unitdown;
	                        $rate .= " $burstThresholdUpload/$burstThresholdDownload";
	                    }

	                    // Append burst time parameters if they are set and not zero
	                    if (!empty($b['burst_time_for_upload']) && !empty($b['burst_time_for_download'])) {
	                        $burstTimeUpload = $b['burst_time_for_upload'];
	                        $burstTimeDownload = $b['burst_time_for_download'];
	                        $rate .= " $burstTimeUpload/$burstTimeDownload";
	                    }

	                    // Now $rate contains the full rate limit string, including burst settings if applicable
	                    // Continue with the code that sends this rate limit to MikroTik


	                    $radiusRate = $b['rate_up'] . $radup . '/' . $b['rate_down'] . $raddown;

	                    //now here is where we create more things for example type should be static

	                    $d = ORM::for_table('tbl_plans')->create();
	                    $d->type = 'static';
	                    $d->name_plan = $name;
	                    $d->id_bw = $id_bw;
	                    $d->price = $price;
	                    $d->validity = $validity;
	                    $d->validity_unit = $validity_unit;
	                    $d->pool = $pool;
	                    if (!empty($radius)) {
	                        $d->is_radius = 1;
	                        $d->routers = '';
	                    } else {
	                        $d->is_radius = 0;
	                        $d->routers = $routers;
	                        $d->pool_expired = $pool_expired;
	                    }
	                    $d->enabled = $enabled;
											$d->allow_purchase = $allow_purchase;
	                    $d->save();
	                    $plan_id = $d->id();

	                    if ($d['is_radius']) {
	                        Radius::planUpSert($plan_id, $radiusRate, $pool);
	                    } else {
	                        $mikrotik = Mikrotik::info($routers);
	                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
	                        Mikrotik::addStaticPlan($client, $name, $pool, $rate);
	                        if (!empty($pool_expired)) {
	                            Mikrotik::setStaticPlan($client, 'EXPIRED FREEISPRADIUS ' . $pool_expired, $pool_expired, '1K/1K');
	                        }
	                    }
	        //check here too how its structured on our case should be static or something services/dtatic
	                    r2(U . 'static/lists', 's', $_L['Created_Successfully']);
	                } else {
	                    r2(U . 'static/add', 'e', $msg);
	                }
	                break;

	                case 'edit-post':
	                    $id = _post('id');
	                    $name = _post('name_plan');
	                    $id_bw = _post('id_bw');
	                    $price = _post('price');
	                    $validity = _post('validity');
	                    $validity_unit = _post('validity_unit');
	                    $routers = _post('routers');
	                    $pool = _post('pool_name');
	                    $pool_expired = _post('pool_expired');
	                    $enabled = _post('enabled');
											$allow_purchase = _post('allow_purchase');


	                    $msg = '';
	        if (Validator::UnsignedNumber($validity) == false) {
	            $msg .= 'The validity must be a number' . '<br>';
	        }
	        if (Validator::UnsignedNumber($price) == false) {
	            $msg .= 'The price must be a number' . '<br>';
	        }
	        if ($name == '' or $id_bw == '' or $price == '' or $validity == '' or $pool == '') {
	            $msg .= $_L['All_field_is_required'] . '<br>';
	        }

	        $d = ORM::for_table('tbl_plans')->where('id', $id)->find_one();
	        if ($d) {
	        } else {
	            $msg .= $_L['Data_Not_Found'] . '<br>';
	        }

	        //check below
	        run_hook('edit_static'); #HOOK
	        if ($msg == '') {
	            $b = ORM::for_table('tbl_bandwidth')->where('id', $id_bw)->find_one();
	            if ($b['rate_down_unit'] == 'Kbps') {
	                $unitdown = 'K';
	                $raddown = '000';
	            } else {
	                $unitdown = 'M';
	                $raddown = '000000';
	            }
	            if ($b['rate_up_unit'] == 'Kbps') {
	                $unitup = 'K';
	                $radup = '000';
	            } else {
	                $unitup = 'M';
	                $radup = '000000';
	            }
	            $rate = $b['rate_up'] . $unitup . "/" . $b['rate_down'] . $unitdown;
	            $radiusRate = $b['rate_up'] . $radup . '/' . $b['rate_down'] . $raddown;

	            if ($d['is_radius']) {
	                Radius::planUpSert($id, $radiusRate, $pool);
	            } else {
	                $mikrotik = Mikrotik::info($routers);
	                $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
	               //needs more research on setpppoe plan
	                Mikrotik::setStaticPlan($client, $name, $pool, $rate);
	                if (!empty($pool_expired)) {
	                    Mikrotik::setStaticPlan($client, 'EXPIRED ' . $pool_expired, $pool_expired, '1K/1K');
	                }
	            }

	            $d->name_plan = $name;
	            $d->id_bw = $id_bw;
	            $d->price = $price;
	            $d->validity = $validity;
	            $d->validity_unit = $validity_unit;
	            $d->routers = $routers;
	            $d->pool = $pool;
	            $d->pool_expired = $pool_expired;
	            $d->enabled = $enabled;
							$d->allow_purchase = $allow_purchase;
	            $d->save();
	//check here needs more
	            r2(U . 'static/lists', 's', $_L['Updated_Successfully']);
	        } else {
	            r2(U . 'static/edit/' . $id, 'e', $msg);
	        }
	        break;



      	#ase 'static-ip':
	     #  $ui->assign('_title', $_L['Static_IP_Plans']);

	     // Replace 'your_table_name' with the actual table name
	    #   $staticIpPlans = ORM::for_table('tbl_static')->find_many();

	     // Assign the fetched data to the Smarty variable
	    # $ui->assign('staticIpPlans', $staticIpPlans);

	     // Render the template
	    # $ui->display('static-ip-plans.tpl');
	    #  break;


	     //my added files incase delete here

    default:
        $ui->display('a404.tpl');
}
