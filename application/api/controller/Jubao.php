<?php
/**
 * Created by PhpStorm.
 * User: Administration
 * Date: 2018/12/6
 * Time: 11:11
 */
namespace app\api\controller;
use think\Controller;
class Jubao extends  Controller
{
    public function userJuBao() {
        $user_id = input('user_id');
        if (!$user_id){
            return 'user_id为空';
        }
        $to_user_id = input('to_user_id');
        if (!$to_user_id){
            return 'to_user_id为空';
        }
        $conents = input('conents');
        if (!$conents){
            return 'conents为空';
        }
        $comment_id = input('comment_id');
        if (!$comment_id){
            return 'comment_id';
        }
        $data =[
            'user_id'=>$user_id,
            'to_user_id'=>$to_user_id,
            'contents'=>$conents,
            'comment_id'=>$comment_id,
            'create_time'=>date('Y-m-d H:i:s',time())
        ];
        $info = model('XcxUserPullBlack')->insert($data);
        if ($info){
            return '举报成功';
        }
    }
}