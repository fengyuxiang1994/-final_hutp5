<?php

namespace app\api\controller;

use think\Controller;


class Home extends Controller
{
    //获取小程序的主页内容
    public function getPageContentApi()
    {
        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');

        $limit = input('get.limit', 4, 'intval');
        $homeData = model('XcxHome')->getFirst($limit, $lastid);
//        $homeData = model('XcxAdd')->getFirst($limit,$lastid);
        foreach ($homeData as $k => $v) {
//            $image = model('XcxImg')->where('imgid',$v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['r_image'] = 'http://192.168.100.224/hutp5/public' . $v['r_image'];
            $v['image'] = 'http://192.168.100.224/hutp5/public' . $v['image'];
        }

        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

    //获取小程序的主页内容
    public function getPageContentInfo()
    {
        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');
        //  获取分类id，按照分类取数据
        $category_id = input('category_id');
        $limit = input('get.limit', 4, 'intval');
//        $homeData = model('XcxHome')->getFirst($limit,  $lastid);
        $homeData = model('XcxAdd')->getFirst($limit, $lastid);
        shuffle($homeData);

        foreach ($homeData as $k => $v) {
            $image = model('XcxImg')->where('imgid', $v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['image'] = $image['name'];
        }
        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

      //获取小程序的主页内容
    public function getPageContentInfoxxggg()
    {
        // 显示查询数据几条
        $limit = input('get.limit', 4, 'intval');

        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');
        //  获取分类id，按照分类取数据
         $cat = input('get.cat', 0, 'intval');
        
        $data = [];
        $data['category_id'] = $cat;
        $data['id'] = $lastid;

        $homeData = model('XcxAdd')->getFirstyyyy($limit, $data);
        shuffle($homeData);

        foreach ($homeData as $k => $v) {
            $image = model('XcxImg')->where('imgid', $v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['image'] = $image['name'];
        }
        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }
    public function getFindApi()
    {
        $id = input('get.id', 0, 'intval');


        $xqData = model('XcxHome')->getXXXcx($id);


        // foreach ($xqData as $k => $v) {
        $xqData['r_image'] = 'http://192.168.100.224/hutp5/public' . $xqData['r_image'];
        $xqData['image'] = 'http://192.168.100.224/hutp5/public' . $xqData['image'];
        // }
        // dump($xqData);
        if (!$xqData) {
            return show(0, 'error');
        }
        return show(1, 'success', $xqData);
    }


    public function getComment()
    {
        $id = input('id');
        $user_id =input('user_id');
        if($id==null && $user_id==null){
            return '参数错误';
        }
        $setting = model('XcxPtSetting')->find();
        $data = model('XcxAdd')
            ->where('id', $id)
            ->find();
        if($setting['browse_setting'] === 0){
//            return $setting;
            model('XcxAdd')
                ->where('id', $id)->update(['browse_num' => $data['browse_num']+1]);
        }else{
            $num = rand(1,10);
            model('XcxAdd')
                ->where('id', $id)->update(['browse_num' => $data['browse_num']+$num]);
        }


//        $jinhao = model('XcxTopic')->where('')
        $at = model('XcxUserAt')
            ->field('to_user_id')
            ->where('add_id',$id)
            ->select();

        $ats =[];
        foreach ($at as $key => $value){
            $user = model('XcxUser')->field('nickName,id')->where('id',$value['to_user_id'])->find();
            $ats[] =$user;
        }

        $image = model('XcxImg')
            ->field('name')
            ->where('imgid', $data['id'])
            ->select();

        $arr = [];
        foreach ($image as $key => $value) {
            $arr[] = $value['name'];
        }
        $data['jinhao']=[];
        $data['image'] = $arr;
        $data['at'] = $ats;
//        $data['huati'] = $arr;
        $userinfo =model('XcxZan')
            ->where('user_id',$user_id)
            ->where('comment_id',$id)
            ->where('type',1)
            ->find();
        if(empty($userinfo)){
            $data['hasChange'] = false;
        }else{
            $data['hasChange'] = true;

        }

        $usercomment =model('XcxShoucang')
            ->where('user_id',$user_id)
            ->where('comment_id',$id)
            ->find();
        if(empty($userinfo)){
            $data['hasChangesc'] = false;

        }else{
            $data['hasChangesc'] = true;

        }

        $guanzhuInfo = model('XcxUserguanzhu')->where('form_user_id',$user_id)->where('user_id',$data['user_id'])->find();
        if ($guanzhuInfo){
            $data['guanInfo'] = true;
        }else{
            $data['guanInfo'] = false;
        }


        return $data;
    }

    //获取首页数据
    public function getHomeInfo()
    {
        $page = input('pages');
        $data = model('XcxAdd')->limit(($page - 1) * 10, 10)->select();
        foreach ($data as $key => $value) {
            $image = model('XcxImg')->where('imgid', $value['id'])->find();
            $value['image'] = $image['name'];
        }

        return $data;

    }

    //获取分类信息
    public function getClassInfo()
    {
        $classInfo = model('Category')
            ->where('status',1)
            ->order('listorder','asc')
            ->select();
        return $classInfo;
    }

    //获取用户关注文章
    public function userGuanzhu()
    {
        $user_id = input('user_id');
        $data = model('XcxUserguanzhu')->where('form_user_id',$user_id)->select();
        $guanzhuInfo =[];
        foreach ($data as $key => $value){
            $res = model('XcxAdd')->where('user_id',$value['user_id'])->select();

            foreach ($res as $k => $v){
                $arr = [];
                $image = model('XcxImg')->where('imgid',$v['id'])->select();
                foreach ($image as $keys => $vla){
                    $arr[] .= $vla['name'];
                }
                $v['image']= $arr;
                $v['hasChange'] = 'false';
                $v['hasChangesc'] = 'false';

            }
            $guanzhuInfo[] = $res;
        }
        $guanzhu=[];
        foreach ($guanzhuInfo as $v => $k){
            foreach ($k as $va => $ke){
                array_push($guanzhu,$ke);

            }
        }
        $ctime_str = [];
        foreach ($guanzhu as $k => $va){
//            $k['ctime_str'] = strtotime($va['update_time']);
//            $ctime_str[] = $guanzhu[$k]['ctime_Str'];
            $guanzhu[$k]['update_time'] = $va['update_time'];
            $ctime_str[] = $guanzhu[$k]['update_time'];
        }
        array_multisort($ctime_str,SORT_DESC,$guanzhu);
        return $guanzhu;
    }

    //我的草稿
    public function caogaoInfo() {
        $user_id = input('user_id');
        $data = model('XcxCaogao')
            ->where('user_id',$user_id)
            ->select();
        foreach ($data as  $k=>$v){
            $image = model('XcxImgcaogao')
                ->field('name')
                ->where('imgid',$v['id'])
                ->find();
            $v['create_time'] = date("m/d",strtotime($v['create_time']));
            $v['image']=$image['name'];
        }

        return $data;
    }
}
