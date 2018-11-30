<?php
namespace app\admin\controller;
use think\Controller;
class Authrule extends  BasController
{

    public function index()
    {
      return $this->fetch();
    }

    public function add()
    {
       if (request()->isPost()) {
            $data = input('post.');
            $pid = $data['pid'];
            // dump($pid);
            $levels = $this->objauthrule->getAuthruleID($pid);
            // dump($levels);
            if ($levels) {
                // echo $levels->level;
                $level = $levels->level+1;
            }else{
                $level= 0;
            }
            $authruleData = [
                'pid' => $data['pid'],
                'title' => $data['title'],
                'name' => $data['name'],
                'level' => $level,
            ];

            // dump($authruleData);
            $authruleDataId = $this->objauthrule->add($authruleData);


            if ($authruleDataId) {
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
             $authruleDatas = $this->objauthrule->getAuthruleFirst();
             $authruleDatass = $this->sort($authruleDatas);
             return $this->fetch( '', [
                'authruleDatass' => $authruleDatass,
                ]);
        }

    }
       
}
