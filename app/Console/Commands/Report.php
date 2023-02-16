<?php

namespace App\Console\Commands;

use App\Models\CustomerLog;
use App\Models\CustomerReport;
use Illuminate\Console\Command;
use App\Models\SystemUser;

class Report extends Command
{
    protected $signature = 'report';
    protected $description = '报表';

    /**
     ** 执行控制台命令
     **/
    public function handle() {
        $data = [];
        $currentTime = date('Y-m-d');
        $day = date('d', strtotime($currentTime));
        $week = date('w', strtotime($currentTime));
        $lastMonth = date('Y-m-d', strtotime("$currentTime -1 month"));
        $lastWeek = date('Y-m-d', strtotime("$currentTime -1 week"));
        if ($day == 1) {
            $data[] = [ 'month', $lastMonth, $currentTime ];
        } 
        echo $week;
        if ($week == 1) {
            $data[] = [ 'week', $lastWeek, $currentTime ];
        }
        $newData = [];
        $model = new SystemUser();
        $userIds = array_column($model->getAllUser()->toArray(), 'id');
        foreach ($data as $item) {
            $type1 = $item[0];
            $startTime = $item[1];
            $endTime = $item[2];
            foreach ([
                1=>[CustomerLog::TYPE_ASSIGN_NEW],
                2=>[CustomerLog::TYPE_ASSIGN],
                4=>[CustomerLog::TYPE_IN],
                5=>[CustomerLog::TYPE_GET],
                99=>[CustomerLog::TYPE_ASSIGN_NEW, CustomerLog::TYPE_ASSIGN, CustomerLog::TYPE_IN, CustomerLog::TYPE_GET],
                ] as $type2=>$assignType) {
                echo $type1 .':' . $type2 . ':' . $startTime . ' | '. $endTime . 'start'.PHP_EOL;
                // 获取时间范围内分配的新用户数据
                $sql = "select customer_id,`after` from customer_log where type in (".implode(',', $assignType).") and create_time >= ? and create_time < ? and `after` > 0 group by customer_id, `after`";
                $customs = app('db')->select($sql, [ $startTime, $endTime]);
                foreach ($customs as $custom) {
                    // 获取这个人在时间范围内最后一个星级状态
                    $sql = "select * from customer_log where type = ? and create_time >= ? and create_time < ? and user_id = ? and customer_id = ? order by id desc limit 1";
                    $log1 = app('db')->select($sql, [CustomerLog::TYPE_STAR, $startTime, $endTime, $custom->after, $custom->customer_id]);
                    // 获取这个人在时间范围内最后一个跟进状态
                    $sql = "select * from customer_log where type = ? and create_time >= ? and create_time < ? and user_id = ? and customer_id = ? order by id desc limit 1";
                    $log2 = app('db')->select($sql, [CustomerLog::TYPE_FOLLOW, $startTime, $endTime, $custom->after, $custom->customer_id]);
                    $newData[] = [
                        'type1' => strval($type1),
                        'type2' => intval($type2),
                        'day' => strval($startTime),
                        'user_id' => intval($custom->after),
                        'customer_id' => intval($custom->customer_id),
                        'star' => intval($log1[0]->after),
                        'follow_status' => intval($log2[0]->after),
                    ];
                }
                $userIds[] = intval($custom->after);
                $userIds = array_unique($userIds);
                echo $type1 .':' . $type2 . ':' . $startTime . ' | '. $endTime . 'end'.PHP_EOL;
            }
            foreach ($userIds as $userId)  {
                $newData[] = [
                    'type1' => strval($type1),
                    'type2' => 98,
                    'day' => strval($startTime),
                    'user_id' => $userId,
                    'customer_id' => 0,
                    'star' => 0,
                    'follow_status' => 0,
                ];
            }
        }
        $newData = array_chunk($newData, 100);
        $index = 0;
        foreach ($newData as $batch) {
            $flag = (new CustomerReport())->insert($batch);
            $index++;
            echo '第'.$index.'批次结束,结束:'.json_encode($flag).PHP_EOL;
        }
    }
}