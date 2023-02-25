<?php

namespace App\Services\AssignRule;

use App\Services\CustomerService;

/**
 * 客户分配相关的service
 */
class Rule4
{
    public function handle($config) {
        // 获取今天分配最少的用户
        if (empty($config['type']) || empty($config['values']) || empty($config['day'])) {
            return ['status'=>0];
        }
        $customService = app(CustomerService::class);
        $time = time() - $config['day'] * 3600 * 24;
        $field = $config['type'] == 1 ? 'follow_status' : 'star';
        $sql = "select * from customer where ".$field." in (".implode(",", $config['values']).") and follow_user_id > 0 and assign_time < ? and follow_time < ?"; // 星级为0且分配时间超过2天
        $data = app('db')->select($sql, [$time, $time]);
        foreach ($data as $item) {
            $customService->giveup($item->id, 0, '超过'.$config['day'].'天没有跟进，流入公共池');
        }
        return ['status'=>1];
    }
}
