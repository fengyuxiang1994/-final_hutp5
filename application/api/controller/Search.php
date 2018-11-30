<?php
/**
 * Created by PhpStorm.
 * User: hu
 * Date: 2018/11/20
 * Time: 16:48
 */

namespace app\api\controller;

use think\Controller;


class Search extends Controller
{
    public function userSearch() {
        $keyword = input('keyword');
        $data = model('XcxUser')
            ->where('nickName','like','%'.$keyword.'%')
            ->select();
        return $data;
    }
    public function userFollow() {
       $user_id = input('user_id');
        $data = model('XcxGuanzhu')
            ->where('user_id',$user_id)
            ->select();
        $arr = [];
        foreach ($data as $key => $value){

            $res = model('XcxUser')
                ->where('id',$value['follow_id'])
                ->find();

            $arr[] = $res;
        }
        return $arr;
    }

    //评论展示
    public function commentList(){
        $comment_id = input('article_id');
//        $user = model('XcxAdd')
//            ->field(['home_uaer_name','r_image'])
//            ->where('id',$comment_id)
//            ->find();
        $list =  model('XcxComment')
            ->where('issue_id',$comment_id)
            ->select();
        $result = [];
        $resu = [];
        foreach($list as $key => $value) {
            $zan = model('XcxZan')->where('comment_id',$value['id'])->where('user_id',$value['user_id'])->find();
            if(empty($zan)){
                $resu['hasChange'] = false;
            }else{
                $resu['hasChange'] = true;
            }
            $user = model('XcxUser')
            ->field(['nickName','avatarUrl','id'])
            ->where('id',$value['user_id'])
            ->find();
            $resu['user']=$user;
            $resu['reply']= $value['reply_msg'];

            $resu['time'] = date("m月d日 H:i",strtotime($value['create_date']));
            $resu['num']=$value['zan_count'];
            $resu['id']=$value['id'];
            $data = model('XcxReply')
                ->where('comment_id',$value['id'])
                ->select();
            $str=[];
            $arr =[];
            foreach ($data as $k => $v) {
                $users = model('XcxUser')
                    ->field(['nickName'])
                    ->where('id',$v['to_user_id'])
                    ->find();
                $usersin = model('XcxUser')
                    ->field(['nickName'])
                    ->where('id',$v['from_user_id'])
                    ->find();
                $arr['reply_id']=$v['id'];
                $arr['to_user_id']= $users;
                $arr['from_user_id']= $usersin;
                $arr['replys']=$v['reply_msg'];
                $str[] = $arr;
            }
            $resu['replyss'] =$str;
            $result[] = $resu;
        }
        return $result;
    }

    //话题列表
    public function topicList() {
        $keyword = input('keyword');
        $data = model('XcxTopic')->where('topic_title','like',"%$keyword%")->select();
        return $data;
    }

    //评论点赞
//    public function commentZan() {
//        $commnet_id = input('comment_id');
//
//    }

    public function commentZan()
    {
        $user_id = input('user_id');
        //评论id
        $comment_id = input('comment_id');

        $zan_count = input('zan_count');
        if (!$user_id || !$comment_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxZan')
            ->where('type', 2)
            ->where('user_id', $user_id)
            ->where('comment_id', $comment_id)
            ->find();
        $resaa =model('XcxCommnet')
            ->save(['zan_count' => $zan_count], ['id' => $comment_id]);
        if ($data || $resaa) {
            $res = model('XcxZan')
                ->where('type', 2)
                ->where('user_id', $user_id)
                ->where('comment_id', $comment_id)
                ->delete();
            if ($res) {
                return toJson('1000', '取消点赞成功', '', '');
            }
        }
        $res = model('XcxZan')
            ->insert(['user_id' => $user_id, 'comment_id' => $comment_id, 'type' => 2]);
        if ($resaa || $res ) {
            return toJson('1000', '点赞成功', '', '');

        }else{
            return toJson('1000', '取消点赞成功', '', '');
        }

    }

    public function hotTopic(){
        $data = model('XcxTopic')->order(['search_num'=>'desc'])->limit(10)->select();
        $max = count($data) > 4 ? 4 : count($data);
        for($i=0;$i<$max;++$i){
            $data[$i]['is_hot'] = true;
        }
        return $data;
    }


}