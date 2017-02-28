<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/8 0008
 * Time: 下午 12:47
 */

namespace app\admin\controller;


use app\admin\model\Cybt;
use app\admin\model\CybtReview;
use app\admin\model\CybtRecheck;
use app\admin\model\Admin;
use app\admin\model\Msg;
use app\user\model\User;
use think\Config;
use think\Controller;
use think\Input;
use think\Request;
use think\response\View;
use think\Session;

class Service extends AdminController
{
    /*
     * @return 返回index视图
     */
    public function index()
    {
        return $this->view->fetch('project/index');
    }

    /**
     * 初审列表页
     * @return View
     */
    public function unchecked()
    {
        return view('project/unchecked');
    }

    /**
     * 复审列表页
     * @return View
     */
    public function recheck()
    {
        return view("project/recheck");
    }

    /**
     * 已通过列表页
     * @return View
     */
    public function confirmed()
    {
        return view('project/confirmed');
    }

    /**
     * 初审预览
     * @return string
     */
    public function preview()
    {
        config('default_return_type','html');
        $url=Input::post();
        $projects_name=Input::post('projects_name');
        $projects_id=Input::post('projects_id');
        $id=$url['id'];
        //这儿根据前台具体情况修改--$id为文件id数组，前两个id为预览文档id，后面的id为图片id
        $response=[];
        foreach($id as $key => $val)
        {
            if($key<2)
            {
                //前两个id为预览文档id，返回文档url地址
                $response[$key]=action('file/get/getPreviewFile',$val);
            }else
            {
                //后面的id为图片id，返回图片url地址
                $response[$key]=action('file/get/getSingleImage',[$val,$key]);
            }
        }
        //数组形式返回所有文件url
        // dump($response);
        $this->view->assign('projects_id',$projects_id);  //创业补贴项目id
        $this->view->assign('projects_name',$projects_name);  //创业补贴项目名称
        $this->view->assign('response',$response);



        return $this->view->fetch('project/preview');
    }

    /**
     * 复审预览
     * @return string
     */
    public function review()
    {
        config('default_return_type','html');
        $url=Input::post();
        $projects_name=Input::post('projects_name');
        $projects_id=Input::post('projects_id');
        $id=$url['id'];
        //这儿根据前台具体情况修改--$id为文件id数组，前两个id为预览文档id，后面的id为图片id
        $response=[];
        foreach($id as $key => $val)
        {
            if($key<2)
            {
                //前两个id为预览文档id，返回文档url地址
                $response[$key]=action('file/get/getPreviewFile',$val);
            }else
            {
                //后面的id为图片id，返回图片url地址
                $response[$key]=action('file/get/getSingleImage',[$val,$key]);
            }
        }
        //数组形式返回所有文件url
        // dump($response);
        $this->view->assign('projects_id',$projects_id);  //创业补贴项目id
        $this->view->assign('projects_name',$projects_name);  //创业补贴项目名称
        $this->view->assign('response',$response);

        return $this->view->fetch('project/review');
    }

    /**
     * 任何人预览
     * @return string
     *
     */
    public function view()
    {
        config('default_return_type','html');
        $url=Input::post();
        $projects_name=Input::post('projects_name');
        $projects_id=Input::post('projects_id');
        $id=$url['id'];
        //这儿根据前台具体情况修改--$id为文件id数组，前两个id为预览文档id，后面的id为图片id
        $response=[];
        foreach($id as $key => $val)
        {
            if($key<2)
            {
                //前两个id为预览文档id，返回文档url地址
                $response[$key]=action('file/get/getPreviewFile',$val);
            }else
            {
                //后面的id为图片id，返回图片url地址
                $response[$key]=action('file/get/getSingleImage',[$val,$key]);
            }
        }
        //数组形式返回所有文件url
        // dump($response);
        $this->view->assign('projects_id',$projects_id);  //创业补贴项目id
        $this->view->assign('projects_name',$projects_name);  //创业补贴项目名称
        $this->view->assign('response',$response);

        return $this->view->fetch('project/view');
    }

    /**
     * 查询所有的项目列表
     * @return array
     */
    public function cybtGetAllList() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $cybt = Cybt::where('status','>=',1)->order('id','desc')->paginate(10);

        if(empty($cybt))
            return error('没有相关申报');

        $data = [];

