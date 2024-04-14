<?php
/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/


if(Admin::getID()){
    r2(U.'dashboard');
}if(User::getID()){
    r2(U.'home');
}else{
    r2(U.'login');
}
