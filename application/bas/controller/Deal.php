<?php
namespace app\bas\controller;
use think\Controller;
class Deal extends  Base
{  
    public function index()
    {
        
    	return $this->fetch();
    }
    
    /**
     * 商品的添加
     */
     public function add()
    {
        $bisid = $this->getLoginUser()->bis_id;
        if (request()->isPost()) {
            $data = input('post.');
            $location = model('BasLocation')->get($data['location_ids'][0]);

            $dealData = [
                'bis_id' => $bisid,
                'name' => $data['name'],  
                'image' => $data['image'], 
                'category_id' => $data['category_id'],
                'se_category_id' => empty($data['se_category_id']) ? '': implode(',', $data['se_category_id']),
                'city_id' => $data['se_city_id'],
                'location_ids' => empty($data['location_ids']) ? '' :implode(',', $data['location_ids']),
                'start_time' => strtotime($data['start_time']),
                'end_time' => strtotime($data['end_time']),
                'total_count' => $data['total_count'],
                'origin_price' => $data['origin_price'],
                'current_price' => $data['current_price'],                
                'coupons_befgin_time' => strtotime($data['coupons_befgin_time']),
                'coupons_end_time' => strtotime($data['coupons_end_time']),
                'notes' => $data['notes'],
                'bis_account_id' => $this->getLoginUser()->id,
                'description' => $data['description'],
                'xpoint' => $location->xpoint,
                'ypoint' => $location->ypoint,

            ];

            // dump($dealData);exit;
            $dealid = model('Deal')->add($dealData);
            if ($dealid) {
                $this->success('添加成功', url('deal/index'));
            }else{
                $this->error('添加失败');
            }

        }else{
            $citys = model('City')->getCity();
            $categorys = model('Category')->getCategory();
            return $this->fetch('', [
                'citys' => $citys, 
                'categorys' => $categorys,
                'bislocation' => model('BasLocation')->getMendin($bisid),
            ]);
        }
    	
    }
}
