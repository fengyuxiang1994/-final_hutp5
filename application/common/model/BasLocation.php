<?php
namespace app\common\model;

use think\Model;

class BasLocation extends BasModel
{  
	public function getMendin($bisid)
	{
		$data = [
		    'bis_id' => $bisid,
		    'status' => 1,
		];

		$order = [
		    'id' => 'desc',
		];

		return $this->where($data)
		            ->order($order)
		            ->select();
	}

	public function getNOrmalLocationsInId($ids)
	{
		$data = [
		    'id' => ['in', $ids],
		    'status' => 1,
		];
		return $this->where($data)
		            ->select();

	}
   
}