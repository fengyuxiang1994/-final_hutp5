<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/20
 * Time: 11:09
 */

namespace app\api\controller;

use think\Controller;
use think\Db;


class Profit extends Controller
{
    //收益计算
    public function userProfit()
    {
        $user_id = input('user_id');
        $comment_id = input('comment_id');
        if ($user_id == null || $comment_id== null){
            return error('0','获取参数失败','');
        }
        $data = model('XcxAdd')
            ->where('id', $comment_id)
            ->find();
        $money = model('XcxPtSetting')
            ->find();
        $time =  date("Y-m-d H:i:s",time());
        $times =$money['time_sj'];
        $res = [
            'xpoint' => $data['xpoint'],
            'ypoint' => $data['ypoint'],
            'form_user_id' => $data['user_id'],
            'user_id' => $user_id,
            'comment_id' => $comment_id,
            'money' => $money['money'],
            'create_time'=>date("Y-m-d H:i:s", time())
        ];
        $result = model('XcxMoney')
            ->where('user_id', $user_id)
            ->where('form_user_id',$data['user_id'])
            ->where('xpoint',$data['xpoint'])
            ->where('ypoint',$data['ypoint'])
            ->select();

        if (empty($result)) {
            $arr =  model('XcxMoney')
                ->insert($res);
            if (!$arr) {

                return '记录添加失败';
            }
            $moy = model('XcxUser')
                ->where('id', $data['user_id'])
                ->field('money')
                ->find();
            $moys = model('XcxUser')
                ->where('id', $data['user_id'])
                ->update(['money'=>$moy['money']+$money['money']]);
            if($moys){
                return '金额修改成功';
            }
        }else{
            $resu = model('XcxMoney')
                ->where('user_id', $user_id)
                ->where('form_user_id',$data['user_id'])
                ->where('xpoint',$data['xpoint'])
                ->where('ypoint',$data['ypoint'])
                ->order('create_time desc')
                ->find();

            $createtime =  date("Y-m-d",strtotime("+{$times} day",strtotime($resu['create_time'])));
            if($time >= $createtime){
                return '查看';
                $arr =  model('XcxMoney')->insert($res);
                if (!$arr) {
                    return '记录添加失败';
                }
                $moy = model('XcxUser')
                    ->where('id', $data['user_id'])->field('money')->find();
                $moys = model('XcxUser')
                    ->where('id', $data['user_id'])
                    ->update(['money'=>$moy['money']+$money['money']]);
                if($moys){
                    return '金额修改成功';
                }
            }
        }

    }


}