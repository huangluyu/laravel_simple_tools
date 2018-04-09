<?php
/**
 * Created by PhpStorm.
 * User: hly852519235
 * Date: 2017/3/10
 * Time: 下午 12:58
 */
namespace App\Http\PublicTools;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
trait eloquentC
{
    /**
     * 日志方法封装
     * @param Request $request 前端请求
     * @param $type 1登录2增加3删除4修改5查看6搜索
     * @param $logInfo 日志信息
     * @param $relate_table 操作数据表名
     * @param $result 操作状态(成功/失败)
     */
    private function addOperateLog(Request $request, $type, $logInfo, $relate_table, $result)
    {
        operateLog::create([
            'created_time' => Carbon::now(),
            'type' => $type,
            'operator' => $request->user()->user_id,
            'operate_content' => $logInfo,
            'relate_table' => $relate_table,
            'result' => $result
        ]);
    }
    /**
     * 数据表操作使用示例
     * @param Request $request 前端请求(page 选定页面, searchInfo 搜索栏搜索信息, orderInfo 排序信息, singlePageProNum 单页显示个数)
     * @return view 视图和信息
     *
     */
    private function forExample(Request $request)
    {
        // 默认单页显示数量
        $DEFAULT_SINGLE_PAGE_NUM = 10;
        // 获取基础拼接筛选数据集合
        $dbCollection = DB::table('exampleDB')->select(DB::raw('*'));
        $page = isset($request->page) ? $request->page : 1;
        $searchInfo = $request->searchInfo;
        // 手动设置允许排序字段的列表
        $allowInfoList = array('allowFiled1', 'allowFiled2', 'allowFiled3');
        if(count($allowInfoList) > 0) {
            // 将从request全部信息里检索允许排序字段的排序信息
            foreach ($request->all() as $orderInfo => $state) {
                if (in_array($orderInfo, $allowInfoList)) {
                    // 手动设置排序时是否默认反向查询
                    $orderInfos[$orderInfo] = $state ? 0 : 1;
                }
            }
        }
        $singlePageNum = isset($request->singlePageProNum) ? $request->singlePageProNum : $DEFAULT_SINGLE_PAGE_NUM;
        if(isset($searchInfo)) {
            // 手动设置搜索内容
            $searchInfos =
                array(
                    'field1' => $searchInfo,
                    'field2' => $searchInfo
                );
            // 拼接模糊搜索信息筛选
            $dbCollection = $this->searchSet($dbCollection, $searchInfos);
        }
        if(isset($orderInfos)) {
            // 拼接排序信息筛选
            $dbCollection = $this->orderSet($dbCollection, $orderInfos);
        }
        // 获取总页数
        $pageCount = $this->getPageNum($dbCollection->get(), $singlePageNum);
        // 拼接页数筛选
        $dbCollection = $this->pageSet($dbCollection, $page, $singlePageNum);
        // 用get获取筛选后的数组
        $infoList = $dbCollection->get();
        return view('bladeAddress', ['infoList' => $infoList, 'page_number' => $pageCount]);
    }
    private function forExample2(Request $request)
    {
        // 默认单页显示数量
        $DEFAULT_SINGLE_PAGE_NUM = 10;
        $page = isset($request->page) ? $request->page : 1;
        $singlePageNum = isset($request->singlePageProNum) ? $request->singlePageProNum : $DEFAULT_SINGLE_PAGE_NUM;
        // 获取基础拼接筛选数据集合
        $allowOrderField = ['allowFiled1', 'allowFiled2', 'allowFiled3'];
        $allowSearchField = ['allowFiled1', 'allowFiled2', 'allowFiled3'];
        $dbCollection = DB::table('exampleDB')->select(DB::raw('*'))
            ->when(true, function ($query) use ($request, $allowOrderField) {
                return $this->orderSet2($query, $request->all(), $allowOrderField);
            })
            ->where(function ($query) use ($request, $allowSearchField){
                $this->searchSet2($query, $request->keyword, $allowSearchField);
            })
            ->where(function ($query) use ($request) {
                $startTime = $request->startTime;
                if (strlen($startTime) > 0) {
                    $query->where('delay_delivery.send_time', '>=', $startTime);
                }
            })
            ->where(function ($query) use ($request) {
                $endTime = $request->endTime;
                if (strlen($endTime) > 0) {
                    $date = date("Y-m-d", strtotime($endTime . "+1 day"));
                    $query->where('delay_delivery.send_time', '<', $date);
                }
            });
        $pageCount = $this->getPageNum($dbCollection->get(), $singlePageNum);
        $dbCollection = $this->pageSet($dbCollection, $page, $singlePageNum);
        $infoList = $dbCollection->get();
        return view('bladeAddress', ['infoList' => $infoList, 'page_number' => $pageCount]);
    }
    /**
     * 获取目标页数的数据表Eloquent Collection
     * @param  $dbCollection 待分页集合; int $page 页数; int $singlePageNum 单页数据个数
     * @return $dbCollection 分页结果
     *
     */
    private function pageSet($dbCollection, $page, $singlePageNum)
    {
        return $dbCollection
            ->skip($singlePageNum * ($page - 1))
            ->take($singlePageNum);
    }
    /**
     * 获取数据集合的总页数信息
     * @param  $data 待分页总集合; int $singlePageNum 单页个数
     * @return $page_number 总页数信息
     *
     */
    private function getPageNum($data, $singlePageNum)
    {
        return count($data) ? ceil(count($data) / $singlePageNum ) : 1;
    }
    /**
     * 获取排序后的数据表Eloquent Collection
     * @param  $dbCollection 待排序集合; Array $orderInfos 排序数组($orderInfo 排序字段, $orderDesc 排序方向)
     * @return $dbCollection 排序结果
     *
     */
    private function orderSet($dbCollection, Array $orderInfos)
    {
        if(count($orderInfos) > 0) {
            //$searchInfos = verifyInfoIsExist($dbCollection, $searchInfos); // 判断该字段是否存在
            foreach($orderInfos as $orderInfo => $state)
            {
                $orderDesc = isset($state) ? $state : 0;
                if($orderDesc == 1) {
                    $dbCollection = $dbCollection->orderBy($orderInfo);
                } else if($orderDesc == 2){
                    $dbCollection = $dbCollection->orderBy($orderInfo, 'desc');
                }
            }
        }
        return $dbCollection;
    }
    private function orderSet2($dbCollection, Array $requestAll, $allowOrderField)
    {
        foreach ($requestAll as $orderInfo => $state) {
            if (in_array($orderInfo, $allowOrderField) && strlen($state) > 0) {
                $orderInfos[$orderInfo] = $state;
            }
        }
        if(isset($orderInfos)) {
            return $this->orderSet($dbCollection, $orderInfos);
        } else
            return $dbCollection;
    }
    /**
     * 获取模糊搜索后的数据表Eloquent Collection
     * @param  $dbCollection 待筛选集合; Array $searchInfos 搜索数组($searchfields 搜索字段, $searchInfo 排序内容)
     * @return $dbCollection 模糊搜索结果
     *
     */
    private function searchSet($dbCollection, Array $searchInfos)
    {
        if(count($searchInfos) >= 1) {
            $firstKey = false;
            foreach($searchInfos as $searchfields => $searchInfo) {
                if($firstKey) {
                    $dbCollection = $dbCollection->where($searchfields, 'like', '%' . $searchInfo . '%');
                    $firstKey = true;
                } else
                    $dbCollection = $dbCollection->orWhere($searchfields, 'like', '%' . $searchInfo . '%');
            }
        }
        return $dbCollection;
    }
    private function searchSet2($dbCollection, $keyword, $allowSearchField)
    {
        if (isset($keyword) && strlen($keyword) > 0) {
            foreach ($allowSearchField as $searchInfo) {
                $keywords[$searchInfo] = $keyword;
            }
            $this->searchSet($dbCollection, $keywords);
        }
    }
    /**
     * 从检索信息中排除不存在或非法的字段
     * @param  $dbCollection 数据集合; Array $infos 待筛选检索数组($fieldName 检索字段名, $info 检索信息)
     * @return $checkedInfos 筛选后的检索数组
     *
     */
    private function verifyInfoIsExist($dbCollection, Array $infos)
    {
        $allowFieldList = $this->getExistFieldList($dbCollection);
        $checkedInfos = array();
        foreach ($infos as $fieldName => $info) {
            if(in_array($fieldName, $allowFieldList))
                $checkedInfos[$fieldName] = $info;
        }
        return $checkedInfos;
    }
    /**
     * 获取该数据集合中所有字段组成的数组
     * @param $dbCollection 数据集合
     * @return array $allowFieldList 字段数组
     *
     */
    private function getExistFieldList($dbCollection) {
        $dbArray = get_object_vars($dbCollection->first());
        $allowFieldList = array();
        foreach ($dbArray as $allowInfo => $uselessInfo) {
            $allowFieldList = $allowInfo;
        }
        return $allowFieldList;
    }
    /**
     * 以该对象数组中对象的指定字段为key, 对象为value建立一个数组
     * @param $objArray $dbCollection->get() 对象数组
     * @param $fieldName key字段名
     * @param bool $coverKey 允许同名后者覆盖前者
     * @return array
     */
    private function getKVArrayByFieldName($objArray, $fieldName, $coverKey = true)
    {
        $objKVArray = array();
        if($coverKey) {
            foreach ($objArray as $obj) {
                $objKVArray[$obj->{$fieldName}] = $obj;
            }
        } else {
            foreach ($objArray as $obj) {
                if(!isset($objKVArray[$obj->{$fieldName}]))
                    $objKVArray[$obj->{$fieldName}] = $obj;
            }
        }
        return $objKVArray;
    }
}
