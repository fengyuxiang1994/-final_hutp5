<?php
namespace app\common\model;

use think\Model;

class Deal extends BasModel
{  
	/**
	 * 通过审核商品的方法
	 */
	public function getNormalDeals($data = [])
	{
		$data['status'] = 1;
		$order = ['id' => 'desc'];
        $return = $this->where($data)
                    ->order($order)
                    ->paginate(1,false,['query'=>request()->param()]);
        echo $this->getLastSql(); //
        return $return;

	}

	/**
	 * 审核中商品方法
	 */
    public function getShDeals($data = [])
	{
		$data['status'] = 0;
		$order = ['id' => 'desc'];
        $return = $this->where($data)
                    ->order($order)
                    ->paginate(1,false,['query'=>request()->param()]);
        // echo $this->getLastSql(); //
        return $return;

	}
    
    /**
     * 删除审核不通过的商品方法
     */
	public function getDeletDeals()
	{
		$data['status'] = -1;
		$datata['status'] = 2;
		$order = ['id' => 'desc'];
        $return = $this->where($data)
                    ->whereOr($datata)
                    ->order($order)
                    ->paginate(1,false,['query'=>request()->param()]);
        // echo $this->getLastSql(); //
        return $return;

	}

	public function getNameCityCategoryId($id, $cityId ,$limit = 10)
	{
		$data = [
		    'end_time' => ['gt' ,time()],
		    'category_id' => $id,
		    'city_id' => $cityId,
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

	/**
	 * 前台列表页
	 *根据分类和排序查询数据
	 */
	public function getDealFlPx($data = [], $orel)
	{
		if (empty($orel['order_sales'])) {
			$order['buy_count'] = 'desc';
		}

		if (empty($orel['order_price'])) {
			$order['current_price'] = 'desc';
		}
		if (empty($orel['order_time'])) {
			$order['current_price'] = 'desc';
		}

		$order['id'] = 'desc';

		 //单个二级分类搜 find_in_set('11', 'se_category_id')第一个值是传递的数据 ， 二是数据库的字段
		$datas[] = "end_time>".time();
		$datas[] = 'status= 1';

		if(!empty($data['se_category_id'])) {
			$datas[]="find_in_set(".$data['se_category_id'].", se_category_id)";
		}

		if(!empty($data['category_id'])) {
			$datas[]= 'category_id ='. $data['category_id'];
		}

		if(!empty($data['city_id'])) {
			$datas[]= 'city_id ='. $data['city_id'];
		}
		$datas[] = 'status= 1';

		$return = $this->where(implode(' AND ', $datas))
                    ->order($order)
                    ->paginate();
        echo $this->getLastSql(); 
        return $return;

	}
   
}