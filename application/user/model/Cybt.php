<?php
namespace app\user\model;

use think\Model;

class Cybt extends Model {
    protected $pk = 'id';

    public function review() {
        return $this->hasOne('CybtReview','cybt_id','id');
    }

    public function recheck() {
        return $this->hasOne('CybtRecheck','cybt_id','id');
    }

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

    public function getFinishTimeAttr($value) {
        return empty($value) ? '-' : date('Y-m-d',$value);
    }

    public function getSubmitTimeAttr($value) {
        return empty($value) ? '-' : date('Y-m-d',$value);
    }

    public function getStartTimeAttr($value) {
        return empty($value) ? '-' : date('Y-m-d',$value);
    }

    public function getAbandonedUrlAttr() {
        return url('user/service/cybtAbandonedById',['id' => $this->id]);
    }

    public function getProjectsNameAttr($value) {
        return empty($value) ? '-' : $value;
    }
}