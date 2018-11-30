<?php
namespace app\admin\controller;
use think\Controller;

class Index extends  BasController
{
    public function index()
    {
        
        // dump(session('adminName', '', 'admin'));
      // echo session('id', '', 'adminid');
       // echo "hh";
       // 
       
    	return $this->fetch();
        // echo "hhhh";
    }

    public function welcome()
    {

       
        //  print(session('sessionKey', '', 'xcxshai'));
        // dump(session('openid', '', 'xcxshai'));
        // $code = '033TljEn0oHzds1oPtEn0ttoEn0TljE0';
        // \Xcx::gerSou($code);
        
    	// \Map::getLngLat('北京昌平沙河地铁');
     //    $message = '东还海道hhhh';
     //    $title = '胡永春完成 结束邮箱';
     //    $data['email'] = '2543745353@qq.com';
     //   // \phpmailer\Email::send($data['email'],$title,$message);
     //    // \phpmailer\Emailer::send('2543745353@qq.com','胡永春邮箱', '好用');
     //    // return '发送邮件成功';
     //     trace('hello world');
    	  return $this->fetch();
    }

    public function map()
    {   
        return \Map::staticimage('北京昌平沙河地铁');
    }

    public function csh()
    {

    }
   
}
