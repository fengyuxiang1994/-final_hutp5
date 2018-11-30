<?php
namespace app\common\model;

use think\Model;

class Category extends Model
{  
    protected $autoWriteTimestamp = true;

	public function add($data)
	{
		$data['status']=1;
		return $this->save($data);
	}

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
    
    // getIndex方法查看顶级和字及数据
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
     /**
      *只查询顶级级分类数据
      */
	 public function getCategory($parentid = 0)
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

    public function getRecommendCats($id = 0, $limit = 5)
    {

		$data = [
		    'parent_id' => $id,
		    'status' => 1,

		];

		$order = [
		    'listorder' => 'desc',
		    'id' => 'desc',
		];
		$result = $this->where($data)
		            ->order($order);
		if ($limit) {
			$result = $result->limit($limit);
		}

		$result = $result->select();
        // echo $this->getLastSql();

		return $result;

    }

    public function getNormalCategoryId($ids)
    {
    	$data = [
		    'parent_id' => ['in', implode(',', $ids)],
		    'status' => 1,

		];

		$order = [
		    'listorder' => 'desc',
		    'id' => 'desc',
		];
		$result = $this->where($data)
		            ->order($order)
		            ->select();
        // echo $this->getLastSql();

		return $result;

    }

}