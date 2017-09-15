<?php
/**
 * Created by PhpStorm.
 * User: 85251
 * Date: 2017/7/27
 * Time: 16:36
 */

namespace App\Traits;


Trait Curl
{
    public function curl($url, $method = 'GET', $data = array(), $returnType = true)
    {
        $ch = curl_init();
        //设置选项，包括URL
        if($method == 'post' || $method == 'POST'|| $method == 'Post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            $str = '';
            foreach ($data as $key => $value) {
                $str .= $key . "=" . $value . "&";
            }
            curl_setopt($ch, CURLOPT_URL, $url."?&".$str);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return json_decode($output, $returnType);
    }

    public function curlGet($url, $data = array(), $returnType = true)
    {
        $ch = curl_init();
        //设置选项，包括URL
        $str = '';
        foreach ($data as $key => $value) {
            $str .= $key . "=" . $value . "&";
        }
        curl_setopt($ch, CURLOPT_URL, $url."?&".$str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return json_decode($output, $returnType);
    }

    public function curlPost($url, $data = array(), $returnType = true)
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return json_decode($output, $returnType);
    }

    //请求外部接口
    public function curlWithStatus($url, $method = 'GET', $data = array(), $returnType = true)
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method=='post'||$method=='POST'||$method=='Post'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //获取请求状态
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //释放curl句柄
        curl_close($ch);
        if ($returnType) {
            $result = (object)array();
            $result->http_status = $http_status;
            $result->data = json_decode($output, false);
        } else {
            $result = [];
            $result['http_status'] = $http_status;
            $result['data'] = json_decode($output, true);
        }
        return $result;
    }
}