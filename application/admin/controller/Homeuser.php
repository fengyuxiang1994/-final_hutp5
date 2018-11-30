<?php
namespace app\admin\controller;
use think\Controller;

class Homeuser extends Controller
{
	public function index()
    {
    	$user = model("XcxUser")->getUserIndex();
    	// dump($user);
    	return $this->fetch('', [
    	   'user' => $user,
        ]);
    }

}
