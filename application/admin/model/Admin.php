<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 13:33
 */
namespace app\admin\model;

use think\Model;

class Admin extends Model{

	/* 一对多关联Review表 */
	public function review() {
        return $this->hasMany('CybtReview','admin_id','id');
    }
    //修改器
    public function getStatusAttr($value)
    {
        $status = [-1=>'未激活',0=>'已激活'];
        return $status[$value];
    }

    public function getPowerIdAttr($value)
    {
        if (empty($value))
            return "-";
        $temp = \think\Db::table('auth_group')->where('id',$value)->find();
        return $temp['title'];
    }
    
}