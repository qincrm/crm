<?php

namespace App\Services;

use App\Models\Approve;
use App\Models\ApproveDetail;
use App\Models\CustomerBack;

/**
 * 审批流相关的service
 */
class ApproveService
{

    const TYPE_BACK = 1; // 回款流程

    const TYPE_MAPPING = [
        self::TYPE_BACK => "回款确认",
    ];

    const STATUS_MAPPING = [
        0 => "审批中",
        1 => "审批通过",
        -1 => "审批拒绝",
        -2 => "审批取消",
    ];
    /**
     * 发起流程
     */
    public function createApprove($id, $type, $form = [], $userId = 0, $approveUserId = []) {
        $approveId = Approve::insertGetId([
            'tid' => $id,
            'type' => $type,
            'form' => json_encode($form),
            'user_id' => $userId
        ]);
        if ($approveId) {
            $index = 1;
            $approveUserId = array_unique($approveUserId);
            foreach ($approveUserId as $uid) {
                $status = 2;
                if (empty($uid)) {
                    continue;
                }
                if ($index == 1) {
                    $status = 0;  // 第一个人开始审批
                }
                ApproveDetail::insert([
                    'aid' => $approveId,
                    'status' => $status,
                    'user_id' => $uid
                ]);
                $index ++;
            }
        }
        return $approveId;
    }


    /**
     * 取消流程
     */
    public function cancelApprove($id) {
        $approve = Approve::find($id);
        if ($approve->status != 0) {
            return false;
        }
        $approve->status = -2;
        $approve->reason = '取消流程';
        $approve->save();
        (new ApproveDetail())->where('status', 0)->where('aid', $id)->update(['status'=>2]);
        $this->noticeBiz($approve->type, $approve->tid, -1);
    }

    /**
     * 获取代办审批数量
     */
    public function noticeNum($userId) {
        $detailModel = new ApproveDetail();
        return $detailModel->where('status', 0)->where('user_id', $userId)->count();
    }


    /**
     * 审批通过
     */
    public function passApprove($id, $userId) {
        $detailModel = new ApproveDetail();
        $detail = $detailModel->where('status', 0)->where('user_id', $userId)->where('aid', $id)->get();
        if (count($detail) == 0) {
            return false; // 还没到审批人审批
        }
        $detailModel->where('status', 0)->where('user_id', $userId)->where('aid', $id)->update(['status'=>1]);
        $detail = $detailModel->where('status', 2)->where('aid', $id)->orderby('id')->get()->take(1);
        if (count($detail) == 0) {
            // 审批完成
            $approve = Approve::find($id);
            $approve->status = 1;
            $approve->save();
            $this->noticeBiz($approve->type, $approve->tid, 1);
        } else {
            // 下一个审批人
            $detailModel->where('status', 2)->where('user_id', $detail[0]->user_id)->where('aid', $id)->update(['status'=>0]);
        }
        
    }


    /**
     * 审批不通过
     */
    public function unpassApprove($id, $userId, $reason = '') {
        $detailModel = new ApproveDetail();
        $detail = $detailModel->where('status', 0)->where('user_id', $userId)->where('aid', $id)->get();
        if (empty($detail)) {
            return false; // 还没到审批人审批
        }
        $approve = Approve::find($id);
        $approve->status = -1;
        $approve->reason = $reason;
        $approve->save();
        (new ApproveDetail())->where('status', 0)->where('user_id', $userId)->where('aid', $id)->update(['status'=>-1]);
        $this->noticeBiz($approve->type, $approve->tid, -1);
    }

    public function getApproveUser($id) {
        $detailModel = new ApproveDetail();
        return $detailModel->where('status', 0)->where('aid', $id)->get()->take(1)->toArray()[0];
    }

    /**
     * 通知业务
     */
    public function noticeBiz($type, $id, $status) {
        if ($type == self::TYPE_BACK) {
            if ($status == 1) {
                (new CustomerBack())->where('status', 0)->where('id', $id)->update(['status'=>1]);
            } else {
                (new CustomerBack())->where('status', 0)->where('id', $id)->update(['status'=>-1]);
            }
        }
    }
}
