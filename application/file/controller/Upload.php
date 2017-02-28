<?php
namespace app\file\controller;
use \app\file\model\File;
use \app\file\model\Image;
use \app\file\Get;
use think\Input;
use think\Session;

//文件上传模块，实现文件上传操作相关功能
class Upload extends \think\Controller
{
    //上传文件，uid--用户id，fileInput--文件输入框名
	public function uploadFile()
	{
		$inputFile=Input::file('file');
		// $type=$this->request->param('type');
		$uid=Session::get('user_id');
		//写入上传的文件到uploads/files/
		$info=$inputFile->rule('date')->move('uploads/files/');
		//写入成功--存数据库
		if($info)
		{
			$fileUrl=$info->getPath().'/'.$info->getFilename();
			$fileUrl=str_replace("\\","",$fileUrl);
			$fileType=$info->getExtension();
			$file=new File();
			$file->uid=$uid;
			$file->name=$inputFile->getInfo()['name'];
			$file->file=$fileUrl;
			$file->file_type=$fileType;
			$file->type=0;
			$file->save();
			//如果上传的文件类型为doc或者docx就生成预览文件
			if($fileType=='doc'||$fileType=='docx')
			{
				//action('Get/convert',$fileUrl);
			}
			//返回上传成功文件id
			return $this->success('上传文件成功',null,['id' => $file->id]);
		}else
		{
			return $this->error('上传文件失败');
		}
	}
	//上传文本并生成html网页文件,uid--用户id,textInput--文本框名
	public function uploadHtml($textInput='text')
	{

		$html=$this->request->param($textInput);
		$uid=Session::get('user_id');
		$date=date('Y-m-d H:m:s');
        $name=strtotime($date);
        $fileDir='uploads/files/'.date('Ymd');
        $fileName=$name.'.html';
        $fileUrl=$fileDir.'/'.$fileName;
        //生成html文件中的其它内容
        $html='
<html>
	<head>
	<title></title>
	</head>
	<body>
	'.$html.'
	</body>
</html>';

		//没有此目录时新建目录，防止无目录错误
	    if(!is_dir($fileDir))
	    	{
	    		mkdir($fileDir);
	    	}
	    //写入html文件
        file_put_contents($fileUrl,$html);

		$file=new File();
		$file->uid=$uid;
		$file->name=$name;
		$file->file=$fileUrl;
		$file->file_type='html';
		$file->type=0;
		$file->save();
		//返回上传成功文件id
		return $this->success('上传文件成功',null,['id' => $file->id]);
	}

	//上传文本编辑器中的照片，uid--用户id,type--照片类型（html照片），imageHtml--照片输入框名
	public function uploadHtmlImage($imageHtml='image')
	{
		$htmlImage=Input::file($imageHtml);
		$type=$this->request->param('type');
		$uid=Session::get('user_id');
		//写入上传的照片到uploads/files/
		$info=$htmlImage->rule('date')->move('uploads/images/');
		//写入成功--存数据库
		if($info)
		{
			$imageUrl=$info->getPath().'/'.$info->getFilename();
			$imageUrl=str_replace("\\","",$imageUrl);
			$image=new Image();
			$image->uid=$uid;
			$image->type=$type;
			$image->image=$imageUrl;
			$image->type=$type;
			$image->save();
			//返回上传成功照片的url
			return $this->success('上传照片成功',null,['url' => $info->getPath().'/'.$info->getFilename()]);
		}else
		{
			return $this->error('上传文照片败');
		}
	}



	//上传照片，uid--用户id,type--照片类型（第几张），imageInput--文件输入框名
	public function uploadImage()
	{
		$inputImage=Input::file('image');
		$type=$this->request->param('type');
		$uid=Session::get('user_id');
		//写入上传的照片到uploads/files/
		$info=$inputImage->rule('date')->move('uploads/images/');
		//写入成功--存数据库
		if($info)
		{
			$imageUrl=$info->getPath().'/'.$info->getFilename();
			$imageUrl=str_replace("\\","",$imageUrl);
			$image=new Image();
			$image->uid=$uid;
			$image->type=$type;
			$image->image=$imageUrl;
			$image->type=$type;
			$image->save();
			//返回上传成功照片的id
			return $this->success('上传照片成功',null,['id' => $image->id]);
		}else
		{
			return $this->error('上传文照片败');
		}
	}

}
