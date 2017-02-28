<?php
namespace app\index\controller;
use app\index\model\Cybt;
use app\index\model\Config;
use app\user\model\User;
use app\file\model\File;
use app\file\model\Image;
use app\index\model\CybtReview;
use app\index\model\CybtRecheck;
use think\Request;

class Service extends Common{

    protected $beforeActionList = [
        'isLogin' =>  ['except'=>'index'],
    ];

    /*登录判断*/
    protected function isLogin() {
        $user_id = \think\Session::get('user_id');
        if (empty($user_id))
            return $this->error('请您先登录哦',"/");
    }

    /**
     * 显示计划列表
     * @return html
     */
    public function index() {

        return $this->view->fetch('index');
    }

    /**
     * 创业补贴流程
     * @return html
     */
    public function cybtProgress() {
        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
                'progress|申报流程' => 'number|require|between:1,4',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        // 这里进行一些逻辑判断
        $temp = Cybt::get( $this->request->param('id') );
        if ( empty($temp) || $temp->user_id != \think\Session::get('user_id') )
            return $this->error('不存在此条申请');

        if ( $this->request->param('progress') != 4 &&  $temp->status >= 1)
            return $this->error('抱歉，你已提交申报。请耐心等待审核！');

        // 赋值到视图
        $this->view->assign('data' , json_encode($temp->toArray()));
        $this->view->assign('cybt',$temp->append(['file1','file2','file3','file4','file5','file6'])->toArray());
        return $this->view->fetch('progress-'.$this->request->param('progress') );
    }

    /**
     * 获取创业补贴申请ID
     * @return json
     */
    public function cybtGetIdTapped() {

        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 这里进行一些逻辑判断
        $cybt_enabled = \think\Db::table('cybt_config')->where('key','cybt_enabled')->find();

        if ( empty($cybt_enabled) || $cybt_enabled['value'] != "1")
            return $this->error('申报入口已关闭，请下次再来，感谢您的支持！',url('index/service/index'));

        // 可以申报
        $user_id = \think\Session::get('user_id');

        $user = User::get( $user_id );
        if ( 2 != $user->auth->status )
            return $this->error('请您先完善资料并且通过实名认证！',url('user/index/auth'));

        // 判断是否可以申请ID
        $temp = Cybt::get(['user_id' => $user_id , 'status' => 0]);
        
        if (!empty($temp)) {
            $progress = 4;
            if ( empty($temp->projects_name) || empty($temp->briefing) || empty($temp->members) || empty($temp->type)){
                $progress = 1;
            } else if ( empty($temp->file_id_1) || empty($temp->file_id_2)) {
                $progress = 2;
            } else if ( empty($temp->file_id_3) || empty($temp->file_id_4) || empty($temp->file_id_5) || empty($temp->file_id_6) ) {
                $progress = 3;
            }
            return $this->error('继续完成之前的申报', url('index/service/cybtProgress', ['id' => $temp->id , 'progress' => $progress] ) );
        }

        $temp = new Cybt();
        $temp->user_id = $user_id;
        $temp->start_time = time();

        $temp->save();

        if ( $temp->id == 0 )
            return $this->error('获取申请ID失败，请重试');

        $next = url('cybtProgress',['id' => $temp->id , 'progress' => 1]);
        return $this->success('获取申请ID成功',$next,['id' => $temp->id , 'user_id' => $user_id]);
    }

