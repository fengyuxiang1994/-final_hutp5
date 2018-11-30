<?php
/**
 * Created by PhpStorm.
 * User: Administration
 * Date: 2018/11/30
 * Time: 10:15
 */
namespace app\api\controller;

use think\Controller;


class Formid extends Controller
{
    public function collectFormId(){
        $user_id = input('user_id');
        $form_id = input('form_id');
    }
}