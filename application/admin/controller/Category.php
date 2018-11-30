<?php
namespace app\admin\controller;
use think\Controller;
class Category extends  BasController
{
    public function index()
    {       
        // echo LOG_PATH;
        $parentId = input('get.parent_id', 0, 'intval');
        // var_dump($_SERVER['HTTP_REFERER']);
        // var_dump($parentId);
        // input('传递方式','默认参数','对参数过滤')
        // 调用getIndex方法查看顶级和字及数据显示在index页面
        $categorys = $this->objcategory->getIndex($parentId);
        return $this->fetch('',[
            'categorys' => $categorys,
            'id' => $parentId,
            'http' => $_SERVER['HTTP_REFERER'],
        ]);
    }

    public function add()
    {
        $categorys = $this->objcategory->getFirst();
        return $this->fetch('',
            ['categorys' => $categorys]);
    }
    public function save()
    {
    	// print_r(input('post.username'));
    	// print_r(input('post.id'));exit;
    	// print_r(request()->post());

        if (!request()->isPost()) {
            $this->error('请求失败');
        }
    	$data = input('post.');
    	$validate = validate('Category');
    	// $hu = $validate->check($data);
    	// echo $hu;
    	
    	if(!$validate->scene('add')->check($data)) {
            $this->error($validate->getError());
        }

        if (!empty($data['id'])) {
            return $this->update($data);
        }
        $res = $this->objcategory->add($data);
        // dump($res);
        if($res) {
            $this->success('添加成功');
        }else {
            $this->error('添加失败');
        }
    }

    public function edit($id = 0)
    {
        if(intval($id) < 1) {
            $this->error('参数不合法');
        }

        $category = $this->objcategory->get($id);
        $categorys = $this->objcategory->getFirst();
        return $this->fetch('',
            ['categorys' => $categorys,
            'category' => $category]);
        // echo input('get.id');
    }

    public function update($data)
    {
        $res =$this->objcategory->save($data, ['id' => intval($data['id'])]); 
         if($res) {
            $this->success('更新成功');
        }else {
            $this->error('更新失败');
        }
    }

    public function listorder($id, $listorder)
    {
        // echo $id."<br>";
        // echo $listorder."<br>";
         $res =$this->objcategory->save(['listorder' => $listorder], ['id' => $id]); 
         if($res) {
            $this->result($_SERVER['HTTP_REFERER'], 1, '成功');
        }else {
            $this->result($_SERVER['HTTP_REFERER'], 0, '失败');
        }
    }

    // public function status()
    // {
    //     $data = input('get.');
    //     $validate = validate('Category');
    //     if(!$validate->scene('status')->check($data)) {
    //         $this->error($validate->getError());
    //     }

    //     $res =$this->objcategory->save(['status' => $data['status']], ['id' => $data['id']]); 

    //     if($res) {
    //         $this->success('状态更新成功');
    //     }else {
    //         $this->error('状态更新失败');
    //     }
    // }
}
