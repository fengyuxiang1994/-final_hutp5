<?php
namespace app\admin\controller;
use think\Controller;
class Bas extends  BasController
{
    /**
     * 商家成功审核
     */
    public function index()
    {  
       $bas = $this->objbas->getBas(1);
       // dump($bas);
       return $this->fetch('', [
          'bas' => $bas,
        ]); 
    }
    /**
     * 入驻申请列表
     */
    public function apply()
    {  
       $bas = $this->objbas->getBas();
       // dump($bas);exit;
       return $this->fetch('', [
          'bas' => $bas,
        ]); 
    }


    /**
     * 查看商家内容
     */
    public function detil()
    {
      $id = input('get.id');
      if (empty($id)) {
         $this->error('ID错误');  
      }
      $citys = $this->objcity->getCity();
      $categorys =  $this->objcategory->getCategory();
      $basData = $this->obj->get($id);
      $accountData = $this->objbasaccount->get([
        'bis_id' => $id, 
        'is_main' => 1, 
        ]);
      $locationData = $this->objbaslocation->get([
        'bis_id' => $id, 
        'is_main' => 1, 
        ]);

      return $this->fetch('', [
        'citys' => $citys, 
        'categorys' => $categorys, 
        'basData' => $basData, 
        'accountData' => $accountData, 
        'locationData' => $locationData, 
        ]);
    }
    /**
     * 从数据库删除审核不通过的商家
     */

    public function delete()
    {
      $data = input('get.');
      // $res = $this->objbas->save(['status' => $data['status']], ['id' => $data['id']]); 
      // $resaccountData = $this->objbasaccount->save(['status' => $data['status']], ['bis_id' => $data['id']], ['is_main' => 1]);
      // $reslocationData = $this->objbaslocation->save(['status' => $data['status']], ['bis_id' => $data['id']], ['is_main' => 1]);
    }

    public function deleteapply()
    {
      $deletBas =  $this->objbas->getDeletBas();
       // dump($deletBas);
       return $this->fetch('', [
          'deletBas' => $deletBas,
        ]); 
    }
}
