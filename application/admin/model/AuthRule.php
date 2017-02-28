<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7 0007
 * Time: 上午 9:30
 */

namespace app\admin\model;


use think\Model;

class AuthRule extends Model
{
	public function getMenuTextAttr($value) {
        $status = [ 1 => '是' , 0 => '否'];
        return $status[$value];
    }

    public function getPidTextAttr() {

        if (empty($this->pid) || $this->pid == 0)
           return '父级菜单';
        $temp = \think\Db::table('auth_rule')->where('id',$this->pid)->find();
        return $temp['name'];
    }
}