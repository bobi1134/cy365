<?php
namespace app\user\controller;

use think\Controller;
use app\user\model\User;

class Msg extends Common {

    /**
     * 获取全部消息
     * @return [type] [description]
     */
    public function getAllMsg() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $data = $this->user->msg()->order('send_time desc')->select();

        foreach ($data as $key => $value) {
        	$data[$key] = $data[$key]->append(['msg_type_text'])->hidden(['content','user_id'])->toArray();
        }

        return $this->success('获取成功',null,$data);
	}

    /**
     * 获取未读消息
     * @return [type] [description]
     */
	public function getNewMsg() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

        $data = $this->user->msg()->where('read',0)->select();

        foreach ($data as $key => $value) {
        	$data[$key] = $data[$key]->append(['msg_type_text'])->hidden(['content','user_id'])->toArray();
        }

        return $this->success('获取成功',null,$data);
	}

    /**
     * 获取未读消息数量
     * @return [type] [description]
     */
    public function getNewMsgCount() {
        // 返回json格式
        \think\Config::set('default_return_type','json');

        $count = $this->user->msg()->where('read',0)->count();

        return $this->success('获取成功',url('user/index/msg'),['count' => $count]);
    }

    /**
     * 获取一个消息的内容
     * @return [type] [description]
     */
    public function getMsgById() {
       // 返回json格式
        \think\Config::set('default_return_type','json');

        $msg = $this->user->msg()->where('id',$this->request->param('id'))->find();

        if (empty($msg))
                return $this->error('不存在');
        $msg->read = 1;
        $msg->save();
        
        $data = $msg->append(['msg_type_text'])->toArray();

        return $this->success('获取成功',null,$data);
    }

}