<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\View;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;

class RuleController extends Controller
{
    protected $view = null;
    protected $request = null; 
   
    /**
     * RuleController constructor.
     * 用于user继承
     */
    public function __construct()
    {
        //初始化
        $this->view= new View();
        $this->request = new Request();
        //拒绝访问重定向
        $request2=request();
        $action=Session::get('action');
        $res=strpos($action,$request2->path());
        if($res!=false){
             return $this->redirect('rule/adminRedirectDeny');
        }

    }

}