        foreach ($cybt as $key => $value) {
            $temp = [
                'cybt_id'   => $value->id,
                'type'      => $value->type_text,
                'name'      => $value->projects_name,
                'real_name' => $value->user->auth->realname,
                'briefing'  => $value->briefing,
                'members'   => $value->members,
                'counselor' => $value->counselor,
                'academy'   => empty($value->user->info->major)? null : $value->user->info->major->academy->academy_name,
                'std_id'    => $value->user->info->std_id,
                'first_review'  => $value->review->result_text,
                'last_review'   => $value->recheck->result_text,
                'dynamic'       => $value->status_text,
                'start_time'    => $value->start_time,
                'submit_time'   => $value->submit_time,
                'file_id_1'     => $value->file_id_2,
                'file_id_2'     => $value->file_id_3,
                'file_id_3'     => $value->file_id_4,
                'file_id_4'     => $value->file_id_5,
                'file_id_5'     => $value->file_id_6,
                'file_id_0'     => $value->file_id_1,
                'projects_name' => $value->projects_name,

            ];
            $data[] = $temp;
        }
        return success('获取成功',null,$data);
    }


    /**
     * 查询需要复审的列表
     * @return array
     */
    public function cybtGetRecheckList()
    {
        \think\Config::set('default_return_type','json');

        $cybt = Cybt::hasWhere('review',['result' => 1])->where('status',3)->select();

        if (empty($cybt))
            return error('您当前没有需要审核的申报');

        $data = [];

        foreach ($cybt as $key => $value) {
            $temp = [
                'cybt_id'       => $value->id ,
                'type'          => $value->type_text ,
                'name'          => $value->projects_name ,
                'submit_time'   => $value->submit_time ,
                'academy'       => empty($value->user->info->major)? null :$value->user->info->major->academy->academy_name ,
                'std_id'        => $value->user->info->std_id ,
                'first_review'  => $value->review->admin->name,
                'file_id_0'     => $value->file_id_1 ,
                'file_id_1'     => $value->file_id_2 ,
                'file_id_2'     => $value->file_id_3 ,
                'file_id_3'     => $value->file_id_4 ,
                'file_id_4'     => $value->file_id_5 ,
                'file_id_5'     => $value->file_id_6 ,
            ];
            $data[] = $temp;
        }
        return success('获取成功',null,$data);
    }

    /**
     * 通过复审的项目
     * @return [type] [description]
     */
    public function cybtGetAllConfirmList()
    {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $cybt = Cybt::hasWhere('recheck',['result' => 1])->where('status',4)->select();

        if(empty($cybt))
            return error('没有相关申报');

        $data = [];

        foreach ($cybt as $key => $value) {
            $temp = [
                'cybt_id'   => $value->id,
                'type'      => $value->type_text,
                'name'      => $value->projects_name,
                'real_name' => $value->user->auth->realname,
                'briefing'  => $value->briefing,
                'members'   => $value->members,
                'counselor' => $value->counselor,
                'academy'   => empty($value->user->info->major) ? null :$value->user->info->major->academy->academy_name ,
                'std_id'    => $value->user->info->std_id,
                'first_review'  => $value->review->admin->name,
                'last_review'   => $value->recheck->result_text,
                'dynamic'   => $value->status_text,
                'start_time'    => $value->start_time,
                'submit_time'   => $value->submit_time,
                'file_id_1' => $value->file_id_2,
                'file_id_2' => $value->file_id_3,
                'file_id_3' => $value->file_id_4,
                'file_id_4' => $value->file_id_5,
                'file_id_5' => $value->file_id_6,
                'file_id_0' => $value->file_id_1,
                'projects_name' => $value->projects_name,

            ];
            $data[] = $temp;
        }
        return success('获取成功',null,$data);
    }

    /**
     * 需要初审的项目
     * @return [type] [description]
     */
    public function getNeedReviewList() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $admin_id = Session::get('userId') ;
        $cybt = Cybt::hasWhere('review',['result' => 0])->where('admin_id',$admin_id)->select();

        if (empty($cybt))
            return error('您当前没有需要审核的申报');

        $data = [];

        foreach ($cybt as $key => $value) {
            $temp = [
                'cybt_id'       => $value->id ,
                'type_text'     => $value->type_text ,
                'projects_name' => $value->projects_name ,
                'submit_time'   => $value->submit_time ,
                'real_name'     => empty($value->user->auth)?null:$value->user->auth->realname ,
                'academy'       => empty($value->user->info->major)? null :$value->user->info->major->academy->academy_name ,
                'std_id'        => $value->user->info->std_id ,
                'result_text'   => $value->review->result_text ,
                'result'        => $value->review->result ,
                'file'          => [
                    $value->file_id_1 ,
                    $value->file_id_2 ,
                    $value->file_id_3 ,
                    $value->file_id_4 ,
                    $value->file_id_5 ,
                    $value->file_id_6 ,
                ] ,
            ];
            $data[] = $temp;
        }
        return success('获取成功',null,$data);
    }

    /**
     * @return 未通过项目
     */
    public function cybtGetAllFailedList(){
        Config::set('default_return_type','json');
        
        $cybt = Cybt::hasWhere('recheck',['result' => -1])->where('status',4)->select();

        $data = [];
        foreach ($cybt as $key => $value) {
            $temp = [
                'cybt_id' => $value->id,
                'type' => $value->type_text,
                'name' => $value->projects_name,
                'academy' => $value->user->info->major->academy->academy_name,
                'std_id' => $value->user->info->std_id,
                'submit_time' => $value->submit_time,
                'first_review' => $value->review->result_text,
                'last_review' => $value->recheck->result_text,
                'file_id_0' => $value->id,
                'file_id_1' => $value->id,
                'file_id_2' => $value->id,
                'file_id_3' => $value->id,
                'file_id_4' => $value->id,
                'file_id_5' => $value->id,
                'projects_name' => $value->projects_name,
            ];
            $data[] = $temp;
        }
        return success('获取成功',null,$data);
    }

    /**
     * 创业补贴审核通过
     * @return [type] [description]
     */
    public function cybtReviewAgree() {
        // 返回json格式
        \think\Config::set('default_return_type','json');
        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
        ]);
        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $cybt = Cybt::get($this->request->param('id'));
        if (empty($cybt))
            return $this->error('不存在此条申请');

        $cybt->review->result = 1;
        $cybt->review->finish_time = time();
        $cybt->status = 3;
        $cybt->save();

        $cybt->review->save();

        $user = User::get($cybt->user_id);

        // 这里发送邮件通知
        $htmlText = "尊敬的用户 ".$user->username."，你好！<br /><br />你所申报的项目:<b>" . $cybt->projects_name . "</b><br />已经通过初审，你需要再次通过复审才能完成申报！复审地点、时间将会联系你，请注意电话的畅通！<br /><br />本邮件由系统自动发送，请勿回复。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";
        $msg = new Msg();
        $msg->user_id = $cybt->user_id;
        $msg->content = $htmlText;
        $msg->send_time = time();
        $msg->title = '创业补贴申报初审通过的通知';
        $msg->save();

        sendEmail($user->email,'创业补贴申报初审通过的通知',$htmlText);

        return $this->success('审核成功',url('admin/service/unchecked'));
    }

    /**
     * 创业补贴审核不通过
     * @return [type] [description]
     */
    public function cybtReviewRefused() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
                'note|备注' => 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $cybt = Cybt::get($this->request->param('id'));
        if (empty($cybt))
            return $this->error('不存在此条申请');

        $cybt->review->result = -1;
        $cybt->review->note = $this->request->param('note');
        $cybt->review->finish_time = time();
        $cybt->review->save();

        $cybt->status = 3;
        $cybt->save();

        $admin = Admin::get($cybt->review->admin_id);
        $user = User::get($cybt->user_id);

        // 这里发送邮件
        $htmlText = "尊敬的用户 ".$user->username."，你好！<br /><br />你所申报的项目:<b>" . $cybt->projects_name . "</b><br />初审没有通过！<br />审核人员:<b>" . $admin->name ."</b><br />原因:<b>" . $cybt->review->note . "</b><br /><br />本邮件由系统自动发送，请勿回复。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        $msg = new Msg();
        $msg->user_id = $cybt->user_id;
        $msg->content = $htmlText;
        $msg->send_time = time();
        $msg->title = '创业补贴申报初审没有通过的通知';
        $msg->save();

        sendEmail($user->email,'创业补贴申报初审没有通过的通知',$htmlText);
        return $this->success('审核成功',url('admin/service/unchecked'));
    }

    public function cybtRecheckAgree()
    {
        // 返回json格式
        \think\Config::set('default_return_type','json');
        // 验证请求
        $validate = new \think\Validate([
            'id|申请ID' => 'number|require',
        ]);
        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $cybt = Cybt::get($this->request->param('id'));
        if (empty($cybt))
            return $this->error('不存在此条申请');

        $cybt->recheck->result = 1;
        $cybt->recheck->finish_time = time();
        $cybt->recheck->admin_id = \think\Session::get('userId');
        $cybt->recheck->save();

        $cybt->status = 4;
        $cybt->save();
        
        $user = User::get($cybt->user_id);

        // 这里发送邮件通知
        $htmlText = "尊敬的用户 ".$user->username."，你好！<br /><br />你所申报的项目:<b>" . $cybt->projects_name . "</b><br />已经通过复审，后续的通知将会通知到您，请注意电话的畅通！<br /><br />本邮件由系统自动发送，请勿回复。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        $msg = new Msg();
        $msg->user_id = $cybt->user_id;
        $msg->content = $htmlText;
        $msg->send_time = time();
        $msg->title = '创业补贴申报复审通过的通知';
        $msg->save();

        sendEmail($user->email,'创业补贴申报复审通过的通知',$htmlText);

        return $this->success('审核成功',url('admin/service/recheck'));
    }


    /**
     * 创业补贴复审不通过
     * @return [type] [description]
     */
    public function cybtRecheckRefused() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
                'note|备注' => 'require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $cybt = Cybt::get($this->request->param('id'));
        if (empty($cybt))
            return $this->error('不存在此条申请');

        $cybt->recheck->result = -1;
        $cybt->recheck->note = $this->request->param('note');
        $cybt->recheck->finish_time = time();
        $cybt->recheck->admin_id = \think\Session::get('userId');
        $cybt->recheck->save();

        $cybt->status = 4;
        $cybt->save();

        $admin = Admin::get($cybt->review->admin_id);
        $user = User::get($cybt->user_id);

        // 这里发送邮件
        $htmlText = "尊敬的用户 ".$user->username."，你好！<br /><br />你所申报的项目:<b>" . $cybt->projects_name . "</b><br />复审没有通过！<br />审核人员:<b>" . $admin->name ."</b><br />原因:<b>" . $cybt->recheck->note . "</b><br /><br />本邮件由系统自动发送，请勿回复。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";

        $msg = new Msg();
        $msg->user_id = $cybt->user_id;
        $msg->content = $htmlText;
        $msg->send_time = time();
        $msg->title = '创业补贴申报复审没有通过的通知';
        $msg->save();

        sendEmail($user->email,'创业补贴申报复审没有通过的通知',$htmlText);
        return $this->success('审核成功',url('admin/service/unchecked'));
    }

    /**
     * 根据id获取创业补贴的相关信息
     */
    public function getCybtById(){
        $cybtInfo = Cybt::get( $this->request->param('id') );
        $userInfo = $cybtInfo->user->getData();
        $data = [
            'projects_name'=>$cybtInfo['projects_name'],//项目名
            'members'=>$cybtInfo['members'],//团队人数
            'counselor'=>$cybtInfo['counselor'],//指导老师
            'phone'=>$userInfo['phone'],//联系电话
            'email'=>$userInfo['email'],//邮件
            'wordId'=>$cybtInfo['file_id_1']//项目计划书id
        ];
        //返回当前页面作为数据接收页面
        $this->view->assign('data', $data);
    }


    /**
     * 发送复审通知邮件
     * @return [type] [description]
     */
    public function sendNoticeEmail() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $param=$this->request->param();
        $id=$param['id'];
        $recheck_address=$param['recheck_address'];
        $recheck_date=$param['recheck_date'];
        $rechech_note=$param['recheck_note'];
        
        //发送复审通知给所选id用户
        foreach($id as $key => $val){
            $cybt = Cybt::get($val);
            $admin = Admin::get($cybt->review->admin_id);
            $user = User::get($cybt->user_id);

            //写入复审信息
            $cybt->recheck->address = $recheck_address;
            $cybt->recheck->date = $recheck_date;
            $cybt->recheck->note = $recheck_note;
            $cybt->recheck->save();


            //这里发送邮件
            $htmlText = "尊敬的用户 ".$user->username."，你好！<br /><br />你所申报的项目:<b>" . $cybt->projects_name . "</b><br />复审通知信息如下："."<br>复审地点：<b>".$recheck_address."</b><br>复审时间：<b>".$recheck_date."</b><br>备注：<b>".$rechech_note."</b><br>审核人员:<b>" . $admin->name ."</b><br />原因:<b>" . $cybt->recheck->note . "</b><br /><br />本邮件由系统自动发送，请勿回复。" . "<br />[<a href=\"http://test.xtype.cn/\">四川农业大学创业365</a>] [<a href=\"http://www.sicau.edu.cn/\">四川农业大学</a>]";
            $msg = new Msg();
            $msg->user_id = $cybt->user_id;
            $msg->content = $htmlText;
            $msg->send_time = time();
            $msg->title = '复审通知';
            $msg->save();
            sendEmail($user->email,'复审通知',$htmlText);
        }
        return $this->success('复审通知已下达',url('admin/service/recheck'));
    }

    /**
     * @return string failed.html视图
     */
    public function failed(){
        return $this->view->fetch('project/failed');
    }

}