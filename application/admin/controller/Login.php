<?php
namespace app\admin\controller;
use think\Controller;

class Login extends  Controller
{
    public function index()
    {   
    	if (request()->isPost()) {
    		$data = input('post.');
    		$rel = model('AdminUser')->get(['username' => $data['username']]);
    		if (!$rel || $rel->status != 1) {
    			$this->error('该用户不存在，或者该用户未通过审核');
    		}
    		if ($rel->password != md5($data['password'].$rel->code)) {
    			$this->error('密码不正确');
    		}
    		model('AdminUser')->updateTime(['last_login_time' => time()], $rel->id);
            //保存用户信息 admin是作用域
    		session('adminName', $rel, 'admin');
            session('id', $rel->id, 'adminid');          
    		return $this->success('登录成功', url('index/index'));
    	}else{
            $account = session('adminName', '', 'admin');
            if ($account && $account->id) {
                return $this->redirect(url('index/index'));
        }else{
            }
    		return $this->fetch();
    	}   
    }


    public function logout()
    {
        session(null, 'admin');
        $this->redirect(url('login/index'));
    }
}