<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/9
 * Time: 11:31
 */
namespace app\admin\controller;
use think\Controller;


class Pinglun extends Controller
{
    public function index()
    {
        $res = model('XcxComment')->get();

        return $this->fetch('',['res' => $res]);
    }

    public function detil($id)
    {

        $res = model('XcxReply')->where('comment_id',$id)->select();
        return $this->fetch('',['res' => $res]);
    }


}