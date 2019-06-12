<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\6\13 0013
 * Time: 6:25
 */
namespace app\common\lib;

class StringUnit
{
    /**
     * 生成随机字符串
     * @param int $len 字符串的长度
     * @param bool $only_math 是否字符串仅仅是数字，false 是字母加数字
     * @return string
     */
    public static function randCode($len = 6, $only_math = false)
    {
        $library = "0123456789abcdefghijklmnpqrstuvwxyzABCDEFJHIJKLMNPQRSTUVWXYZ";
        $library_math = "0123456789";

        if (!is_int($len) || (integer)$len < 1 ) {
            $len = 6; // 传参的不符合条件，转换
        }
        $lib = !$only_math ? $library : $library_math;
        $max_len = strlen($lib) - 1;
        $string = '';
        for ($i = 0; $i < $len ;$i++) {
            $string .= $lib[mt_rand(0,$max_len)];
        }
        return $string;
    }
}