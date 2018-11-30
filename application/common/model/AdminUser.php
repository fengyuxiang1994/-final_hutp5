<?php
namespace app\common\model;

use think\Model;

class AdminUser extends BasModel
{
      public function updateTime($data, $id)
	{
		// allowField 过data数组中非数据表中的数据
		return $this->allowField(true)->save($data, ['id' => $id]);

	}

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
		            ->select();
        // echo $this->getLastSql();

		return $res;
	}
}