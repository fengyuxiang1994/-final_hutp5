<?php

namespace app\Common\model;

use think\Model;


class City extends BasModel
{
    public function getCity($parentid = 0)
    {
    	$data = [
		    'status' => 1,
		    'parent_id' => $parentid,
		];

		$order = [
		    'id' => 'asc',
		];

		return $this->where($data)
		            ->order($order)
		            ->select();
    }

     public function getChaxCity()
    {
    	$data = [
		    'status' => 1,
		    'parent_id' => ['gt', 0],
		];

		$order = [
		    'id' => 'asc',
		];

		$result = $this->where($data)
		            ->order($order)
		            ->select();
        // echo $this->getLastSql();

		return $result;
    }

    // getFirst方法查询顶级数据 add页面使用
    public function getFirst()
	{
		$data = [
		    'status' => 1,
		    'parent_id' => 0,
		];

		$order = [
		    'id' => 'desc',
		];

		return $this->where($data)
		            ->order($order)
		            ->select();
	}

	 // getIndex方法查看顶级和字及数据 index页面使用
	public function getIndex($parentId = 0)
	{
		$data = [
		    'parent_id' => $parentId,
		    'status' => ['neq',-1],

		];

		$order = [
		    'listorder' => 'desc',
		    'id' => 'desc',
		];
		$res = $this->where($data)
		            ->order($order)
		            ->paginate(5);
        // echo $this->getLastSql();

		return $res;
	}
}
