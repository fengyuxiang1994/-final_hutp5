<?php
/**
 * Created by PhpStorm.
 * User: fengy
 * Date: 2018/11/28
 * Time: 17:19
 */

namespace app\api\controller;
use app\baiduyun\AipImageCensor;
use think\Controller;

use OSS\OssClient;
use OSS\Core\OssException;
class Upload extends Controller
{
    public function index()
    {
        $scr = $_FILES['file']['tmp_name'];
        //图片鉴定
        //---------------------------------------------


        //---------------------------------------------
        $id = input('post.id');
        $status = input('types');
        if ($status == null) {
            $username = "add-image";    //我们给每个用户动态的创建一个文件夹

        } else {
            $username = "comment-image";    //我们给每个用户动态的创建一个文件夹

        }
        $ext = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.') + 1); // 上传文件后缀
        $dst = 'daotuba-images/' . $username . '/' . md5(time()) . $scr . '.' . $ext;     //上传文件名称
        // $this->load->library('AliUpload');
        $url = $this->upload($dst, $scr);
        $arr = parse_url($url);
        $urls = $arr['scheme'] .'s://'.$arr['host'].$arr['path'];
        $data = array('url' => $urls);

        $imgData = [
            'imgid' => $id,
            'name' => $data['url'],
        ];
        if ($status == null) {
            $imgda = model('XcxImg');
        } else {

            $imgda = model('XcxImgcaogao');
        }
        $imgda->data($imgData);
        $imgda->save();
        return show(1, 'success', $imgda);
    }

    public function banDuYun() {
        $apP_id = config('xcx.APP_ID');
        $api_key=config('xcx.API_KEY');
        $secret_key =config('xcx.SECRET_KEY');
//        $baiduyun = new \AipImageCensor($apP_id,$api_key,$secret_key);
        $baiduyun = new AipImageCensor($apP_id,$api_key,$secret_key);
        return $baiduyun;
    }


    public function upload($dst, $src)
    {
        $accessKeyId = config('xcx.KeyId');
        $accessKeySecret = config('xcx.KeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = config('xcx.Endpoint');
        $bucket = config('xcx.Bucket');

        @error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

        //获取对象
        $auth = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        try {
            //上传图片
            $result = $auth->uploadFile($bucket, $dst, $src);
            // dump($result);die;
            return $result['info']['url'];
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    public function deleteComment()
    {
        $comment_id = input('comment_id');
        $accessKeyId = config('xcx.KeyId');
        $accessKeySecret = config('xcx.KeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = config('xcx.Endpoint');
        $bucket = config('xcx.Bucket');
        $commentInfo = model('XcxAdd')
            ->where('id', $comment_id)
            ->find();
        if ($commentInfo) {
            $image = model('XcxImg')
                ->where('imgid', $comment_id)
                ->select();
            if ($image) {
                try {
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                    foreach ($image as $key => $value) {
                        $arr = parse_url($value['name']);
                        $aaa = substr($arr['path'], 1);
                        $ossClient->deleteObject($bucket, $aaa);
                    }
                } catch (OssException $e) {
                    printf(__FUNCTION__ . ": FAILED\n");
                    printf($e->getMessage() . "\n");
                    return;
                }
            }
            model('XcxImg')
                ->where('imgid', $comment_id)
                ->delete();
            model('XcxAdd')
                ->where('id', $comment_id)
                ->delete();
            model('XcxComment')
                ->where('issue_id', $comment_id)
                ->delete();
            model('XcxReply')
                ->where('comment_id', $comment_id)
                ->delete();
            print(__FUNCTION__ . ": OK" . "\n");
            return '删除成功';
        } else {
            return '文章不存在';
        }
    }

    public function url(){
        $arr = parse_url('http://images-daotuba123.oss-cn-hangzhou.aliyuncs.com/daotuba-images/add-image/b37c00aabaf7addd76372fac75604e5a/tmp/php93QEp6.jpg');
        $url = $arr['scheme'] .'s://'.$arr['host'].$arr['path'];
          dump($url);
    }

    public function aa(){
        $orderLogic = new OrderLogic();
        $timegap = I('timegap');
        if($timegap){
            $gap = explode('-', $timegap);
            $begin = strtotime($gap[0]);
            $end = strtotime($gap[1]);
        }else{
            //@new 新后台UI参数
            $begin = strtotime(I('add_time_begin'));
            $end = strtotime(I('add_time_end'));
        }
        // 搜索条件
        $condition = array();
        $keyType = I("keytype");
        $keywords = I('keywords','','trim');

        $consignee =  ($keyType && $keyType == 'consignee') ? $keywords : I('consignee','','trim');
        $consignee ? $condition['consignee'] = trim($consignee) : false;

        if($begin && $end){
            $condition['add_time'] = array('between',"$begin,$end");
        }

        $store_name = ($keyType && $keyType == 'store_name') ? $keywords :  I('store_name','','trim');
        if($store_name)
        {
            $store_id_arr = M('store')->where("store_name like '%$store_name%'")->getField('store_id',true);
            if($store_id_arr)
            {
                $condition['store_id'] = array('in',$store_id_arr);
            }
        }
        $condition['order_status'] = array('gt',0);
        $condition['order_prom_type'] = array('lt',5);
        $order_sn = ($keyType && $keyType == 'order_sn') ? $keywords : I('order_sn') ;
        $order_sn ? $condition['order_sn'] = trim($order_sn) : false;

        I('order_status') != '' ? $condition['order_status'] = I('order_status') : false;
//        I('pay_status') != '' ? $condition['pay_status'] = I('pay_status') : false;
        I('pay_code') != '' ? $condition['pay_code'] = I('pay_code') : false;
        I('shipping_status') != '' ? $condition['shipping_status'] = I('shipping_status') : false;
        I('user_id') ? $condition['user_id'] = trim(I('user_id')) : false;
        I('order_statis_id') != '' ? $condition['order_statis_id'] = I('order_statis_id') : false; // 结算统计的订单
        $sort_order = I('order_by','DESC').' '.I('sort');


    }

}