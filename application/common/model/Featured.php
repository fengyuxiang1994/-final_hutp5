<?php
namespace app\common\model;

use think\Model;

class Featured extends BasModel
{
    public function getFeatured($type)
    {
    	$data = [
    	    'type' => $type,
    	    'status' => ['neq', -1],
    	];

    	$order = [
    	    'id' => 'desc',
    	];

    	$return = $this->where($data)
    	            ->order($order)
    	            ->paginate(1);
    	// echo $this->getLastSql();
    	return $return;
    }   
     
}