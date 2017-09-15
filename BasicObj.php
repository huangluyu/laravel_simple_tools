<?php
/**
 * Created by PhpStorm.
 * User: 85251
 * Date: 2017/7/26
 * Time: 11:27
 */

namespace App\Traits;


trait BasicObj
{
    /**
     * 转变字符串与数组
     * @param $input
     * @param bool $handle
     * @return mixed
     */
    public function convertArrayObject($input, $handle = false)
    {
        if (($handle == false && is_array($input)) || ($handle == true && !is_array($input))) {
            return json_decode(json_encode($input), $handle);
        }
        return $input;
    }

    /**
     * 获取一个空对象
     * @return object
     */
    public function getNullObj()
    {
        return (object)array();
    }
}