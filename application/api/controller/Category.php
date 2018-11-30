<?php

namespace app\api\controller;

use think\Controller;


class Category extends Controller
{
    
     private $obj;
    public function _initialize()
    {
        $this->obj = model('Category');
           
    }

     public function getCategoryByParentId()
    {
        $id = input('post.id', 0, 'intval');
        if(!$id){
            $this->error('ID不合法');
        }
        //通过顶级的id获取二级城市
        $category = $this->obj->getCategory($id);
        if(!$category){
            return show(0, 'error');
        }
        return show(1, 'success', $category);
    }
    
    public function getXcxCategoryByData()
    {
       
        //获取顶级分类
        $category = $this->obj->getFirst();;
        if(!$category){
            return show(0, 'error');
        }
        return show(1, 'success', $category);

    }

   



}
