<?php
namespace app\api\controller;
use think\Controller;


class Label extends Controller
{
	//获取小程序的主页内容
    public function getLabelApi()
    {
   
        $data = input('get.');
        $sdata = []; 

        if (!empty($data['username'])) {
            $sdata['username'] = ['like', '%'.$data['username'].'%'];
        }

        $dataLabel = model('XcxLabel')->getNormalDeals($sdata);
        if($dataLabel){
        	return show(1, 'success', $dataLabel);
        }else{
        	return show(0, 'success', "没数据");
        }
        
    }

}
