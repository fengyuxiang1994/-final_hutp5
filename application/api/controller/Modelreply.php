<?php
/**
 * Created by PhpStorm.
 * User: hu
 * Date: 2018/11/22
 * Time: 11:16
 */


namespace app\api\controller;

use think\Controller;


class Modelreply extends Controller
{
    //评论通知
    public function replySend()
    {
        $user_id = input('user_id');
        $comment_id =input('comment_id');

        if (empty($user_id)  || empty($comment_id)) {
            return $this->error('参数缺失');
        }
        $reply_time = date("Y-m-d H:i", time());
        $addinfo = model('XcxAdd')
            ->where('id',$comment_id)
            ->find();
//        if ($user_id == $addinfo['user_id']){
//            return error('自己的文章不需要发送通知');
//        }
        $userinfo = model('XcxUser')
            ->where('id', $addinfo['user_id'])
            ->find();
        $page = input('page');
//        $touser = $userinfo['openid'];
        $touser ="opnXT5HSYAoewG7n2HKptiMAbMyE";
        $form_id = input('form_id');
        $reply_msg = input('reply_msg');
//        $page ="pages/index/index";
//        $touser ="oHgcj0Sd94gzaHtAfdxiiSJiXXnQ";
//        $form_id = "";
        $template_id = 'QwNPUR942z5TFgh007_KW1YoA6w87KZE7CChmUjQiJ4';
        $access_token = $this->getAccessToken();
        $datas = [
            "touser" => $touser,
            "template_id" => $template_id,
            "page" => $page,
            "form_id" => $form_id,
            "data" => [
                "keyword1" => [
                    "value" => $addinfo['description']
                ],
                "keyword2" => [
                    "value" => $userinfo['nickName']
                ],
                "keyword3" => [
                    "value" => $reply_msg
                ],
                "keyword4" => [
                    "value" => $reply_time
                ]
            ],
            "emphasis_keyword" => "keyword3.DATA"
        ];
        $datas = json_encode($datas);
        $urlData = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$access_token}";
        $apiData = json_decode($this->curlHttp($urlData, true,true,$datas), true);
        return $apiData;

    }
    //回复通知
    public function replySend_s()
    {
        $user_id = input('user_id');
        if (empty($user_id)) {
            return $this->error('参数缺失');
        }
        $reply_time = date("Y-m-d H:i", time());
        $reply_msg = input('reply_msg');
        $comment_id =input('comment_id');
        $userinfo = model('XcxUser')
            ->where('id', $user_id)
            ->find();
        $from_user_id =model('XcxReply')->where('comment_id',$comment_id)->find();
        $addinfo = model('XcxReply')
            ->where('comment_id',$comment_id)
            ->where('to_user_id',$user_id)
            ->where('from_user_id',$from_user_id['from_user_id'])
            ->find();
        $page = input('page');
        $touser = $userinfo['openid'];
        $form_id = input('form_id');
//        $page ="pages/index/index";
//        $touser ="oHgcj0Sd94gzaHtAfdxiiSJiXXnQ";
//        $form_id = "";
        $template_id = 'cwyVcIILTJ48YQ6Dpc2dkXnFH3WC5buJqvkFYLi6VgI';
        $access_token = $this->getAccessToken();
        $datas = [
            "touser" => $touser,
            "template_id" => $template_id,
            "page" => $page,
            "form_id" => $form_id,
            "data" => [
                "keyword1" => [
                    "value" => $addinfo['reply_msg']
                ],
                "keyword2" => [
                    "value" => $userinfo['nickName']
                ],
                "keyword3" => [
                    "value" => $reply_msg
                ],
                "keyword4" => [
                    "value" => $reply_time
                ]
            ],
            "emphasis_keyword" => "keyword3.DATA"
        ];
        $datas = json_encode($datas);
        $urlData = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$access_token}";
        $apiData = json_decode($this->curlHttp($urlData, true,true,$datas), true);
        return $apiData;

    }
    public function getAccessToken()
    {
        $secret = config('xcx.secret');
        $appid = config('xcx.appid');
        $urlData = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $apiData = json_decode($this->curlHttp($urlData, true), true);
        return $apiData['access_token'];
    }

    //@param
    //$url   接口地址
    //$https  是否是一个Https 请求
    //$post  是否是post 请求
    //$post_data post 提交数据  数组格式
    public static function curlHttp($url, $https = false, $post = false, $post_data = array())
    {
        $ch = curl_init();                                                        //初始化一个curl
        curl_setopt($ch, CURLOPT_URL, $url);         //设置接口地址  如：http://wwww.xxxx.co/api.php
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否把CRUL获取的内容赋值到变量
        curl_setopt($ch, CURLOPT_HEADER, 0);//是否需要响应头
        /*是否post提交数据*/
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (!empty($post_data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }
        /*是否需要安全证书*/
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}