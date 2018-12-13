<?php
namespace app\admin\controller;
use think\Controller;

class Add extends Controller
{
	public function index()
    {
    	$add = model("XcxAdd")->getAddIndex();
    	foreach ($add as &$voo) {
    		$id =$voo['id'];
    		$imgd= model("XcxImg")->seleIndex($id);
    		$uu = [];
	    	foreach ($imgd as &$vo) {
	    		//array_push($uu, 'http://www.xcx.com'.$vo['name']);
                $uu[] = $vo['name'];
	    	}
	    	$voo['image'] = $uu;	
    	}
        // dump($add);
    	return $this->fetch('', [
    	   'add' => $add,
        ]);   
    }

    public function look() {
	    $id = input('id');
        $data['issue_id'] = $id;
        $order = ['create_date' => 'desc'];
        $add = model('XcxComment')
            ->where($data)
            ->order($order)
            ->paginate(20,false,['query'=>request()->param()]);
        return $this->fetch('', [
            'add' => $add,
        ]);
    }

    public function delete_add() {
	    $id = input('id');
	    $status = input('status');
	    $info = model('XcxAdd')->where('id',$id)->update(['status'=>'-1']);
        $this->success('删除成功', 'add/index');
    }

}
