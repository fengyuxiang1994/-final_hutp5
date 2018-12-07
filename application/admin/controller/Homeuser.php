<?php
namespace app\admin\controller;
use think\Controller;

class Homeuser extends Controller
{
	public function index()
    {
    	$user = model("XcxUser")->getUserIndex();
    	// dump($user);
    	return $this->fetch('', [
    	   'user' => $user,
        ]);
    }
    public function looking()
    {
        $id = input('id');
        $data['status'] = 1;
        $order = ['create_time' => 'asc'];
        $add = model('XcxAdd')->where($data)
            ->where('user_id',$id)
            ->order($order)
            ->paginate(20,false,['query'=>request()->param()]);
        // echo $this->getLastSql();
        foreach ($add as &$voo) {
            $id =$voo['id'];
            $imgd= model("XcxImg")->seleIndex($id);
            $uu = [];
            foreach ($imgd as &$vo) {
                $uu[] = $vo['name'];
            }
            $voo['image'] = $uu;
        }
        // dump($add);
        return $this->fetch('', [
            'add' => $add,
        ]);
    }

}
