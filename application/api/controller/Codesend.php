<?php
/**
 * Created by PhpStorm.
 * User: hu
 * Date: 2018/11/24
 * Time: 6:27
 */

namespace app\api\controller;

use think\Controller;
use think\Request;

class Codesend extends Controller
{
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


    /**
     * 发起http请求
     * @param string $url 访问路径
     * @param array $params 参数，该数组多于1个，表示为POST
     * @param int $expire 请求超时时间
     * @param array $extend 请求伪造包头参数
     * @param string $hostIp HOST的地址
     * @return array    返回的为一个请求状态，一个内容
     */
    public function makeRequest($url, $params = array(), $expire = 0, $extend = array(), $hostIp = '')
    {
        if (empty($url)) {
            return array('code' => '100');
        }

        $_curl = curl_init();
        $_header = array(
            'Accept-Language: zh-CN',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache'
        );
        // 方便直接访问要设置host的地址
        if (!empty($hostIp)) {
            $urlInfo = parse_url($url);
            if (empty($urlInfo['host'])) {
                $urlInfo['host'] = substr(DOMAIN, 7, -1);
                $url = "http://{$hostIp}{$url}";
            } else {
                $url = str_replace($urlInfo['host'], $hostIp, $url);
            }
            $_header[] = "Host: {$urlInfo['host']}";
        }

        // 只要第二个参数传了值之后，就是POST的
        if (!empty($params)) {
            curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($_curl, CURLOPT_POST, true);
        }

        if (substr($url, 0, 8) == 'https://') {
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($_curl, CURLOPT_URL, $url);
        curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_curl, CURLOPT_USERAGENT, 'API PHP CURL');
        curl_setopt($_curl, CURLOPT_HTTPHEADER, $_header);

        if ($expire > 0) {
            curl_setopt($_curl, CURLOPT_TIMEOUT, $expire); // 处理超时时间
            curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $expire); // 建立连接超时时间
        }

        // 额外的配置
        if (!empty($extend)) {
            curl_setopt_array($_curl, $extend);
        }

        $result['result'] = curl_exec($_curl);
        $result['code'] = curl_getinfo($_curl, CURLINFO_HTTP_CODE);
        $result['info'] = curl_getinfo($_curl);
        if ($result['result'] === false) {
            $result['result'] = curl_error($_curl);
            $result['code'] = -curl_errno($_curl);
        }

        curl_close($_curl);
        return $result;
    }

    public function getWXACodeUnlimit()
    {
        $ACCESS_TOKEN = $this->getAccessToken();
        $page = input('page');
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $ACCESS_TOKEN;
        $post_data =
            array(
                'scene' => "34",
                'page' => "pages/index/index",
                'width' => "280"
            );
        $post_data = json_encode($post_data);
        $data = $this->send_post($url, $post_data);
//        $result=$this->data_uri($data,'image/png');
        return $data;
    }

    //二维码获取
    public function getWXACode() {
         $page = input('page');
        $width = 280;
        $data = [
            'path' => $page

        ];
        $data = json_encode($data);
        $access_token = $this->getAccessToken();
        $urlData ="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token={$access_token}";
        $apiData = $this->get_http_array($urlData, $data);
//        $apiData = json_decode($this->curlHttp($urlData, true,true,$data), true);
        $result=$this->data_uri($apiData,'image/png');
        return $result;
    }
    //开启curl post请求
    public function get_http_array($url,$post_data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //没有这个会自动输出，不用print_r();也会在后面多个1
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        echo $output;
        die;
    }


    /**
     * 消息推送http
     * @param $url
     * @param $post_data
     * @return bool|string
     */
    protected function send_post($url, $post_data)
    {
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/json',
                //header 需要设置为 JSON
                'content' => $post_data,
                'timeout' => 60
                //超时时间
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

//二进制转图片image/png
    public function data_uri($contents, $mime)
    {
        $base64 = base64_encode($contents);
        return ('data:' . $mime . ';base64,' . $base64);
    }

}