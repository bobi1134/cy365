<?php
namespace app\file\model;
//Image模型，实现图片相关数据库操作
class Image extends \think\Model
{
	//设置自动写入时间
	protected $autoWriteTimestamp=true;
	//设置类型转换
	protected $type=[
		'create_time'=>'datetime',
		'update_time'=>'datetime',
		// 'uid'=>'integer',
	];
	//修改器
	public function setImageAttr($value){
		return $value;
	}
	//获取器
	public function getIdAttr($value)
	{
		return $value;
	}

}