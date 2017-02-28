<?php
namespace app\admin\model;

use think\Model;

class UserAuth extends Model {
    protected $pk = 'id';

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 =>'未认证' , 1 => '审核中' , 2 => '正常' ];
        return $status[$data['status']];
    }
}