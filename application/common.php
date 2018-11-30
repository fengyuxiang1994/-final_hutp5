<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

error_reporting(E_ERROR | E_PARSE );
/**
 * [status description]
 * @param  [type] $status [状态]
 * @return [type]         [正常，待审，不通过，删除]   
 */
function status($status){
	if ($status == 1) {
		$str="<span class='label label-success radius'>正常</span>";
	}elseif ($status == 0) {
		$str="<span class='label label-dangre radius' style='background-color:#EE6363;'>待审</span>";
	}elseif ($status == 2) {
		$str="<span class='label label-dangre radius' style='background-color:#131415;'>不通过</span>";
	}else{
		$str="<span class='label label-dangre radius'>删除</span>";
	}
	return $str;
}

/**
 * [sex description]
 * @param  [type] $sex [性别]
 * @return [type]       [男，女，无]
 */
function sex($sex){
	if ($sex == 1) {
		$sex="<span class='label label-success radius'>男</span>";
	}elseif ($sex == 2) {
		$sex="<span class='label label-success radius'>女</span>";
	}else{
		$sex="<span class='label label-success radius'>无</span>";
	}
	return $sex;
}

/**
 * 微信小程序
 * [xcxCUrl description]
 * @param  [type] $url [description]
 * @return [type]      [$output]
 */ 
function xcxCUrl($url){
 		$info = curl_init();
		curl_setopt($info,CURLOPT_RETURNTRANSFER,true);//如果成功只返回结果不输出内容 //执行结果是否被返回，0是返回，1是不返回 //获取页面内容，但不输出
	    curl_setopt($info,CURLOPT_HEADER,0);//0参数表示不输出header的头 //参数设置，是否显示头部信息，1为显示，0为不显示
	    curl_setopt($info,CURLOPT_NOBODY,0); //如果你不想在输出中包含body部分，设置这个选项为一个非零值。
	    curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);//不做服务器认证
	    curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);//不做客户端认证
	    curl_setopt($info,CURLOPT_URL,$url); // 设置你需要抓取的URL
	    $output= curl_exec($info); // 运行cURL，请求网页
	    curl_close($info); // 关闭URL请求
	    return $output;
} 

/**
 * [authGroup 角色是否启用]
 * @param  [type] $status [status]
 * @return [type]         [是，否]
 */
function authGroup($status){
	if ($status == 1) {
		$str="<span class='label label-success radius'>是</span>";
	}else{
		$str="<span class='label label-dangre radius' style='background-color:#EE6363;'>否</span>";
	}
	return $str;
}



 /**
  * [doCurl 获取百度地图]
  * @param  [type]  $url  [description]
  * @param  integer $type [description]
  * @param  array   $data [description]
  * @return [type]        [output]
  */
 function doCurl($url, $type=0, $data=[]){
 	$ch = curl_init();//初始化
 	// 设置选项
 	curl_setopt($ch, CURLOPT_URL, $url);//
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//如果成功只返回结果不输出内容
 	curl_setopt($ch, CURLOPT_HEADER, 0);//0参数表示不输出header的头
 	if ($type == 1) {
 		curl_setopt($ch, CURLOPT_POST, 1);
 		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

 	}
 	//执行并获取
 	$output = curl_exec($ch);
 	curl_close($ch);
 	return $output;
 }

/**
 * [bisRegister 商户入驻申请文案]
 * @param  [type] $status [status]
 * @return [type]         [str]
 */
 function bisRegister($status){
 	if ($status == 1) {
 		$str = "入驻申请成功";
 	}elseif ($status == 0) {
 		$str = "待审核，审核后平台会发送邮件通知，请关注邮件";
 	}elseif ($status == 2) {
 		$str = "非常抱歉，您提交的材料不符合该平台的条件，请重新提交";
 	}else{

 		$str = "该入驻申请已删除";
 	}
 	return $str;
 }

/**
 * [paginate 分页]
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
function paginate($obj){
	if (!$obj) {
		return '';
	}
	//request()->param()获取URL后面的参数方法
	$params = request()->param();
	return '<div class="cl pd-5 bg-1 bk-gray mt-20"> 
		       <span class="ren">'.$obj->appends($params)->render().'</span>
	        <div>';

}

/**
 * [getSeCityName 城市]
 * @param  [type] $path [path]
 * @return [type]       [$city->name]
 */
function getSeCityName($path){
	if (empty($path)) {
		return '';
	}

	if (preg_match('/,/', $path)) {
		$cityPath = explode(',', $path);
		$cityId = $cityPath[1];
	}else {
		$cityId = $path;
	}

	$city = model('City')->get($cityId);
    return $city->name;
}


function countLocation($ids){
	if (!$ids) {
		return 1;
	}
	if (preg_match('/,/', $ids)) {
		$arr = explode(',', $ids);
		return count($arr);
	}

}

/**
 * 设置订单号
 *microtime()方法PHP内置时间函数;
 */
function setorderSn(){
	list($t1, $t2) = explode(' ', microtime());
	// dump(microtime());
	// echo $t1."<br>".$t2;
	$t3 = explode('.', $t1*10000);
	// dump($t3);
	return $t2.$t3[0].(rand(10000, 99999));
}

/**
 * 图片操作
 */
/**
 * [getImageInfo 返回图片信息]
 * @param  [type] $filename [文件名称]
 * @return [type]           [包含图片的宽度，高度，创建和输出的字符串以及扩展名]
 */
