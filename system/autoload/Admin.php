<?php
/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/


Class Admin{
    public static function _info(){
        $id = $_SESSION['aid'];
        $d = ORM::for_table('tbl_users')->find_one($id);
        return $d;
    }
}