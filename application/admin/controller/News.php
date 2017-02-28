<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12 0012
 * Time: 下午 5:46
 */

namespace app\admin\controller;


use app\admin\model\Admin;
use think\Db;
use think\Session;

class News extends AdminController
{
    public function index()
    {
        return $this->view->fetch('news/index');
    }

    public function create()
    {
        return $this->view->fetch('news/create');
    }

    /**
     * 获取所有的新闻-异步
     */
    public function getAllNews(){
        \think\Config::set('default_return_type','json');
//        $news = \app\admin\model\News::all();
        $news = \app\admin\model\News::order('id','desc')->select();
        if (empty($news)){
            return $this->error('当前还没有新闻资讯喔！');
        }
        $data = [];
        foreach ($news as $key => $value) {
            $temp = [
                'id' => $value->id,
                'title' => $value->title,
                'read_num' => $value->read_num,
                'is_top' => $value->is_top = $value->is_top == 1 ? '置顶' : '不置顶',
                'author' => \app\admin\model\News::get(['author'=>$value->author])->admin->getData()['name'],
                'created_at' => $value->created_at,
            ];
            $data[] = $temp;
        }
        return $this->success('获取所有新闻资讯成功！', null, $data);
    }

    /**
     * 保存新闻-异步
     */
    public function save(){
        \think\Config::set('default_return_type','json');
        $news = new \app\admin\model\News();
        //校验
        $validate = new \think\Validate([
            'title|标题' => 'require',
            'content|内容' => 'require',
            'is_top|是否置顶' => 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //保存
        $news->title = $this->request->param('title');
        $news->content = $this->request->param('content');
        $news->is_top = $this->request->param('is_top');
        $news->author = Session::get('userId');
        $result = $news->save();
        if ($result !== false){
            return $this->success('保存成功！');
        }else{
            return $this->error('保存失败！');
        }
    }

    /**
     * 跳转到修改新闻资讯页面-同步
     */
    public function edit(){
        //校验
        $validate = new \think\Validate([
            'id|id' => 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //根据id获取
        $news = \app\admin\model\News::get(['id'=>$this->request->param('id')]);
        $this->assign('article', $news);
        return $this->view->fetch('news/edit');
    }

    /**
     * 修改新闻资讯-异步
     */
    public function update(){
        \think\Config::set('default_return_type','json');
        //校验
        $validate = new \think\Validate([
            'id|id' => 'require',
            'title|标题' => 'require',
            'content|内容' => 'require',
            'is_top|是否置顶' => 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //更新
        $news = \app\admin\model\News::get(['id'=>$this->request->param('id')]);
        $news->title = $this->request->param('title');
        $news->content = $this->request->param('content');
        $news->is_top = $this->request->param('is_top');
        $news->author = Session::get('userId');
        $result = $news->save();
        if ($result !== false){
            return $this->success('更新成功！');
        }else{
            return $this->error('更新失败！');
        }
    }

    /**
     * 预览新闻资讯-同步
     */
    public function prev(){

    }

    /**
     * 删除新闻资讯-异步
     */
    public function delete(){
        \think\Config::set('default_return_type','json');
        //校验
        $validate = new \think\Validate([
            'id|id' => 'require',
        ]);
        if (!$validate->check($this->request->param())){
            return $this->error($validate->getError());
        }
        //删除
        $news = \app\admin\model\News::get(['id'=>$this->request->param('id')]);
        if (empty($news)){
            return $this->error('不存在该条新闻资讯！');
        }
        $result = $news->delete();
        if ($result !== false){
            return $this->success('删除成功！');
        }else{
            return $this->error('删除失败！');
        }
    }
}