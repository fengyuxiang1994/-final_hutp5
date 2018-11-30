<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 15:19
 */
/**
 * 禁词过滤
 * 执行效率：每篇用时0.05秒
 * @author liuxu
 *
 */
namespace app\api\keyword;
class Logic_BlackWord
{

    const APP_FORUM = 1;
    const APP_BLOG  = 2;
    const APP_VOTE  = 3;

    /**
     * 过滤得到禁词
     * @param unknown $txt
     * @return Ambigous <multitype:, unknown>
     */
    public function getHitList($txt)
    {
        $hitList = array();

        //对禁词分批过滤
        $max = $this->getMax();
        if($max)
        {
            $size = 1000;
            $last = ceil($max/$size);
            for($page=1;$page<=$last;$page++)
            {
                $result = $this->getHitListByPage($txt,$page,$size);
                if($result) $hitList = array_merge($hitList,$result);
            }
        }

        $hitList2 = array();
        foreach($hitList as $hit=>$type)
        {
            $hitList2[$type][] = $hit;
        }

        return $hitList2;
    }

    private function getMax()
    {
        $redis = Rds::factory();
        $memKey = 'blackWord_max';
        $max = $redis->get($memKey);
        if($max===false)
        {
            $max = 0;
            $blackWord = new Model_BlackWord_BlackWord();
            $para['field'] = "MAX(id) AS max";
            $result = $blackWord->search($para);
            if(isset($result[0]['max'])) $max = $result[0]['max'];

            $redis->setex($memKey,300,$max);
        }

        return $max;
    }

    /**
     * 分批过滤得到禁词
     * @param unknown $txt
     * @param number $page
     * @param number $size
     * @return multitype:Ambigous <multitype:unknown, multitype:arr >
     */
    private function getHitListByPage($txt,$page=1,$size=1000)
    {
        $hitList = array();

        //分批得到禁词树
        $wordTree = $this->getWordTreeByPage($page,$size);

        $txt = strip_tags($txt);
        $txt = preg_replace('/[^a-zA-Z0-9\\x{4e00}-\\x{9fa5}]/iu','',$txt);

        $len = mb_strlen($txt,'UTF-8');
        for($i=0;$i<$len;$i++)
        {
            $char = mb_substr($txt,$i,1,'UTF-8');
            if(isset($wordTree[$char]))
            {
                $result = $this->getHitListByTree(mb_substr($txt,$i,50,'UTF-8'),$wordTree);
                if($result)
                {
                    foreach($result as $hit=>$type)
                    {
                        $hitList[$hit] = $type;
                    }
                }
            }
        }

        return $hitList;
    }

    /**
     * 是否禁词
     * @param str $txt
     * @param arr $wordTree
     * @return multitype:unknown
     */
    private function getHitListByTree($txt,&$wordTree)
    {
        $len = mb_strlen($txt,'UTF-8');
        $point = & $wordTree;
        $hit = '';
        $hitList = array();
        for($i=0;$i<$len;$i++)
        {
            $char = mb_substr($txt,$i,1,'UTF-8');
            if(isset($point[$char]))
            {
                $hit .= $char;
                $point = & $point[$char];

                if(isset($point['type']))//匹配成功
                {
                    $hitList[$hit] = $point['type'];
                }
            }
            else
            {
                break;
            }

        }

        return $hitList;
    }

    /**
     * 分批得到禁词树
     * @param int $page
     * @param int $size
     * @return arr:
     */
    private function getWordTreeByPage($page=1,$size=1000)
    {
        $redis = Rds::factory();
        $memKey = 'blackWord_tree_'.$page.'_'.$size;
        $wordTree = $redis->get($memKey);
        if($wordTree===false)
        {
            $wordTree = array();
            $blackWord = new Model_BlackWord_BlackWord();
            $start = ($page-1)*$size;
            $end = $start + $size;
            $para['where'] = "status=1 AND id>".$start." AND id<=".$end;
            $result = $blackWord->search($para);
            if($result)
            {
                foreach($result as $value)
                {
                    if($value['word'])
                    {
                        $value['word'] = preg_split('/(?<!^)(?!$)/u',$value['word']);
                        $point = & $wordTree;
                        foreach($value['word'] as $char)
                        {
                            $point = & $point[$char];
                        }

                        $point['type'] = $value['type'];
                    }
                }
            }

            $redis->setex($memKey,300,$wordTree);
        }

        return $wordTree;
    }

}
