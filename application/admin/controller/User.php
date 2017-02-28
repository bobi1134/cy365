<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 13:34
 */
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use email\PHPMailer;
use think\Config;
use think\Controller;
use think\Input;
use think\Request;
use think\Session;
use think\Validate;

class User extends RuleController{

    /**
     * 登陆跳转-登陆页面
     * @return View
     */
    public function adminRedirectLogin(){
        return view("index/login");
    }

    /**
     * 登陆跳转-管理页面
     * @return View
     */
    public function adminRedirectIndex(){
        return view("index/index");
    }

    public function testHtml(){
        return view("index/testHtml");
    }

    /**
     * 用户登陆
     */
    public function login(){
        $input = Input::post('');

        $validate = new Validate([
            'email' => 'require|email',
            'passwd' => 'require'
        ]);
        $data=[
            'email' => $input['email'],
            'passwd' => $input['passwd']
        ];

        //页面字段验证
        if(!$validate->check($data)){
            return $this->error($validate->getError());
        }

        //账号密码验证
        $user = Admin::get(['email'=>$input['email']]);
        if (empty($user)){
            return $this->error('账号不存在！');
        }

        //验证密码
        if ($user->passwd != md5($input['passwd'])){
            return $this->error('密码错误！');
        }

        //登陆成功，放进session,id字段
        Session::set("userId",$user->id);
        Session::set("userName",$user->name);

        return $this->success('登录成功',url('/admin/index/index'));
    }


    /**
     * 返回管理员列表视图
     * @return mixed
     */
    public function lists()
    {
        $group=AuthGroup::all();
        $this->view->assign("group",$group);
        return $this->view->fetch("user/lists");
    }

    /**
     * 返回增加管理员视图
     * @return string
     */
    public function addUser()
    {
        $auth_group=AuthGroup::all();
        $this->view->assign("authGroup",$auth_group);
        return $this->view->fetch('user/adduser');
    }

    /**
     * 返回权限管理视图
     * @return mixed
     */
    public function power()
    {
        $auth_group=AuthGroup::all();
        $this->view->assign("authGroup",$auth_group);
        return $this->view->fetch("user/power");
    }

