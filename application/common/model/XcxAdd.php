<?php

namespace app\common\model;

use think\Model;

class XcxAdd extends BasModel
{
    /**
     * 重写add方法
     */
    public function add($data)
    {
        $data['status'] = 1;
        $this->save($data);
        return $this->id;
    }

    /**
     * 在后台查看发布信息
     */
    public function getAddIndex()
    {
        $data['status'] = 1;
        $order = ['create_time' => 'asc'];
        $return = $this->where($data)
            ->order($order)
            ->paginate(20, false, ['query' => request()->param()]);
        // echo $this->getLastSql();
        return $return;
    }

    public function getFirst($limit, $lastid)
    {

        if ($lastid > 0) {
            $lastid = $lastid;

            $data = [
                'id' => ['lt', $lastid],
                'status' => 1,
            ];
        } else {
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

    public function getFirstyyyy($limit, $data = [])
    {
        //$order['id'] = 'desc';
        $order = [
            'listorder' => 'asc',
            'browse_num' => 'desc',
            'user_praise' => 'desc',
            'create_time' => 'desc',
        ];
        // $data['id'] = 94;

        if (!empty($data['id'])) {
            $datas[] = "id<" . $data['id'];
        }

        if (!empty($data['category_id'])) {
            $datas[] = 'category_id =' . $data['category_id'];
        }
        $datas[] = 'status= 1';
        $return = $this->where(implode(' AND ', $datas))
            ->where('city','like','%'. $data['city'].'%')
            ->order($order)
            ->paginate($limit);
        // echo $this->getLastSql(); 
        return $return;
    }

    public function quick_sort($limit, $data = [], $arr)
    {
        if (!$arr) {
            $arr = mt_rand(1, 10);
        }
        switch ($arr) {
            case 1:
                $order = [
                    'listorder' => 'asc',

                ];
                break;
            case 2:
                $order = [
                    'browse_num' => 'desc',
                ];
                break;
            case 3:
                $order = [
                    'user_praise' => 'desc',
                ];
                break;
            case 4:
                $order = [

                    'create_time' => 'desc',
                ];
                break;
            case 5:
                $order = [
                    'browse_num' => 'desc',
                    'create_time' => 'desc',
                    'listorder' => 'asc',

                ];
                break;
            case 6:
                $order = [
                    'user_praise' => 'desc',
                    'create_time' => 'asc',
                ];
                break;
            case 7:
                $order = [
                    'create_time' => 'desc',
                    'listorder' => 'asc',
                    'browse_num' => 'desc',
                ];
                break;
            case 8:
                $order = [
                    'user_praise' => 'desc',
                    'browse_num' => 'desc',
                ];
                break;
            case 9:
                $order = [
                    'create_time' => 'desc',
                    'listorder' => 'asc',
                    'user_praise' => 'desc',
                ];
                break;
            default:
                $order = [
                    'listorder' => 'asc',
                    'browse_num' => 'desc',
                    'user_praise' => 'desc',
                    'create_time' => 'desc',
                ];
        }

        if (!empty($data['id'])) {
            $datas[] = "id<" . $data['id'];
        }

        if (!empty($data['category_id'])) {
            $datas[] = 'category_id =' . $data['category_id'];
        }
        $datas[] = 'status= 1';

        $return = $this->where(implode(' AND ', $datas))
            ->order($order)
            ->paginate($limit);
        return $return;
    }

}