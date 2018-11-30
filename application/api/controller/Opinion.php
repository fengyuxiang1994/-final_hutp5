<?php
namespace app\api\controller;

use think\Controller;
use think\Request;



class Opinion extends Controller
{
    public function getOpinion()
    {
        // $files=request()->file('file');
        // dump($files);
        //   return show(1, 'success', $files);
        // if (empty($_FILES['file'])) {
           
        //  }
         $files = $_FILES['file'];
         // dump($files);
         // return show(1, 'success', $files);  
        //  $user =input('post.user');
        // dump($files);
        // is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的。
        if(is_uploaded_file($files['tmp_name'])) {      
            //把文件转存到你希望的目录（不要使用copy函数）      
            $uploaded_file=$files['tmp_name'];      
            $username = "min_img";    //我们给每个用户动态的创建一个文件夹      
            $user_path=$_SERVER['DOCUMENT_ROOT']."/mmm_mmm/".$username;   
               //判断该用户文件夹是否已经有这个文件夹  file_exists() 函数检查文件或目录是否存在。如果指定的文件或目录存在则返回 true，否则返回 false。    
            if(!file_exists($user_path)) {          
                mkdir($user_path, 0777, true);      
            }     
            $file_true_name=$files['name'];    
            $move_to_file=$user_path."/".time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));
            // strrops($file_true,".")//查找“.”在字符串中最后一次出现的位置   
          
            // // echo $str= '你好,这里是卖咖啡!';
            // echo '<br />';
            // echo iconv('GB2312', 'UTF-8', $str);      //将字符串的编码从GB2312转到UTF-8
            // echo '<br />';
            // echo iconv_substr($str, 1, 1, 'UTF-8');   //按字符个数截取而非字节         
            // print_r(iconv_get_encoding());            //得到当前页面编码信息 
            // echo iconv_strlen($str, 'UTF-8');         //得到设定编码的字符串长度
            // $content = iconv("UTF-8","gbk//TRANSLIT",$content); //也有这样用的
            //move_uploaded_file 函数将上传的文件移动到新位置。   
             $imgName = time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,".")); 

            if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {       
                   
                  // $a=array();
                  // array_push($a,$imgName );
                  // print_r($a);
                   return show(1, 'success',  $imgName);  
            } else {      
                   return 0;  
            }  
        } else {
                   // return show(2, 'error', "上传失败");  
                  return 0;  
        }
    }


    public function imageupload()
  {
      //名字获取
      $wxid=$_POST["num"];
      //时间获取
      $diaryTime=$_POST["datetime"];
      //获取日期
      $date = $_POST["date"];
      $a = substr($_FILES['file']['type'],6);//获取图片后缀
      //$a =  strstr( $_FILES['file']['type'], '/');
      $file_name=$date.'\\'.$diaryTime.'_'.$wxid.'.'.$a;//拼装存储地址path
      $file_name1=$date.'/'.$diaryTime.'_'.$wxid.'.'.$a;//拼装图片浏览path
      // $path = $_SERVER['DOCUMENT_ROOT']."/public/hh/".$file_name;//存储path
      // $dir = iconv("UTF-8", "GBK", $_SERVER['DOCUMENT_ROOT']."/public/hh/".$date);//判断文件夹是否存在
      // if (!file_exists($dir)){
      //     mkdir ($dir,0777,true);//不存在 创建新文件夹
      //     $panduan = move_uploaded_file($_FILES['file']['tmp_name'], $path);//存入图片
      // } else {
      //     $panduan = move_uploaded_file($_FILES['file']['tmp_name'], $path);//存入已有文件夹内
      // }
      // //保存到指定路径  指定名字
      // if ($panduan){//存储成功
      //     $res = ['errCode'=>0,'errMsg'=>'图片上传成功','file'=>$file_name1,'Success'=>true];
      //     return json($res);
      // }else{//失败
      //     $res = ['errCode'=>0,'errMsg'=>'图片上传失败','file'=>'https://127.0.0.1:80/xxxx.png','Success'=>!true];
      //     return json($res);
      // }
      return $file_name1;
  }

  public function getImg()
    { 
           $files_arr = [];
        // $a = [];
        // array_push($a,$this->getImg().',');
         $files_arr =$this->getImg();
    
       $filenameurl = join(',', $files_arr);
       print_r($filenameurl);

        //  $file = request()->file('file');
        //  dump($file);
        // if ($file) {
        //     $info = $file->move('public/upload/weixin/');
        //     if ($info) {
        //         $file = $info->getSaveName();
        //         $res = ['errCode'=>0,'errMsg'=>'图片上传成功','file'=>$file];
        //         // dump($file);
        //         return  $res;
        //     }
        // }
        
        if ($_FILES["file"]["error"] > 0){
          echo "Error: " . $_FILES["file"]["error"] . "<br>";
        }else{
          //获取上传的文件名称
          // echo $_FILES["file"]["name"] . "<br>";
          // //获取上传的文件类型
          // echo $_FILES["file"]["type"] . "<br>";
          // echo $_FILES["file"]["tmp_name"] . "<br>";
          // //获取上传的文件大小
          // echo ($_FILES["file"]["size"] / 1024) . " Kb";

               if(is_uploaded_file($_FILES['file']['tmp_name'])) {       
                $uploaded_file=$_FILES['file']['tmp_name'];      
                $username = "uploadsadd";    //我们给每个用户动态的创建一个文件夹      
                $user_path=$_SERVER['DOCUMENT_ROOT']."/public/".$username;   
                if(!file_exists($user_path)) {          
                    mkdir($user_path, 0777, true);      
                }     
                $file_true_name=$_FILES['file']['name'];    
                $move_to_file=$user_path."/".time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));
                $imgName = time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,".")); 
                
                if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {  
                         
                        return  $imgName;  
                } else {      
                        return 2;      
                }  
            } else { 
                       return 2;
            } 
        }


  


       // $files = $_FILES['file'];
        // if(is_uploaded_file($_FILES['file']['tmp_name'])) {       
        //     $uploaded_file=$_FILES['file']['tmp_name'];      
        //     $username = "uploadsadd";    //我们给每个用户动态的创建一个文件夹      
        //     $user_path=$_SERVER['DOCUMENT_ROOT']."/public/".$username;   
        //     if(!file_exists($user_path)) {          
        //         mkdir($user_path, 0777, true);      
        //     }     
        //     $file_true_name=$_FILES['file']['name'];    
        //     $move_to_file=$user_path."/".time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));
        //     $imgName = time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,".")); 
            
        //     if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {  
                     
        //             return  $imgName;  
        //     } else {      
        //             return 2;      
        //     }  
        // } else { 
        //            return 2;
        // } 
        // 
        // 
        // 
        // //图片上传