    /**
     * 添加管理员用户
     * @return array|void
     */
    public function add(){
        $email = $this->request->param('email');
        $passwd = md5($this->request->param('passwd'));
        $name = $this->request->param('name');
        $phone = $this->request->param('phone');
        $power_id = $this->request->param('power_id');
        $rand = "";
        for ($i = 1; $i <= 20; $i++) {
            $rand = chr(rand(97, 122)).$rand;
        }
        $validate = new Validate([
            'email|登陆邮箱' 		=> 'require',
            'passwd|登陆密码' 	=> 'require',
            'name|管理员姓名' 	=> 'require',
            'phone|手机号码' 	=> 'require',
            'power_id|管理员权限'		=> 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //校验邮箱是否已经存在
        $admin = Admin::get(['email'=>$email]);
        if (!empty($admin)){
            return $this->error('该邮箱已存在！');
        }
        //保存进数据库
        $admin = new Admin();
        $admin->power_id = $power_id;
        $admin->email = $email;
        $admin->name = $name;
        $admin->passwd = $passwd;
        $admin->phone = $phone;
        $admin->code = $rand;
        $admin->save();
        //发送邮件
        $url = 'http://test.xtype.cn'.url('admin/user/active',['code' => $rand]);
        $htmlText = "尊敬的管理员用户 ".$name." ，你好！<br /><br />在使用创业365前，你需要点击下面的链接来激活你的账户:<br /><br /><a href=".$url.">".$url."</a><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";
        if(sendEmail($admin['email'],'激活365管理员账户',$htmlText)){
            return $this->success('邮件发送成功，请前往激活！');
        }else{
            return $this->error('邮件发送失败');
        }
    }


    /**
     * 管理员邮箱激活
     * @param $code 激活身份识别code
     * @return array
     */
    public function active(){
        $user = Admin::get(['code'=>$this->request->param('code')]);
        if (!empty($user)){
            //修改状态，销毁Code
            $user->status = 0;
            $user->code = '';
            $user->save();
            return $this->success("激活成功！", url('admin/index/index'));
        }
    }

    /**
     * 自动分配3位老师
     * @return array
     */
    public function allotTeacher(){
        $user = new Admin();
        $ids = $user->column('id');
        $autoIds = array_rand($ids,3);
        return $this->success("自动分配老师成功！", null, $autoIds);
    }

    /**
     * 修改管理员密码-发送邮箱验证码
     */
    public function sendEmail(){
        Config::set('default_return_type','json');
        //验证码-随机生成6位数字
        $rand = "";
        for ($i = 1; $i <= 6; $i++) {
            $rand = chr(rand(97, 122)).$rand;
        }
        $userId = Session::get("userId");
        $admin = Admin::get($userId);
        //发送邮件
        $htmlText = "尊敬的管理员 " . $admin['username'] . " ，你好！<br /><br />你正在修改创业365账户密码，下面是验证码，你需要复制并粘贴在表单里:<br /><br /><b>" . $rand . "</b><br /><br />本邮件由系统自动发送，请勿回复。如果不是你在操作，请忽略本邮件。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";
        //验证码存入表单
        if(sendEmail($admin['email'],'你正在修改创业365账户密码',$htmlText)){
            $admin->code = $rand;
            $admin->save();
            return $this->success('邮件发送成功！');
        }else{
            return $this->error('邮件发送失败！');
        }
    }

    /**
     * 修改管理员密码
     */
    public function updatePwd(){
        //Config::set('default_return_type','json');
        //输入校验
        $validate = new \think\Validate([
            'old_passwd|旧密码' 		=> 'require',
            'new_passwd|新密码' 	=> 'require',
            'ok_passwd|确认新密码' 	=> 'require',
            'verify_code|校验码'		=> 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //校验新旧密码是否相同
        if ($validate->check($this->request->param('new_passwd') != $this->request->param('ok_passwd'))){
            return $this->error('两次密码不一致！');
        }
        $userId = Session::get("userId");
        $admin = Admin::get(['id'=>$userId]);
        //校验旧密码
        if ($validate->check(md5($this->request->param('old_passwd')) != $admin['passwd'])){
            return $this->error('旧密码错误！');
        }
        //校验验证码
        if ($validate->check($this->request->param('verify_code') != $admin['code'])){
            return $this->error('验证码错误！');
        }
        //更新密码
        $admin->passwd = md5($this->request->param('ok_passwd'));
        $admin->save();
        return $this->success('更新成功');
    }

    /**
     * 获取所有管理员列表
     * @return json 返回所有管理员列表数组
     */
    public function getAllUser(){
        Config::set('default_return_type','json');
        
        $admin=Admin::all();
        if(empty($admin)){
            return error('获取失败');
        }
            return $admin;
    }

    /**
     * 修改权限-根据auth_group表的id进行修改
     */
    public function updateRules(){
        Config::set('default_return_type','json');
        $id = $this->request->param('id');
        $title = $this->request->param('title');
        //获取rules数组
        $param=$this->request->param();
        $rules = $param['rules'];
        $str = '';
        foreach($rules as $key => $val){
            if(empty($str)){
                $str = $val;
            }else{
                $str = $str.','.$val;
            }
        }
        //校验
        $validate = new Validate([
//            'id|id' 		=> 'require',
            'title|角色' 		=> 'require',
            'rules|权限' 	=> 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        if(empty($id)){
            $id = '0';
        }
        $authGroup = AuthGroup::get(['id'=>$id]);
        if (empty($authGroup)){
            $bc = new AuthGroup();
            $bc->title = $title;
            $bc->rules = $str;
            $bc->save();
            return $this->success('保存角色及权限成功！');
        }else{
            $authGroup->title = $title;
            $authGroup->rules = $str;
            $authGroup->save();
            return $this->success('更新权限成功！');
        }
    }

    /**
     * 获取所有权限列表
     */
    public function ruleLists()
    {
        $id=request()->param("id");
        $groupRules=AuthGroup::get($id);
        $rules=AuthRule::all();
        if(empty($groupRules)){
            $this->view->assign('res',null);
        }else{

            $res=explode(',',$groupRules->rules);
            $this->view->assign('res',$res);


        }
        $this->view->assign("groupRules",$groupRules);
        $this->view->assign("rules",$rules);
        return $this->view->fetch("user/rulelists");
    }

    /**
     * 修改管理员资料
     */
    public function update(){
        Config::set('default_return_type','json');
        $id = request()->param('admin_id');
        $name = request()->param('name');
        $phone = request()->param('phone');
        $power_id = request()->param('power_id');
        $validate = new Validate([
            'name|管理员姓名' 		=> 'require',
            'phone|手机号码' 		=> 'require',
            'power_id|管理员权限' 	=> 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        $user = Admin::get(['id'=>$id]);
        if(!empty($user)){
            $user->name = $name;
            $user->phone = $phone;
            $user->power_id = $power_id;
            $user->save();
            return $this->success('更新管理员资料成功！');
        }else{
            return $this->success('更新管理员资料失败！');
        }
    }

    public function logout() {
        \think\Session::delete('userId');
        \think\Session::clear();

        return $this->success('退出成功',url('admin/index/index'));
    }
}