<?php
namespace app\user\model;

use think\Model;

class Msg extends Model {
    protected $pk = 'id';

    public function getMsgTypeTextAttr($value,$data) {
    	$status = [ 1 =>'系统消息' , 2 => '推广消息' ];
        return $status[$data['msg_type']];
    }

    public function getSendTimeAttr($value) {
        return date('Y-m-d h:m:s',$value);
    }
}