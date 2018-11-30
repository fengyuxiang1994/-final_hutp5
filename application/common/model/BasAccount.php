<?php
namespace app\common\model;

use think\Model;

class BasAccount extends BasModel
{  
	public function updateTime($data, $id)
	{
		// allowField 过data数组中非数据表中的数据
		return $this->allowField(true)->save($data, ['id' => $id]);

	}
   
}