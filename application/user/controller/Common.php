<?php
namespace app\user\controller;

use app\user\model\Msg;
use think\Controller;
use app\user\model\User;
use app\user\model\Config;
use think\Request;
use think\Route;

class Common extends Controller {

	protected $view = null;
	protected $user = null;

	public function _initialize() {
		$this->user = User::get( \think\Session::get('user_id') );
        if (empty($this->user))
            return $this->error('亲，你需要先登录哦！',"/");

        if ( 0 == $this->user->info->status && $this->request->action() != 'index' && $this->request->action() != 'updateinfo' && $this->request->action() != 'edit' && $this->request->action() != 'getuserinfo' && (!Request::instance()->isAjax()))
        	return $this->redirect('user/index/edit');

        $this->view = new \think\View();

        $config = Config::all('webname,organization,beianhao,description,keywords,logo');
        foreach ($config as $key => $value) {
            $this->view->assign($value->key,$value->value);
        }

	}

}