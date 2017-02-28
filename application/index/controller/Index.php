<?php
namespace app\index\controller;
use app\index\model\Slide;

class Index extends Common {
	
    public function index() {
    	// 获取轮播图
    	$slides = Slide::all();
    	$this->view->assign('slides',$slides);
    	
        return $this->view->fetch('index');
    }

}
