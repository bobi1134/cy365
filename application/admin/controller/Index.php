<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use think\Controller;
use think\Session;
use think\View;

class Index extends AdminController
{
    /**
     * 返回首页视图
     * @return string
     */
    public function index()
    {
    	action('admin/rule/setRule');//增加修改权限时更新权限界面session
        return $this->view->fetch('index/index');
    }

}
