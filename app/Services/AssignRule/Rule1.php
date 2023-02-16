<?php

namespace App\Services\AssignRule;

use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\UserService;

/**
 * 客户分配相关的service
 */
class Rule1
{
    public function handle($config) {
        echo PHP_EOL;
        // 获取今天还有分配名额的用户
        $customService = app(CustomerService::class);
        $userService = app(UserService::class);
        $users = $userService->getAllAssignUser();
        echo "RULE1: 新数据分配的所有用户" . json_encode($users).PHP_EOL;
        // 获取所有新数据公共池用户
        $model = new Customer();
        $customers = $model->where('user_from', 1)->where('follow_user_id', 0)->get();
        echo "RULE1: 一共" . count($customers) . "待分配用户" .PHP_EOL;
        foreach ($customers as $customer) {
            if (empty($users)) {
                continue;
            }
            foreach ($users as $userId => $leftCount) {
                $userIdStr = $userId;
                $userId = intval($userId);
                if ($leftCount <= 0) {
                    continue;
                }
                echo "RULE1: 分配" . $customer->id . ' --> ' . $userId .PHP_EOL;
                $flag = $customService->assign($customer->id, $userId, 0, CustomerService::ASSIGN_TYPE_NEW);
                $users[$userIdStr] = $leftCount - 1;
                break;
            }
            // 把第一位的用户排到最后一位用作轮训
            $key = array_keys($users);
            $users[$key[0]] = array_shift($users);
        }
        return ['status'=>1];
    }
}
