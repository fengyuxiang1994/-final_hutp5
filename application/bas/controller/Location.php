<?php
namespace app\bas\controller;
use think\Controller;
class Location extends  Base
{  
    public function index()
    {
    	return $this->fetch();
    }

    public function add()
    {
    	if (request()->isPost()) {
    		$data = input('post.');
    		$basid = $this->getLoginUser()->bis_id;
	        //总店信息入库

	        $lenlat = \Map::getLngLat($data['address']);
	        // if(is_array($lenlat)){
	        if (empty($lenlat) || $lenlat['status'] != 0 ||$lenlat['result']['precise'] != 1 ) {
	            $this->error('无发获取数据，或者是填写的地址不够详细');
	        }
	        // }

	        $data['cat'] = '';

	        if (!empty($data['se_category_id'])) {
	          $data['cat'] = implode("|", $data['se_category_id']);   
	        }
            $locationData = [
	            'bis_id' => $basid,
	            'name' => $data['name'],
	            'tel' => $data['tel'],
	            'contact' => $data['contact'],
	            'category_id' => $data['category_id'],
	            'category_path' => $data['category_id']. ',' . $data['cat'],
	            'city_id' => $data['city_id'],
	            'city_path' => empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
	            'logo' => $data['logo'],
	            'address' => $data['address'],
	            'api_address' => $data['address'],
	            'open_time' => $data['open_time'],
	            'content' => empty($data['content']) ? '' : $data['content'],
	            'is_main' => 0,
	            'xpoint' => empty($lenlat['result']['location']['lng']) ? '' : $lenlat['result']['location']['lng'],
	            'ypoint' => empty($lenlat['result']['location']['lat']) ? '' : $lenlat['result']['location']['lat'],
	        ];
    
            $basLocationid = model('BasLocation')->add($locationData);   
            if ($basLocationid) {
             		return $this->success('门店申请成功');
             	}else{
             		return $this-error('门店申请失败');
             	} 	
        }else{
	    	$citys = model('City')->getCity();
	        $categorys = model('Category')->getCategory();
	    	return $this->fetch('', ['citys' => $citys, 'categorys' => $categorys ]);
	    }
    }
}