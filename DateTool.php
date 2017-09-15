<?php
/**
 * Created by PhpStorm.
 * User: 85251
 * Date: 2017/8/9
 * Time: 19:23
 */

namespace App\Traits;


trait DateTool
{
    // 获取工作日天数
    public function getWeekDayNum($timeGroup, $date)
    {
        $dateStartEnd = $this->getFirstLastDay($timeGroup, $date);
        $dateStart = $dateStartEnd['dateStart'];
        $dateEnd = $dateStartEnd['dateEnd'];
        $dayNum = (strtotime($dateEnd) - strtotime($dateStart)) / 86400 + 1;
        $leftDay = 0;
        $weekLeftDay = $dayNum % 7;
        if ($weekLeftDay > (8 - date('w', strtotime($dateStart)))) {
            $leftDay = $dayNum % 7 - 2;
        } elseif ($weekLeftDay > 0) {
            for ($i = 0; $i < $weekLeftDay; $i++) {
                if (!in_array(date('w', strtotime($dateStart."+$i day")), ['6', '0']))
                    $leftDay++;
            }
        }
        $weekDayNum = (int)floor($dayNum / 7) * 5 +  $leftDay;
        return $weekDayNum;
    }

    // 判断今天是否是工作日 是工作日返回true
    public function isWeekDay($date)
    {
        return in_array(date('w', strtotime($date)), ['1', '2', '3', '4', '5']);
    }

    public function getFirstLastDay($timeGroup, $date)
    {
        if ($timeGroup == 'week') {
            $dateEnd = $this->getWeekLastDay($date);
            $dateStart = $this->getWeekFirstDay($date);
            $monthStart = $this->getMonthFirstDay($date);
            $monthEnd = $this->getMonthLastDay($date);
            if (strtotime($monthStart) > strtotime($dateStart))
                $dateStart = $monthStart;
            if (strtotime($monthEnd) < strtotime($dateEnd))
                $dateEnd = $monthEnd;
        } elseif ($timeGroup == 'trueWeek') {
            $dateEnd = $this->getWeekLastDay($date);
            $dateStart = $this->getWeekFirstDay($date);
        } elseif ($timeGroup == 'month') {
            $dateEnd = $this->getMonthLastDay($date);
            $dateStart = $this->getMonthFirstDay($date);
        } elseif ($timeGroup == 'year') {
            $dateEnd = $this->getYearLastDay($date);
            $dateStart = $this->getYearFirstDay($date);
        } elseif ($timeGroup == 'day') {
            $dateEnd = date("Y-m-d", strtotime($date));
            $dateStart = date("Y-m-d", strtotime($date));
        } else {
            $dateStart = null;
            $dateEnd = null;
        }
        return [
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd
        ];
    }

    public function getYear($date)
    {
        return date('Y', strtotime($date));
    }

    public function getWeekFirstDay($date)
    {
        return date('Y-m-d', strtotime("$date sunday - 6 day"));
    }

    public function getWeekLastDay($date)
    {
        return date('Y-m-d', strtotime("$date sunday"));
    }

    public function getMonthFirstDay($date)
    {
        return date('Y-m-01', strtotime($date));
    }

    public function getMonthLastDay($date)
    {
        $beginDate = $this->getMonthFirstDay($date);
        return date('Y-m-d', strtotime("$beginDate +1 month -1 day"));
    }

    public function getYearFirstDay($date)
    {
        if (strlen($date) <= 4)
            $date = "$date-1-1";
        elseif (strlen($date) <= 7)
            $date = "$date-1";
        return date('Y-1-1', strtotime($date));
    }

    public function getYearLastDay($date)
    {
        if (strlen($date) <= 4)
            $date = "$date-1-1";
        elseif (strlen($date) <= 7)
            $date = "$date-1";
        $beginDate = $this->getYearFirstDay($date);
        return date('Y-m-d', strtotime("$beginDate +1 year -1 day"));
    }

    public function getMonth($date)
    {
        return date('Y-m', strtotime($date));
    }

    public function compareDateMonth($date1, $date2)
    {
        return $this->getMonth($date1) === $this->getMonth($date2);
    }

    public function getDaysAgo($date, $day)
    {
        if ($day > 0)
            return date('Y-m-01', strtotime("$date - $day day"));
        else {
            $day = -$day;
            return date('Y-m-01', strtotime("$date + $day day"));
        }
    }

    public function getSixNAgo($timeGroup, $date)
    {
        if ($timeGroup == 'day') {
            $dateEnd = date("Y-m-d", strtotime($date));
            $dateStart = date("Y-m-d", strtotime($date.'-5day'));
        } elseif ($timeGroup == 'week') {
            $dateEnd = $this->getWeekLastDay($date);
            $dateStart = date('Y-m-d', strtotime("$date sunday - 41 day"));
        } elseif ($timeGroup == 'month') {
            $dateEnd = $this->getMonthLastDay($date);
            $dateStart = date('Y-m-01', strtotime("$date - 5 month"));
        } else {
            $dateStart = null;
            $dateEnd = null;
        }
        return [
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd
        ];
    }
}