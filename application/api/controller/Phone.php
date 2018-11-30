<?php
namespace app\api\controller;

use think\Controller;
use think\Request;

class Phone extends Controller
{
    public function getPhoneNumber()
    {
    	// $code = input('get.code');
    	$APPID = 'wxc63f3326fcfff6bd';
 		$AppSecret = '2eccd78860180c32f2a07106bfcb327e';
 		$code = input('get.code');
 		dump($code);
 		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$APPID.'&secret='.$AppSecret.'&js_code='.$code.'&grant_type=authorization_code';
 		$arra = $this -> vegt($url);
 		$arr = json_decode( $arra, true );
 		// dump($arr);
 		if((isset($arr['errcode']) && $arr['errcode']>0) || !isset($arr['session_key']) || !isset($arr['openid'])){
    		return show(0, 'error');
    	}
        $session_key = $arr['session_key'];
        $openid = $arr['openid'];
        // dump($session_key);
 		// 数字签名校验
 		// $signature = input('get.signature');
 		// $rawData = input('get.rawData');
 		// $signature2 = sha1($rawData.$session_key);
 		// if($signature != $signature2){
 		// 	echo "数字签名失败";
 		// 	die;
 		// }
   //      $encryptedData =  input('get.encryptedData');
   //      $iv =  input('get.iv');
   //      $pc = new Wxbizdatacrypt($APPID,$session_key);
 		// $errCode = $pc->decryptData($encryptedData,$iv,$data);
 		// $dataa = json_decode($data,true);
 		// if(!isset($dataa['purePhoneNumber'])){
   //  		return show(0, 'errorhh');
   //  	}
 		// $phonenumber = $dataa['purePhoneNumber'];
 		// dump($phonenumber);
       // if(!$signature){
       //      return show(0, 'error');
       //  }
        return show(1, 'success', $openid);
    }


    public function sendCode(){
 		$APPID = '################APPID';
 		$AppSecret = '#################';
 		$code = input('get.code');
 		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$APPID.'&secret='.$AppSecret.'&js_code='.$code.'&grant_type=authorization_code';
 		$arr = $this -> vegt($url);
 
 		$arr = json_decode($arr,true);
 		// $openid = $arr['openid'];
 		$session_key = $arr['session_key'];
 
 		// 数字签名校验
 		$signature = input('get.signature');
 		$signature2 = sha1($_GET['rawData'].$session_key);
 		if($signature != $signature2){
 			echo "数字签名失败";
 			die;
 		}
 		// 获取信息，对接口进行解密
 		Vendor("PHP.wxBizDataCrypt");
 		$encryptedData = $_GET['encryptedData'];
 		$iv = $_GET['iv'];
 		if(empty($signature) || empty($encryptedData) || empty($iv)){
 			echo "传递信息不全";
 		}
 		include_once "PHP/wxBizDataCrypt.php";
 		$pc = new \WXBizDataCrypt($APPID,$session_key);
 		$errCode = $pc->decryptData($encryptedData,$iv,$data);
 		if($errCode != 0){
 			echo "解密数据失败";
 			die;
 		}else {
 			$data = json_decode($data,true);
 			session('myinfo',$data);
 			$save['openid'] = $data['openId'];
 			$save['uname'] = $data['nickName'];
 			$save['unex'] = $data['gender'];
 			$save['address'] = $data['city'];
 			$save['time'] = time();
 			$map['openid'] = $data['openId'];
 			!empty($data['unionId']) && $save['unionId'] = $data['unionId'];
 
 			$res = \think\Db::name('user') -> where($map) -> find();
 			if(!$res){
	 			$db = \think\Db::name('user') -> insert($save); 
	 			if($db !== false){
	 				echo "保存用户成功";
	 			}else{
	 				echo "error";
	 			}
 			}else{
 				echo "用户已经存在";
 			}
 		}
		//生成第三方3rd_session
		$session3rd  = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;
		for($i=0;$i<16;$i++){
		    $session3rd .=$strPol[rand(0,$max)];
		}
		// echo $session3rd;
 	}
 	public function vegt($url){
 		$info = curl_init();
		curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
	    curl_setopt($info,CURLOPT_HEADER,0);
	    curl_setopt($info,CURLOPT_NOBODY,0);
	    curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($info,CURLOPT_URL,$url);
	    $output= curl_exec($info);
	    curl_close($info);
	    return $output;
 	}

   
}
