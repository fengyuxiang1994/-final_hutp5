<?php
namespace app\admin\controller;
use think\Controller;


class Home extends Controller
{
	public function index()
    {
    	return $this->fetch();
    }

    public function add()
    {
    	$categorys = model('Category')->getCategory();
	    return $this->fetch('', ['categorys' => $categorys ]);
    }

    public function save()
    {
        if (!request()->isPost()) {
    		$this->error('请求错误');	
        }

        $data = input('post.');
       
        $data['cat'] = '';

		if (!empty($data['se_category_id'])) {

		  $data['cat'] = implode("|", $data['se_category_id']);   

		}
        if (!empty($data['r_image'])) {
              $data['r_image']=strtr($data['r_image'],'\/','/');
            
        }

        if (!empty($data['image'])) {
              $data['image']=strtr($data['image'],'\/','/');
            
        }
		$data['description'] = preg_replace("/<p.*?>|<\/p>/is","", $data['description']);
    	$homeData = [
            'home_uaer_name' => htmlentities($data['home_uaer_name']),
            'category_id' => $data['category_id'],
            'category_path' => $data['category_id']. ',' . $data['cat'],
            'r_image' => $data['r_image'],
            'image' => $data['image'],
            'description' => empty($data['description']) ? '' : $data['description'],
            'praise' => $data['praise'],

        ];
        $homeid = model('XcxHome')->add($homeData);
         
         dump($homeData);
        if ($homeid) {
     		return $this->success('新增成功');
     	}else{
     		return $this-error('新增失败');
     	} 	
       
    }
}
