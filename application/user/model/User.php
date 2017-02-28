<?php
namespace app\user\model;

use think\Model;

class User extends Model {
    protected $pk = 'id';
    
    public function info() {
        return $this->hasOne('UserInfo','user_id','id');
    }

    public function bank() {
    	return $this->hasOne('UserBank','user_id','id');
    }

    public function auth() {
        return $this->hasOne('UserAuth','user_id','id');
    }

    public function cybt() {
        return $this->hasMany('Cybt','user_id','id');
    }

    public function msg() {
        return $this->hasMany('Msg','user_id','id');
    }

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 =>'未激活' , 1 => '正常' ];
        return $status[$data['status']];
    }

}