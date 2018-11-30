<?php
namespace app\common\model;

use think\Model;

class BasModel extends Model
{  
    protected $autoWriteTimestamp = 'datetime';

	public function add($data)
	{
		$data['status']=0;
		$this->save($data);
		return $this->id;
	}

	public function updateById($data, $id)
	{
		return $this->allowField(true)->save($data, ['id' => $id]);

	}

}