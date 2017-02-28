<?php
namespace app\index\controller;

class News extends Common {

    public function index() {
        return $this->view->fetch('index');
    }

    /**
     * 获取所有的新闻
     */
    public function getNews(){
        $news = \app\index\model\News::all();
        $this->view->assign('$news',$news);
        return $this->success('获取所有新闻成功！');
    }
}
