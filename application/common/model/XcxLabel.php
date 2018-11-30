<?php
namespace app\common\model;

use think\Model;

class XcxLabel extends BasModel
{  
   /**
	 * 重写标签add方法
	 */
	public function add($data = [])
	{
		if (!is_array($data)) {
			exception('传递的数据不是数组');
		}
		$data['status']=1;
		return $this->allowField(true)
		            ->save($data);
		 // $this->id;
	}

    /**
	 * 根据用标签名称获取信息
	 */	
	public function getNormalDeals($data = [])
	{
		$data['status'] = 1;
		$order = ['id' => 'desc'];
        $return = $this->where($data)
                    ->order($order)
                    ->select();
        // echo $this->getLastSql(); //
        return $return;

	}
   
}