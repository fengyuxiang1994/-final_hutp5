<?php
namespace app\bas\controller;
use think\Controller;
use think\Request;

class Register extends  Controller
{
	
    public function index()
    {
    	$citys = model('City')->getCity();
        $categorys = model('Category')->getCategory();
    	return $this->fetch('', ['citys' => $citys, 'categorys' => $categorys ]);
    }

    public function add()
    {
    	if (!request()->isPost()) {
    		$this->error('请求错误');	
        }

        $data = input('post.');

        $validate = validate('Bas');
        if(!$validate->scene('add')->check($data)) {
            $this->error($validate->getError());
        }
        $lenlat = \Map::getLngLat($data['address']);
        // if(is_array($lenlat)){
        if (empty($lenlat) || $lenlat['status'] != 0 ||$lenlat['result']['precise'] != 1 ) {
            $this->error('无发获取数据，或者是填写的地址不够详细');
        }
        // }
        $accountRequest = model('BasAccount')->get(['username' => $data['username']]);

        if ($accountRequest) {
            $this->error('该用户已注册');
        }
        
        $basData = [
            'name' => htmlentities($data['name']),
            'city_id' => $data['city_id'],
            'city_path' => empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
            'logo' => $data['logo'],
            'licence_logo' => $data['licence_logo'],
            'description' => empty($data['description']) ? '' : $data['description'],
            'bank_info' => $data['bank_info'],
            'bank_name' => $data['bank_name'],
            'bank_user' => $data['bank_user'],
            'faren_tel' => $data['faren_tel'],
            'faren' => $data['faren'],
            'email' => $data['email'],
        ];
        // $file = request()->file('logo');
        // dump($file);
        // // , ROOT_PATH . 'public' . DS . 'uploadsh'
        // getImageInfo($file);exit;
        $basid = model('Bas')->add($basData);
       
        //总店信息入库
        $data['cat'] = '';

        if (!empty($data['se_category_id'])) {
          $data['cat'] = implode("|", $data['se_category_id']);   
        }

        $locationData = [
            'bis_id' => $basid,
            'name' => $data['name'],
            'tel' => $data['tel'],
            'contact' => $data['contact'],
            'category_id' => $data['category_id'],
            'category_path' => $data['category_id']. ',' . $data['cat'],
            'city_id' => $data['city_id'],
            'city_path' => empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'].','.$data['se_city_id'],
            'logo' => $data['logo'],
            'address' => $data['address'],
            'api_address' => $data['address'],
            'open_time' => $data['open_time'],
            'content' => empty($data['content']) ? '' : $data['content'],
            'is_main' => 1,//1代表是总店
            'xpoint' => empty($lenlat['result']['location']['lng']) ? '' : $lenlat['result']['location']['lng'],
            'ypoint' => empty($lenlat['result']['location']['lat']) ? '' : $lenlat['result']['location']['lat'],
        ];
         
        $basLocationid = model('BasLocation')->add($locationData);
        //账号信息
        $data['code'] = mt_rand(100, 10000);
        $accountData = [
            'bis_id' => $basid,
            'username' => $data['username'],
            'code' => $data['code'],
            'password' => md5($data['password'].$data['code']),            
            'is_main' => 1,//1代表是总管理员
        ];
        
        $basAccountid = model('BasAccount')->add($accountData);

        if (!$basAccountid) {
            $this->error('申请失败');
        }

        $url = request()->domain().url('bas/register/waiting',['id' => $basid]);
        $title = "h申请通知";
        // $message = "<a>" ."hh"."</a>";
        $message ="你提交的申请需要等待平台审核, 你可以通过点击链接<a href='".$url."' target='_blank'>查看链接</a>查看审核状态";
        // $data['email'] = '2543745353@qq.com';
        \phpmailer\Email::send($data['email'],$title,$message);

        $this->success('申请入驻成功 登录邮箱查看审核结果', url('register/waiting', ['id' => $basid]));
    }

    public function waiting($id)
    {
        // 若变量已存在、非空字符串或者非零，则返回 false 值 ,反侧为true；
        if (empty($id)) {
            $this->error('error');
        }
         $detil = model('Bas')->get($id);

        return $this->fetch("", [
            'detil' => $detil,
            ]);
        
    }

}  