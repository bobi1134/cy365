<?php
namespace app\user\controller;

use think\Controller;
use app\user\model\User;
use app\user\model\UserInfo;
use app\user\model\UserBank;
use app\user\model\UserAuth;
use think\Request;

/**
*  登录、注册、激活
*/
class Login extends Controller {

    /**
     * 用户登录
     * @return [type] [description]
     */
	public function login() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $msg = [
            'username.require' => '账户 ID不能为空',
            'username' => '这似乎不是有效的账户 ID',
            'passwd.require' => '请输入你的密码',
        ];

		// 验证请求
        $validate = new \think\Validate([
                'username|账户 ID' => 'require|alphaNum|length:4,32',
                'passwd|密码' => 'require',
        ],$msg);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $user = User::get(['username' => $this->request->param('username')]);

		if ( empty($user) )
			return $this->error('我们的数据库中没有找到账户ID '.$this->request->param('username'),"/");

		if ( md5( $this->request->param('passwd') ) != $user->passwd  )
			return $this->error('账户 ID 和密码不匹配',"/");

		if ( 0 == $user->status )
			return $this->error('你可能需要激活你的账户 ID',url('user/login/renewSendActivedEmail',['sid' => $user->sid]));

        $user->login_time = time();
        $user->save();

        // 设置登录信息
		\think\Session::set( 'user_id' , $user->id );
        $username = ($user->auth->status == 2) ? $user->auth->realname : $user->username ;
        \think\Cookie::set('username',$username);
        \think\Cookie::set('email',$user->email);
        \think\Cookie::set('head', ($user->info->status ==1 ) ? 'http://jiaowu.sicau.edu.cn/photo/'.$user->info->std_id.'.jpg' : 'http://ww1.sinaimg.cn/crop.0.0.1080.1080.1024/80d92288jw8eukigysfvaj20u00u0jtv.jpg');
        
		// 如果是记住登录，缓存sid一周
		if ( $this->request->param('remember') == 'on' )
			\think\Cookie::set( 'sid' , $user->sid , 604800 );

