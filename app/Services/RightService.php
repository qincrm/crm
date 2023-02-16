<?php

namespace App\Services;

use App\Models\SystemRight;
use App\Models\SystemRole;
use App\Models\SystemUser;

class RightService
{
    /**
     * 获取权限树
     * 
     * @param userId 信贷员id
     */
    public function getRightTree($userId = 0)
    {
        $rightModel = new SystemRight();
        if ($userId > 0) {
            $rights = $rightModel->getRightByUserId($userId);
        } else {
            $rights = $rightModel->getAllRight();
        }
        $notParentRights = [];
        $realRights = [];
        foreach ($rights as $right) {
            $item = [
                'id' => $right->id,
                'title' => $right->name_cn,
                'value' => $right->name_cn,
                'key'  => $right->id,
                'path' => $right->router,
                'name' => $right->router,
                'parent_id' => $right->parent_id,
                'meta' => [
                    'locale' => $right->name,
                    'icon' => $right->icon,
                    'hideInMenu' => $right->hide_in_menu ? true : false,
                    'requiresAuth' => true
                ]
            ];
            if ($right->parent_id > 0) {
                if (isset($realRights[$right->parent_id]) && $realRights[$right->parent_id]) {
                    // 有上级才能有下级权限
                    $realRights[$right->parent_id]['children'][$right->id] = $item;
                } else {
                    $notParentRights[$right->parent_id][] = $item;
                }
            } else {
                $realRights[$right->id] = $item;
            }
        }
        $returnRights = [];
        $leafs = [];
        foreach ($realRights as $id => $levelOneRights) {
            if (!empty($levelOneRights['children'])) {
                $tempRights = [];
                foreach ($levelOneRights['children'] as $subid => $levelTwoRights) {
                    if (!empty($notParentRights[$subid])) {
                        $levelTwoRights['children'] = $notParentRights[$subid];
                        foreach ($levelTwoRights['children'] as $levelThreeRights) {
                            $leafs[] = $levelThreeRights['id'];
                        }
                    } else {
                        $leafs[] = $subid;
                    }
                    $tempRights[] = $levelTwoRights;
                }
                $levelOneRights['children'] = $tempRights;
            } else {
                $leafs[] = $id;
            }
            $returnRights[] = $levelOneRights;
        }
        return [
            'tree' => $returnRights,
            'leafs' => $leafs
        ];
    }

    /**
     * 默认默认开放的权限
     */
    public function getDefaultRight()
    {
        return [
            [
                'key'  => 9999,
                'path' => 'Usersetting',
                'name' => 'Usersetting',
                'parent_id' => 0,
                'meta' => [
                    'locale' => 'menu.nothing',
                    'hideInMenu' =>  true,
                    'requiresAuth' => true
                ]
            ],
            [
                'key'  => 9998,
                'path' => 'NoticeList',
                'name' => 'NoticeList',
                'parent_id' => 0,
                'meta' => [
                    'locale' => 'menu.nothing',
                    'hideInMenu' =>  true,
                    'requiresAuth' => true
                ]
            ],
            [
                'key'  => 9997,
                'path' => 'Working',
                'name' => 'Working',
                'parent_id' => 0,
                'meta' => [
                    'locale' => 'menu.nothing',
                    'icon' => "",
                    'hideInMenu' =>  true,
                    'requiresAuth' => true
                ]
            ]
        ];
    }

    /**
     * 获取某个账号的可见客户权限
     */
    public function getCustomViews($userId) {
        // 如果有角色是可以查看所有客户权限，直接返回all
        $model = new SystemRole();
        $roles = $model->getRoleByUserId($userId);
        foreach ($roles as $role) {
            if ($role->views == 1) {
                return 'all';
            }
        }
        // 如果只能查看下级权限，递归查询下级
        $allUserId = [];
        $this->getSubUserids($userId, $allUserId);
        $model = new SystemUser();
        $user = $model->find($userId);
        $allUserId[$userId] = $user->name;
        return $allUserId;
    }

    /**
     * 获取下一级的用户
     */
    public function getSubUserids($userId, &$nowSubUserids = [], $time = 0) {
        if ($time > 20 ) {
            return [];
        }
        $model = new SystemUser();
        $subIds = $model->getAllUserByParentId($userId)->mapWithKeys(
            function($item) {
                return [$item->id => $item->name];
            }
        )->toArray();
        $time++;
        foreach ($subIds as $subId => $name) {
            if (in_array($subId, $nowSubUserids)) {
                continue;
            }
            $nowSubUserids[$subId] = $name;
            $this->getSubUserids($subId, $nowSubUserids, $time);
        }
    }

}
