<?php
namespace app\user\controller;

use app\user\model\Major;
use think\Controller;
use app\user\model\User;
use app\user\model\Cybt;
use app\user\model\UserInfo;
use think\Input;

class Service extends Common {
	
	/**
	 * 获取创业补贴列表
	 * @return [type] [description]
	 */
	public function cybtGetList() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'page|页码' => 'number',
                'limit|每页数量' => 'number',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $page = (int)(empty($this->request->param('page')) ? 1 : $this->request->param('page'));
        $limit = (int)(empty($this->request->param('limit')) ? 10 : $this->request->param('limit'));

		$cybtList = $this->user->cybt()->field( 'id,projects_name,submit_time,start_time,status' )->order( 'start_time desc' )->page( $page , $limit )->select();

		$count = $this->user->cybt()->count();
		$data = [
			'cybtList'	=>	$cybtList ,
			'totalPage'	=>	ceil($count / $limit) ,
			'totalNum'	=>	$count ,
			'presentPage'	=>	$page ,
			'presentNum'	=>	($page-1)*$limit + count($cybtList) ,
		];
		return $this->success('获取创业补贴列表成功',url('index/service/cybtGetIdTapped'),$data);
	}

	/**
	 * 通过ID获取一个申报的详细信息
	 * @return [type] [description]
	 */
	public function cybtGetInfoById() {

		// 返回json格式
        \think\Config::set('default_return_type','json');

        $cybt = Cybt::get( $this->request->param('id') );

        // 如果不为空则判断是否为当前用户的申请
        if (empty($cybt) || $cybt->user_id != $this->user->id)
        	return $this->error('不存在此条申请');

        $mainInfo = $cybt->append(['status_text','type_text','pass_text','abandoned_url'])->toArray();
        $reviewInfo = $cybt->review->append(['result_text'])->hidden(['admin_id','cybt_id','id'])->toArray();
        $adminInfo = $cybt->review->admin->hidden(['id','power_id','passwd','academy_id','code','status'])->toArray();
        $userInfo = $cybt->user->auth->realname;
        $recheckInfo = empty($cybt->recheck) ? null : $cybt->recheck->append(['result_text','user'])->toArray();
        $data = [
        	'main'		=> $mainInfo ,
        	'review'	=> $reviewInfo ,
        	'admin'		=> $adminInfo ,
        	'user'		=> ['realname' => $userInfo ] ,
        	'recheck'	=> $recheckInfo ,
        ];

        return $this->success('获取成功',null,$data);
	}

	/**
	 * 通过ID放弃一个提交的申报
	 * @return [type] [description]
	 */
	public function cybtAbandonedById() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $cybt = Cybt::get( $this->request->param('id') );

        // 如果不为空则判断是否为当前用户的申请
        if (empty($cybt) || $cybt->user_id != $this->user->id)
        	return $this->error('不存在此条申请');

        if ($cybt->status == 4) 
        	return $this->error('改项目已经完成，不可放弃哦！让它静静的躺在这里吧！');

        // 这里是删除文件和删除相关的申报
        if ( !empty($cybt->review) )
        	$cybt->review->delete();

        if ( !empty($cybt->recheck) )
        	$cybt->recheck->delete();

        $cybt->delete();

        return $this->success('放弃成功');
	}

	public function preview()
	{
		config('default_return_type','html');
		$url=Input::post();
		$projects_name=Input::post('projects_name');
		$projects_id=Input::post('projects_id');
		$id=$url['id'];
		//这儿根据前台具体情况修改--$id为文件id数组，前两个id为预览文档id，后面的id为图片id
		$response=[];
		foreach($id as $key => $val)
		{
			if($key<2)
			{
				//前两个id为预览文档id，返回文档url地址
				$response[$key]=action('file/get/getPreviewFile',$val);
			}else
			{
				//后面的id为图片id，返回图片url地址
				$response[$key]=action('file/get/getSingleImage',[$val,$key]);
			}
		}
		//数组形式返回所有文件url
		// dump($response);
		$this->view->assign('projects_id',$projects_id);  //创业补贴项目id
		$this->view->assign('projects_name',$projects_name);  //创业补贴项目名称
		$this->view->assign('response',$response);
		return $this->view->fetch('index/preview');
	}

	public function ajaxMajor()
	{
		\think\Config::set('default_return_type','json');
		$data=Input::post('academy_id');
		$major=Major::where("academy_id",$data)->select();
		$data=[];
		foreach($major as $key => $value){
			$data[$key]['id']=$value->id;
			$data[$key]['text']=$value->major_name;
		}

		return $data;
	}
}