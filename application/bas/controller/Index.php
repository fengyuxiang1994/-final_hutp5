<?php
namespace app\bas\controller;
use think\Controller;
class Index extends  Base
{  
    public function index()
    {
    	return $this->fetch();
       // 
       //  echo $this->getLoginUser();
    }

     public function welcome()
    {
    	// dump(session('basAccount', '', 'bas'));
    	
    	return $this->fetch();
    }
}
