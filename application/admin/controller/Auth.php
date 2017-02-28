<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/22 0022
 * Time: 下午 7:08
 */

namespace app\admin\controller;


use think\Controller;
use think\Session;

class Auth
{
    public static function check()
    {
        return Session::get("userId")? true : false ;
    }

}