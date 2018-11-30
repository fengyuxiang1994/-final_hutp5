<?php
namespace app\common\model;

use think\Model;

class Bas extends BasModel
{  
	/**
	 * 商家成功审核和入驻申请列表
	 */
	public function getBas($status = 0)
	{
		$data = [
		    'status' => $status,
		];

		// $dataa = [
		//     'status' => 2,->whereOr($dataa)
		// ];

		// where('id',['>',0],['<>',10],'or')
		// $data["status"] = $status;
        // $data["status"] = 2;

		$order = [
		    'id' => 'desc',
		];

		return $this->where($data)
		            ->order($order)
		            ->paginate(1);
	}
// $where["a"] =1;
// $where["b"] =2;
// $map["c"] = 3;
// $map["d"] = 4;
// Db::name("table")->where($where)->whereOr($map)->select();

   /**
	 * 商家成功审核和入驻申请列表
	 */
	public function getDeletBas()
	{
		$data = [
		    'status' => -1,
		];

		$dataa = [
		    'status' => 2,
		];

		$order = [
		    'id' => 'desc',
		];

		$return = $this->where($data)
		            ->whereOr($dataa)
		            ->order($order)
		            ->paginate(1);
		// echo $this->getLastSql();
		return $return;
	} 
}
