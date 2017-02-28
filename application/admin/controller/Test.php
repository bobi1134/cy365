<?php
namespace app\admin\controller;


use app\admin\model\Cybt;
use app\admin\model\CybtReview;
use app\admin\model\Admin;
use think\Controller;
use think\Request;

/**
* 
*/
class Test extends AdminController
{
	
	public function cybtGetAllList() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

		$cybt = Cybt::where('status',1)->order('id','desc')->paginate(10);

		if (empty($cybt))
			return error('您当前没有需要审核的申报');

		$data = [];

		foreach ($cybt as $key => $value) {	
			if (empty($value->review))
				continue;
			$temp = [
				'id' 		=> $value->id ,
				'type_text'		=> $value->type_text ,
				'name'		=> $value->projects_name ,
				'real_name'	=> $value->userinfo->real_name ,
				'academy'	=> $value->userinfo->academy->name ,
				'std_id'	=> $value->userinfo->std_id , 
				'first_review' 	=> $value->review->result_text ,
				'last_review'	=> $value->pass_text ,
				'status_text' 		=> $value->status_text ,
				'start_time'	=> $value->start_time ,
				'submit_time'	=> $value->submit_time ,

			];
			$data[] = $temp;
		}
		return success('获取成功',null,$data);
	}

	public function cybtGetNeedReviewList() {
		// 返回json格式
        \think\Config::set('default_return_type','json');

		$admin_id = 1000 ;
		$review = CybtReview::all(['admin_id' => $admin_id , 'result' => 0]);


		if (empty($review))
			return error('您当前没有需要审核的申报');

		$data = [];

		foreach ($review as $key => $value) {
			if (empty($value->cybt))
				continue;
			$temp = [
				'cybt_id'		=> $value->cybt_id ,
				'type_text'		=> $value->cybt->type_text ,
				'projects_name'	=> $value->cybt->projects_name ,
				'submit_time'	=> $value->cybt->submit_time ,
				'real_name'		=> $value->cybt->userinfo->real_name ,
				'academy'		=> $value->cybt->userinfo->academy->name ,
				'std_id'		=> $value->cybt->userinfo->std_id ,
				'result_text'	=> $value->result_text ,
				'result'		=> $value->result ,
			];
			$data[] = $temp;
		}

		return success('获取成功',null,$data);
	}

	public function cybtAssignedReview( $cybt_id = 0) {
		if ($cybt_id == 0)
			return false;

		$temp = CybtReview::get(['cybt_id' => $cybt_id]);
		if (!empty($temp))
			return false;

		$temp = \think\Db::table('admin')->field('a.id,count(a.id) as count')->alias('a')->where('a.status',0)->join('cybt_review r',  'r.admin_id= a.id' ,$type = 'LEFT')->group('a.id')->order('count asc')->find();
		if (empty($temp))
			return false;

		$admin_id = $temp['id'];

		$review = new CybtReview();
		$review->cybt_id = $cybt_id;
		$review->admin_id = $admin_id;
		$review->start_time = time();

		$review->save();

		return true;
	}

}