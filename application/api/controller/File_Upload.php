<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 0:00
 */

namespace app\api\controller;


class File_Upload
{
 public static function resoult($type,$path,$size,$name){		//上传文件允许的类型，要上传到的目录，允许上传的文件大小(以M为单位),form表单的name
     if (!file_exists($path)) {			//判断要上传到的目录存不存在，不存在则创建
         mkdir($path);
     }
     if (is_uploaded_file($_FILES[$name]['tmp_name'])) {		//判断是否上传了文件，是不是上传的文件，$_FILES[form表单控件名][要判断的类型(缓存文件)]
         $class=explode('.',$_FILES[$name]['name']);			//分割出控件原始上传文件后缀名，此时可能存在文件名其他位置有. 所以还需要再次判断
         if ($_FILES[$name]['size']<=$size*1024*1024) {		//判断文件的大小是否符合要求
             if (in_array($class[count($class)-1],$type)) { 		//$class[count($class)-1]将上面初步分割的文件名取出来，提取后缀名，并判断上传文件类型是否符合要求
                 $newName=md5(microtime());					//符合要求的上传文件进行用时间重命名（当前毫秒数）并再次加密，防止用户上传文件出现重名
                 if (!file_exists($path.'/'.$class[count($class)-1].'/')) {		//判断上传的文件类型分类文件夹是否存在，对用户上传文件进行分类保存，
                     mkdir($path.'/'.$class[count($class)-1].'/');				//不存在则创建分类文件夹
                     move_uploaded_file($_FILES[$name]['tmp_name'],$path.'/'.$class[count($class)-1].'/'.$newName.'.'.$class[count($class)-1]);		//创建目录后将用户上传文件存入相应的分类文件夹
                     echo '文件上传成功！目录：'.$path.'/'.$class[count($class)-1].'/'.$newName.'.'.$class[count($class)-1];
                     return;						//未创建分类文件夹的文件上传到此结束
                 }else{		//上传文件相应的分类文件夹已存在，直接进入文件保存
                     move_uploaded_file($_FILES[$name]['tmp_name'],$path.'/'.$class[count($class)-1].'/'.$newName.'.'.$class[count($class)-1]);
                     echo '文件上传成功！目录：'.$path.'/'.$class[count($class)-1].'/'.$newName.'.'.$class[count($class)-1];
                     return;			//文件上传到此结束
                 }
             }
         }
     }
     echo '上传失败：文件大小或类型不支持';		//不符合要求的上传文件在此向用户做出回应
 }
}