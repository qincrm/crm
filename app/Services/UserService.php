<?php

namespace App\Services;

use App\Models\CustomerLog;
use App\Models\SystemUser;


class UserService
{
    /**
     * 获取有新数据分配权限的所有用户和剩余的分配次数
     */
    public function getAllAssignUser() {
        $model = new SystemUser();
        $logmodel = new CustomerLog();
        $users = $model->where('status', 1)->where('is_del', 0)->where('online', 1)->get();
        $retUser = [];
        $sort1 = [];
        $sort2 = [];
        foreach ($users as $user) {
            $config = json_decode($user['assign_rights'], true);
            if ($config["new"] > 0) {
                $count = $logmodel->getCountByUserIdAndTime($user['id'], CustomerLog::TYPE_ASSIGN_NEW, date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00', strtotime('+1 day')))[0];
                $leftCount = $config["new"] - $count->cnt;
                $sort1[] = intval($count->id);
                $sort2[] = $leftCount;
                $retUser[$user['id'].'-UID'] = $leftCount; 
            }
        }
        array_multisort($sort1, SORT_ASC, $sort2, SORT_DESC, $retUser);
        return $retUser;
    }

    /**
     * 获取有认领剩余次数
     */
    public function getGetLeftTime($userId) {
        $model = new SystemUser();
        $logmodel = new CustomerLog();
        $user = $model->find($userId);
        $config = json_decode($user['assign_rights'], true);
        $leftCount = 0;
        if ($config["public"] > 0) {
            $count = $logmodel->getCountByUserIdAndTime($user['id'], CustomerLog::TYPE_GET, date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00', strtotime('+1 day')))[0];
            $leftCount = $config["public"] - $count->cnt;
        }
        return $leftCount;
    }

       /**
     * 获取权限树
     * 
     * @param userId 信贷员id
     */
    public function getUserTree($userId = 0, $userIds = [])
    {
        $model = new SystemUser();
        $users = $model->getAllUserByParentId($userId);
        $list = [];
        foreach ($users as $user) {
            if (in_array($user->id, $userIds)) {
                continue;
            }
            $userIds[] = $user->id;
            $item = [
                'title' => $user->name,
                'key'  => $user->id,
            ];
            $children = $this->getUserTree($user->id, $userIds);
            if ($children) {
                $item['children'] = $children;
            }
            $list[] = $item;
        }
        return $list;
    }
}
