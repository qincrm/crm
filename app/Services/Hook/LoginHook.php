<?php

namespace App\Services\Hook;

use App\Models\SystemUserRole;

/**
 * 登录hook ， 机构定制
 */
class LoginHook
{
    public static function canLogin($userId) {
        $model = new SystemUserRole();
        $roles = $model->getRoleByUserId($userId);
        $rolesIds = array_column($roles, 'role_id');
        // 非管理员 8 - 20点不允许登录系统
        if (!in_array(1, $rolesIds) && (date('Hi') < 800 || date('Hi') > 2130)) {
            return ['status'=>false, 'msg' => '请在8:00 ~ 21:30 之间登录系统'];
        }
        return ['status'=>true, 'msg' => ''];
    }
}