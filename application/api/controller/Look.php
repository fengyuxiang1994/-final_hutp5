<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/10
 * Time: 11:20
 */

namespace app\api\controller;

use http\Env\Request;
use think\Controller;
use app\common\model\XcxUser as User;
use app\common\model\XcxComment as Comment;
use app\common\model\XcxAdd as Article;
use app\common\model\XcxImg as Image;

class Look extends Controller
{
    public function pingtaiSetting(){
        $data = model('XcxPtSetting')->find();
      return $this->success('success', '', $data);
    }
    //接口我的晒文章
    public function looking()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxAdd')->where('user_id', $user_id)->select();

        foreach ($data as $k => $v) {
            $arr = [];

            $image = model('XcxImg')->where('imgid', $v['id'])->select();

            foreach ($image as $key => $value) {
                $arr[] .=  $value['name'];
            }
            $v['image'] = $arr;
        }
//        $image_id = $data.iamge;
        return toJson('1000', 'Success', count($data), $data);
    }
    public function shouCangInfo()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxShoucang')->where('user_id', $user_id)->select();
        $arr = [];

        foreach ($data as $k => $v) {
            $info =model('XcxAdd')->where('id',$v['comment_id'])->find();
            $image = model('XcxImg')->where('imgid', $info['id'])->find();

            $info['image'] = $image['name'];
            $arr[] =$info;
        }
