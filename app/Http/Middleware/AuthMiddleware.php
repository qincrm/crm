<?php

namespace App\Http\Middleware;

use App\Models\SystemRight;
use App\Models\SystemSetting;
use App\Models\SystemUserRole;
use Closure;
use Illuminate\Support\Facades\Log;

class AuthMiddleware
{

    private $whiteList = ['user/menu', 'user/online', 'user/info', 'user/resetpassword', 'message/unreadlist', 'message/list', 'message/read', 'user/dashboard', 'custom/getnoticelist'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userId = $request->session()->get('user_id', 0);

        if (empty($userId)) {
            echo json_encode(['code'=>50008, 'msg'=>'登录过期,请重新登录']);
            exit;
        }

        $url = str_replace("/api/", "", $request->getPathInfo());
        Log::info("IN1666510036 行为记录: " . $userId . " : " . $url . " : " . $request->ip());

        if (!in_array($url, $this->whiteList)) {
            $right = new SystemRight();
            $menus  = $right->getRightByUserId($userId);
            $hasAuth = false;
            foreach ($menus as $menu) {
                if ($menu->url == $url ) {
                    $hasAuth = true;
                    break;
                }
            }

            if (!$hasAuth) {
                echo json_encode(['code'=>50000, "msg"=>"无操作权限"]);
                exit;
            }
        }

        $model = new SystemUserRole();
        $roles = $model->getRoleByUserId($userId);
        $rolesIds = array_column($roles, 'role_id');

        $setting = SystemSetting::find(1);
        if ($setting['ip']) {
            $ip = explode(",", $setting['ip']) ;
            if (!in_array($request->ip(), $ip) && !in_array(1, $rolesIds)) {
                echo json_encode(['code'=>50008, 'msg' => '非法IP, 请联系管理员']);
                exit;
            }
        }
        

        return $next($request);
    }
}