		return $this->success('你已经完成你的登录', $_SERVER["HTTP_REFERER"] );

	}

    /**
     * 账号可用性检测
     * @return [type] [description]
     */
	public function availability() {
		// 返回json格式
		\think\Config::set('default_return_type','json');

        $msg = [
            'username.length' => '账户 ID长度需要大于四个字符' ,
            'username.alphaNum' => '账户 ID需要数字和字母的组合'];
		// 验证请求
        $validate = new \think\Validate([
                'username|账户 ID' => 'require|alphaNum|length:4,32',
        ],$msg);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $temp = User::where('username', $this->request->param('username') )->find();

        if (!empty($temp))
        	return $this->error('账户ID 已存在');

        return $this->success('该账户ID 可用');
	}

    /**
     * 用户注册
     * @return [type] [description]
     */
	public function register() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $msg = [
            'email.email'   =>  '这似乎不是有效的电子邮箱账户' ,
            'phone.require' =>  '你可能需要输入手机号码' ,
            'phone'         =>  '这似乎不是有效的手机号码' ,
        ];

        // 验证请求
        $validate = new \think\Validate([
                'username|账户 ID' => 'require|alphaNum|length:4,32',
                'passwd|密码' => 'require',
                'email|邮箱' => 'require|email',
                'phone|手机号码' => 'require|number|length:11',
        ],$msg);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());
        $temp = User::where('username', $this->request->param('username') )->find();
        if (!empty($temp))
            return $this->error('账户ID 已存在');

        $temp = User::where('email', $this->request->param('email') )->find();
        if (!empty($temp))
            return $this->error('邮箱 已存在');

        // 创建user表记录
        $user = new User();
        $user->username = $this->request->param('username');
        $user->passwd = md5( $this->request->param('passwd') );
        $user->email = $this->request->param('email');
        $user->phone = $this->request->param('phone');
        $user->reg_time = time();

        $user->save();
        if ( empty($user->id) )
        	return error('注册失败');
       	$user->sid = md5( $user->id . $user->username . $user->passwd . $user->phone . $user->email . time());
       	$user->code = md5( $user->id . $user->passwd . $user->username . $user->email . $user->phone . time());

       	$user->save();

        // 创建userinfo表的记录
        $info = new UserInfo();
        $info->user_id = $user->id;
        $info->save();

        // 创建userbank表的记录
        $bank = new UserBank();
        $bank->user_id = $user->id;
        $bank->save();

        // 创建userauth表的记录
        $auth = new UserAuth();
        $auth->user_id = $user->id;
        $auth->save();

       	// 这里发送激活邮件
        $url = "http://test.xtype.cn".url('user/login/actived',['code' => $user->code , 'user_id' => $user->id]);

        $htmlText = "尊敬的用户 ".$user->username." ，你好！<br /><br />在使用创业365前，你需要点击下面的链接来激活你的账户:<br /><br /><a href=".$url.">".$url."</a><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

       	if(sendEmail($user->email,'请激活你的创业365账号',$htmlText)){
            return $this->success('就差最后一步，你需要打开你的邮箱来激活账户ID');
        }

       	return $this->success('注册成功');
	}

    /**
     * 重新发送激活邮件，接收sid码
     * @return [type] [description]
     */
    public function renewSendActivedEmail() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $user = User::where('sid', $this->request->param('sid') )->find();

        if (empty($user) || $user->code == null)
            return $this->error('错误');

        $url = "http://test.xtype.cn".url('user/login/actived',['code' => $user->code , 'user_id' => $user->id]);

        $htmlText = "尊敬的用户 ".$user->username." ，你好！<br /><br />在使用创业365前，你需要点击下面的链接来激活你的账户:<br /><br /><a href=".$url.">".$url."</a><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";


        if(sendEmail($user->email,'请激活你的创业365账号',$htmlText)){
            return $this->success('激活链接已发送至你的邮箱');
        }

        return error('发送失败');
    }

    /**
     * 激活
     * @return [type] [description]
     */
	public function actived() {

        // 验证请求
        $validate = new \think\Validate([
                'code|激活码' => 'require',
                'user_id|用户ID' => 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $user = User::get(['id' => $this->request->param('user_id')]);

        if (empty($user))
        	return $this->error('账号ID 不存在');

        if ( $this->request->param('code') != $user->code )
        	return $this->error('很抱歉，你的激活码已失效或者你已经激活',url('index/index/index'));

        $user->status = 1;
        $user->code = null;

 		$user->save();

 		return $this->success('恭喜你，你的账户ID 已激活成功。请登录后，完善信息！',url('/index/index/index'));
	}

    /**
     * 退出登录
     * @return [type] [description]
     */
    public function logout() {
        // 返回json格式
        \think\Config::set('default_return_type','json');
        
        \think\Cookie::delete('sid');
        \think\Cookie::delete('email');
        \think\Cookie::delete('username');
        \think\Cookie::delete('head');
        \think\Session::delete('user_id');
        \think\Session::clear();

        return $this->success('退出成功',url('index/index/index'));
    }

    /**
     * 发送修改密码验证码
     * @return [type] [description]
     */
    public function sendPasswdVerify() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'username|用户名'      => 'require',
                'email|邮箱'    => 'require|email',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $user = User::get(['username' => $this->request->param('username')]);
        if (empty($user))
            return $this->error('不存在此账户 ID');
        if ( $user->email != $this->request->param('email') )
            return $this->error('邮箱与账户 ID不匹配');

        $code = md5($user->id.time());
        $user->pass_code = $code;
        $htmlText = "尊敬的用户 " . $user->username . " ，你好！<br /><br />你正在修改创业365账户密码，下面是验证码，你需要复制并粘贴在表单里:<br /><br /><b>" . $code . "</b><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        if(sendEmail($user->email,'你正在修改创业365账户密码',$htmlText)){
            $user->save();
            return $this->success('校验码已发送至你的邮箱');
        }

        return $this->error('出了一些问题，邮件发送失败');
    }

    /**
     * 更新修改密码
     * @return [type] [description]
     */
    public function updatePasswd() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'username|用户名'      => 'require',
                'new_passwd|新密码'    => 'require',
                'verify_code|校验码'   => 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $user = User::get(['username' => $this->request->param('username')]);

        if ( empty($user->pass_code) || $user->pass_code != $this->request->param('verify_code') )
            $this->error('校验码不正确，更新失败');

        $user->passwd = md5($this->request->param('new_passwd'));
        $user->pass_code = null;
        $user->save();

        return $this->success('更新成功',url('user/index/index'));
    }

}