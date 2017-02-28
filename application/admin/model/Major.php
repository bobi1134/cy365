<?php
namespace app\admin\model;

use think\Model;

class Major extends Model {
    protected $pk = 'id';

    /* 一对一 关联 Academy表 */
    public function academy() {
        return $this->hasOne('Academy','id','academy_id');
    }
}