<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class Add extends Controller
{
    public function getAddAip()
    {

        $data = input('get.');
        $user_exits = json_decode($data['user_info']);
        $topic_exits = json_decode($data['topic_info']);
        $GLOBALS['user_exits'] = array_map(function ($v){return $v->user_name;},$user_exits);
        $GLOBALS['topic_exits'] = array_map(function ($v){return $v->topic_title;},$topic_exits);
        $description = preg_replace_callback('/#[^#@\s]*[#]{1}[\s]{1}/',function ($v){
            if(in_array(substr(trim($v[0]),1,strlen(trim($v[0]))-2),$GLOBALS['topic_exits'])){
                return '';
            }else{
                return $v[0];
            }
        },$data['description']);
        $description = preg_replace_callback('/@[^#@\s]*[\s]{1}/',function ($v){
            if(in_array(substr(trim($v[0]),1),$GLOBALS['user_exits'])){
                return '';
            }else{
                return $v[0];
            }
        },$description);
        $description = trim($description);

        $addData = [
            'home_uaer_name' => $data['home_uaer_name'],
            'r_image' => $data['r_image'],
            'image' => $data['image'],
            'description' => $description,
            'user_id' => $data['id'],
            'category_id' => $data['categoryID'],
            'city' => mb_substr($data['address'], 3, 3, 'utf-8'),
            'address' => $data['address'],
            'addressname' => $data['addressname'],
            'xpoint' => $data['latitude'],
            'topic_id'=>json_decode($data['topic_info'])[0]->topic_id,
            'ypoint' => $data['longitude'],
        ];
        $addid = model('XcxAdd');
        $addid->data($addData);
        $addid->save();
        $user_info = json_decode($data['user_info']);
        foreach ($user_info as $k => $v){
            $datas =[
                'user_id'=>$data['id'],
                'to_user_id'=>$v->user_id,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
                'add_id' => $addid['id']
            ];
            model('XcxUserAt')->data($datas)->save();
        }

        $userguanzhu = model('XcxUserguanzhu')
            ->where('user_id',$data['id'])
            ->select();
        if (!empty($userguanzhu)){
            foreach ($userguanzhu as $key => $value){
                $userid = $value['form_user_id'];

                $data = [
                    'user_id' => $userid,
                    'add_id' => $addid['id'],
                    'create_date' => date('Y-m-d H:i:s',time()),
                ];
                model('XcxAddnotice')->insert($data);
            }
        }

//        $topic_info = json_decode($data['topic_info']);
//        foreach ($topic_info as $key => $val){
//            var_dump($val);
//        }
        if ($addid) {
            return show(1, 'success', $addid);
        }
    }

    //存草稿
    public function getAddCaogao()
    {
        $data = input('get.');
        $user_exits = json_decode($data['user_info']);
        $topic_exits = json_decode($data['topic_info']);
        $GLOBALS['user_exits'] = array_map(function ($v){return $v->user_name;},$user_exits);
        $GLOBALS['topic_exits'] = array_map(function ($v){return $v->topic_title;},$topic_exits);
        $description = preg_replace_callback('/#[^#@\s]*[#]{1}[\s]{1}/',function ($v){
            if(in_array(substr(trim($v[0]),1,strlen(trim($v[0]))-2),$GLOBALS['topic_exits'])){
                return '';
            }else{
                return $v[0];
            }
        },$data['description']);
        $description = preg_replace_callback('/@[^#@\s]*[\s]{1}/',function ($v){
            if(in_array(substr(trim($v[0]),1),$GLOBALS['user_exits'])){
                return '';
            }else{
                return $v[0];
            }
        },$description);
        $description = $data['description'];

        $addData = [
            'home_uaer_name' => $data['home_uaer_name'],
            'r_image' => $data['r_image'],
            'image' => $data['image'],
            'description' => $description,
            'user_id' => $data['id'],
            'category_id' => $data['categoryID'],
            'city' => mb_substr($data['address'], 3, 3, 'utf-8'),
            'address' => $data['address'],
            'addressname' => $data['addressname'],
            'xpoint' => $data['latitude'],
            'topic'=>json_decode($data['topic_info'])[0]->topic_id,
            'ypoint' => $data['longitude'],
        ];
        $addid = model('XcxCaogao');
        $addid->data($addData);
        $addid->save();
        $user_info = json_decode($data['user_info']);
        foreach ($user_info as $k => $v){
            $datas =[
                'user_id'=>$data['id'],
                'to_user_id'=>$v->user_id,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
                'add_id' => 'caogao'.$addid['id']
            ];
            model('XcxUserAt')->data($datas)->save();
        }
//        $topic_info = json_decode($data['topic_info']);
//        foreach ($topic_info as $key => $val){
//            var_dump($val);
//        }
        if ($addid) {
            return show(1, 'success', $addid);
        }

//        $data = input('get.');
//        $addData = [
//            'home_uaer_name' => $data['home_uaer_name'],
//            'r_image' => $data['r_image'],
//            'image' => $data['image'],
//            'description' => $data['description'],
//            'user_id' => $data['id'],
//            'category_id' => $data['categoryID'],
//            'city' => mb_substr($data['address'], 3, 3, 'utf-8'),
//            'address' => $data['address'],
//            'addressname' => $data['addressname'],
//            'xpoint' => $data['latitude'],
//            'ypoint' => $data['longitude'],
//        ];
//        $user_info = $data['user_info'];
//        foreach ($user_info as $k => $v){
//            var_dump($v);
//        }
//        $topic_info = $data['topic_info'];
//        $addid = model('XcxCaogao');
//        $addid->data($addData);
//        $addid->save();
//        if ($addid) {
//            return show(1, 'success', $addid);
//        }
    }


    public function getOpinion()
    {

//        $imgData = [
//            'imgid' => input('imgid'),
//            'name' => input('name'),
//        ];
//        $imgda = model('XcxImgcaogao')->insert($imgData);
//        return $imgda;
        $files = $_FILES['file'];
        $id = input('post.id');
        $status = input('types');

        // is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的。
        if (is_uploaded_file($files['tmp_name'])) {
            //把文件转存到你希望的目录（不要使用copy函数）
            $uploaded_file = $files['tmp_name'];
            if ($status == null) {
                $username = "min_img";    //我们给每个用户动态的创建一个文件夹

            } else {
                $username = "min_img_Caogao";    //我们给每个用户动态的创建一个文件夹

            }
            $user_path = "./public/mmm_mmm/" . $username;
            //判断该用户文件夹是否已经有这个文件夹  file_exists() 函数检查文件或目录是否存在。如果指定的文件或目录存在则返回 true，否则返回 false。
            if (!file_exists($user_path)) {
                mkdir($user_path, 0777, true);
            }
            $file_true_name = $files['name'];
            $hhtime = time() . rand(1, 1000) . "-" . date("Y-m-d") . substr($file_true_name, strrpos($file_true_name, "."));
            $move_to_file = $user_path . "/" . $hhtime;
            $imgName = 'http://' . $_SERVER['HTTP_HOST'] . "/public/mmm_mmm/" . $username . "/" . $hhtime;
            if (move_uploaded_file($uploaded_file, iconv("utf-8", "gb2312", $move_to_file))) {
                $imgData = [
                    'imgid' => $id,
                    'name' => $imgName,
                ];

                if ($status == null) {
                    $imgda = model('XcxImg');

                } else {

                    $imgda = model('XcxImgcaogao');

                }
                $imgda->data($imgData);
                $imgda->save();

                // return show(1, 'success',  $imgda);
                // return show(1, 'success',  $imgName);
                // $res = ['errCode'=>0,'errMsg'=>'图片上传成功','file'=>$imgda,'Success'=>true];
                return show(1, 'success', $imgda);
            } else {
                return show(2, 'error', "上传失败");
            }
        } else {
            return show(2, 'error', "上传失败");

        }
    }

    //获取小程序的主页内容
    public function getPageContentApi()
    {
        $lastid = input('get.lastid', 0, 'intval');
        $limit = input('get.limit', 4, 'intval');
//        $homeData = model('XcxAdd')->getFirst($limit,  $lastid);
        $homeData = model('XcxAdd')->limit($lastid, $limit)->select();
        foreach ($homeData as $k => $v) {
//            $v['r_image'] = $v['r_image'];
//            $id = $v['id'];
//            $v['hasChange'] ='false';
//            $imgd= model("XcxImg")->seleIndex($id);
            $image = model('XcxImg')->field('name')->where('imgid', $v['id'])->select();
            $v['image'] = $image[0]['name'];
//            $uu = [];
//            foreach ($imgd as &$vo) {
//                array_push($uu, 'http://192.168.100.224'.$vo['name']);
//            }
//            $v['image'] = $uu;
        }
        // dump($homeData);
        shuffle($homeData);

        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

    public function getFindApi()
    {
        $id = input('get.id', 0, 'intval');
        $xqData = model('XcxAdd')->getXXXcx($id);
        // foreach ($xqData as $k => $v) {
        $xqData['r_image'] = $xqData['r_image'];
        $xqData['hasChange'] = 'false';
        $imgd = model("XcxImg")->seleIndex($id);
        $uu = [];
        foreach ($imgd as &$vo) {
            array_push($uu, 'http://www.xcx.com' . $vo['name']);
        }
        $xqData['image'] = $uu;

        if (!$xqData) {
            return show(0, 'error');
        }
        return show(1, 'success', $xqData);
    }

    public function addTopic(){
        $topic_title = input('topic_title');
        $topic_content = input('topic_content');
        $add_data = [
            'topic_title' => $topic_title,
            'topic_content' => $topic_content
        ];
        $add = model('XcxTopic');
        $add->data($add_data);
        $add->save();
        return $add;
    }



    public function userinfo() {
        $arr ='';
        $user_id = input('user_id');
        $user = model('XcxUserguanzhu')->field('user_id')->where('form_user_id',$user_id)->select();
        foreach ($user as $key =>$value){
            $arr .= $value['user_id'] .',';
        }
        $arr .= $user_id;
        $info = model('XcxUser')->where('id','not in',$arr)->select();
        return $info;
    }
}
