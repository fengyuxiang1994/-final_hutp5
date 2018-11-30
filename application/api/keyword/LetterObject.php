<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8
 * Time: 17:42
 */
namespace app\api\keyword;
header("Content-type:text/html; charset=utf-8");

class LetterObject
{
    public $value;
    public $frequency;

    public function __construct($value)
    {
        $this->value = $value;
        $this->frequency = 1;
    }
}
