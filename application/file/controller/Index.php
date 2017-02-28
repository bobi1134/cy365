<?php
namespace app\file\controller;
use \app\file\model\File;
use \app\file\model\Image;
//文件模块，实现文件操作相关功能
class Index extends \think\Controller
{

	//传入要预览文件的id。并存在服务器中
	public function index($id=45)
	{
		return view();
	}
	

}
