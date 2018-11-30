<?php
namespace app\common\model;

use think\Model;

class XcxCaogao extends BasModel
{
    /**
     * 重写add方法
     */
    public function add($data)
    {
        $data['status']=1;
        $this->save($data);
        return $this->id;
    }

    /**
     * 在后台查看发布信息
     */
    public function getAddIndex()
    {
        $data['status'] = 1;
        $order = ['id' => 'desc'];
        $return = $this->where($data)
            ->order($order)
            ->paginate(2,false,['query'=>request()->param()]);
        // echo $this->getLastSql();
        return $return;
    }

    public function getFirst($limit, $lastid)
    {
        if($lastid>0){
            $lastid =  $lastid;

            $data = [
                'id' => ['lt', $lastid],
                'status' => 1,
            ];
        }else{
            $data = [
                'status' => 1,
            ];
        }

        $order = [
            'id' => 'desc',
        ];

        $return = $this->where($data)
            ->order($order)
            ->paginate($limit);
        // ->limit($limit)->select();
        // echo $this->getLastSql();
        return $return;
    }
    /**
     * 小程序获取详情信息
     */
    public function getXXXcx($id)
    {
        $data = ['id' => $id];
        return $this->where($data)
            ->find();
    }
}