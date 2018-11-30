<?php
namespace app\common\model;

use think\Model;

class AuthGroup extends BasModel
{   /**
     * 角色列表页方法
     */
	public function getIndex()
	{
		$data = [
		    'status' => ['neq',-1],

		];

		$order = [
		    'id' => 'asc',
		];
		$res = $this->where($data)
		            ->order($order)
		            ->paginate(5);
        // echo $this->getLastSql();

		return $res;
	}
}
