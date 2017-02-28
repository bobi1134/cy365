<?php
namespace app\user\controller;

use think\Controller;
use app\user\model\User;
use app\user\model\UserInfo;

class Account extends Common {

    public function getUserInfo() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $data = [
            'username'      => $this->user->username , 
            'email'         => $this->user->email ,
            'phone'         => $this->user->phone ,
            'reg_time'      => $this->user->reg_time ,
            'realname'      => $this->user->auth->realname ,
            'major'         => empty($this->user->info->major) ? null : $this->user->info->major->major_name ,
            'academy'       => empty($this->user->info->major) ? null : $this->user->info->major->academy->academy_name ,
            'class'         => $this->user->info->class ,
            'auth_status'   => $this->user->auth->status ,
            'auth_status_text'   => $this->user->auth->status_text ,
            'bank_name'     =>$this->user->bank->bank_name ,
			'std_id'        =>$this->user->info->std_id,
        ];

        return $this->success('获取成功',null,$data);
    }


	/**
	 * 更新资料
	 * @return [type] [description]
	 */
	public function updateInfo() {

        // 返回json格式
        \think\Config::set('default_return_type','json');


        $msg = [
            'std_id'    => '输入的学号似乎不正确' ,
            'class'     => '输入的班级格式错误' ,
            'phone'     => '输入的手机号码似乎不正确'
        ];

        // 验证请求
        $validate = new \think\Validate([
                'std_id|学号'       => 'number|require|length:8' ,
                'major_id|专业ID'       => 'number|require' ,
                'class|班级'       => 'require|length:6' ,
                'phone|手机号码'       => 'require|number|length:11' ,

        ],$msg);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

		$this->user->info->status = 1;
		$this->user->info->std_id = $this->request->param('std_id');
		$this->user->info->major_id = $this->request->param('major_id');
		$this->user->info->class = $this->request->param('class');
		$this->user->info->save();

		$this->user->phone = $this->request->param('phone');
		$this->user->save();

		return $this->success('更新成功',url('user/index/index'));
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
                'passwd|当前密码' 		=> 'require',
                'new_passwd|新密码' 	=> 'require',
                'verify_code|校验码'		=> 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return error($validate->getError());

        if ( empty($this->user->pass_code) || $this->user->pass_code != $this->request->param('verify_code') )
        	$this->error('校验码不正确，更新失败');
        if ( $this->user->passwd != md5( $this->request->param('passwd') ) )
        	$this->error('当前密码不对！');

        $this->user->passwd = md5($this->request->param('new_passwd'));
       	$this->user->pass_code = null;
        $this->user->save();

		return $this->success('更新成功',url('user/index/index'));
	}

	/**
	 * 上传实名认证信息
	 * @return [type] [description]
	 */
	public function uploadAuthInfo() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'realName|真实姓名' => 'require',
                'Idcard|身份证号码' => 'require|length:18',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return error($validate->getError());

        if ( 2 == $this->user->auth->status )
        	return $this->error('你已通过认证，请勿再次提交');

        if (!empty($this->user->auth->auth_time) && 1 == $this->user->auth->status)
        	return $this->error('你已提交，请等待审核');

        if ( (time() - $this->user->auth->auth_time) <= 86400 )
        	$this->error('你今天已提交过一次申请，请明天再试');


        $auth = new \tools\IdcardAuth(config('idcard_key'), $this->request->param('Idcard') , $this->request->param('realName'));

        // 请求失败
        if(!$auth->auth())
			return $this->error($auth->getReason());
		
		$this->user->auth->auth_time = time();
		$this->user->auth->status = 2;
		$this->user->auth->save( $auth->getData() );
        $this->user->info->real_name = $this->user->auth->realname;
        $this->user->info->save();

        if (!$this->user->auth->isok) {
            $this->user->auth->status = 1;
            $this->user->auth->save();
            return $this->success('提交完成，但需要审核'); 
        }
        
        return $this->success('提交完成，实名认证通过');
	}

    /**
     * 认证状态的返回
     * @return [type] [description]
     */
    public function getAuthStatus() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $data = [
            'status'        =>  $this->user->auth->status ,
            'status_text'   =>  $this->user->auth->status_text ,
            'realname'      =>  $this->user->auth->realname ,
            'idcard'        =>  empty($this->user->auth->idcard) ? null : substr_replace($this->user->auth->idcard,'****',11,4) ,
            'area'          =>  $this->user->auth->area ,
            'idcardphoto'   =>  $this->user->auth->idcardphoto ,
        ];

        return $this->success('获取成功',null,$data);
    }

    /**
     * 获取银行卡信息
     * @return [type] [description]
     */
    public function getBankInfo() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $bank = $this->user->bank->append(['status_text'])->toArray();
        $bank['bank_account'] = empty($bank['bank_account']) ? null : substr_replace($bank['bank_account'],'****',4,10);
        return $this->success('获取成功',null,$bank);
    }

	/**
	 * 更新银行信息
	 * @return [type] [description]
	 */
	public function updateBank() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'bank_name|开户银行' 		=> 'require',
                'bank_account|银行账号' 	=> 'require',
                'bank_ownership|开户人' 	=> 'require',
                'verify_code|校验码'		=> 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return error($validate->getError());

        if ( empty($this->user->bank->code) || $this->user->bank->code != $this->request->param('verify_code') )
        	$this->error('校验码不正确，更新失败');

        $this->user->bank->bank_name = $this->request->param('bank_name');
        $this->user->bank->bank_account = $this->request->param('bank_account');
        $this->user->bank->bank_ownership = $this->request->param('bank_ownership');
        $this->user->bank->status = 1;
        $this->user->bank->code = null;

        $this->user->bank->save();

		return $this->success('更新成功',url('user/index/index'));
	}

	/**
	 * 发送更新银行卡验证码
	 * @return [type] [description]
	 */
	public function sendBankVerify() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $code = md5($this->user->id.time());
        $this->user->bank->code = $code;
        $htmlText = "尊敬的用户 " . $this->user->username . " ，你好！<br /><br />你正在更新你的创业365绑定银行卡，下面是验证码，你需要复制并粘贴在表单里:<br /><br /><b>" . $code . "</b><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        if(sendEmail($this->user->email,'你正在更新你的创业365绑定银行卡',$htmlText)){
        	$this->user->bank->save();
            return success('邮件发送成功');
        }

        return error('发送失败');
	}

	/**
	 * 发送修改密码验证码
	 * @return [type] [description]
	 */
	public function sendPasswdVerify() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $code = md5($this->user->id.time());
        $this->user->pass_code = $code;
        $htmlText = "尊敬的用户 " . $this->user->username . " ，你好！<br /><br />你正在修改创业365账户密码，下面是验证码，你需要复制并粘贴在表单里:<br /><br /><b>" . $code . "</b><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        if(sendEmail($this->user->email,'你正在修改创业365账户密码',$htmlText)){
        	$this->user->save();
            return success('邮件发送成功');
        }

        return error('发送失败');
	}

}