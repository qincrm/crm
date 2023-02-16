<?php

namespace App\Services\AssignRule;
use App\Services\CustomerService;


/**
 * 客户分配相关的service
 */
class Rule13
{
    public function handle($config) {
        echo "RULE13: 配置" . json_encode($config).PHP_EOL;
        if (empty($config['hour'])) {
            echo "RULE13: 配置异常".PHP_EOL;
            return ['status'=>0];
        }
        $customService = app(CustomerService::class);
        $time = time() - $config['hour'] * 3600 * 24;
        $sql = "select * from customer where follow_user_id > 0 and assign_time < ? and follow_time < ? and user_from != 1  and follow_time < assign_time";
        $data = app('db')->select($sql, [$time, $time]);
        foreach ($data as $item) {
            echo "RULE13: 超过".$config['hour']."天没有跟进，流入公共池, id: ".$item->id.PHP_EOL;
            $customService->giveup($item->id, 0, '再分配客户超过'.$config['hour'].'天没有跟进，流入公共池');
        }
        return ['status'=>1];
    }
}
