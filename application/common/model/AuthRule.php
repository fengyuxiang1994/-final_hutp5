<?php
namespace app\common\model;

use think\Model;

class AuthRule extends BasModel
{  
	public function getAuthruleFirst()
	{
		$data = [
		    'status' => ['neq',-1],
		];

		$order = [
		    'id' => 'asc',
		];

		$return = $this->where($data)
		               ->order($order)
		               ->select();
		// echo $this->getLastSql();
		return $return;
	}
    /**
     * 根据id=pid查询出level
     */
	public function getAuthruleID($pid)
	{
		$data = [
		    'status' => ['neq',-1],
		    'id' => $pid,
		];

		$order = [
		    'id' => 'asc',
		];

		$return = $this->where($data)
		               ->order($order)
		               ->find();
		// echo $this->getLastSql();
		return $return;
	}

	public function getAuthruleGroup()
	{
		$data = [
		    'status' => ['neq',-1],
		];

		$return = $this->where($data)
		               ->select();
		// dump($return);
		// echo $this->getLastSql();
		return $return;
	}

	
}
 