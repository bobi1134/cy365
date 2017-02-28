<?php
namespace app\admin\model;

use think\Model;

class UserInfo extends Model {
    protected $pk = 'id';    

    /* 一对一 关联 Major表 */
    public function major() {
        return $this->hasOne('Major','id','major_id');
    }

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 =>'未认证' , 1 => '已实名认证' ];
        return $status[$data['status']];
    }

    public function getSexTextAttr($value,$data) {
        $status = [ 0 => '男生' , 1 => '女生' , 2 => '保密' ];
        return $status[$data['sex']];
    }

    public function getQualificationsTextAttr($value,$data) {
        $status = [ 0 => '本科' , 1 => '研究生硕士' , 2 => '研究生博士' ];
        return $status[$data['qualifications']];
    }
}