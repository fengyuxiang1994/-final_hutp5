<?php
namespace app\common\model;

use think\Model;

class XcxUser extends BasModel
{  
     /**
	 * 重写add方法
	 */
	public function add($data)
	{
		$data['status']=1;
		$this->save($data);
		return $this->id;
	}

	/**
	 * 在后台查看用户信息
	 */
	public function getUserIndex()
	{
		$data['status'] = 1;
		$order = ['id' => 'desc'];
        $return = $this->where($data)
                    ->order($order)
                    ->paginate(1,false,['query'=>request()->param()]);
        // echo $this->getLastSql(); //
        return $return;
	}

	/**
	 * 小程序获取用户信息
	 */
	public function getUserXcx($openid)
	{
		$data = ['openid' => $openid];
	    return $this->where($data)
	    	            ->find();
    }
    

     public function getMone($moneid)
    {
    	 $data = [
			    'id' => ['eq', $moneid],
			    'status' => 1,
			];

			$return = $this->field('money')
		               ->where($data)
		                ->find();
		    // echo $this->getLastSql();
    	    return $return;
		  
    }

}