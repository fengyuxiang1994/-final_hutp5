<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8
 * Time: 14:38
 */

namespace app\api\controller;

use app\api\keyword\SensitiveWordTree;
use think\Controller;


class Comment extends Controller
{
    public function commentsSave()
    {

        $issue_id = Input('comment_id');
        $user_id = Input('user_id');
        $reply_msg = Input('reply_msg');

        if ($issue_id == null || $user_id == null || $reply_msg == null) {
            return error('1540', '回复失败，请查看参数设定', '');
        }
        $example = new SensitiveWordTree();
        //获取评论关键词
        $data = model('XcxKeywords')->select();

        for ($i = 0; $i < count($data); $i++) {
            $sensitiveWordList[] = $data[$i]['keywords'];
        }
//        $sensitiveWordList = [
//            '中国人',
//            '中国男人',
//            '中国女人',
//            '中国男',
//            '中国女',
//            '中国',
//            '男人',
//            '女人',
//            '男',
//            '女',
//            '人',
//            '人们',
//        ];

        foreach ($sensitiveWordList as $eachWord) {
            $example->addWordToTree($eachWord);

        }
        $result = $example->search($reply_msg);

        if ($result) {
            foreach ($result as $eachData) {
                return toJson(1001, '存在敏感关键词', count($eachData['word']), $eachData['word']);
            }
        }
        $data = [
            'issue_id' => $issue_id,
            'user_id' => $user_id,
            'reply_msg' => $reply_msg,
        ];

        //存储评论
        $res = model('XcxComment')->insert($data);
        if ($res) {
            $re= model('XcxComment')->where('issue_id',$issue_id)->select();
            $user_commnet = count($re);
              model('XcxAdd')
                ->where('id',$issue_id)
                ->update(['user_comment'=> $user_commnet]);
            return toJson('1000', 'Success', '0', '');
        }
    }


    // 评论回复
    public function commentReply()
    {
        $comment_id = Input('comment_id');
        $from_user_id = Input('from_user_id');
        $to_user_id = Input('to_user_id');
        $reply_msg = Input('reply_msg');
        $create_date = date("Y-m-d H:i:s", time());
        if ($comment_id == null || $from_user_id == null || $to_user_id == null || $reply_msg == null) {
            return error('1540', '回复失败，请查看参数设定', '');
        }
        $commet = model('XcxComment')->where('id', $comment_id)->select();
        if (!$commet) {
            return error('10021', '该评论已删除', '');
        }
        $form_userid = model('XcxUser')->where('id', $from_user_id)->select();
        if (!$form_userid) {
            return error('10022', '用户未注册', '');
        }
        $to_userid = model('XcxUser')->where('id', $to_user_id)->select();
        if (!$to_userid) {
            return error('10022', '用户未注册', '');
        }
        $example = new SensitiveWordTree();
        //获取评论关键词
        $data = model('XcxKeywords')->select();
        for ($i = 0; $i < count($data); $i++) {
            $sensitiveWordList[] = $data[$i]['keywords'];
        }
        foreach ($sensitiveWordList as $eachWord) {
            $example->addWordToTree($eachWord);
        }
        $result = $example->search($reply_msg);

        if ($result) {
            foreach ($result as $eachData) {
                return toJson(1001, '存在敏感关键词', count($eachData['word']), $eachData['word']);
            }
        }
        $data = [
            'comment_id' => $comment_id,
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id,
            'reply_msg' => $reply_msg,
            'create_date' => $create_date,
        ];
        //存储评论
        $res = model('XcxReply')->insert($data);
        if ($res) {
            return toJson('1000', '回复评论成功', '0', '');
        }

    }


// 个人删除评论  或者管理员直接删除接口
    public function deleteReply()
    {
        $comment_id = Input('comment_id');
        if ($comment_id == null) {
            return error('1540', '删除失败，请查看参数设定', '');
        }
        $res = model('XcxComment')
            ->where('id', $comment_id)
            ->find();
        $result = model('XcxReply')
            ->where('comment_id', $comment_id)
            ->select();
        if (!$res) {
            return error('1510', '改条评论已删除，请刷新小程序', '');
        }
        if (!$result) {
            $res = model('XcxComment')
                ->where('id', $comment_id)
                ->delete();
            if (!$res) {
                return error('2000', '评论删除失败请联系管理员', '');
            }
            return toJson('1000', '评论删除成功', '', '');

        }



        $res = model('XcxComment')
            ->where('id', $comment_id)->delete();
        $result = model('XcxReply')
            ->where('comment_id', $comment_id)->delete();
        if (!$res || !$result) {
            return error('2000', '评论删除失败请联系管理员', '');
        }
        $re= model('XcxComment')->where('issue_id',$res['issue_id'])->select();
        $user_commnet = count($re);
        model('XcxAdd')->where('id',$res['issue_id'])
            ->update(['user_comment',$user_commnet]);
        return toJson('1000', '评论删除成功', '', '');
    }


    public function dianZan()
    {
        $wenzhang_id = input('wenzhang_id');
        $comment_id = input('comment_id');
        $user_id = input('user_id');
        if ($wenzhang_id == null || $comment_id != null) {
            $data = [
                'comment_id' => $comment_id,
                'type' => 2,
                'user_id' => $user_id,
                'status' => 1
            ];
            $res = model('XcxZan')->insert($data);
            if (!$res) {
                return error('2001', '点赞条数插入失败', '');
            }
            return toJson('1000', '文章点赞成功', '', '');
        } else {
            $data = [
                'comment_id' => $wenzhang_id,
                'type' => 1,
                'user_id' => $user_id,
                'status' => 1
            ];
            $res = model('XcxZan')->insert($data);
            if (!$res) {
                return error('2001', '点赞条数插入失败', '');
            }
            return toJson('1000', '点赞成功', '', '');
        }
    }

}