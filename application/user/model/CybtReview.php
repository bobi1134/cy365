<?php
namespace app\user\model;

use think\Model;

class CybtReview extends Model {
    protected $pk = 'id';

    public function getResultTextAttr($value,$data) {
        $status = [ -1 => '不通过' , 0 => '-' , 1 => '通过' , 2 => '存在争议' ,3 => '通过'];
        return $status[$data['result']];
    }

    public function admin() {
        return $this->hasOne('Admin','id','admin_id');
    }

    public function getFinishTimeAttr($value) {
        return empty($value) ? "-" : date('Y-m-d',$value);
    }

    public function getNoteAttr($value) {
        return empty($value) ? "-" : $value;
    }
}