<?php

namespace App\Http\Controllers;

use App\Models\CustomerRuleConfig;
use App\Models\SystemDict;
use App\Models\SystemLog;
use App\Models\SystemUser;
use App\Services\AssignService;
use App\Services\SelectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignController extends Controller
{
    /**
     * 数据分配权限修改
     */
    public function edit(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $data = [];
        // 加工下前端数据
        foreach ($params['config']['types'] as $type) {
            $data[$type] = strval($params['config'][$type."Limit"]);
        }
        // 保存权限
        $model = new SystemUser();
        $model = $model->find($params['id']);
        DB::beginTransaction();
        $oldAssignRight = $model->assign_rights;
        $model->assign_rights = json_encode($data);
        $flag = $model->save();
        if ($flag && $oldAssignRight != $model->assign_rights) {
            $logmodel = new SystemLog();
            $logmodel->saveLog($logmodel::TYPE_ASSIGN, $params['id'], $oldAssignRight, $model->assign_rights, $userId, strval($params['config']['remark']));
        }
        DB::commit();
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 查看数据分配权限修改日志
     */
    public function log(Request $request) {
        $params = $request->all();
        $logmodel = new SystemLog();
        $list = $logmodel->getLogListById($logmodel::TYPE_ASSIGN, $params['id'], $params);
        $service  = app(AssignService::class);
        $data['total'] = $logmodel->getLogCountById($logmodel::TYPE_ASSIGN, $params['id'], $params);
        $model = new SystemUser();
        $allUser = collect($model->getAllUserWithDel())->mapWithKeys(function ($item){
            return [$item['id'] => $item['name']];
        });
        foreach ($list as $key => $item) {
            $item['before'] = $service->genText($item['before']);
            $item['after'] = $service->genText($item['after']);
            $item['user'] = $allUser[$item['user_id']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 数据分配权限修改日志
     */
    public function config(Request $request) {
        $logmodel = new CustomerRuleConfig();
        $dictModel = new SystemDict();
        $list = $logmodel->where('is_del', 0)->orderby('myorder')->get();
        foreach ($list as $key => $item) {
            $config = json_decode($item['config'], true);
            $item['config'] = $config;
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $followStatus = $dictModel->getListByType($dictModel::TYPE_FOLLOW); 
        $starStatus = $dictModel->getListByType($dictModel::TYPE_STAR); 
        $data['followStatus'] = app(SelectService::class)->genSelectByKV($followStatus); // 城市
        $data['starStatus'] = app(SelectService::class)->genSelectByKV($starStatus); // 城市
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 修改流转规则
     */
    public function editrule (Request $request) {
        $model = new CustomerRuleConfig();
        $params = $request->all();
        $rules = $params['rule'];
        foreach ($rules as $rule) {
            $model->where('id', $rule['id'])->update(['status' => $rule['status'], 'config' => json_encode($rule['config'])]);
        }
        return $this->apiReturn(static::OK, '修改成功');

    }

    /**
     * 修改流转规则状态，开启或关闭
     */
    public function setstatus(Request $request) {
        $model = new CustomerRuleConfig();
        $params = $request->all();
        $model = $model->find($params['id']);
        if (empty($model)) {
            return $this->apiReturn(static::ERROR, [], '操作失败');
        }
        $model->status = $params['status'];
        $model->save();
        return $this->apiReturn(static::OK, '修改成功');

    }
}
