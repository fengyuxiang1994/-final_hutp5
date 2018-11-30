<?php
namespace app\admin\controller;
use think\Controller;

class Admin extends  BasController
{
  
    public function index()
    {
        $adminUser = $this->objadmin->getIndex();
        // var_dump($adminUser);
        $auth=new Auth();
        foreach ($adminUser as $k => $v) {
            $addgroupTitle=$this->auth->getGroups($v['id']);
            var_dump($auth->getGroups($v['id']));
            $groupTitle=$addgroupTitle[0]['title'];
            $v['groupTitle']=$groupTitle;
        }
       
    	return $this->fetch('',[
            'adminUser' =>  $adminUser,
            ]);
    }

    public function add()
    {
        $adminGroups = $this->objauthgroup->getIndex();
        return $this->fetch('', [
            'adminGroups' =>  $adminGroups,
            ]);
    }

    public function save()
    {
    	if (!request()->isPost()) {
    		$this->error('请求失败');
    	}

    	$data = input('post.');
    	 if ($data['password'] != $data['repassword']) {
                $this->error('两次密码不一致');
            }

        $data['code'] = mt_rand(100, 10000);

        $data['password'] = md5($data['password'].$data['code']);

    	$adminData = [
            'username' => $data['username'],
            'password' => $data['password'], 
            'code' => $data['code'],
            'email' => $data['email'],
            'sex' => $data['sex'],
            'phone' => $data['phone'],
    	];
    	$adminID = $this->objadmin->add($adminData);
        
        $authGroupAccessData = [
            'uid' => $adminID,
            'group_id' => $data['group_id']
        ];
        $authGroupAccess = $this->objauthgroupaccess->add($authGroupAccessData);
         if ($authGroupAccess) {
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
}
