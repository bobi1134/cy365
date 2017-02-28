<?php
namespace app\file\controller;
use \app\file\model\File;
use \app\file\model\Image;
// use think\Input;
use think\Session;

//文件删除模块，实现文件删除操作相关功能
class Delete extends \think\Controller
{
	//删除照片，uid--照片id
	public function DeleteImage()
	{
			$id=$this->request->param('id');
		    if (empty($id))
				return error('文件id不能为空');
			$image=Image::get($id);

			$uid=$image->uid;
			$uids=Session::get('user_id');
			if($uid!=$uids)
			{
				return $this->error('此照片不能删除');
			}
			//从本地删除相应照片
			$info=unlink($image->image);
			//从数据库删除相应照片
			$num=$image->delete();
			//判断是否删除成功，删除成功返回删除成功照片的id
			if($info==true&&$num==1)
			{
			return $this->success('删除照片成功',null,['id' => $id]);
				
			}else
			{
				return $this->error('删除照片失败');
			}
	}
}
