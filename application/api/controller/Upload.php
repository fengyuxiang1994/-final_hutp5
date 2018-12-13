<?php
/**
 * Created by PhpStorm.
 * User: fengy
 * Date: 2018/11/28
 * Time: 17:19
 */

namespace app\api\controller;
use app\baiduyun\AipImageCensor;
use think\Cache;
use think\Controller;

use OSS\OssClient;
use OSS\Core\OssException;
class Upload extends Controller
{
    public function index()
    {
        $scr = $_FILES['file']['tmp_name'];
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

        //图片鉴定
        //---------------------------------------------
        $accessKeyId = config('xcx.KeyId');
        $accessKeySecret = config('xcx.KeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = config('xcx.Endpoint');
        $bucket = config('xcx.Bucket');
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

        $data = $this->banDuYun($urls);
        foreach ($data['data'] as $key => $value){
            if ($value['msg'] === '存在二维码内容'){
                $arr = parse_url($urls);
                $aaa = substr($arr['path'], 1);
                $ossClient->deleteObject($bucket, $aaa);
                            $urls = "https://daotuba-image.oss-cn-hangzhou.aliyuncs.com/_20181211180617.png";

            }
            if ($value['msg'] === '存在色情内容'){
                $arr = parse_url($urls);
                $aaa = substr($arr['path'], 1);
                $ossClient->deleteObject($bucket, $aaa);
                            $urls = "https://daotuba-image.oss-cn-hangzhou.aliyuncs.com/_20181211180617.png";

            }
            if ($value['msg'] === '存在暴恐内容'){
                $arr = parse_url($urls);
                $aaa = substr($arr['path'], 1);
                $ossClient->deleteObject($bucket, $aaa);
                                         $urls = "https://daotuba-image.oss-cn-hangzhou.aliyuncs.com/_20181211180617.png";

            }
            if ($value['msg'] === '存在政治敏感内容'){
                $arr = parse_url($urls);
                $aaa = substr($arr['path'], 1);
                $ossClient->deleteObject($bucket, $aaa);
                                          $urls = "https://daotuba-image.oss-cn-hangzhou.aliyuncs.com/_20181211180617.png";

            }
          //  if ($value['msg'] === '存在水印码内容'){
           //     $arr = parse_url($urls);
           //     $aaa = substr($arr['path'], 1);
           //     $ossClient->deleteObject($bucket, $aaa);
          //      return ['msg' => '存在水印码内容'];
           // }
        }

        //---------------------------------------------
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

    public function banDuYun($url) {
        $app_id ='15112367';
        $api_key='22y6vWtG6OgXocyRDP708vZ3';
        $secret_key ='MvOopQeqWG0fK4KAQD8SaWXGlYj3oXrn';
        $client = new AipImageCensor($app_id,$api_key,$secret_key);
        $result = $client->imageCensorUserDefined($url);
        return $result;
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



}