<?php
namespace app\index\model;

use think\Model;

class Cybt extends Model {
    protected $pk = 'id';
    protected $auto = ['number'];

    public function getFile1Attr() {
    	if (empty($this->file_id_1))
    		return null;

    	$temp = \think\Db::table('file')->where('id',$this->file_id_1)->find();
        return $temp['name'];
    }

    public function getFile2Attr() {
    	if (empty($this->file_id_2))
    		return null;
    	$temp = \think\Db::table('file')->where('id',$this->file_id_2)->find();
        return $temp['name'];
    }

    public function getFile3Attr() {
    	if (empty($this->file_id_3))
    		return null;

    	$temp = \think\Db::table('image')->where('id',$this->file_id_3)->find();
        return '\\'.$temp['image'];
    }

    public function getFile4Attr() {
    	if (empty($this->file_id_4))
    		return null;

    	$temp = \think\Db::table('image')->where('id',$this->file_id_4)->find();
        return '\\'.$temp['image'];
    }

    public function getFile5Attr() {
    	if (empty($this->file_id_5))
    		return null;

    	$temp = \think\Db::table('image')->where('id',$this->file_id_5)->find();
        return '\\'.$temp['image'];
    }
    
    public function getFile6Attr() {
    	if (empty($this->file_id_6))
    		return null;

    	$temp = \think\Db::table('image')->where('id',$this->file_id_6)->find();
        return '\\'.$temp['image'];
    }

    public function setNumberAttr() {
        if ( !empty($this->number) )
            return $this->number;

        $count = \think\Db::table('cybt')->where('start_time','>',strtotime(date('Y-m-d')) )->count();
        if ($count < 10 ) {
            $str = '000'.($count + 1);
        } else if ($count < 100){
            $str = '00'.($count + 1);
        } else if ($count < 1000){
            $str = '0'.($count + 1);
        } else if ($count < 10000){
            $str = $count + 1;
        }

        return date('Ymd',time()).$str;
    }
}