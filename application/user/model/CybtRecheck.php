<?php
namespace app\user\model;

use think\Model;

class CybtRecheck extends Model {
    protected $pk = 'id';

    public function admin() {
        return $this->hasOne('Admin','id','admin_id');
    }

    public function getUserAttr() {
        if (empty($this->admin_id))
            return "-";
        $temp = \think\Db::table('admin')->where('id',$this->admin_id)->find();
        return $temp['name'];
    }

    public function getResultTextAttr($value,$data) {
        $status = [ -1 => '不通过' , 0 => '-' , 1 => '通过' ];
        return $status[$data['result']];
    }

    public function getAddressAttr($value) {
        return empty($value) ? "-" : $value;
    }

    public function getDateAttr($value) {
        return empty($value) ? "-" : $value;
    }

    public function getFinishTimeAttr($value) {
    	return empty($value) ? "-" : date('Y-m-d',$value);
    }

    public function getNoteAttr($value) {
        return empty($value) ? "-" : $value;
    }
}