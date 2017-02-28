<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/7 0007
 * Time: 下午 1:45
 */

namespace app\admin\controller;
use \app\admin\model\AuthRule;
use \app\admin\model\AuthGroup;
use \app\admin\model\Admin;
use \think\Session;


class Rule extends AdminController
{
    public function index()
    {
        return $this->view->fetch('rule/index');
    }

    public function getAllMenu() {
    	$rule = AuthRule::all();
        foreach ($rule as $key => $value) {
            $rule[$key] = $rule[$key]->append(['pid_text']);
        }
    	return $rule;
    }
    /**
     * 添加菜单
     */
    public function addMenu(){
    	config('default_return_type','json');
    	$rule=new AuthRule();
    	$rule->action=$this->request->param('action');
    	$rule->name=$this->request->param('name');
    	$rule->enabled=$this->request->param('enabled');
    	$rule->menu=$this->request->param('menu');
    	$rule->pid=$this->request->param('pid');
    	$status=$rule->save();
    	if($status>=0){
    		return success('添加菜单成功');
    	}else{
    		return error('添加菜单失败');
    	}
    }

     /**
     * 删除菜单
     * @param int $id 要删除菜单的id
     */
    public function deleteRule(){
        config('default_return_type','json');
        $id=$this->request->param('id');
        if (empty($id))
            return error('文件id不能为空');
        $rule=AuthRule::get($id);
        $num=$rule->delete();
        if($num>0){
            return $this->success('删除菜单成功');
        }else{
            return $this->error('删除菜单失败');
        }
    }

    /**
     * 修改菜单
     * @param int $id 要修改菜单的id
     */
    public function updateMenu(){
        config('default_return_type','json');
        $rule=AuthRule::get($this->request->param('id'));
        $rule->action=$this->request->param('action');
        $rule->name=$this->request->param('name');
        $rule->enabled=$this->request->param('enabled');
        $rule->menu=$this->request->param('menu');
        $rule->pid=$this->request->param('pid');
        $status=$rule->save();
        if($status>0){
            return success('修改菜单成功');
        }else{
            return error('修改菜单失败');
        }
    }
    
     /**
     * 拒绝访问重定向
     * @return [type] [description]
     */
    public function adminRedirectDeny(){
        return $this->error('对不起，您没有权限访问此页面');
    }
    
    /**
     * 根据不同权限显示不同菜单，设置session
     * @return [type] [<description>]
     */
    public function setRule(){
            // config('default_return_type','json');
            $id=\think\Session::get('userId');
            $admin=Admin::where('id',$id)->column('power_id');
            $group=AuthGroup::where('id',$admin[0])->column('rules');
            $rule_str='';
            $rule=new AuthRule();
            //允许访问的菜单session
            $rule_in=$rule->where('id','in',$group[0])->select();
            Session::set("rule_in",$rule_in);
            //禁止访问的方法session
            $rule_notin=$rule->where('id','not in',$group[0])->select();
            foreach ($rule_notin as $key => $value) {
                $rule_str.=$value->action.',';
            }
            //禁止访问action数组字符串
            $action='@,'.$rule_str.',';
            //写入session
            Session::set("action",$action);
            return success('设置sesion成功');
    }

}