<?php
/**
 * Created by PhpStorm.
 * User: zjz
 * Date: 2018/11/24
 * Time: 12:58
 * Description: 草稿发布
 */

namespace app\api\controller;

use think\Controller;

class Newadd extends Controller
{

    public function caogaoTopublish(){  //  草稿改发布

        $data = input('get.');
        $caogao_id = input('caogao_id');
        $description = preg_replace_callback('/#[^#@\s]*[#]{1}[\s]{1}/',function ($v){
                return '';
        },$data['description']);
        $description = preg_replace_callback('/@[^#@\s]*[\s]{1}/',function ($v){
                return '';
        },$description);
        $description = trim($description);
        $old_topic_id = model('XcxCaogao')->where('id',$caogao_id)->select();
        $old_topic_id = $old_topic_id[0]['topic'];

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
            'topic_id'=>json_decode($data['topic_info'])[0]->topic_id ? json_decode($data['topic_info'])[0]->topic_id: $old_topic_id,
            'ypoint' => $data['longitude'],
        ];
        $addid = model('XcxAdd');   //  创建新的发布数据
        $addid->data($addData);
        $addid->save();
        model('XcxCaogao')->where('id',$caogao_id)->delete();  // 删除旧的草稿数据

        //  取出旧的图片
        $img_data = model('XcxImgcaogao')->where('imgid',$caogao_id)->select();
        foreach ($img_data as $v){
            $img = model('XcxImg');
            $data = [
                'name'=>$v['name'],
                'imgid'=>$addid['id'],
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time())
            ];
            $img->data($data);
            $img->save();
        }

        // 删除旧的图片记录
        $accessKeyId = config('xcx.KeyId');
        $accessKeySecret = config('xcx.KeySecret');
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = config('xcx.Endpoint');
        $bucket = config('xcx.Bucket');
        $image = model('XcxImgcaogao')
            ->where('imgid', $caogao_id)
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
        model('XcxImgcaogao')->where('imgid',$caogao_id)->delete();

        $user_info = json_decode($data['user_info']);
        $at_state = false;
        foreach ($user_info as $k => $v){   //  更新旧的@数据的数据
            $state = model('XcxUserAt')->where('add_id','caogao'.$caogao_id)->where('to_user_id',$v->user_id)->select();
            if (!$state){
                $datas =[
                    'user_id'=>$data['id'],
                    'to_user_id'=>$v->user_id,
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'update_time'=>date('Y-m-d H:i:s',time()),
                    'add_id' => $addid['id']
                ];
                model('XcxUserAt')->data($datas)->save();
                $at_state = true;
            }else{
                model('XcxUserAt')
                    ->where('add_id','caogao'.$caogao_id)
                    ->where('to_user_id',$v->user_id)
                    ->update([
                        'update_time'=>date('Y-m-d H:i:s',time()),
                        'add_id' => $addid['id']
                    ]);
                $at_state = true;
            }
        }
        if ($at_state!=true&&preg_match('/@[^#@\s]*[\s]{1}/',input('description'))){
            $old_at = model('XcxUserAt')->where('add_id','caogao'.$caogao_id)->select();
            foreach ($old_at as $v){
                $datass = [
                    'user_id'=>$v['user_id'],
                    'to_user_id'=>$v['to_user_id'],
                    'add_id'=>$addid['id']
                ];
                model('XcxUserAt')->where('id',$v['id'])->update($datass);
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
}