<?php
namespace app\admin\model;

use think\Model;

class User extends Model {
    protected $pk = 'id';
    
    /* 一对一 关联 UserInfo表 */
    public function info() {
        return $this->hasOne('UserInfo','user_id','id');
    }

    /* 一对一 关联 Bank表 */
    public function bank() {
    	return $this->hasOne('UserBank','user_id','id');
    }

    /* 一对一 关联 Auth表 */
    public function auth() {
        return $this->hasOne('UserAuth','user_id','id');
    }

    /* 一堆垛 关联 Cybt表 */
    public function cybt() {
        return $this->hasMany('Cybt','user_id','id');
    }

    public function getStatusTextAttr($value,$data) {
        $status = [ 0 =>'未激活' , 1 => '正常' ];
        return $status[$data['status']];
    }

}