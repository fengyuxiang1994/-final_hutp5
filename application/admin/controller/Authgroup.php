<?php
namespace app\admin\controller;
use think\Controller;
class Authgroup extends  BasController
{
    /**
     * 角色列表页
     */
    public function index()
    {   
        $authgroups = $this->objauthgroup->getIndex();

        // echo $authgroups->status;

        // dump($authgroups[0]['status']);
    	return $this->fetch('', [
            'authgroups' => $authgroups,
            ]);

    }
   
    
    public function add()
    {
        $ruleGroup = $this->objauthrule->getAuthruleGroup();
        $ruleGroups = $this->sort($ruleGroup);
        // dump($ruleGroups);

    	return $this->fetch('', [
            'ruleGroups' => $ruleGroups,
            ]);
    }

    public function save()
    {
    	if (!request()->isPost()) {
    		$this->error('请求失败');
    	}
    	$data = input('post.');
        if($data['rules']){
            $data['rules']=implode(',', $data['rules']);
        }
        
    	$authgroupData = [
    	    'title' => $data['title'],
    	    'description' => $data['description'],
            'rules' => $data['rules'],
    	];
        // dump($authgroupData);exit;
    	$authgroupId = $this->objauthgroup->add($authgroupData);
        if ($authgroupId) {
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
}