    /**
     * 设置或更改创业补贴申请类型
     * @param  integer $id   申请ID
     * @param  integer $type 类型ID
     * @return json
     */
    public function cybtTypeTapped() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $msg = [
            'projects_name.length'  => '项目名称长度在1到32个字之间' ,
            'briefing.length'       => '项目简介长度在1到150个字之间'
        ];
        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
                'type|项目类型' => 'number|require|between:1,3',
                'projects_name|项目名称' => 'require|length:1,96',
                'briefing|项目简介' => 'require|length:1,450',
                'members|成员人数' => 'number|require',
        ],$msg);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $temp = Cybt::get( $this->request->param('id') );
        if ( empty($temp) || $temp->user_id != \think\Session::get('user_id') )
            return $this->error('不存在此条申请');

        // 这里进行一些逻辑判断
        if ( $temp->status >= 1)
            return $this->error('你已提交此申报，请耐心等待审核！');
        
        // 验证成功，更改
        $temp->type = $this->request->param('type');
        $temp->projects_name = $this->request->param('projects_name');
        $temp->briefing = $this->request->param('briefing');
        $temp->members = $this->request->param('members');
        $temp->counselor = $this->request->param('counselor');
        $temp->save();

        $next = url('cybtProgress',['id' => $this->request->param('id') , 'progress' => 2]);
        return $this->success('类型设置成功',$next);
    }

    /**
     * 设置或更改创业补贴申请所需文件 
     * @param  integer $id        申请ID
     * @param  integer $file_id   文件ID
     * @param  integer $file_type 文件类
     *  1:项目计划书、2:创业补贴申请表、3:身份证正面、4:身份证反面、5:学生证第一面、 6:营业执照
     * @return json
     */
    public function cybtFileTapped() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
                'file_id|文件ID' => 'number|require',
                'file_type|文件类' => 'number|require|between:1,6',
        ]);
        
        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $temp = Cybt::get($this->request->param('id'));
        if ( empty($temp) || $temp->user_id != \think\Session::get('user_id') )
            return $this->error('不存在此条申请');

        // 这里进行一些逻辑判断
        if ( $temp->status >= 1)
            return $this->error('抱歉，你已提交申报。请耐心等待审核！');

        $docArr = [ 'doc' , 'docx' , 'pdf' , 'html'];
        $imgArr = [ 'png' , 'jpeg' , 'jpg' , 'gif' ];
        $file_id = $this->request->param('file_id');
        switch ($this->request->param('file_type')) {
            case 1:
            case 2:
                $file = File::get($file_id);
                if (empty($file) || $file->uid != $temp->user_id ) {
                    return $this->error('文件信息错误');
                }
                if ( !in_array($file->file_type ,$docArr) ) {
                    return $this->error('文件类型错误');
                }
                break;
            case 3:
            case 4:
            case 5:
            case 6:
                break;
            default:
                return $this->error('有一些逻辑性的错误');
                break;
            }
        // 验证成功，更改
        switch ($this->request->param('file_type')) {
            case 1:
                $temp->file_id_1 = $file_id;
                break;
            case 2:
                $temp->file_id_2 = $file_id;
                break;
            case 3:
                $temp->file_id_3 = $file_id;
                break;
            case 4:
                $temp->file_id_4 = $file_id;
                break;
            case 5:
                $temp->file_id_5 = $file_id;
                break;
            case 6:
                $temp->file_id_6 = $file_id;
                break;
            default:
                return $this->error('有一些逻辑性的错误');
                break;
        }
        $temp->save();

        $next = url('cybtProgress',['id' => $this->request->param('id') , 'progress' => 3]);
        return $this->success('文件ID设置成功',$next);
    }

    /**
     * 提交申请，其实这里就是在设置提交时间，分配老师
     * @param  integer $id 申请ID
     * @return json
     */
    public function cybtSubmitTapped() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        // 验证请求
        $validate = new \think\Validate([
                'id|申请ID' => 'number|require',
        ]);

        if ( !$validate->check( $this->request->param() ) )
            return $this->error($validate->getError());

        $temp = Cybt::get( $this->request->param('id') );

        if ( empty($temp) || $temp->user_id != \think\Session::get('user_id')  )
            return $this->error('不存在此条申请');
        
        // 这里进行一些逻辑判断
        if ( $temp->status >= 1)
            return $this->error('抱歉，你已提交申报。请耐心等待审核！');
        // 首先各项文件不能为空
        $msg = [
            'type.between' => '请选择申报类型',
            'file_id_1.require' => '请上传项目计划书',
            'file_id_2.require' => '请上传创业补贴申请表',
            'file_id_3.require' => '请上传身份证正面',
            'file_id_4.require' => '请上传身份证反面',
            'file_id_5.require' => '请上传学生证第一面',
        ];
        $validate = new \think\Validate([
                'type|申报类型' => 'number|require|between:1,3',
                'file_id_1|项目计划书' => 'number|require',
                'file_id_2|创业补贴申请表' => 'number|require',
                'file_id_3|身份证正面' => 'number|require',
                'file_id_4|身份证反面' => 'number|require',
                'file_id_5|学生证第一面' => 'number|require',
        ],$msg);
        
        if ( !$validate->check( $temp->getData() ) )
            return $this->error($validate->getError());

        // 如果是类型3，必须有营业执照文件
        if ( $temp->type == 3 && $temp->file_id_6 == null)
            return $this->error('你需要上传营业执照扫描件');

        if (!$this->cybtAssignedReview($this->request->param('id')))
            return $this->error('分配审核人员的时候失败，遇到问题联系管理员');

        $temp->submit_time = time();
        $temp->status = 1;
        $temp->save();

        action('file/get/getPreviewFile',$temp->file_id_1);
        action('file/get/getPreviewFile',$temp->file_id_2);

        $next = url('cybtProgress',['id' => $this->request->param('id') , 'progress' => 4]);
        return $this->success('提交完成',$next);
    }

    /**
     * 自动平均分配管理员审核
     * @param  integer $cybt_id [description]
     * @return [type]           [description]
     */
    protected function cybtAssignedReview( $cybt_id = 0) {
        if ($cybt_id == 0)
            return false;

        $temp = CybtReview::get(['cybt_id' => $cybt_id]);
        if (!empty($temp))
            return $this->error('这个项目已经分配了初审人员');

        $review_group_id = \think\Db::table('cybt_config')->where('key','review_group_id')->find();

        //如果没有设置或者在管理员表中没有有关权限的管理员
        if (empty($review_group_id)) {
            return $this->error('目前没有指定审核人员，请稍后提交');
        }

        $temp = \think\Db::table('admin')->field('a.id,count(a.id) as count')->alias('a')->where('a.power_id',(int)$review_group_id['value'])->join('cybt_review r',  'r.admin_id= a.id' ,$type = 'LEFT')->group('a.id')->order('count asc')->find();
        if (empty($temp))
            return $this->error('目前没有分配初审人员，请稍后提交');

        $admin_id = $temp['id'];

        // 初审记录
        $review = new CybtReview();
        $review->cybt_id = $cybt_id;
        $review->admin_id = $admin_id;

        $review->save();

        // 复审记录
        $recheck = new CybtRecheck();
        $recheck->cybt_id = $cybt_id;
        $recheck->save();

        return true;
    }
    
}
