<?php
/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/



 header("Access-Control-Allow-Origin: *");
 header("Access-Control-Allow-Methods: POST");
 header("Access-Control-Allow-Headers: Content-Type");
 


if(function_exists($routes[1])){
    call_user_func($routes[1]);
}else{
    r2(U.'dashboard', 'e', 'Function not found');
}
