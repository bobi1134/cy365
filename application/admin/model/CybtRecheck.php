<?php
namespace app\admin\model;

use think\Model;

class CybtRecheck extends Model {
    protected $pk = 'id';

    public function getResultTextAttr($value,$data) {
        $status = [ -1 => '不通过' , 0 => '-' , 1 => '通过' ];
        return $status[$data['result']];
    }

    public function getAddressAttr($value) {
        if (empty($value))
        	return "-";
        return $value;
    }

    public function getDateAttr($value) {
        if (empty($value))
        	return "-";
        return $value;
    }

    public function getFinishTimeAttr($value) {
    	if (empty($value))
    		return "无";
    	return date('Y-m-d',$value);
    }
}