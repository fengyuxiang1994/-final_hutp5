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
        $data = model('XcxShoucang')->field(['user_id','comment_id'])->where('comment_id','in', $user_id)->page($page,10)->order(['create_date'=>'desc'])->select();

        foreach ($data as $v => $k) {
            $user = model('XcxUser')->where('id', $k['user_id'])->find();

            $res['user']['nickName'] = $user['nickName'];
            $res['user']['avatarUrl'] = $user['avatarUrl'];

            $resu = model('XcxAdd')->where('id', $k['comment_id'])->find();
            $image = model('XcxImg')->field('name')->where('imgid', $resu['id'])->find();

            $arr = $image['name'];
            $res['id'] = $k['comment_id'];
            $res['image'] = $arr;
            $ress[] = $res;
        }
        return $ress;


    }

    //别人点我点赞列表
    public function userdianZanList()
    {
        $user_id = input('user_id');
        if (!$user_id) {
            return $this->error('用户id不存在', '', '', '');
        }
        $page = input('page') ? input('page') : 0;
        $wenzhang = model('XcxZan')->where('touser_id',$user_id)->page($page,10)->order(['id'=>'desc'])->select();
        $res = [];
        $ress = [];
        foreach ($wenzhang as $keys => $values) {
            $comment = model('XcxAdd')->field('id')->where('id', $values['comment_id'])->find();
            $iamge = model('XcxImg')->field('name')->where('imgid', $comment['id'])->find();
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
        $data = model('XcxUserguanzhu')
            ->field('form_user_id')
            ->where('user_id', $user_id)
            ->page($page,10)
            ->select();
        if (count($data)==0){
            return [];
        }
        $data = array_map(function ($v){
            return intval($v['form_user_id']);
        },$data);
        $touser_list = model('XcxUser')
            ->field(['autograph_name','nickName','avatarUrl','id'])
            ->where('id','in',$data)
            ->order(['id'=>'desc'])
            ->select();

        // 该用户关注的粉丝
        $is_both_notice = model('XcxUserguanzhu')
            ->field(['user_id'])
            ->where('form_user_id',$user_id)
            ->select();
        $is_both_notice = array_map(function ($v){
            return intval($v['user_id']);
        },$is_both_notice);
        $GLOBALS['data'] = $is_both_notice;

        // 增加用户的互相关注状态
        $touser_list = array_map(function ($v){
            $v['state']=in_array($v['id'],$GLOBALS['data']) ? '已关注' : '关注';
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
        $page = input('page') ? input('page') : 0;
        $article_list = model('XcxAdd')->field('id')->where('user_id',$user_id)->select();
        $article_list = array_map(function ($v){return $v['id'];},$article_list);
        $message_list = model('XcxComment')
            ->field([
                'hutp_xcx_comment.user_id',
                'hutp_xcx_comment.id',
                'hutp_xcx_comment.reply_msg',
                'user.nickName',
                'img.name',
                'user.avatarUrl',
                'hutp_xcx_comment.issue_id'
            ])
            ->where('issue_id','in',$article_list)
            ->join('hutp_xcx_user user','hutp_xcx_comment.user_id=user.id')
            ->join('hutp_xcx_img img','hutp_xcx_comment.issue_id=img.imgid')
            ->order(['hutp_xcx_comment.create_date'=>'desc'])
            ->page($page,10)
            ->select();
          if (empty('$message_list')) {
              return [];
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
        $at_mine = model('XcxUserAt')
            ->field([
                'art.id',
                'art.image',
                'user.nickName',
                'user.avatarUrl'
            ])
            ->join('hutp_xcx_add art','art.id = hutp_xcx_user_at.add_id')
            ->join('hutp_xcx_user user','user.id = hutp_xcx_user_at.user_id')
            ->where('to_user_id',$user_id)
            ->order(['hutp_xcx_user_at.create_time'=>'desc'])
            ->page($page,10)
            ->select();

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
        $follow_arr = model('XcxUserguanzhu')
            ->field('user_id')
            ->where('form_user_id',$user_id)
            ->select();
        $follow_arr = array_map(function ($v){
            return intval($v['user_id']);
        },$follow_arr);
        date_default_timezone_set('PRC');
        $current_date = date('Y-m-d H:i:s',time()-3600*24);
        $follow_arr = model('XcxAdd')
            ->field(['home_uaer_name','id','create_time'])
            ->where('user_id','in',$follow_arr)
            ->where('status',1)
            ->where('create_time','>',$current_date)
            ->order(['create_time'=>'desc'])
            ->page($page,10)
            ->select();
        foreach ($follow_arr as $k => $v){
            $image = model('XcxImg')->field('name')->where('imgid',$v['id'])->select();
            $v['image'] = $image[0]['name'];
        }
        $follow_arr = array_map(function ($v){
            $v['type'] = 1;
            return $v;
        },$follow_arr);
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
            return $this->success('关注成功');
        }
    }
}