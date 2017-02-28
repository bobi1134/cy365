<?php
namespace app\user\model;

use think\Model;

class Major extends Model {
    protected $pk = 'id';

    public function academy() {
        return $this->hasOne('Academy','id','academy_id');
    }
}