<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerRuleConfig;

class Assign extends Command
{
    protected $signature = 'assign';
    protected $description = '分配策略';

    /**
     ** 执行控制台命令
     **/
    public function handle() {
        // 取到所有规则
        echo date('Y-m-d H:i:s') . ' start.......'.PHP_EOL;
        $model = new CustomerRuleConfig();
        $rules = $model->get();
        foreach ($rules as $rule) {
            $rulename = $rule->name;
            $config = json_decode($rule->config, true);
            $status = $rule->status;
            if ($status == 0) {
                echo $rulename . ':配置关闭' . PHP_EOL;
                continue;
            }
            $ruleClass = '\App\Services\AssignRule\Rule'.$rule->id;
            if (!class_exists($ruleClass)) {
                echo $rulename . ':找不到规则处理器' . PHP_EOL;
                continue;
            }
            echo $rulename . ': 执行开始 ---> ';
            $ruleStrategy = new $ruleClass;
            $result = $ruleStrategy->handle($config);
            if ($result['status'] != 1) {
                echo '异常, '.$result['error'].PHP_EOL;
            } else {
                echo '执行结束'.PHP_EOL;
            }
        }
        echo date('Y-m-d H:i:s') . ' end.......' . PHP_EOL;
    }
}
