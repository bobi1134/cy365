<?php
namespace app\admin\model;

use think\Model;

class CybtReview extends Model {
    protected $pk = 'id';

    /* 一对一 关联 Cybt表 */
    public function cybt() {
        return $this->hasOne('Cybt','id','cybt_id');
    }

    public function getResultTextAttr($value,$data) {
        $status = [ -1 =>'不通过' , 0 => '-' , 1 => '通过' , 2 => '存在争议',3=>'通过'];
        return $status[$data['result']];
    }

    public function admin()
    {
        return $this->hasOne('admin','id','admin_id');
    }
}