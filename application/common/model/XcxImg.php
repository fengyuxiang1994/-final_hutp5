<?php
namespace app\common\model;

use think\Model;

class XcxImg extends BasModel
{  
     /**
	 * 重写add方法
	 */
	public function add($data)
	{
		
		return $this->save($data);
	}
     

     public function seleIndex($id)
	{
		// $data['id'] = $id;
		//  $return = $this->where($data)
  //                       ->select();
  //       echo $this->getLastSql(); 
  //       return $return;

         $data = [
			    'imgid' => $id,
			];

			$return = $this->where($data)
		                     ->select();
//		    echo $this->getLastSql();
    	    return $return;
	}
}