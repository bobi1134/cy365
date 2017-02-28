<?php
namespace app\user\controller;

use app\admin\model\Cybt;
use app\user\model\Academy;
use app\user\model\Major;
use app\user\model\UserAuth;
use think\Controller;
use app\user\model\User;
use app\user\model\UserInfo;

class Index extends Common{
    
   	/**
	 * 账户基本信息页面显示
	 * @return [type] [description]
	 */
    public function index() {

		$this->view->assign('user',$this->user->id);
    	return $this->view->fetch('index');
    }


	/**
	 * 用户申报的创业补贴项目页面
	 * @return mixed
     */
	public function cybtList()
	{
		$projects_finish=Cybt::where(['user_id'=>$this->user->id,'status'=>'1|2|3'])->select();
		$projects_unfinish=Cybt::where(['user_id'=>$this->user->id,'status'=>0])->select();
		$this->view->assign('projects',$projects_finish);
		$this->view->assign('projects_un',$projects_unfinish);
		return $this->view->fetch('cybtlist');
	}

    /**
     * 实名认证页面显示
     * @return [type] [description]
     */
    public function auth() {
		$this->view->assign('user',$this->user->auth->toArray());
    	return $this->view->fetch('auth');
    }

	/**
	 * 编辑资料页面
	 * @return mixed
     */
	public function edit()
	{
		$user = User::get($this->user->id);
		$userinfo=$user->info->getData();
		$users=$user->getData();
		if($user->info->major_id!=0){
			$usermajor=$user->info->major->getData();
		}else{
			$usermajor['major_name']=0;
			$usermajor['academy_id']=0;
		};

		$data = array_merge($userinfo,$users,$usermajor);
		$academy = Academy::all();  //获取所有学院

		$academy_id = $usermajor['academy_id'];
		//$major=Major::all(['academy_id',$academy_id]);
		if($academy_id==0){
			$academy_id = 9;
		}
		$major=Major::all(['academy_id'=>$academy_id]);
		$this->view->assign('major',$major);

		$this->view->assign('academy',$academy);
		$this->view->assign('user',$data);

		return $this->view->fetch("edit");
	}


	/**
	 * 修改密码页面
	 * @return mixed
	 */
	public function password()
	{
		$user=$this->user->id;
		$this->view->assign("user_id",$user);
		return $this->view->fetch('password');
	}

	/**
	 * 绑定银行卡页面
	 */
	public function bank()
	{
		$status=$this->user->bank->status;//获取用户是否绑定银行卡
		$this->view->assign("user_id",$this->user->id);
		$this->view->assign("status",$status);
		return $this->view->fetch("bank");
	}

	public function notice()
	{
		return $this->view->fetch("notice");
	}
}
