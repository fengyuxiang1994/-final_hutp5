<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/7
 * Time: 16:37
 */

namespace app\api\controller;

use think\Controller;

class Tixian extends Controller
{
    public function putForward()
    {
        $id = Input('id');
        $money = Input('money');
        if ($id == null || $money == null) {
            return '参数为空';
        }
        $data = model('XcxUser')->where('id', $id)->find();
//        var_dump($data->nickName);die;
        $info = [
            'nickName' => $data['nickName'],
            'userPhone' => $data['phone'],
            'txFangshi' => '微信',
            'txZtai' => '未提现',
            'listorder' => '',
            'tx_time' =>  date("Y-m-d H:i:s", time()),
            'xcx_user_id' => $id,
            'money' => $money
        ];
        if ($data['money'] < $money) {
            return '金额错误';
        }
        $datas = model('XcxTxuser')->insert($info);

        if (!$datas) {
            return '提现发起失败';
        }
        $res = model('XcxUser')->where('id', $id)->update(['money' => $data['money'] - $money]);

        if (!$res) {
            return '金额减少失败';
        }
        return '提现发起成功';

    }
    public function tiXianJL()
    {
        $id = Input('id');
        $data = model('XcxTxuser')->where('xcx_user_id',$id)->where('txZtai','成功')->select();
        $this->success('success', '', $data);
    }
}