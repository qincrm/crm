<?php

namespace App\Services\Hook;

use App\Models\SystemSetting;
use App\Models\SystemUserRole;

/**
 * 登录hook 
 */
class LoginHook
{
    /**
     * 判断用户是否可以登录
     */
    public static function canLogin($userId, $request) {
        $model = new SystemUserRole();
        $roles = $model->getRoleByUserId($userId);
        $rolesIds = array_column($roles, 'role_id');
        $setting = SystemSetting::find(1);
        if ($setting['ip']) {
            $ip = explode(",", $setting['ip']) ;
            if (!in_array($request->ip(), $ip) && !in_array(1, $rolesIds)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 获取用户第一个能打开的页面
     */
    public static function getPage($userId) {
        $service = app(\App\Services\RightService::class);
        $realMenus = ($service->getRightTree($userId)['tree']);
        foreach ($realMenus as $menu) {
            return $menu['path'];
        }
        return '';
    }
}