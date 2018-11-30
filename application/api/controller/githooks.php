<?php
/**
 * Created by PhpStorm.
 * User: hu
 * Date: 2018/11/17
 * Time: 15:38
 */

namespace app\api\controller;
use http\Env\Request;
use think\Controller;

class githooks extends Controller
{
    public function push(Request $request){
        exec('cd /www/wwwroot/hutp5 && git pull origin master');
    }
}