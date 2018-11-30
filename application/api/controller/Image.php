<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\file;

class Image extends Controller
{

    public function upload()
    {
    	$file = Request::instance()->file('file');
    	//给目录
    	$info = $file->move('uploaadd');
    	if ($info && $info->getpathname()) {
    		return show(1, 'success', '/'.$info->getpathname());
    	}
    	return show(0, 'upload error');

    	// print_r($info->getpathname());
    }

}