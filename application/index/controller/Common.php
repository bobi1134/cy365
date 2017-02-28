<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Config;
use app\user\model\User;

/**
 * index模块的公共控制器
 */
class Common extends Controller {

    protected $view = null;

    /**
     * 网站访问控制、统计
     * @return void
     */
	public function _initialize() {
        $config = Config::get('availability');
        
        $value = empty($config) ? '0' : $config['value'];
        if ( $value == '0') {
        	return $this->error('网站维护中','http://info.xtype.cn/');
        }

        // 视图对象，在这里可以对视图进行操作
        $this->view = new \think\View();
        $config = Config::all('webname,organization,beianhao,description,keywords,logo');
        foreach ($config as $key => $value) {
            $this->view->assign($value->key,$value->value);
        }

        // $sid = \think\Cookie::get('sid');
        // $user_id = \think\Session::get('user_id');
        // if ( empty($user_id) && !empty($sid) ){
        //     $user = User::get(['sid' => $sid]);
        //     if ( !empty($user) ) {
        //         \think\Session::set('user_id',$user->id);
        //         $username = ($user->auth->status == 2) ? $user->auth->realname : $user->username ;
        //         \think\Cookie::set('username',$username);
        //         \think\Cookie::set('email',$user->email);
        //         \think\Cookie::set('head','http://ww1.sinaimg.cn/crop.0.0.1080.1080.1024/80d92288jw8eukigysfvaj20u00u0jtv.jpg');
        //     }
        // }
        
    }
}