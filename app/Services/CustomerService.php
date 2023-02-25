<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAssign;
use App\Models\CustomerLog;
use App\Models\SystemDict;
use App\Models\SystemFields;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerService
{

    const ASSIGN_TYPE_PUBLIC = 1; // 公共池
    const ASSIGN_TYPE_INSIDE = 2; // 内部流转
    const ASSIGN_TYPE_NEW = 3; // 新数据分配
    const ASSIGN_TYPE_SELF = 4; // 录入
    const ASSIGN_TYPE_FOLLOW = 5; // 公共池认领
    const ASSIGN_TYPE_INTRO = 6; // 转介绍

    const ASSIGN_TYPE_MAPPING = [
        self::ASSIGN_TYPE_PUBLIC => '公共池分配',
        self::ASSIGN_TYPE_INSIDE => '再分配',
        self::ASSIGN_TYPE_NEW => '新数据分配',
        self::ASSIGN_TYPE_SELF => '自主录入',
        self::ASSIGN_TYPE_FOLLOW => '公共池认领',
        self::ASSIGN_TYPE_INTRO => '转介绍',
    ];

    const HOUSE_MAPPING = [
        1=>'无房',
        2=>'本地房',
        3=>'外地房',
    ];

    const CAR_MAPPING = [
        2=>'有车',
        1=>'无车',
    ];

    const POLICY_MAPPING = [
        2=>'有保单',
        1=>'无保单',
    ];

    const WAGE_MAPPING = [
        2=>'有打卡工资',
        1=>'无打卡工资',
    ];

    const FUNDS_MAPPING = [
        2=>'有公积金',
        1=>'无公积金',
    ];

    const INSURANCE_MAPPING = [
        2=>'有社保',
        1=>'无社保',
    ];

    const CREDIT_MAPPING = [
        1=>'无逾期',
        2=>'有逾期',
    ];

    /**
     * 两个客户的diff
     */
    public function diff($cust1, $cust2) {
        $diff = [];
        $fieldModel = new SystemFields();
        $fields = $fieldModel->all()->map(function($item) {
            return $item['name'];
        })->toArray();
        foreach ($fields as $field) {
            if ($cust1->$field != $cust2->$field) {
                $diff[$field] = [
                    'old' => $cust1->$field,
                    'new' => $cust2->$field
                ];
            }
        }
        return ($diff);
    }

    public function canAssign($customId) {
        $customModel = new Customer();
        $custom = $customModel->find($customId);
        $data = $customModel->where('mobile', $custom['mobile'])->where('follow_user_id', '>', 0)->where('id', '!=', $customId)->get()->toArray();
        if ($data) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 客户分配
     */
    public function assign($customId, $followUserId, $operUserId = 0, $assignType = 0) {
        DB::beginTransaction();
        $customModel = new Customer();
        $custom = $customModel->lockForUpdate()->find($customId);
        $oldFollowUserId = $custom->follow_user_id;
        if (empty($custom) || $oldFollowUserId == $followUserId || ($oldFollowUserId != 0 && $assignType == CustomerService::ASSIGN_TYPE_NEW)) {
            // 新用户已经分配了退出
            return true;
        }

        if ($assignType == 0) {
            if ($custom->follow_user_id == 0) {
                $assignType = static::ASSIGN_TYPE_PUBLIC;
            } else {
                $assignType = static::ASSIGN_TYPE_INSIDE;
            }
        }

        if ($custom->first_follow_user_id == 0) {
            $custom->first_follow_user_id = $followUserId;
        }

        $result = true;
        $custom->follow_user_id = $followUserId;
        $custom->assign_time = time();
        $custom->status = 1;
        // 之前有跟进人变成内部流转
        if ($oldFollowUserId != 0) {
            $custom->user_from = 2;
        }
        $flag = $custom->save();
        if (!$flag) {
            $result = false;
        }

        $logModel = new CustomerLog();
        $logType =  $assignType == static::ASSIGN_TYPE_NEW ? $logModel::TYPE_ASSIGN_NEW : $logModel::TYPE_ASSIGN;
        $flag = $logModel->saveLog($logType, $customId, $oldFollowUserId, $followUserId, $operUserId, $assignType);
        if (!$flag) {
            $result = false;
        }

        if ($result) {
            DB::commit();
        } else {
            DB::rollBack();
        }
        return true;
    }

    /**
     * 客户认领
     */
    public function get($customId, $followUserId, $operUserId = 0, $assignType = 0) {
        $customModel = new Customer();
        $custom = $customModel->find($customId);
        $oldFollowUserId = $custom->follow_user_id;
        if (empty($custom) || $oldFollowUserId == $followUserId) {
            return true;
        }

        if ($custom->follow_user_id != 0) {
            return false;
        }

        DB::beginTransaction();
        $result = true;

        if ($custom->first_follow_user_id == 0) {
            $custom->first_follow_user_id = $followUserId;
        }

        $custom->follow_user_id = $followUserId;
        $custom->assign_time = time();
        $custom->user_from = self::ASSIGN_TYPE_FOLLOW;
        $flag = $custom->save();
        if (!$flag) {
            $result = false;
        }

        $logModel = new CustomerLog();
        $flag = $logModel->saveLog($logModel::TYPE_GET, $customId, $oldFollowUserId, $followUserId, $operUserId, $assignType);
        if (!$flag) {
            $result = false;
        }

        if ($result) {
            DB::commit();
        } else {
            DB::rollBack();
        }
        return true;
    }

    /**
     * 移入公海 
     */
    public function giveup($customId, $userId = 0, $remark = "") {
        $model = Customer::find($customId);
        if ($model->follow_user_id == 0) {
            return true;
        }
        $oldFollowUserId = $model->follow_user_id;
        $model->follow_user_id = 0;
        $model->user_from = 2;
        DB::beginTransaction();
        $model->save();
        $logmodel = new CustomerLog();
        $logmodel->saveLog($logmodel::TYPE_GIVEUP, $customId, $oldFollowUserId, $model->follow_user_id, $userId, $remark);
        DB::commit();
        return true;
    }
    /**
     * 资质信息文案装换
     */
    public function genZizhi($custom) {
        $zizhis = [];
        if ($custom['house'] && $custom['house'] != 1 && static::HOUSE_MAPPING[$custom['house']]) {
            $zizhis[] = static::HOUSE_MAPPING[$custom['house']];
        }
        if ($custom['car'] && $custom['car'] != 1 && static::CAR_MAPPING[$custom['car']]) {
            $zizhis[] = static::CAR_MAPPING[$custom['car']];
        }
        if ($custom['policy'] && $custom['policy'] != 1 && static::POLICY_MAPPING[$custom['policy']]) {
            $zizhis[] = static::POLICY_MAPPING[$custom['policy']];
        }
        if ($custom['wage'] && $custom['wage'] != 1 && static::WAGE_MAPPING[$custom['wage']]) {
            $zizhis[] = static::WAGE_MAPPING[$custom['wage']];
        }
        if ($custom['funds'] && $custom['funds'] != 1 && static::FUNDS_MAPPING[$custom['funds']]) {
            $zizhis[] = static::FUNDS_MAPPING[$custom['funds']];
        }
        if ($custom['insurance'] && $custom['insurance'] != 1 && static::INSURANCE_MAPPING[$custom['insurance']]) {
            $zizhis[] = static::INSURANCE_MAPPING[$custom['insurance']];
        }
        if ($custom['credit'] && $custom['credit'] == 1 && static::CREDIT_MAPPING[$custom['credit']]) {
            $zizhis[] = static::CREDIT_MAPPING[$custom['credit']];
        }
        return implode("/", $zizhis);
    }

    /**
     * 多少天没有跟进
     */
    public function getNotFollowTime($custom) {
        $lastTime = max(intval($custom['assign_time']), intval($custom['follow_time']));
        if ($lastTime < 1) {
            return '';
        }
        $cha = time() - $lastTime;
        $data = intval($cha / ( 24 * 3600));
        $hour = intval(($cha % ( 24 * 3600)) / 3600);
        return $data. '天'.$hour .'小时';
    }

    /**
     * 解析excel
     */
    public function genCustomByExcelRow($row, $dict) {
        $returnData = [
            'status' => 0,
            'error' => [],
            'custom' => []
        ];
        if (!$row[0]) {
            $returnData['error'][] = '姓名不能为空';
        }
        $returnData['custom']['name'] = $row[0];
        if (!$row[1]) {
            $returnData['error'][] = '手机号不能为空';
        }
        if (!empty($row[1]) && !preg_match("/^1[3456789]\d{9}$/", $row[1])) {
            $returnData['error'][] = '手机号格式不正确';
        }
        $returnData['custom']['mobile'] = $row[1];
        $returnData['custom']['age'] = intval($row[2]);
        $row[3] = trim($row[3]);
        $row[6] = trim($row[6]);
        $row[7] = trim($row[7]);
        $row[8] = trim($row[8]);
        $row[9] = trim($row[9]);
        $row[10] = trim($row[10]);
        $row[11] = trim($row[11]);
        $row[12] = trim($row[12]);
        if (!empty($row[3]) && empty(array_flip($dict[SystemDict::TYPE_CITY])[$row[3]])) {
            $returnData['error'][] = '找不到对应的城市';
        }
        $returnData['city'] = intval(array_flip($dict[SystemDict::TYPE_CITY])[$row[3]]);
        $returnData['custom']['amount'] = floatval($row[4]);
        if (empty($row['5'])) {
            $applyTime = time();
        } else {
            $applyTime = strtotime($row['5']);
        }
        if (!$applyTime)  {
            $returnData['error'][] = '申请时间格式不对';
        }
        $returnData['custom']['apply_time'] = $applyTime;
        if (!empty($row[6]) && empty(array_flip(static::HOUSE_MAPPING)[$row[6]])) {
            $returnData['error'][] = '找不到对应的房产信息类型';
        }
        $returnData['custom']['house'] = intval(array_flip(static::HOUSE_MAPPING)[$row[6]]);

        if (!empty($row[7]) && empty(array_flip(static::CAR_MAPPING)[$row[7]])) {
            $returnData['error'][] = '找不到对应的车辆信息类型';
        }
        $returnData['custom']['car'] = intval(array_flip(static::CAR_MAPPING)[$row[7]]);

        if (!empty($row[8]) && empty(array_flip(static::POLICY_MAPPING)[$row[8]])) {
            $returnData['error'][] = '找不到对应的保单信息类型';
        }
        $returnData['custom']['policy'] = intval(array_flip(static::POLICY_MAPPING)[$row[8]]);
        if (!empty($row[9]) && empty(array_flip(static::WAGE_MAPPING)[$row[9]])) {
            $returnData['error'][] = '找不到对应的打卡工资信息类型';
        }
        $returnData['custom']['wage'] = intval(array_flip(static::WAGE_MAPPING)[$row[9]]);

        if (!empty($row[10]) && empty(array_flip(static::FUNDS_MAPPING)[$row[10]])) {
            $returnData['error'][] = '找不到对应的公积金信息类型';
        }
        $returnData['custom']['funds'] = intval(array_flip(static::FUNDS_MAPPING)[$row[10]]);

        if (!empty($row[11]) && empty(array_flip(static::INSURANCE_MAPPING)[$row[11]])) {
            $returnData['error'][] = '找不到对应的社保信息类型';
        }
        $returnData['custom']['insurance'] = intval(array_flip(static::INSURANCE_MAPPING)[$row[11]]);

        if (!empty($row[12]) && empty(array_flip(static::CREDIT_MAPPING)[$row[12]])) {
            $returnData['error'][] = '找不到对应的信用情况信息类型';
        }
        $returnData['custom']['credit'] = intval(array_flip(static::CREDIT_MAPPING)[$row[12]]);
        if (empty($returnData['error'])) {
            $returnData['status'] = 1;
        }

        return $returnData;
    }

    /**
     * 新增渠道过来的用户
     */
    public function addChannelCustomer($custom) {
        $id = 0;
        $hasInfo = Customer::where('source', $custom['source'])->where('channel_id', $custom['channel_id'])->get()->toArray();
        // 判断客户已经存在
        if (empty($hasInfo)) {
            $customService = app(CustomerService::class);
            $userService = app(UserService::class);
            $item['mobile_md5'] = md5($custom['mobile']);
            // 写客户信息表
            $id = (new Customer())->insertGetId($item);
            $users = $userService->getAllAssignUser();
            // 遍历所有可以对接客户的销售
            foreach ($users as $userId => $leftCount) {
                $userId = intval($userId);
                // 销售今天没有名额了
                if ($leftCount <= 0) {
                    continue;
                }
                Log::info("addChannelCustomer 分配" . $id . " --> " . $userId);
                $customService->assign($id, $userId, 0, CustomerService::ASSIGN_TYPE_NEW);
                break;
            }
        }
        return $id;
    }
}
