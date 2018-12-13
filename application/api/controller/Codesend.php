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
use Guzzle\Http\Client;

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
                $post_data =json_encode($post_data);
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




    //获得二维码
    public function aaa()
    {
        $secret = config('xcx.secret');
        $appid = config('xcx.appid');
        $token = $this->getAccessToken();
      $page = input('page');
      $scene = input('scene');
      if(!$page && !$scene){
      	return '参数为空';
      }
      $qcode ="https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$token";
      $param = json_encode(array("page"=>"$page","scene"=> $scene));

      //POST参数
      $result = $this->httpRequest( $qcode, $param,"POST");
      //生成二维码
      //file_put_contents("qrcodes.png", $result);
      $base64_image ="data:image/jpeg;base64,".base64_encode( $result );
   	return $base64_image;
    }
  
  //把请求发送到微信服务器换取二维码
  public function httpRequest($url, $data='', $method='GET'){
    $curl = curl_init();  
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
    if($method=='POST')
    {
        curl_setopt($curl, CURLOPT_POST, 1); 
        if ($data != '')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }
    }

    curl_setopt($curl, CURLOPT_TIMEOUT, 30);  
    curl_setopt($curl, CURLOPT_HEADER, 0);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    $result = curl_exec($curl);  
    curl_close($curl);  
    return $result;
  } 




}