<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/22 0022
 * Time: 下午 7:32
 */

namespace app\admin\controller;


use think\Controller;
use think\Request;
use think\Session;
use think\View;

use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;

class AdminController extends Controller
{
    protected $view = null;
    protected $request = null;
    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        //初始化
        $this->view= new View();
        $this->request = new Request();
        $userId = Session::get("userId");
        if (empty($userId)){
            return $this->redirect('user/adminRedirectLogin');
        }else{
            //拒绝访问重定向
            $request2=request();
            $action=Session::get('action');
            $res=strpos($action,$request2->path());
            if($res!=false){
                 return $this->redirect('rule/adminRedirectDeny');
            }
            return $this->view->fetch('index/index');
        }
    }

}