function getImageInfo($filename){
	// if (@!$info = getimagesize($filename)) {
	// 	dump(getimagesize($filename));
	// 	exit('上传的不是图片');
	// }
    
 //    $fileInfo['width'] = $info[0];
 //    $fileInfo['height'] = $info[1];

 //    $mime = image_type_to_mime_type($info[2]);
 //    $createFun = str_repeat('/', 'createfrom', $mime);
 //    $outFun = str_repeat('/', '', $mime);
 //    $fileInfo['createFun'] = $createFun;
 //    $fileINfo['ext'] = strtolower(image_type_to_extension($info[2]));
    return $filename;

}

/**
 * [thumb 缩略图]
 * @param  [type]  $filename  [文件名]
 * @param  string  $dest      [缩略图的保存路径，默认保存在thumb的目录下]
 * @param  string  $pre       [默认前缀为thumb_]
 * @param  [type]  $dst_w     [最大宽度]
 * @param  [type]  $dst_h     [最大高度]
 * @param  float   $scale     [默认缩放比例]
 * @param  boolean $delSource [是否删除文件的标志]
 * @return [type]             [返回保存路径及文件名称]
 */
function thumb($filename, $dest = 'thumb', $pre = 'thumb_', $dst_w = null, $dst_h = null, $scale = 0.5, $delSource = false){
	$fileInfo = getImageInfo($filename);
	$src_w = $fileInfo['width'];
	$src_h = $fileInfo['height'];

	if (is_numeric($dst_w) && is_numeric($dst_h)) {
		$ratio_orig = $src_w / $src_h;
		if ($dst_w / $dst_h > $ratio_orig) {
			$dst_w = $dst_h * $ratio_orig;
		}else{
			 $dst_h = $dst_w * $ratio_orig;
		}
	}else{
		$dst_w = ceil($src_w * $ratio_orig);
		$dst_h = ceil($src_h * $ratio_orig);
	}

	$dst_image = imagecreatetruecolor($dst_w, $dst_h);
	$src_image = $fileInfo['createFun']($filename);
	imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	if ($dest && !file_exists($dest)) {
		mkdir($dest, 0777, true);
	}
	$randNum = mt_rand(100000,999999);
	$dstName = "{$pre}{RandNUm}".$fileInfo['ext'];
	$destination = $dest ? $dest. '/' . $dstName : $dstName;
	$fileInfo['outFun']($dst_image, $destination);
	imagedestroy($src_image);
	imagedestroy($dst_image);
	if ($delSource) {
		@unlink($filename);
	}

	return $destination;
}

/**
 * [water_pic 图片水印]
 * @param  [type]  $dstName   [description]
 * @param  [type]  $srcName   [description]
 * @param  integer $pos       [description]
 * @param  string  $dest      [description]
 * @param  string  $pre       [description]
 * @param  integer $pct       [description]
 * @param  boolean $delsource [description]
 * @return [type]             [description]
 */
function water_pic($dstName, $srcName, $pos=0, $dest = 'waterPic', $pre = 'waterPic', $pct =50, $delsource = false){
	$dstInfo = getImageInfo($dstName);
	$srcInfo = getImageInfo($srcName);
	$dst_im = $dstInfo['createFun']($dstName);
	$dst_im = $srcInfo['createFun']($srcName);
	$src_width = $dstInfo['width'];
	$src_height = $dstInfo['height'];
	$dst_width = $srcInfo['width'];
	$dst_height = $srcInfo['height'];
    switch ($pos) {
    	case 0:
    		$x = 0;
    		$y = 0;
    		break;
    	case 1:
    		$x = ($dst_width - $src_width)/2;
    		$y = 0;
    		break;
    	case 2:
    		$x = $dst_width - $src_width;
    		$y = 0;
    		break;
    	case 3:
    		$x = ($dst_height - $src_height)/2;
    		$y = 0;
    		break;
    	case 4:
    		$x = ($dst_width - $src_width)/2;
    		$y = ($dst_height - $src_height)/2;
    		break;
    	case 5:
    		$x = $dst_width - $src_width;
    		$y = ($dst_height - $src_height)/2;
    		break;
    	case 6:
    		$x = 0;
    		$y = $dst_height - $src_height;
    		break;
    	case 7:
    		$x = ($dst_width - $src_width)/2;
    		$y = $dst_height - $src_height;
    		break;
    	case 8:
    		$x = $dst_width - $src_width;
    		$y = $dst_height - $src_height;
    		break;
    	default:
    		$x = 0;
    		$y = 0;
    		break;
    }

    imagecopymerge($dst_im, $src_im, $x, $y, 0, 0, $src_width, $src_height, $pct);
    if ($dest && !file_exists($dest)) {
    	mkdir($dest, 0777, true);
    }

    $randNum = mt_rand(10000, 99999);
    $dstName = "{$pre}_{$randNum}".$dstInfo['exit'];
    $destination = $dest ? $dest . '/' . $dstName : $dstName;
    $dstInfo['outFun']($dst_im, $destination);
    imagedestroy($src_im);
    imagedestroy($dst_im);
    if ($delSource) {
    	@unlink($filename);
    }

    return $destination;
}

/**
 * 把对象转换成数组
 * @param  [type] $obj [description]
 * @return [type]      [description]
 */
function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }
 
    return $obj;
}