<?php
namespace app\bas\controller;
use think\Controller;
class Base extends  Controller
{
    
    public $account;
    public $authname;
    
    public function _initialize()
    {
        //判断用户是否登录
    	$isLogin = $this->isLogin();
    	if (!$isLogin) {
            return $this->redirect(url('login/index'));
    	}
        $this->assign('username', $this->getLoginUser());
        // $auth=new Auth();
        // $notCheck=array('Index/index','Index/welcome','Login/logout');
        // $name =$this->auth();
        // dump($name);
        // if(session('id')!=1){
        //     if(!in_array($name, $notCheck)){
        //         if(!$auth->check($name,session('id'))){
        //             $this->error('没有权限',url('/bas/index/index'));
        //         }
        //     }
        // }
    }
    
    //判断是否登录
    public function isLogin()
    {   
        //获取session
    	$user = $this->getLoginUser();
    	if ($user && $user->id) {
    		return true;
    	}
    	return false;
    }
    //获取session值得方法
    public function getLoginUser()
    {
        // 不存在$this->account则获取$this->account
        if (!$this->account) {
        	$this->account = session('basAccount', '', 'bas');
        }
        
        return $this->account;    	

    }
    public function auth()
    { 
        $module = request()->module();
        $controller = request()->controller();
        $action = request()->action();
        $this->authname = $controller.'/'.$action;
        return $this->authname;
    }
}
 