<?php

namespace App\Services\AssignRule;

use App\Models\Customer;
use App\Models\CustomerLog;
use App\Services\CustomerService;
use Illuminate\Support\Facades\DB;

/**
 * 客户分配相关的service
 */
class Rule11
{
    public function handle($config) {
        // 获取今天分配最少的用户
        echo "RULE11: 配置" . json_encode($config).PHP_EOL;
        if (empty($config['day'])) {
            echo "RULE11: 配置异常" .PHP_EOL;
            return ['status'=>0];
        }
        $time = date('Y-m-d H:i:s', time() - $config['day'] * 3600 * 24);
        $sql = "SELECT a.id FROM `customer` a , customer_log b where a.id = b.customer_id and `lock` = 1 and b.type = 4  group by a.id HAVING max(b.create_time) < ?"; // 星级为0且分配时间超过2天
        echo $time;
        $data = app('db')->select($sql, [$time]);
        var_dump($data);
        foreach ($data as $item) {
            echo "RULE11: 锁定超过".$config['day']."天，流入公共池, id: ".$item->id.PHP_EOL;
            DB::beginTransaction();
            $model = Customer::find($item->id);
            $model->lock = 0;
            $model->save();
            $logmodel = new CustomerLog();
            $logmodel->saveLog($logmodel::TYPE_UNLOCK, $item->id, '', '', 0, '锁定超过'.$config['day'].'天');
            DB::commit();
        }
        return ['status'=>1];
    }
}
