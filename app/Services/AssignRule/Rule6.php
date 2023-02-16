<?php

namespace App\Services\AssignRule;

use App\Services\CustomerService;

/**
 * 客户分配相关的service
 */
class Rule6
{
    public function handle($config) {
        // 获取今天分配最少的用户
        echo "RULE6: 配置" . json_encode($config).PHP_EOL;
        if (empty($config['type']) || empty($config['values']) || empty($config['day'])) {
            echo "RULE6: 配置异常" .PHP_EOL;
            return ['status'=>0];
        }
        $customService = app(CustomerService::class);
        $time = time() - $config['day'] * 3600 * 24;
        if ($config['type'] == 1) {
            $sql = "select * from customer where follow_status in (".implode(",", $config['values']).") and follow_user_id > 0 and assign_time < ? and follow_time < ?"; // 星级为0且分配时间超过2天
        } else if ($config['type'] == 2) {
            $sql = "select * from customer where star in  (".implode(",", $config['values']).") and follow_user_id > 0 and assign_time < ? and follow_time < ?"; // 星级为0且分配时间超过2天
        }
        $data = app('db')->select($sql, [$time, $time]);
        foreach ($data as $item) {
            echo "RULE6: 超过".$config['day']."天没有跟进，流入公共池, id: ".$item->id.PHP_EOL;
            $customService->giveup($item->id, 0, '超过'.$config['day'].'天没有跟进，流入公共池');
        }
        return ['status'=>1];
    }
}
