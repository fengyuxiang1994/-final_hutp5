<?php
namespace app\admin\controller;
use think\Controller;


class System extends Controller
{
	public function index()
    {
    	return $this->fetch();
    }

}
