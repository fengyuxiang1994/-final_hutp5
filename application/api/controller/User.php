<?php

namespace app\api\controller;

use phpencry\Encrypted;
use think\Controller;
use think\Request;
use think\Cache;
use weiuser\WXBizDataCrypt;

require_once "extend/weiuser/wxBizDataCrypt.php";
class User extends Controller
{


    public function getPhone()
    {

        $code = input('get.code');
        $arr = \Xcx::gerSou($code);
        // dump($arr);
        if ((isset($arr['errcode']) && $arr['errcode'] > 0) || !isset($arr['session_key']) || !isset($arr['openid'])) {
            return show(0, 'huerror');
        }
        $session_key = $arr['session_key'];
        $openid = $arr['openid'];
        $encryptedData = input('get.encryptedData');
        $iv = input('get.iv');
        $data = \phpencry\Encrypted::encryptedData($encryptedData, $iv, $session_key);
        // if($errCode != 0){
        //   echo "解密数据失败";
        //   die;
        // }else {
        //     $data = json_decode($data,true);
        //     $res = model('XcxUser')->getXcxName($openid);
        //     if (!$res) {
        //       $data = [
        //         'openid' => $openid,
        //         'phone' => $data['phoneNumber'],
        //       ];
        //       $phone = model('XcxUser')->add($data);
        //       if($phone){
        //         return show(1, 'success', '成功');
        //       }else{
        //           return show(1, 'success', '失败');
        //       }

        //     }else{
        //        $data = [
        //         'phone' => $data['phoneNumber'],
        //       ];
        //       $phone = model('XcxUser')->updatePhone($data, $openid);
        //        if($phone){
        //         return show(1, 'success', '成功');
        //       }else{
        //           return show(1, 'success', '失败');
        //       }
        //     }
        // }
    }


    public function getMoneyApi()
    {
        $moneid = input('get.id', 0, 'intval');
        $userRes = model('XcxUser')->getMone($moneid);
        if ($userRes) {
            return show(1, 'success', $userRes);
        }
    }


    public function getUses() {
        $code = $_GET['code'];
        $secret = config('xcx.secret');
        $appid = config('xcx.appid');
        $apiUrl = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        $apiData = json_decode($this->curlHttp($apiUrl, true), true);
        $user = model('XcxUser')->where('openid', $apiData['openid'])->find();
        if (!empty($user)) {
            $weizhang = model('XcxAdd')->where('user_id',$user['id'])->select();
            $this->fansInfo($user['id']);
            return [$user,$apiData];
        }
        $data['apiData'] =$apiData;

        return $data;
    }

    public function getUserInfo()
    {
        $openid =  $_GET['openid'];
        $session_key=$_GET['session_key'];
        $code = $_GET['code'];
        $encryptedData = $_GET['encryptedData'];
        $iv = $_GET['iv'];
//        if (!$code) {
//            return '缺少code参数';
//        }
        if (!$encryptedData) {
            return '缺少encryptedData参数';
        }
        if (!$iv) {
            return '缺少iv参数';
        }
//        $secret = config('xcx.secret');
        $appid = config('xcx.appid');

//        $apiUrl = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
//        $apiData = json_decode($this->curlHttp($apiUrl, true), true);
        //查找用户是否存在
        if ($openid) {
            $user = model('XcxUser')
                ->where('openid', $openid)
                ->find();
            if (!empty($user)) {
                return $user;
            }else{
                $result= self::user($code,$appid,$session_key,$encryptedData,$iv);
                $users = model('XcxUser')
                    ->where('openid', $openid)
                    ->find();
                model('XcxSettingNews')
                    ->insert(['user_id' => $users['id']]);
                model('XcxSettingPrivacy')->insert(['user_id' => $users['id']]);
                $resu = json_decode($result,true);

            }

        }


    }

    public function user($code,$appid,$session_key,$encryptedData,$iv){
        $data = [];

        $pc = new wxBizDataCrypt($appid, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0) {
            print($data . "\n");
        } else {
            print($errCode . "\n");
        }
        $data = json_decode($data, true);
        $open_id = $data['openId'];

        if (is_array($data)) {
            $userData = [
                'nickName' => $data['nickName'],
                'avatarUrl' => empty($data['avatarUrl']) ? '' : $data['avatarUrl'],
                'openid' => $open_id,
                'code' => $code,
                'gender' => empty($data['gender']) ? '' : $data['gender'],
                'city' => empty($data['city']) ? '' : $data['city'],
                'province' => empty($data['province']) ? '' : $data['province'],
            ];
            $res = model('XcxUser')->add($userData);
            if ($res) {
                $datas = model('XcxUser')->where('openid', $open_id)->find();
                return $datas;
            }
        }
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
        public function getUseris() {
        $openid =  $_GET['openid'];
        $user = model('XcxUser')->where('openid', $openid)->find();
        $this->fansInfo($user['id']);
        $users = model('XcxUser')->where('openid', $openid)->find();
        return $users;
    }


    public function fansInfo($user_id) {
        $dianzan = model('XcxAdd')
        ->where('user_id',$user_id)
        ->field('id,user_praise')
        ->select();
        $zan_num = 0;
        foreach ($dianzan as $key => $value) {
             // return $value['user_praise'];
            $zan_num = $zan_num + $value['user_praise'];
        }
        $fans = model('XcxUserguanzhu')
        ->where('user_id',$user_id)
        ->count();
        $guanzhu = model('XcxUserguanzhu')
        ->where('form_user_id',$user_id)
        ->count();
        $data = [
            'user_praise' => $zan_num,
            'user_fans' => $fans,
            'user_follow' =>$guanzhu 
        ];
        $info = model('XcxUser')
        ->where('id',$user_id)
       ->update($data);
        if (!$info) {
           return '点赞数更新失败';
        }
        return 'Success';
    }

}
