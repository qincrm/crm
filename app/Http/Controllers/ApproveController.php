<?php

namespace App\Http\Controllers;

use App\Models\Approve;
use App\Models\ApproveDetail;
use App\Models\SystemUser;
use App\Services\ApproveService;
use App\Services\SelectService;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    /**
     * 客户列表
     */
    public function list(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $params['user_id'] = $userId;
        if ($params['ltype'] == 1) {
            $model = new Approve();
        } else {
            $model = new ApproveDetail();
        }
        $list = $model->getLists($params);
        // 加工显示项目
        $userModel = new SystemUser();
        $userList = $userModel->getAllUser();
        $userArray = collect($userList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $service = new ApproveService();
        foreach ($list as $key => $item) {
            if ($params['ltype'] != 1) {
                $approve = Approve::find($item['aid']);
                $item['id'] = $item['aid'];
                $item['type'] = $approve['type'];
                $item['status'] = $approve['status'];
                $item['user_id'] = $approve['user_id'];
            }
            $item['name'] = ApproveService::TYPE_MAPPING[$item['type']];
            $item['status_name'] = ApproveService::STATUS_MAPPING[$item['status']];
            if ($item['status'] == 0) {
                $detail = $service->getApproveUser($item['id']);
                $item['user_name'] = $userArray[$detail['user_id']];
            } else {
                $item['user_name'] = '暂无';
            }
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $model->getCount($params);
        $data['types'] = app(SelectService::class)->genSelectByKV(ApproveService::TYPE_MAPPING);
        $data['status'] = app(SelectService::class)->genSelectByKV(ApproveService::STATUS_MAPPING);
        return $this->apiReturn(static::OK, $data);
    }


    /**
     * 获取用户信息
     */
    public function cancel(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $service = app(ApproveService::class);
        $service->cancelApprove($params['id'], $userId);
        return $this->apiReturn(static::OK);
    }


    /**
     * 修改用户信息
     */
    public function pass(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $service = app(ApproveService::class);
        if ($params['status'] == 1) {
            $service->passApprove($params['id'], $userId);
        } else {
            $service->unpassApprove($params['id'], $userId);
        }
        return $this->apiReturn(static::OK);
    }

    /**
     * 客户列表
     */
    public function view(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $id = $params['id'];
        $model = new Approve();
        $detailModel = new ApproveDetail();
        $approve = $model::find($id);
        $details = $detailModel->where('aid', $id)->orderby('id')->get();
        $approveDetail = [];
        $userModel = new SystemUser();
        $userList = $userModel->getAllUserWithDel();
        $userArray = collect($userList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        foreach ($details as $detail) {
            if ($detail['status'] == 2)  {
                $content = "";
            } else if ($detail['status'] == 0)  {
                $content = ApproveService::STATUS_MAPPING[$detail['status']];
            } else {
                $content = ApproveService::STATUS_MAPPING[$detail['status']] . " (".$detail['update_time'].")";
            }
            $approveDetail[$userArray[$detail['user_id']]] = $content;
        }
        $service = new ApproveService();
        $user = $service->getApproveUser($id);
        return $this->apiReturn(static::OK, [
            'title' => '审批流-'.$id,
            'detail' => app(SelectService::class)->genSelectByVK(json_decode($approve['form'], true)),
            'approve' => app(SelectService::class)->genSelectByVK($approveDetail),
            'can_approve' => $user['user_id'] == $userId ? 1 : 0,
        ]);
    }
}
