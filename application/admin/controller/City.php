<?php
namespace app\admin\controller;
use think\Controller;
class City extends  BasController
{
   public function index()
   {
        

      $parentId = input('get.parent_id', 0, 'intval');
      // var_dump($_SERVER['HTTP_REFERER']);
      // var_dump($parentId);
      // input('传递方式','默认参数','对参数过滤')
      // 调用getIndex方法查看顶级和字及数据显示在index页面
      $cityIndexSelect = $this->objcity->getIndex($parentId);

      return $this->fetch('', [
        'cityIndexSelect' => $cityIndexSelect,
        'id' => $parentId,
        'http' => $_SERVER['HTTP_REFERER'],
      ]);

   }

   public function add()
   {    
        //调用getFirst方法查询顶级数据显示在添加页面
        $cityAddSelect = $this->objcity->getFirst();
        return $this->fetch('', [
          'cityAddSelect' => $cityAddSelect
        ]);

   }
    public function save()
    {
    	if (!request()->isPost()) {
    		$this->error('请求失败');
    	}

    	$data = input('post.');
        // dump($data);die;
    	$cityData = [
            'name' => $data['name'],
            'uname' => $data['uname'],
            'parent_id' => $data['parent_id'],
           
    	];
    	$cityId = $this->objcity->add($cityData);
    	 if($cityId) {
            $this->success('添加成功');
        }else {
            $this->error('添加失败');
        }
    }
}
