<?php

namespace app\api\controller;

use think\Controller;


class Home extends Controller
{
    //获取小程序的主页内容
    public function getPageContentApi()
    {
        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');

        $limit = input('get.limit', 4, 'intval');
        $homeData = model('XcxHome')->getFirst($limit, $lastid);
//        $homeData = model('XcxAdd')->getFirst($limit,$lastid);
        foreach ($homeData as $k => $v) {
//            $image = model('XcxImg')->where('imgid',$v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['r_image'] = 'http://192.168.100.224/hutp5/public' . $v['r_image'];
            $v['image'] = 'http://192.168.100.224/hutp5/public' . $v['image'];
        }

        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

    //获取小程序的主页内容
    public function getPageContentInfo()
    {
        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');
        //  获取分类id，按照分类取数据
        $category_id = input('category_id');
        $limit = input('get.limit', 4, 'intval');
//        $homeData = model('XcxHome')->getFirst($limit,  $lastid);
        $homeData = model('XcxAdd')->getFirst($limit, $lastid);
        shuffle($homeData);

        foreach ($homeData as $k => $v) {
            $image = model('XcxImg')->where('imgid', $v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['image'] = $image['name'];
        }
        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

    //获取小程序的主页内容
    public function getPageContentInfoxxggg()
    {
        // 显示查询数据几条
        $limit = input('get.limit', 4, 'intval');

        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');
        //  获取分类id，按照分类取数据
        $cat = input('get.cat', 0, 'intval');

        $data = [];
        $data['category_id'] = $cat;
        $data['id'] = $lastid;

        $homeData = model('XcxAdd')->getFirstyyyy($limit, $data);
        shuffle($homeData);

        foreach ($homeData as $k => $v) {
            $image = model('XcxImg')->where('imgid', $v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['image'] = $image['name'];
        }
        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }

    public function getFindApi()
    {
        $id = input('get.id', 0, 'intval');


        $xqData = model('XcxHome')->getXXXcx($id);


        // foreach ($xqData as $k => $v) {
        $xqData['r_image'] = 'http://192.168.100.224/hutp5/public' . $xqData['r_image'];
        $xqData['image'] = 'http://192.168.100.224/hutp5/public' . $xqData['image'];
        // }
        // dump($xqData);
        if (!$xqData) {
            return show(0, 'error');
        }
        return show(1, 'success', $xqData);
    }


    public function getComment()
    {
        $id = input('id');
        $user_id = input('user_id');
        if ($id == null && $user_id == null) {
            return '参数错误';
        }
        $setting = model('XcxPtSetting')->find();
        $data = model('XcxAdd')
            ->where('id', $id)
            ->find();
        if ($setting['browse_setting'] === 0) {
//            return $setting;
            model('XcxAdd')
                ->where('id', $id)->update(['browse_num' => $data['browse_num'] + 1]);
        } else {
            $num = rand(1, 10);
            model('XcxAdd')
                ->where('id', $id)->update(['browse_num' => $data['browse_num'] + $num]);
        }


//        $jinhao = model('XcxTopic')->where('')
        $at = model('XcxUserAt')
            ->field('to_user_id')
            ->where('add_id', $id)
            ->select();

        $ats = [];
        foreach ($at as $key => $value) {
            $user = model('XcxUser')->field('nickName,id')->where('id', $value['to_user_id'])->find();
            $ats[] = $user;
        }

        $image = model('XcxImg')
            ->field('name')
            ->where('imgid', $data['id'])
            ->select();

        $arr = [];
        foreach ($image as $key => $value) {
            $arr[] = $value['name'];
        }
        $data['jinhao'] = [];
        $data['image'] = $arr;
        $data['at'] = $ats;
//        $data['huati'] = $arr;
        $userinfo = model('XcxZan')
            ->where('user_id', $user_id)
            ->where('comment_id', $id)
            ->where('type', 1)
            ->find();
        if (empty($userinfo)) {
            $data['hasChange'] = false;
        } else {
            $data['hasChange'] = true;

        }

        $usercomment = model('XcxShoucang')
            ->where('user_id', $user_id)
            ->where('comment_id', $id)
            ->find();
        if (empty($userinfo)) {
            $data['hasChangesc'] = false;

        } else {
            $data['hasChangesc'] = true;

        }

        $guanzhuInfo = model('XcxUserguanzhu')->where('form_user_id', $user_id)->where('user_id', $data['user_id'])->find();
        if ($guanzhuInfo) {
            $data['guanInfo'] = true;
        } else {
            $data['guanInfo'] = false;
        }


        return $data;
    }

    //获取首页数据
    public function getHomeInfo()
    {
        $page = input('pages');
        $data = model('XcxAdd')->limit(($page - 1) * 10, 10)->select();
        foreach ($data as $key => $value) {
            $image = model('XcxImg')->where('imgid', $value['id'])->find();
            $value['image'] = $image['name'];
        }

        return $data;

    }

    //获取分类信息
    public function getClassInfo()
    {
        $classInfo = model('Category')
            ->where('status', 1)
            ->order('listorder', 'asc')
            ->select();
        return $classInfo;
    }

    //获取用户关注文章
    public function userGuanzhu()
    {
        $user_id = input('user_id');
        $data = model('XcxUserguanzhu')->where('form_user_id', $user_id)->select();
        $guanzhuInfo = [];
        foreach ($data as $key => $value) {
            $res = model('XcxAdd')->where('user_id', $value['user_id'])->select();

            foreach ($res as $k => $v) {
                $arr = [];
                $image = model('XcxImg')->where('imgid', $v['id'])->select();
                foreach ($image as $keys => $vla) {
                    $arr[] .= $vla['name'];
                }
                $v['image'] = $arr;
                $v['hasChange'] = 'false';
                $v['hasChangesc'] = 'false';

            }
            $guanzhuInfo[] = $res;
        }
        $guanzhu = [];
        foreach ($guanzhuInfo as $v => $k) {
            foreach ($k as $va => $ke) {
                array_push($guanzhu, $ke);

            }
        }
        $ctime_str = [];
        foreach ($guanzhu as $k => $va) {
//            $k['ctime_str'] = strtotime($va['update_time']);
//            $ctime_str[] = $guanzhu[$k]['ctime_Str'];
            $guanzhu[$k]['update_time'] = $va['update_time'];
            $ctime_str[] = $guanzhu[$k]['update_time'];
        }
        array_multisort($ctime_str, SORT_DESC, $guanzhu);
        return $guanzhu;
    }

    //我的草稿
    public function caogaoInfo()
    {
        $user_id = input('user_id');
        $data = model('XcxCaogao')
            ->where('user_id', $user_id)
            ->select();
        foreach ($data as $k => $v) {
            $image = model('XcxImgcaogao')
                ->field('name')
                ->where('imgid', $v['id'])
                ->find();
            $v['create_time'] = date("m/d", strtotime($v['create_time']));
            $v['image'] = $image['name'];
        }

        return $data;
    }

    //根据城市进行获取内容
    public function getCityHomeInfo()
    {
        // 显示查询数据几条
        $limit = input('get.limit', 10, 'intval');

        $lastid = input('get.lastid', 0, 'intval');
//        $user_id = input('user_id');
        //  获取分类id，按照分类取数据
        $cat = input('get.cat', 0, 'intval');

        $data = [];
        $data['category_id'] = $cat;
        $data['id'] = $lastid;
        $data['city'] = input('city');
//        $homeData = model('XcxAdd')->getFirstyyyy($limit, $data);
        $homeData = model('XcxAdd')->getFirstyyyy($limit, $data);
        // shuffle($homeData);

        foreach ($homeData as $k => $v) {
            $image = model('XcxImg')->where('imgid', $v['id'])->find();
//            $v['image'] = $image['name'];
            $v['hasChange'] = 'false';
            $v['image'] = $image['name'];
        }
        if (!$homeData) {
            return show(0, 'error');
        }
        return show(1, 'success', $homeData);
    }


    public function city()
    {
        $data = model('XcxCity')
            ->where('pid', 7)
            ->select();
        $datas = [];
        foreach ($data as $key => $value) {
            if ($value['id'] === 280 || $value['id'] === 279) {
                // $value['citys'] =[];
                $result[] = $value;
                $datas = array_merge($result, $datas);
            } else {
                $result = model('XcxCity')
                    ->where('pid', $value['id'])
                    ->select();
                //$value['citys'] = $result;
                $datas = array_merge($result, $datas);
            }
        }
        $res['China'] = $this->chartSort($datas);

        //国际
        $guoji = model('XcxCity')
            ->where('level', 1)
            ->select();
        $info = [];
        $infos = [];
        foreach ($guoji as $key => $value) {
            $results = model('XcxCity')
                ->where('pid', $value['id'])
                ->where('id', '<>', 7)
                ->where('level', 2)
                ->select();
            foreach ($results as $k => $v) {
                switch ($v['name']) {
                    case '韩国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }

                        $info[] = $arr;
                        break;
                    case '日本':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '菲律宾':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '越南':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '老挝':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '柬埔寨':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '缅甸':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '泰国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '马来西亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '印度尼西亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '印度':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '斯里兰卡':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '马尔代夫':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '俄罗斯':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '德国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '奥地利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '英国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '荷兰':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '比利时':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '法国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '意大利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '澳大利亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '新西兰':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '美国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '匈牙利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '阿联酋':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    default:

                        $infos[] = $v;
                }
            }
        }

        foreach ($info as $key => $value) {
            $res = array_merge($value, $infos);
        }
        $res['international'] = $this->chartSort($res);

        return $res;
    }


    /**
     * 将数组按字母A-Z排序
     * @return [type] [description]
     */
    public function chartSort($user)
    {
        foreach ($user as $k => &$v) {
            $v['chart'] = $this->getFirstChart($v['name_pinyin']);
        }
        $data = [];
        foreach ($user as $k => $v) {
            if (empty($data[$v['chart']])) {
                $data[$v['chart']] = [];
            }
            $data[$v['chart']][] = $v;
        }
        ksort($data);
        $datas = [];
        foreach ($data as $key => $value) {
            $datas[] = $value;
        }
        $res = [];
        foreach ($datas as $keys => $values) {
            $res[$keys]['initial'] = $values[0]['chart'];
            $res[$keys]['cityInfo'] = $values;
        }
        return $res;
    }

    /**
     * 返回取汉字的第一个字的首字母
     * @param  [type] $str [string]
     * @return [type]      [strind]
     */
    public function getFirstChart($str)
    {
        if (empty($str)) {
            return '';
        }
        $char = ord($str[0]);
        if ($char >= ord('A') && $char <= ord('z')) {
            return strtoupper($str[0]);
        }
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return null;
    }

    /**
     * 格式化打印函数
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function p($str)
    {
        echo '<pre>';
        print_r($str);
        echo '</pre>';
    }

    public function citys()
    {
//    $data = model('XcxCity')
//            ->where('pid',7)
//            ->select();
//    $datas=[];
//       foreach ($data as $key=>$value){
//         if($value['id'] === 280 || $value['id'] === 279){
//          // $value['citys'] =[];
//           $result[] = $value;
//            $datas= array_merge($result,$datas);
//         }else{
//         	 $result =model('XcxCity')
//                ->where('pid',$value['id'])
//                ->select();
//            //$value['citys'] = $result;
//            $datas= array_merge($result,$datas);
//         }
//    	}

        $guoji = model('XcxCity')
            ->where('level', 1)
            ->select();
        $info = [];
        $infos = [];
        foreach ($guoji as $key => $value) {
            $results = model('XcxCity')
                ->where('pid', $value['id'])
                ->where('id', '<>', 7)
                ->where('level', 2)
                ->select();
            foreach ($results as $k => $v) {
                switch ($v['name']) {
                    case '韩国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }

                        $info[] = $arr;
                        break;
                    case '日本':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '菲律宾':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '越南':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '老挝':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '柬埔寨':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '缅甸':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '泰国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '马来西亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '印度尼西亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '印度':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '斯里兰卡':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '马尔代夫':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '俄罗斯':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '德国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '奥地利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '英国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '荷兰':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '比利时':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '法国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '意大利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '澳大利亚':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '新西兰':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '美国':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
                        }
                        $info[] = $arr;
                        break;
                    case '匈牙利':
                        $arr = model('XcxCity')
                            ->where('pid', $v['id'])
                            ->select();
                        foreach ($arr as $keys => $values) {
                            $values['name'] = $values['name'] . '(阿联酋)';
                        }
                        $info[] = $arr;
                        break;
//                    case '阿拉伯联合酋长国':
//                        $arr = model('XcxCity')
//                            ->where('pid', $v['id'])
//                            ->select();
//                        foreach ($arr as $keys => $values) {
//                            $values['name'] = $values['name'] . '(' . $v['name'] . ')';
//                        }
//                        $info[] = $arr;
//                        break;
                    default:
                        if($v['name'] === '阿拉伯联合酋长国'){
                            $v['name'] = '阿联酋';
                        }
                        $infos[] = $v;
                }
            }
        }
        $arr =[];
        foreach ($info as $key => $value) {
            foreach($value as $ke =>$v){
                $arr[] = $v;
            }
        }
        foreach ($infos as $k => $val) {
            $arr[] = $val;
        }

        $res['international'] = $this->chartSort($arr);
        return $res['international'];
    }

}
