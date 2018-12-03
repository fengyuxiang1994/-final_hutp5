<?php
/**
 * Created by PhpStorm.
 * User: Administration
 * Date: 2018/12/3
 * Time: 14:45
 */

namespace app\admin\controller;
use think\Controller;

class Tixian extends Controller
{
    public function index() {
        $order = ['tx_time' => 'desc'];
        $add = model('XcxTxuser')
            ->order($order)
            ->paginate(20,false,['query'=>request()->param()]);
        return $this->fetch('', [
            'res' => $add,
        ]);
    }

    public function updateTxian(){
        $id = input('id');
        if (!$id){
            return '参数为空';
        }
        $money =model('XcxTxuser')
            ->where('id',$id)
            ->find();
        if (!$money){
            return '已提现';
        }
        $userinfo = model('XcxUser')
            ->where('id',$money['xcx_user_id'])
            ->find();
        $amount = $money['money'];
        $re_openid=$userinfo['openid'];
        $desc='晒一下提现';
        $check_name=$userinfo['nickName'];
        $wechatpay = new Pay();
//        $info = $wechatpay->sendMoney($amount,$re_openid,$desc,$check_name);
        $res =model('XcxTxuser')
            ->where('id',$id)
            ->update(['status'=>1,'txZtai'=>'已提现','tx_time'=> date('Y-m-d H:i:s',time())]);
        if ($res){
            return '提现成功';

        }
    }
    public function noTxian(){
        $id = input('id');
        if (!$id){
            return '参数为空';
        }
        $res =model('XcxTxuser')
            ->where('id',$id)
            ->find();
        $userinfo = model('XcxUser')
            ->where('id',$res['xcx_user_id'])
            ->find();
        model('XcxUser')->where('id',$res['xcx_user_id'])->update(['money'=>$userinfo['money']+$res['money']]);
        $res =model('XcxTxuser')
            ->where('id',$id)
            ->update(['status'=>2,'txZtai'=>'已驳回','tx_time'=> date('Y-m-d H:i:s',time())]);
    }
}