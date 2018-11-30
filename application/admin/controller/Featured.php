<?php
namespace app\admin\controller;
use think\Controller;
class Featured extends  BasController
{
    
    public function index()
    {  
        $types = config('featured.featured_type');

        $type = input('get.type', 0, 'intval');

        $featureds = $this->objfeatured->getFeatured($type);
        $a = array('Tom','Mary','Peter','Jack'); 

        // dump($a);
        // dump($types);


        return $this->fetch('', [
            'types' => $types,
            'featureds' => $featureds,
            'type' => empty($type) ? '' : $type,
        ]); 
    }

    public function add()
    {  
        if (request()->isPost()) {

            $data = input('post.');
            $featuredData = [
                'title' => $data['title'],
                'type' => $data['type'],
                'image' => $data['image'],
                'url' => $data['url'],
                'description' => $data['description'],
            ];

            $featuredID = $this->objfeatured->add($featuredData);
            if ($featuredID) {
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            $types = config('featured.featured_type');
            return $this->fetch('', [
              'types' => $types,
            ]);
        } 
    }


}
