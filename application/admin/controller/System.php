<?php
namespace app\admin\controller;
use think\Controller;
use think\Model;


class System extends Controller
{
	public function index()
    {
    	return $this->fetch();
    }

    public function shielding()
    {
        $keywords =Model('XcxKeywords')
            ->select();
        $arr ='';
        $num = count($keywords);

        foreach ($keywords as $key =>$value){
            if ($key == $num-1){
                $arr .=$value['keywords'];
            }else{
                $arr .=$value['keywords'].'|';
            }
        }
        $this->assign('keyword',$arr);
        return $this->fetch();
    }
    public function addKeyword(){
	    $keyword = input('keyword');
        trim($keyword);
        $arr = explode("|",$keyword);
        $keywords =Model('XcxKeywords')
            ->select();

        foreach ($keywords as $key => $value){
            \model('XcxKeywords')->where('id',$value['id'])->delete();
        }
        foreach ($arr as $key => $value){
            \model('XcxKeywords')->insert(['keywords'=>$value]);
        }
        return $this->success('添加成功','shielding',0);
    }
}
