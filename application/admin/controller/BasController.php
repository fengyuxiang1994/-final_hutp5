<?php
namespace app\admin\controller;
use think\Controller;

class BasController extends  Controller
{  
    //公共的变量名
    public $objadmin;
    public $objauthgroup;
    public $objauthrule;
    public $objauthgroupaccess;
    public $objbas;
    public $objbasaccount;
    public $objbaslocation;  
    public $objcategory;
    public $objdeal;
    public $objfeatured;
    public $objcity;

  
    public $account;
    public $authname;

    public $auth;
    
    public function _initialize()
    {
        //公共的初始化model类
        $this->objadmin = model('AdminUser');
        $this->objauthgroup = model('AuthGroup');
        $this->objauthrule = model('AuthRule');
        $this->objauthgroupaccess= model('AuthGroupAccess');
        $this->objbas = model('Bas');
        $this->objbasaccount = model('BasAccount');
        $this->objbaslocation = model('BasLocation');
        $this->objcategory = model('Category');
        $this->objcity = model('City');
        $this->objdeal = model('Deal');
        $this->objfeatured = model('Featured');
        $this->auth=new Auth();
        //判断用户是否登录
        $isLogin = $this->isLogin();
        if (!$isLogin) {
            return $this->redirect(url('login/index'));
        }
        $this->assign('username', $this->getLoginUser());
       
        // var_dump($auth);
        $notCheck=array('Index/index','Index/welcome','Login/logout');
        $name =$this->auth();

        // dump($auth->check($name,session('id', '', 'adminid')));
        if(session('id', '', 'adminid')!=1){
            if(!in_array($name, $notCheck)){
                if(!$auth->check($name, session('id', '', 'adminid'))){
                    $this->error('没有权限');
                }
            }
        } 
    }
    public function isLogin()
    {
        $user = $this->getLoginUser();
        if ($user && $user->id) {
            return true;
        }
        return false;
    }

    public function getLoginUser()
    {
        if (!$this->account) {
            $this->account = session('adminName', '', 'admin');
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

    public function status()
    {
        $data = input('get.');
        $validate = validate('Category');
        if(!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        $model = request()->controller();
        // echo $model;exit;

        $res =model($model)->save(['status' => $data['status']], ['id' => $data['id']]); 

        if($res) {
            $this->success('状态更新成功');
        }else {
            $this->error('状态更新失败');
        }
    }

     public function sort($data,$pid=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['dataid']=$this->getparentid($v['id']);
                $arr[]=$v;
                $this->sort($data,$v['id']);
            }
        }
        // dump($arr);
        return $arr;
    }


    public function getparentid($authRuleId){
        $AuthRuleRes= model('AuthRule')->getAuthruleGroup();
        // dump($AuthRuleRes);
        return $this->_getparentid($AuthRuleRes,$authRuleId,true);
    }

  

    public function _getparentid($AuthRuleRes,$authRuleId,$clear=false){
        static $arr=array();
        if($clear){
            $arr=array();
        }
        foreach ($AuthRuleRes as $k => $v) { 
            if($v['id'] == $authRuleId){
                $arr[]=$v['id'];
                $this->_getparentid($AuthRuleRes,$v['pid'],false);
            }
        }
        asort($arr);
        $arrStr=implode('-', $arr);
        // dump($arrStr);
        return $arrStr;
    }
}
