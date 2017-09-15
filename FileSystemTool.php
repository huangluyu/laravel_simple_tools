<?php
/**
 * Created by PhpStorm.
 * User: 85251
 * Date: 2017/8/18
 * Time: 10:19
 */

namespace App\Traits;


trait FileSystemTool
{
    /*
     * 递归创建目录
     *
     * 需要传递相应路径
     * @param $path
     * @return bool
     */
    public function mkDirs($path)
    {
        if(is_dir($path)){//已经是目录了就不用创建
            return true;
        }
        if(is_dir(dirname($path))){//父目录已经存在，直接创建
            return mkdir($path,0777,true);
        }
        $this->mkDirs(dirname($path));//从子目录往上创建
        return mkdir($path,0777,true);//因为有父目录，所以可以创建路径
    }
}