//        $image_id = $data.iamge;
        return toJson('1000', 'Success', count($arr), $arr);
    }
    //我的收藏
    public function shouCang()
    {
        $user_id = input('user_id');
//        $issue_id = input('issue_id');
        if (!$user_id) {
            return error('fail', '请查看参数', '');
        }
        $ress = [];
        $res = [];
        $data = model('XcxShoucang')->where('user_id', $user_id)->select();

        foreach ($data as $v => $k) {
            $user = model('XcxUser')->where('id', $user_id)->find();

            $res['user']['nickName'] = $user['nickName'];
            $res['user']['avatarUrl'] = $user['avatarUrl'];

            $resu = model('XcxAdd')->where('id', $k['comment_id'])->find();
            $image = model('XcxImg')->field('name')->where('imgid', $resu['id'])->find();

            $arr = $image['name'];
            $res['image'] = $arr;
            $ress[] = $res;
        }
        return $ress;


    }

    //点击收藏点击取消
    public function bintTap()
    {
        $user_id = input('user_id');
        $issue_id = input('comment_id');
        $sc_count = input('sc_count');

        $create_date = date("Y-m-d H:i:s", time());
        if (!$user_id || !$issue_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxShoucang')->where('user_id', $user_id)->where('comment_id', $issue_id)->find();

        if ($data) {
            $res = model('XcxShoucang')->where('user_id', $user_id)->where('comment_id', $issue_id)->delete();
            if ($res) {
                model('XcxAdd')->where('id',$issue_id)->update(['user_collection'=>$sc_count]);
                return toJson('1000', '取消收藏成功', '', '');
            }
        }
        $res = model('XcxShoucang')->insert(['user_id' => $user_id, 'comment_id' => $issue_id, 'create_date' => $create_date]);
        if ($res) {
            model('XcxAdd')->where('id',$issue_id)->update(['user_collection'=>$sc_count]);
            return toJson('1000', '收藏成功', '', '');

        }
    }

    public function getInfo()
    {
        $user_id = input('user_id');
        $data = model('XcxUser')->where('id', $user_id)->select();
        return $data;
    }

    //个人信息修改
    public function setingUserInfo()
    {
        $user_id = input('user_id');
        $nickName = input('nickName');
        $gender = input('gender');
        $birthday = input('birthday');
        $autograph_name = input('autograph_name');
        if ($nickName) {
            $res = model('XcxUser')->where('id', $user_id)->update(['nickName' => $nickName]);
            if ($res) {
                return $this->success('成功', '', '', '');
            }
        }
        if ($gender) {
            $res = model('XcxUser')->where('id', $user_id)->update(['gender' => $gender]);
            if ($res) {
                return $this->success('成功', '', '', '');
            }
        }
        if ($birthday) {
            $res = model('XcxUser')->where('id', $user_id)->update(['birthday' => $birthday]);
            if ($res) {
                return $this->success('成功', '', '', '');
            }
        }
        if ($autograph_name) {
            $res = model('XcxUser')->where('id', $user_id)->update(['autograph_name' => $autograph_name]);
            if ($res) {
                return $this->success('成功', '', '', '');
            }
        }
//        $file = request()->file('file');
//        if($file->isValid()){
//            $ext = $file->getClientOriginalExtension();//文件扩展名
//            $file_name = date("YmdHis",time()).'-'.uniqid().".".$ext;//保存的文件名
//            if(!in_array($ext,['jpg','jpeg','gif','png']) ) return response()->json(err('文件类型不是图片'));
//            //把临时文件移动到指定的位置，并重命名
//            $path = public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'wchat_img'.DIRECTORY_SEPARATOR.date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR.date('d').DIRECTORY_SEPARATOR;
//            $bool =  $file->move($path,$file_name);
//            if($bool){
//                $img_path = '/uploads/wchat_img/'.date('Y').'/'.date('m').'/'.date('d').'/'.$file_name;
//                $data = [
//                    'domain_img_path'=>get_domain().$img_path,
//                    'img_path'=>$img_path,
//                ];
//                return response()->json(succ($data));
//            }else{
//                return response()->json(err("图片上传失败！"));
//            }
//        }else{
//            return response()->json(err("图片上传失败！"));
//        }
    }

    //文章点赞
    public function dianZan()
    {
        $user_id = input('user_id');
        $comment_id = input('comment_id');
        $user_praise = input('user_praise');
        if (!$user_id || !$comment_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxZan')->where('type', 1)->where('user_id', $user_id)->where('comment_id', $comment_id)->find();
        $resaa =model('XcxAdd')->save(['user_praise' => $user_praise], ['id' => $comment_id]);
        if ($data || $resaa) {
            $res = model('XcxZan')->where('type', 1)->where('user_id', $user_id)->where('comment_id', $comment_id)->delete();
            // $resa = model('XcxAdd')->where('id', $user_id)->where('user_praise', $comment_id)->delete();
              // $resa =model('XcxAdd')->save(['user_praise' => $user_praise], ['id' => $comment_id]); 
            if ($res) {
                return toJson('1000', '取消点赞成功', '', '');
            }
        }
        
         $touser_id = model('XcxAdd')->where('id',$comment_id)->field('user_id')->select();
          $res = model('XcxZan')->insert(['touser_id'=>$touser_id[0]['user_id'],'user_id' => $user_id, 'comment_id' => $comment_id, 'type' => 1]);
        if ($resaa || $res ) {
            return toJson('1000', '点赞成功', '', '');
           
        }else{
             return toJson('1000', '取消点赞成功', '', '');
        }

    }

    //评论点赞
    public function commentDianZan()
    {
        $user_id = input('user_id');
        $comment_id = input('comment_id');
        $zan_count = input('zan_count');
        if (!$user_id || !$comment_id) {
            return error('fail', '请查看参数', '');
        }
        $data = model('XcxZan')->where('type', 2)->where('user_id', $user_id)->where('comment_id', $comment_id)->find();
        $resaa =model('XcxComment')->save(['zan_count' => $zan_count], ['id' => $comment_id]);
        if ($data || $resaa) {
            $res = model('XcxZan')->where('type', 2)->where('user_id', $user_id)->where('comment_id', $comment_id)->delete();
            // $resa = model('XcxAdd')->where('id', $user_id)->where('user_praise', $comment_id)->delete();
            // $resa =model('XcxAdd')->save(['user_praise' => $user_praise], ['id' => $comment_id]);
            if ($res) {
                return toJson('1000', '取消点赞成功', '', '');
            }
        }


        $res = model('XcxZan')->insert(['user_id' => $user_id, 'comment_id' => $comment_id, 'type' => 2]);
        if ($resaa || $res ) {
            return toJson('1000', '点赞成功', '', '');

        }else{
            return toJson('1000', '取消点赞成功', '', '');
        }

    }


    //别人点我点赞列表
    public function userdianZanList()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return $this->error('用户id不存在', '', '', '');
        }
        $page = input('page') ? input('page') : 0;
        $wenzhang = model('XcxZan')->where('touser_id',$user_id)->limit($page,10)->select();
        $res = [];
        $ress = [];
        foreach ($wenzhang as $keys => $values) {
            $comment = model('XcxComment')->field('issue_id')->where('id', $values['comment_id'])->find();
            $iamge = model('XcxImg')->field('name')->where('imgid', $comment['issue_id'])->find();
            $res['image'] = $iamge['name'];
            $data = model('XcxUser')->field('avatarUrl,nickName')->where('id', $values['user_id'])->find();
            $data['nickName'] = $data['nickName'] . '攒了你的分享';
            $res['user'] = $data;
            $ress[] = $res;
        }
        return $ress;

    }

    //w我点赞列表
    public function dianZanList()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return $this->error('请查看参数设定', '', '', '');
        }
        $data = model('XcxZan')->where('type', 1)->where('user_id', $user_id)->select();
        $arr = [];
        foreach ($data as $k => $v) {
//            $arrs = [];

            $result = model('XcxAdd')->where('id', $v['comment_id'])->find();
//            $arr[] = $result;
            $image = model('XcxImg')->where('imgid', $result['id'])->find();
//            foreach ($image as $key => $value) {
//                $arrs[] .=  $value['name'];
//            }
            $result['image'] = $image['name'];
            $arr[] = $result;
        }
        return $this->success('success', '', $arr);
    }

    public function yinSiSet()
    {
        $user_id = input('user_id');
        $res = model('XcxSettingPrivacy')->where('user_id', $user_id)->find();
        return $res;
    }

    //隐私设置
    public function yinSiSetting()
    {
        $user_id = input('user_id');
        $kg = input('kg');
        $val = input('val');
        $data = [];
        if ($kg === 'pinglun_kg') {
            $data['pinglun_kg'] = $val;
        }
        if ($kg === 'atw_kg') {
            $data['atw_kg'] = $val;
        }
        if ($kg === 'dlwz_kg') {
            $data['dlwz_kg'] = $val;
        }
        if ($kg === 'syxx_kg') {
            $data['syxx_kg'] = $val;
        }
        if (!$user_id) {
            return $this->error('请查看参数设定', '', '', '');
        }

        $res = model('XcxSettingPrivacy')->where('user_id', $user_id)->find();
        if (!$res) {
            model('XcxSettingPrivacy')->insert(['user_id' => $user_id]);
            $result = model('XcxSettingPrivacy')->where('user_id', $user_id)->update($data);
            if ($result) {
                return $this->success('success', '', '');
            }
        }
        $resul = model('XcxSettingPrivacy')->where('user_id', $user_id)->update($data);
        if ($resul) {
            return $this->success('success', '', '');
        }
    }

    public function newsSet()
    {
        $user_id = input('user_id');
        $res = model('XcxSettingNews')->where('user_id', $user_id)->find();
        return $res;
    }

    //消息设置
    public function newsSetting()
    {
        $user_id = input('user_id');
        $kg = input('kg');
        $val = input('val');
        $data = [];
        if ($kg === 'yratw_kg') {
            $data['yratw_kg'] = $val;
        }
        if ($kg === 'yrpl_kg') {
            $data['yrpl_kg'] = $val;
        }
        if ($kg === 'yrdz_kg') {
            $data['yrdz_kg'] = $val;
        }
        if ($kg === 'xzsc_kg') {
            $data['xzsc_kg'] = $val;
        }
        if ($kg === 'tzxx_kg') {
            $data['tzxx_kg'] = $val;
        }
        if (!$user_id) {
            return $this->error('请查看参数设定', '', '', '');
        }

        $res = model('XcxSettingNews')->where('user_id', $user_id)->find();

        if (!$res) {
            model('XcxSettingNews')->insert(['user_id' => $user_id]);
            $result = model('XcxSettingNews')->where('user_id', $user_id)->update($data);
            if ($result) {
                return $this->success('success', '', '');
            }
        }
        $resul = model('XcxSettingNews')->where('user_id', $user_id)->update($data);
        if ($resul) {
            return $this->success('success', '', '');
        }
    }

    //社区规范
    Public function sheGuiFan()
    {
        $v = 'http://' . $_SERVER['SERVER_NAME'];
        $json_url = 'http://' . $_SERVER['HTTP_HOST'] . '/public/shequguifan.json';//文件名称和路径
        // 从文件中读取数据到PHP变量
        $json_string = file_get_contents($json_url);
        // 把JSON字符串转成PHP数组
        $data = json_decode($json_string, true);
        return $data;

    }


    //关注 取消关注
    public function guanZhu()
    {
        $form_user_id = input('form_user_id');
        $user_id = input('user_id');
        $state = model('XcxUserguanzhu')
            ->where('user_id',$user_id)
            ->where('form_user_id',$form_user_id)
            ->find();
        if($state!=null){
            model('XcxUserguanzhu')
                ->where('user_id',$user_id)
                ->where('form_user_id',$form_user_id)
                ->delete();
            $data['hasChange'] = false;
            return $data;
        }else{
            model('XcxUserguanzhu')->insert(['user_id'=>$user_id,'form_user_id'=>$form_user_id,'create_date'=>date('Y-m-d H:i:s')]);
            $data['hasChange'] = true;
            return $data;
        }
//        $comment_id = input('comment_id');
//        $data = model('XcxAdd')
//            ->where('id', $comment_id)
//            ->find();
//        $res = model('XcxGuanzhu')
//            ->where('user_id', $user_id)
//            ->where('follow_id', $data['user_id'])
//            ->find();
//        if ($res) {
//          $arr =  model('XcxGuanzhu')
//                ->where('user_id', $user_id)
//                ->where('follow_id', $data['user_id'])
//                ->delete();
//          if($arr){
//              return '取消关注';
//          }
//        } else {
//           $res = model('XcxGuanzhu')
//                ->insert(['user_id' => $user_id, 'follow_id' => $data['user_id']]);
//           if($res){
//               return '关注';
//           }
//        }
    }
    //新增粉丝
    public function userGuanZhu()
    {
        $user_id = input('user_id');  // 获取用户id
        if ($user_id === ''){  // 用户id 未传递
            return '';
        }

        // 该用户的粉丝
        $data = model('XcxGuanzhu')
            ->field('user_id')
            ->where('touser_id', $user_id)
            ->select();
        $data = array_map(function ($v){
            return intval($v['user_id']);
        },$data);
        $touser_list = model('XcxUser')
            ->field(['autograph_name','nickName','avatarUrl','id'])
            ->where('id','in',$data)
            ->select();

        // 该用户关注的粉丝
        $is_both_notice = model('XcxGuanZhu')
            ->field(['touser_id'])
            ->where('user_id',$user_id)
            ->select();
        $is_both_notice = array_map(function ($v){
            return intval($v['touser_id']);
        },$is_both_notice);
        $GLOBALS['data'] = $is_both_notice;

        // 增加用户的互相关注状态
        $touser_list = array_map(function ($v){
            $v['state']=in_array($v['id'],$GLOBALS['data']) ? '已关注' : '未关注';
            return $v;
        },$touser_list);
        return $touser_list;
    }

    // 收到的评论
    public function receive_comment(){
        $user_id = input('user_id');
        if ($user_id == null){
            return $this->error('用户信息错误!','','','');
        }
        $message_list = model('XcxComment')
            ->field(['issue_id'])
            ->where('user_id',$user_id)
            ->select();
        $message_list = array_map(function ($v){
            return intval($v['issue_id']);
        },$message_list);
        $comment = model('XcxAdd')
            ->field(['home_uaer_name','r_image','image'])
            ->where('id','in',$message_list)
            ->select();
        return $comment;
    }

    // 我的晒一晒列表
    public function articleMine(){
        $user_id = input('user_id');
        $article = model('XcxAdd')  //  提取用户文章
            ->field(['home_uaer_name','r_image','description'])
            ->where('user_id',$user_id)
            ->select();

        $article_list = array_map(function ($v){  // 替换关键字
            $article = $v['description'];
            $article = preg_replace('/@\d{1,9}/','here_uid',$article);
            return explode('here_uid',$article);
        },$article);

        $at_user_list = array_map(function ($v){   //  提取所@用户的id
            preg_match_all('/@\d{1,9}/',$v['description'],$user_arr);
            return $user_arr;
        },$article);

        //  提取真实的用户id
        $at_user_list = array_map(function ($v){
            if (count($v[0]) == 0){
                return $v[0];
            }else{
                $res = array_map(function ($val){
                    return intval(substr($val,1));
                },$v[0]);
                return $res;
            }
        },$at_user_list);

        //  根据用户id提取用户名
        $user_list = array_map(function ($v){
            if (count($v)==0){
                return $v;
            }else{
                $v = array_map(function ($v){
                    $user = model('XcxUser')
                        ->field(['nickName','avatarUrl'])
                        ->where('id','in',$v)
                        ->select();
                    return $user[0];
                },$v);
                return $v;
            }
        },$at_user_list);

        //  拼合返回的数据
        foreach ($article_list as $k => &$v){
            $res = [$v];
            array_push($res,$user_list[$k]);
            $v = $res;
        }
        return $article_list;
        // TODO: @功能暂未实现
    }

    // @我的列表
    public function atMine(){
        $user_id = input('user_id');
        if (!$user_id){
            return $this->error('用户id不存在','','','');
        }
        $page = input('page') ? (input('page') * 10) : 0;
        $at_mine = model('XcxUserAt')
            ->field([
                'hutp_xcx_user_at.id',
                'art.image',
                'user.nickName',
                'user.avatarUrl'
                ])
            ->join('hutp_xcx_add art','art.id = hutp_xcx_user_at.add_id')
            ->join('hutp_xcx_user user','user.id = hutp_xcx_user_at.user_id')
            ->where('to_user_id',$user_id)
            ->limit($page,10)
            ->select();

        return $at_mine;
    }

    public function get_user_info(){  // 单独获取用户信息
        $user_id = input('user_id');
        if($user_id==null){
            return $this->error('用户id不能为空');
        }
        $user = model('XcxUser')
        ->where('id',$user_id)
        ->select();
        $user = $user[0];
        $this->fansInfo($user['id']);
        $users = model('XcxUser')->where('id',$user_id)->find();
        $mine = input('mine');
        $state = model('XcxUserguanzhu')
        ->where('form_user_id',$mine)
        ->where('user_id',$user_id)->select();
        if ($state!=null){
            $user['hasChange'] = true;
        }else{
            $user['hasChange'] = false;
        }
        return $user;
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