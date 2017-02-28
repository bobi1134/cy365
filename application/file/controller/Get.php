<?php
namespace app\file\controller;
use \app\file\model\File;
use \app\file\model\Image;
use \app\admin\controller;

// use think\Input;

//文件下载模块，实现文件下载操作相关功能
class Get extends \think\Controller
{
	//linux word 在线浏览测试接口
	public function getPre($id=32)
	{
		config('default_return_type','html');
		$file=File::get($id);
		
		$doc=$file->file;
		$pdf=$doc.'.pdf';

		// echo $pdf;

		$command='java -jar /opt/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar '.$doc.' '.$pdf;
		if(!file_exists($pdf))
			exec($command);
		return	$this->fetch('index/convert',['url'=>$pdf]);	
		
	}
	
	//获取所有文件url地址
	public function getAllUrl($id)
	{
		config('default_return_type','html');
		//这儿根据前台具体情况修改--$id为文件id数组，前两个id为预览文档id，后面的id为图片id
		$response=[];
		foreach($id as $key => $val)
		{
			if($key<2)
			{
				//前两个id为预览文档id，返回文档url地址
				$response[$key]=action('getPreviewFile',$val);
			}else
			{
				//后面的id为图片id，返回图片url地址
				$response[$key]=action('getSingleImage',[$val,$key]);
			}
		}
		//数组形式返回所有文件url
		// dump($response);

		return $this->success('所有文件',null,['response'=>json_encode($response)]);
	}

	/**
	 * 下载文件
	 * @param int $id 要下载的文件id
	 * @return json 重定向进行下载
	 */
	public function getSrcFile()
	{
		$id=$this->request->param('id');
		if(empty($id))
		{
			return error('文件不存在');
		}
		$file=File::get($id);
		$srcFile=$this->request->domain().'/'.$file->file;
		//重定向进行下载
		$this->redirect($srcFile,302);
	}

	//获取单个照片，id--要获取文件的id,key--文件标识
	public function getSingleImage($id,$key=0)
	{
		if (empty($id))
		{
			if($key>=5)
			{
				return '';
			}else
			{
				return error('文件不存在');
			}
		}
		$image=Image::get($id);
		if(file_exists($image->image)){
			$response='/'.str_replace("\\","",$image->image);
			return $response;
		}else{
			return $this->error("照片不存在");
		}

	}
	//word转pdf方法，doc--要转换文件的路径
	public function convert($doc)
	{
		$pdf=$doc.'.pdf';
		if(file_exists($pdf))
		{
			return $pdf;
		}else
		{
			//word转换为pdf
			$com='java -jar /opt/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar '.$doc.' '.$pdf;
			exec($com,$res,$state);
			if($state==0)
			{
				if(file_exists($pdf))
				{
					return $pdf;
				}else
				{
					return '';
				}
			}else
			{
				//开启转换服务
				$com2='/opt/openoffice4/program/soffice -headless -accept="socket,host=127.0.0.1,port=8100;urp;" -nofirststartwizard &';
				exec($com2,$res2,$state2);
				exec($com,$res,$state);
				if(file_exists($pdf))
				{
					return $pdf;
				}else
				{
					return '';
				}
			}
		}
	}

	//获取预览文件，id--要预览文件的id,此文件并存在于服务器中
	public function getPreviewFile($id)
	{
		// config('default_return_type','html');
		if ( empty($id) )
			return error('文件不存在');
		$file=File::get($id);
		//判断文件的类型，pdf,html不进行生成
		if($file->file_type=='pdf'||$file->file_type=='html')
		{
			if($file->file_type=='pdf')
			{
				//返回pdf文件地址
				// return $this->success('pdf类型文件',null,['fileUrl'=>$file->file]);
				return '/'.$file->file;
			}else
			{
				//返回txt文件地址
				// return $this->success('html类型文件',null,['fileUrl'=>$file->file]);
				return '/'.$file->file;
			}
		}else
		//生成word预览文件
		{
			return '/'.action('file/get/convert',$file->file);
		}
	}




	/*
	 * 获取所有的照片，接收照片id数组
	 * */
	public function getAllImages()
	{
		$id=$this->request->param();
		$response=[];
		foreach($id[0] as $key => $val){
			if (empty($val))
				return error('文件不存在');
			$image=Image::get($val);

			if(file_exists($image->image)){

				$response[$key][]='/'.str_replace("\\","",$image->image);

			}else{
				$response[$key][]="";
			}
		}
		return $response;
	}


	//下载文件模板，type--什么类型的模板(项目计划书模板，在校大学生创业补贴模板)。文件模板位于uploads/files/templates/
 	public function getTemplate($type='1')
 	{
		//返回文件下载地址
		$type=$this->request->param('type');
		if (empty($type))
			return error('文件类型不能为空');
		$fileUrl='uploads/files/templates/'.$type.'.docx';
		if(file_exists($fileUrl))
		{
			return $this->success('下载模板',null,['fileUrl'=>$fileUrl]);	
			
		}else
		{
			return $this->error('文件不存在');
		}
    }



	
}
