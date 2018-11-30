<?php


namespace app\api\controller;

use think\Controller;
use think\Request;

class Caogao extends Controller
{
	public function caogaoInfo(){
		$caogao_id = input('id');
		$info = model('XcxCaogao')->where('id',$caogao_id)->find();
		$img = model('XcxImgcaogao')->where('imgid',$caogao_id)->select();

		$arr =[];
		foreach ($img as $key => $value) {

			$arr[] = $value['name'];
		}
		$info['image']=$arr;
		$category=model('Category')->field('name')->where('id',$info['category_id'])->find();
		$info['category_name']=$category['name'];
		return $info;
	}

	public function deleteCaogao(){
		$caogao_id = input('id');
		$info = model('XcxCaogao')->where('id',$caogao_id)->delete();
		$img = model('XcxImgcaogao')->where('imgid',$caogao_id)->delete();
		if ($info || $img) {
			return '删除成功';
		}
	}



	//屏蔽人数
	public function pingBiInfo() {
		$user_id = input('user_id');
		$info = model('XcxPingbi')->where('user_id',$user_id)
		->select;
		return $info;
	}
	//屏蔽取消屏蔽
	public function userPb(){
		$commen_id = input('comment_id');
		$user_id =input('user_id');
		$to_user_id =model('XcxComment')->where('id',$commen_id)->find();
		$info =model('XcxPingbi')
		->where('user_id',$user_id)
		->where('to_user_id',$to_user_id['user_id'])
		->find();

		if($info){
			$result = model('XcxPingbi')
			->where('user_id',$user_id)
			->where('to_user_id',$to_user_id['user_id'])
			->delete();
			if($result){
				return '取消屏蔽';
			}
		}else{
				$data = [
					'user_id'=>$user_id,
					'to_user_id'=>$to_user_id['user_id']
				];
			$result = model('XcxPingbi')->insert($data);

			if ($result) {
				return '屏蔽';
			}
		}
	}
}