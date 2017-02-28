<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/12 0012
 * Time: 上午 10:58
 */

namespace app\admin\model;


use think\Model;

class Cybt extends Model
{
	/* 一对一 关联 Review表 */
    public function review() {
        return $this->hasOne('CybtReview','cybt_id','id');
    }

    /* 一对一 关联 Review表 */
    public function recheck() {
        return $this->hasOne('CybtRecheck','cybt_id','id');
    }

    /* 一对一 关联 UserInfo表 */
    public function user() {
        return $this->hasOne('User','id','user_id');
    }

    public function getTypeTextAttr($value,$data) {
        $status = [ 1 =>'个人领办' , 2 => '创业团队领办' , 3 =>'创业实体领办' , 0=>'未选择'];
        return $status[$data['type']];
    }

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 => '填报中' , 1 => '填报完成' , 2 => '初审中' , 3 => '复审中' , 4 => '完成'];
        return $status[$data['status']];
    }

    public function getSubmitTimeAttr($value) {
        return date("Y-m-d",$value);
    }
}