// $files_arr = [];
// foreach($_FILES['pictures']['error'] as $key => $error) {
//     if($error == UPLOAD_ERR_OK) {
//         $tmp_name = $_FILES['pictures']['tmp_name'][$key];
//         $name = date('Ymd').rand(1000, 9999).$_FILES['pictures']['name'][$key];
//         $dir = 'upload/';
//         $filenameurl = $dir.$name;
//         $files_arr[] = $filenameurl;
//         move_uploaded_file($tmp_name, $filenameurl);
//     }
// }
$filenameurl = join(',', $files_arr);
    }






   
    // public function getImg()
    // {
    //      $files = $_FILES['file'];
    //      $id=input('post.id');

    //     // is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的。
    //     if(is_uploaded_file($files['tmp_name'])) {      
    //         //把文件转存到你希望的目录（不要使用copy函数）      
    //         $uploaded_file=$files['tmp_name'];      
    //         $username = "uploadsadd";    //我们给每个用户动态的创建一个文件夹      
    //         $user_path=$_SERVER['DOCUMENT_ROOT']."/public/".$username;   
    //            //判断该用户文件夹是否已经有这个文件夹  file_exists() 函数检查文件或目录是否存在。如果指定的文件或目录存在则返回 true，否则返回 false。    
    //         if(!file_exists($user_path)) {          
    //             mkdir($user_path, 0777, true);      
    //         }     
    //         $file_true_name=$files['name'];    
    //         $move_to_file=$user_path."/".time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,"."));
    //         // strrops($file_true,".")//查找“.”在字符串中最后一次出现的位置   
    //         // echo iconv('GB2312', 'UTF-8', $str);      //将字符串的编码从GB2312转到UTF-8
    //         //move_uploaded_file 函数将上传的文件移动到新位置。   
    //         $imgName = time().rand(1,1000)."-".date("Y-m-d").substr($file_true_name,strrpos($file_true_name,".")); 
    //          // $a = [];
    //          // array_push($a,$imgName);
    //          // print_r($a);
    //         if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {  
    //                    $imgData = [
    //                        'imgid' =>  $id,
    //                        'imgname' => $imgName,
            
    //                    ];   
    //                $imgda =  model('XcxImg')->add($imgData);
    //                 return show(1, 'success',  $imgda);  
    //         } else {      
    //                return show(2, 'error', "上传失败");      
    //         }  
    //     } else { 
    //               return show(2, 'error', "上传失败");  
    //     }  

    // }


    public function userFeedbackCreate(){   // 用户反馈添加接口
        $user_id = input('user_id');
        $content = input('textarea');
        if($user_id==null||$content==null){
            return error('请求错误','','参数不完整');
        }
        $feedback = model('XcxUserFeedback');
        $feedback->data([
            'user_id' => $user_id,
            'content_text' => $content,
        ]);
        $feedback->save();
        return $feedback;
    }

    public function userFeedbackImgCreate(){
        $file = $_FILES['file'];
        $feedback_id = input('feedback_id');
        $target_path = $_SERVER['DOCUMENT_ROOT'].'/public/images/feedback/';
        if(!is_dir($target_path)){
            mkdir($target_path,755,true);
        }else{
            if (is_uploaded_file($file['tmp_name'])){
                $file_extens = explode('.',$file['name']);
                $file_extens = $file_extens[count($file_extens)-1];
                $file_name = sha1(microtime().rand(1,99999)).'.'.$file_extens;
                move_uploaded_file($file['tmp_name'],$target_path.$file_name);
                $feedback_data = [
                  'image_url' => 'https://www.daotuba.cn'.'/public/images/feedback/'.$file_name,
                  'feedback_id' => $feedback_id
                ];
                $feedback_img = model('XcxUserFeedbackImg');
                $feedback_img->data($feedback_data);
                $feedback_img->save();
                // TODO: 用户反馈页的图片上传
                return $feedback_img;
            }
        }
        return error(403,'未选择文件',[]);
    }


}
