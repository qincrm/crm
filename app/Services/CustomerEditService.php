<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAssign;
use App\Models\CustomerLog;
use App\Models\CustomerRemarkLog;
use App\Models\SystemDict;
use App\Models\SystemFields;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\Cloner\Cursor;

class CustomerEditService
{

    public function edit($params, $operUserId) {
        $result = ['status'=>1, 'error'=>''];
        $customModel = new Customer();
        $service = app(CustomerService::class);

        $customId = $params['id'];
        $customModel = $customModel->find($params['id']);
        $oldInfo = clone ($customModel);
        $params['mobile_md5'] = md5($params['mobile']);
        $flag = $customModel->update($params);

        if (!$flag) {
            $result = ['status'=>0, 'error'=>"基本信息保存失败"];
        }
        $diff = $service->diff($oldInfo, $customModel);
        if ($diff['star']) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_STAR, $params['id'], $diff['star']['old'], $diff['star']['new'], $operUserId, $customModel->remark);
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"星级变化保存失败"];
            }
        }
        if ($diff['follow_status']) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_FOLLOW, $params['id'], $diff['follow_status']['old'], $diff['follow_status']['new'], $operUserId, $customModel->remark);
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"跟进变化保存失败"];
            }
            $customModel->follow_time = time();
            $customModel->save();
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"跟进时间保存失败"];
            }
        } else if (!empty($params['remark'])) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_FOLLOW, $params['id'], intval($params['follow_status']), intval($params['follow_status']), $operUserId, $params['remark']);
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"跟进变化保存失败"];
            }
            $customModel->follow_time = time();
            $customModel->save();
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"跟进时间保存失败"];
            }
        }
        unset($diff['star']);
        unset($diff['follow_status']);
        if (count($diff) > 0) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_EDIT, $params['id'], '', '', $operUserId, '');
            $logmodel = new SystemLog();
            $logmodel->saveLog($logmodel::TYPE_EDIT, $params['id'], json_encode($diff), '', $operUserId, strval($params['config']['remark']));
        }
        if ($params['remark']) {
            $customerRemarkLogModel = new CustomerRemarkLog();
            $customerRemarkLogModel->saveLog($customId, $params['remark'], $operUserId);
        }
        return $result;
    }

    public function add($params, $operUserId) {
        $customModel = new Customer();

        $result = ['status'=>1, 'error'=>''];
        $params['add_user_id'] = $operUserId;
        $params['apply_time'] = time();
        $params['assign_time'] = time();
        // 转介绍
        $params['user_from'] = empty($params['introid']) ? CustomerService::ASSIGN_TYPE_SELF : CustomerService::ASSIGN_TYPE_INTRO;
        $params['follow_user_id'] = $operUserId;
        $params['mobile_md5'] = md5($params['mobile']);
        if ($params['follow_status']) {
            $params['follow_time'] = time();
        }
        $custom = $customModel->create($params);
        if (!$custom->id) {
            $result = ['status'=>0, 'error'=>"客户录入失败"];
        }
        $logModel = new CustomerLog();
        if ($params['introid']) {
            $flag = $logModel->saveLog($logModel::TYPE_INTRO, $custom->id, $params['introid'], '', $operUserId, "转介绍");
            $flag = $logModel->saveLog($logModel::TYPE_INTRO, $params['introid'], $custom->id, '', $operUserId, "转介绍");
        } else {
            $flag = $logModel->saveLog($logModel::TYPE_IN, $custom->id, '', '', $operUserId, "手动录入");
        }
        if (!$flag) {
            $result = ['status'=>0, 'error'=>"客户录入失败"];
        }
        if ($params['star']) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_STAR, $custom->id, 0, $params['star'], $operUserId, strval($params['remark']));
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"星级变化保存失败"];
            }
        }
        if ($params['follow_status']) {
            $logModel = new CustomerLog();
            $flag = $logModel->saveLog($logModel::TYPE_FOLLOW, $custom->id, 0, $params['follow_status'], $operUserId, strval($params['remark']));
            if (!$flag) {
                $result = ['status'=>0, 'error'=>"跟进变化保存失败"];
            }
        }
            
        $customId = $custom->id;

        if ($params['remark']) {
            $customerRemarkLogModel = new CustomerRemarkLog();
            $customerRemarkLogModel->saveLog($customId, $params['remark'], $operUserId);
        }

        return $result;
    }
}