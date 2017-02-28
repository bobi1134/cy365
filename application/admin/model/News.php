<?php

namespace app\admin\model;

use think\Model;

class News extends Model{
    protected $pk = 'id';

    /* 一对一 关联 admin表 */
    public function admin() {
        return $this->hasOne('Admin','id','author');
    }
}