<?php
namespace app\admin\controller;
use think\Controller;

class Deal extends  BasController
{
    /**
     * 通过审核商品
     */
    public function index()
    {
        $data = input('get.');
        // dump($data);
        $sdata = []; 
        if (!empty($data['start_time']) && !empty($data['end_time']) && strtotime($data['start_time']) < strtotime($data['end_time'])) {
            $sdata['create_time'] = [
                ['gt', strtotime($data['start_time'])],
                ['lt', strtotime($data['end_time'])],

            ];
        }
        if (!empty($data['category_id'])) {
            $sdata['category_id'] = $data['category_id'];
        }

        if (!empty($data['city_id'])) {
            $sdata['city_id'] = $data['city_id'];
        }
       
        if (!empty($data['name'])) {
            $sdata['name'] = ['like', '%'.$data['name'].'%'];
        }

        // dump($sdata);

        $dealData = $this->objdeal->getNormalDeals($sdata);

    	$categorychx = model('Category')->getCategory();
        $categoryArrs = $cityArrs = [];
         foreach ($categorychx as $category) {
            $categoryArrs[$category->id] = $category->name;
        }
    	$citychx = model('City')->getChaxCity();
        foreach ($citychx as $city) {
            $cityArrs[$city->id] = $city->name;
        }
        // dump($categoryArrs[$dealData[0]['category_id']]);
        // dump($dealData);
        // var_dump($dealData[0]['category_id']);
        // print_r($dealData[0]['category_id']);
    	return $this->fetch('',[
            'cityName' => $cityArrs,
            'categoryName' => $categoryArrs,
    		'categorychx' => $categorychx,
    		'citychx' => $citychx,
            'dealData' => $dealData,
            'category_id' => empty($data['category_id']) ? '' : $data['category_id'],
            'city_id' => empty($data['city_id']) ? '' : $data['city_id'],
            'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
            'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
            'name' => empty($data['name']) ? '' : $data['name'],
    		]);
    }
    
   /**
    * 审核中商品
    */

    public function apply()
    {
         $data = input('get.');
        // dump($data);
        $sdata = []; 
        if (!empty($data['start_time']) && !empty($data['end_time']) && strtotime($data['start_time']) < strtotime($data['end_time'])) {
            $sdata['create_time'] = [
                ['gt', strtotime($data['start_time'])],
                ['lt', strtotime($data['end_time'])],

            ];
        }
        if (!empty($data['category_id'])) {
            $sdata['category_id'] = $data['category_id'];
        }

        if (!empty($data['city_id'])) {
            $sdata['city_id'] = $data['city_id'];
        }
       
        if (!empty($data['name'])) {
            $sdata['name'] = ['like', '%'.$data['name'].'%'];
        }

        // dump($sdata);

        $dealData = $this->objdeal->getShDeals($sdata);

        $categorychx = model('Category')->getCategory();
        $categoryArrs = $cityArrs = [];
         foreach ($categorychx as $category) {
            $categoryArrs[$category->id] = $category->name;
        }
        $citychx = model('City')->getChaxCity();
        foreach ($citychx as $city) {
            $cityArrs[$city->id] = $city->name;
        }
        // dump($categoryArrs[$dealData[0]['category_id']]);
        // dump($dealData);
        // var_dump($dealData[0]['category_id']);
        // print_r($dealData[0]['category_id']);
        return $this->fetch('',[
            'cityName' => $cityArrs,
            'categoryName' => $categoryArrs,
            'categorychx' => $categorychx,
            'citychx' => $citychx,
            'dealData' => $dealData,
            'category_id' => empty($data['category_id']) ? '' : $data['category_id'],
            'city_id' => empty($data['city_id']) ? '' : $data['city_id'],
            'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
            'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
            'name' => empty($data['name']) ? '' : $data['name'],
            ]);
    }
    
    /**
     * 删除审核不通过的商品
     */
    public function deleteapply()
    {
        $data = input('get.');
        // dump($data);
        $sdata = []; 
        if (!empty($data['start_time']) && !empty($data['end_time']) && strtotime($data['start_time']) < strtotime($data['end_time'])) {
            $sdata['create_time'] = [
                ['gt', strtotime($data['start_time'])],
                ['lt', strtotime($data['end_time'])],

            ];
        }
        if (!empty($data['category_id'])) {
            $sdata['category_id'] = $data['category_id'];
        }

        if (!empty($data['city_id'])) {
            $sdata['city_id'] = $data['city_id'];
        }
       
        if (!empty($data['name'])) {
            $sdata['name'] = ['like', '%'.$data['name'].'%'];
        }

        // dump($sdata);

        $dealData = $this->objdeal->getDeletDeals($sdata);

        $categorychx = model('Category')->getCategory();
        $categoryArrs = $cityArrs = [];
         foreach ($categorychx as $category) {
            $categoryArrs[$category->id] = $category->name;
        }
        $citychx = model('City')->getChaxCity();
        foreach ($citychx as $city) {
            $cityArrs[$city->id] = $city->name;
        }
        // dump($categoryArrs[$dealData[0]['category_id']]);
        // dump($dealData);
        // var_dump($dealData[0]['category_id']);
        // print_r($dealData[0]['category_id']);
        return $this->fetch('',[
            'cityName' => $cityArrs,
            'categoryName' => $categoryArrs,
            'categorychx' => $categorychx,
            'citychx' => $citychx,
            'dealData' => $dealData,
            'category_id' => empty($data['category_id']) ? '' : $data['category_id'],
            'city_id' => empty($data['city_id']) ? '' : $data['city_id'],
            'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
            'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
            'name' => empty($data['name']) ? '' : $data['name'],
            ]);
    }
}
