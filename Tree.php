<?php

/**
 * Created by PhpStorm.
 * User: hly852519235
 * Date: 2017/6/6
 * Time: 下午 01:57
 */
namespace App\Http\Traits;

trait Tree
{
    private function getRecursionSimpleInfo($parId, $getInfoByParId, $getText) {
        $childInfos = $getInfoByParId($parId);
        $infoArray = [];
        foreach ($childInfos as $cInfo) {
            $info['text'] = $getText($cInfo);
            $info['attr']['id'] = $cInfo->id;
            $children = $this->getRecursionSimpleInfo($cInfo->id, $getInfoByParId, $getText);
            if(count($children) > 0) {
                $info['type'] = 'folder';
                $info['attr']['children'] = $children;
            } else {
                $info['type'] = 'item';
            }
            $infoArray[] = $info;
        }
        return $infoArray;
    }
}