<?php
/**
 * Created by PhpStorm.
 * User: hu
 * Date: 2018/11/19
 * Time: 15:58
 */
namespace app\api\controller;

use http\Env\Request;
use think\Controller;

class Zilook extends Controller
{
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
        $page = input('page') ? input('page') : 0;
        $user_id = model('XcxAdd')->field('id')->where('user_id',$user_id)->select();
        $user_id = array_map(function ($v){
            return $v['id'];
        },$user_id);
        $data = model('XcxShoucang')->where('comment_id','in', $user_id)->page($page,10)->order(['create_date'=>'desc'])->select();

        $sear = new Search();
        foreach ($data as $v => $k) {
            model('XcxShoucang')->where('id',$k['id'])->update(['shoucang_status'=>1]);

            $user = model('XcxUser')->where('id', $k['user_id'])->find();

            $res['user']['nickName'] = $user['nickName'];
            $res['user']['avatarUrl'] = $user['avatarUrl'];

            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($k['create_date'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $res['time'] = "刚刚";
                }else{
                    $res['time']  = $aaa.'小时前';
                }
            }else{
                $res['time'] = date("m月d日 H:i",strtotime($k['create_date']));
            }

            $resu = model('XcxAdd')->where('id', $k['comment_id'])->find();
            $image = model('XcxImg')->field('name')->where('imgid', $resu['id'])->find();
            $res['status'] =$k['shoucang_status'];
            $arr = $image['name'];
            $res['id'] = $k['comment_id'];
            $res['image'] = $arr;
            $ress[] = $res;
        }
        return $ress;
    }

    public function status() {
        $user_id = input('user_id');
        if (!$user_id) {
            return $this->error('参数错误', '', '', '');
        }
        $zanstatus = model('XcxZan')->where('touser_id',$user_id)->where('status',0)->find();
        if (empty($zanstatus)){
            $data['DianZan']['status'] = 1;
        }else{
            $data['DianZan']['status'] = 0;
        }
        $shoustatus = model('XcxAdd')->where('user_id',$user_id)->select();
        foreach ($shoustatus as $key => $value){
            $info = model('XcxShoucang')->where('comment_id',$value['id'])->where('shoucang_status',0)->find();
            if ($info){
                $data['ShouStatus']['status'] = 0;
                continue;
            }else{
                $data['ShouStatus']['status'] = 1;
            }
        }
        $pinglunstatus = model('XcxAdd')->where('user_id',$user_id)->select();
        foreach ($pinglunstatus as $key => $value){
            $info = model('XcxComment')->where('issue_id',$value['id'])->where('comment_status',0)->find();
            if ($info){
                $data['pinglunstatus']['status'] = 0;
                continue;
            }else{
                $data['pinglunstatus']['status'] = 1;
            }
        }
        $fanstatus = model('XcxUserguanzhuNotion')->where('user_id',$user_id)->where('guanzhu_status',0)->find();

            if ($fanstatus){
                $data['fanstatus']['status'] = 0;
            }else{
                $data['fanstatus']['status'] = 1;
            }
         $tongzhi = model('XcxAddnotice')
             ->where('tongzhi_status',0)
             ->where('user_id',$user_id)
             ->find();
        if ($tongzhi){
            $data['tongzhi']['status'] = 0;
        }else{
            $data['tongzhi']['status'] = 1;
        }
       $atuser = model('XcxUserAtNotion')
           ->where('at_notice_status',0)
            ->where('to_user_id',$user_id)
           ->find();
        if ($atuser){
            $data['atuser']['status'] = 0;
        }else{
            $data['atuser']['status'] = 1;
        }

        return $data;
    }


    //别人点我点赞列表
    public function userdianZanList()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return $this->error('参数错误', '', '', '');
        }
        $page = input('page') ? input('page') : 0;
        model('XcxZan')->where('touser_id',$user_id)->update(['status'=>1]);
        $wenzhang = model('XcxZan')
            ->where('touser_id',$user_id)
            ->page($page,10)
            ->order(['id'=>'desc'])
            ->select();
        $res = [];
        $ress = [];
        $sear = new Search();
        foreach ($wenzhang as $keys => $values) {

            $comment = model('XcxAdd')->field('id')->where('id', $values['comment_id'])->find();
            $iamge = model('XcxImg')->field('name')->where('imgid', $comment['id'])->find();

            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($values['create_date'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $res['time'] = "刚刚";
                }else{
                    $res['time']  = $aaa.'小时前';
                }
            }else{
                $res['time'] = date("m月d日 H:i",strtotime($values['create_date']));
            }
            $res['status'] = $values['status'];
            $res['image'] = $iamge['name'];
            $data = model('XcxUser')->field('avatarUrl,nickName')->where('id', $values['user_id'])->find();
            $res['user'] = $data;
            $res['id'] = $values['comment_id'];
            $ress[] = $res;
        }
        return $ress;

    }

    //新增粉丝
    public function userGuanZhu()
    {
        $user_id = input('user_id');  // 获取用户id
        if ($user_id === ''){  // 用户id 未传递
            return '';
        }
        $page = input('page') ? input('page') : 0;
        // 该用户的粉丝
        $data = model('XcxUserguanzhuNotion')
            ->where('user_id', $user_id)
            ->order(['create_date'=>'desc'])
            ->page($page,10)
            ->select();
        $sear = new Search();
        foreach ($data as  $key => $value){

            $userinfo =model('XcxUser')->where('id',$value['form_user_id'])->find();
            $value['nickName'] = $userinfo['nickName'];
            $value['avatarUrl'] = $userinfo['avatarUrl'];
            $value['autograph_name'] = $userinfo['autograph_name'];

            $datas = model('XcxUserguanzhu')
                ->where('user_id', $value['form_user_id'])
                ->where('form_user_id',$user_id)
                ->find();
            if (!$datas){
                $value['zhuangtai'] = '0';
            }else{
                $value['zhuangtai'] = '1';
            }

            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($value['create_date'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $value['create_date'] = "刚刚";
                }else{
                    $value['create_date']  = $aaa.'小时前';
                }
            }else{
                $value['create_date'] = date("m月d日 H:i",strtotime($value['create_date']));
            }
            model('XcxUserguanzhuNotion')
                ->where('user_id', $user_id)
                ->update(['guanzhu_status'=>1]);
        }

        return $data;
    }

    // 收到的评论
    public function receive_comment(){
        $user_id = input('user_id');
        if ($user_id == null){
            return $this->error('用户信息错误!','','','');
        }
        $page = input('page') ? input('page') : 0;
        $article_list = model('XcxAdd')->field('id')->where('user_id',$user_id)->select();
        $article_list = array_map(function ($v){return $v['id'];},$article_list);

        model('XcxComment')->where('issue_id','in',$article_list)->update(['comment_status'=>1]);
        $message_list =  model('XcxComment')
            ->where('issue_id','in',$article_list)
            ->order(['create_date'=>'desc'])
            ->page($page,10)
            ->select();
        if (empty($message_list)) {
              return [];
          }
        $sear = new Search();
        $res = [];
        foreach ($message_list as $key => $value){

            $userinfo = model('XcxUser')->where('id',$value['user_id'])->find();
            $value['nickName'] = $userinfo['nickName'];
            $value['avatarUrl'] = $userinfo['avatarUrl'];

            $sss = model('XcxImg')->where('imgid',$value['issue_id'])->find();
            $value['image'] = $sss['name'];
            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($value['create_date'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $value['create_date'] = "刚刚";
                }else{
                    $value['create_date']  = $aaa.'小时前';
                }
            }else{
                $value['create_date'] = date("m月d日 H:i",strtotime($value['create_date']));
            }
//            if ($value['comment_status']==0){
//                model('XcxComment')->where('id',$value['id'])->update(['comment_status'=>1]);
//            }
        }

        return $message_list;
    }

    public function get_comment(){
        $comment_id = input('comment_id');
        if(!$comment_id){
            return $this->error('评论id不能为空');
        }
        $add = model('XcxAdd')->where('id',$comment_id)->select();
        return $add[0];
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
        $page = input('page') ? input('page') : 0;
        model('XcxUserAtNotion')
            ->where('to_user_id',$user_id)
            ->update(['at_notice_status'=>1]);
        $at_mine = model('XcxUserAtNotion')
            ->where('to_user_id',$user_id)
            ->order(['create_time'=>'desc'])
            ->page($page,10)
            ->select();
        $sear = new Search();
        foreach ($at_mine as $key => $value){
            $userinfo = model('XcxUser')->where('id',$value['user_id'])->find();
            $value['nickName'] = $userinfo['nickName'];
            $value['avatarUrl'] = $userinfo['avatarUrl'];
            $img = model('XcxImg')->where('imgid',$value['add_id'])->find();
            $value['image'] = $img['name'];
            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($value['create_time'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $value['create_time'] = "刚刚";
                }else{
                    $value['create_time']  = $aaa.'小时前';
                }
            }else{
                $value['create_time'] = date("m月d日 H:i",strtotime($value['create_time']));
            }
        }

        return $at_mine;
    }

    //  通知列表
    public function noticeList(){
        $user_id = input('user_id');
        if (!$user_id){
            return $this->error('参数错误','','','');
        }
        $page = input('page') ? input('page') : 0;

        //  关注用户的新文章
        model('XcxAddnotice')
            ->where('user_id',$user_id)
            ->update(['tongzhi_status'=>1]);
        $follow_arr = model('XcxAddnotice')
            ->field(['add_id','create_date','tongzhi_status'])
            ->where('user_id',$user_id)
            ->order(['create_date'=>'desc'])
            ->page($page,10)
            ->select();
        $sear = new Search();
        foreach ($follow_arr as $key => $value){
            $addinfo = model('XcxAdd')
                ->field(['home_uaer_name','id'])
                ->where('status',1)
                ->where('id',$value['add_id'])
                ->find();
            $image = model('XcxImg')
                ->field('name')
                ->where('imgid',$addinfo['id'])
                ->find();
            $value['home_uaer_name'] = $addinfo['home_uaer_name'];
            $value['id'] =$addinfo['id'];
            $value['image'] = $image['name'];
            $new = date('Y-m-d H:i:s',time());
            $arr = $sear->diffDate($value['create_date'],$new);
            if ($arr['a']<1){
                $aaa = substr($arr['h'], 1);

                if($arr['h'] == '0'){
                    $value['create_date'] = "刚刚";
                }else{
                    $value['create_date']  = $aaa.'小时前';
                }
            }else{
                $value['create_date'] = date("m月d日 H:i",strtotime($value['create_date']));
            }
        }
        return $follow_arr;
    }

    //  关注
    public function gotoFans(){
        $user_id = input('user_id');
        $form_user_id = input('form_user_id');
        if ($user_id==null||$form_user_id==null){
            return $this->error('用户id不能为空','','','','');
        }
        $state = model('XcxUserguanzhu')->where('user_id',$form_user_id)->where('form_user_id',$user_id)->find();
        if ($state!=null){
            model('XcxUserguanzhu')->where('user_id',$form_user_id)->where('form_user_id',$user_id)->delete();
            return $this->success('取消关注成功');
        }else{
            model('XcxUserguanzhu')
                ->insert([
                    'user_id'=>$form_user_id,
                    'form_user_id'=>$user_id,
                    'create_date'=>date('Y-m-d H:i:s',time())
                ]);
            model('XcxUserguanzhuNotion')
                ->insert([
                    'user_id'=>$form_user_id,
                    'form_user_id'=>$user_id,
                    'create_date'=>date('Y-m-d H:i:s',time())
                ]);

            return $this->success('关注成功');
        }
    }
}