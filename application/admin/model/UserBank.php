<?php
namespace app\admin\model;

use think\Model;

class UserBank extends Model {
    protected $pk = 'id';

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 =>'未添加' , 1 => '正常' ];
        return $status[$data['status']];
    }
}