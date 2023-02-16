<?php

namespace App\Services\AssignRule;

use App\Services\CustomerService;

/**
 * 客户分配相关的service
 */
class Rule10
{
    public function handle($config) {
        // 获取今天分配最少的用户
        return ['status'=>1];
    }
}
