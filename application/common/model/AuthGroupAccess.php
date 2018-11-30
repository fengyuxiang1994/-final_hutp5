<?php
namespace app\common\model;

use think\Model;

class AuthGroupAccess extends Model
{
	public function add($data)
	{
		return $this->save($data);
	}
}