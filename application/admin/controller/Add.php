<?php
namespace app\admin\controller;
use think\Controller;

class Add extends Controller
{
	public function index()
    {
    	$add = model("XcxAdd")->getAddIndex();
    	foreach ($add as &$voo) {
    		$id =$voo['id'];
    		$imgd= model("XcxImg")->seleIndex($id);
    		$uu = [];
	    	foreach ($imgd as &$vo) {
	    		array_push($uu, 'http://www.xcx.com'.$vo['name']);
	    	}
	    	$voo['image'] = $uu;	
    	}
        // dump($add);
    	return $this->fetch('', [
    	   'add' => $add,
        ]);   
    }



}
