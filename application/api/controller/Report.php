<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 15:02
 */
namespace app\api\controller;

use think\Controller;

class Report extends Controller
{
    public function userReport(){
        $user_id = input('user_id');
        $comment_id = input('comment_id');
        $Report_content = input('content');
        $data = model('XcxReport')->insert(['user_id'=> $user_id,'comment_id'=>$comment_id,'content'=>$Report_content]);
        return $this->success('success','','');
    }
}