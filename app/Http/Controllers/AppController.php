<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLog;
use App\Models\CustomerReport;
use App\Models\SystemRole;
use App\Models\SystemTeam;
use App\Models\SystemUser;
use Illuminate\Http\Request;

class AppController extends Controller
{
    const KEY = '73a90acaae2b1ccc0e969709665bc62f';
    /**
     * 生成签名
     * @param array $args
     * @param string $token
     * return string
     */
    private function makeSign(array $args) {
        ksort($args);
        $sign_str = '';
        foreach ($args as $k => $v) {
            if ($v || is_numeric($v)) {
                $sign_str .= $k.$v;
            }
        }
        return strtoupper(md5(md5($sign_str) . static::KEY));
    }

    private function checkSign($request)
    {
        $params = $request->all();
        $sign = $params['sign'];
        unset($params['sign']);
        $validSign = $this->makeSign($params);
        if (empty($params['timestamp']) || $sign != $validSign) {
            return false;
        }
        return true;
    }

    public function login(Request $request) {
        if (!$this->checkSign($request)) {
            return $this->apiReturn(static::ERROR, [], '验签失败');
        }
        $params = $request->all();
        $userName = $params['mobile'];
        $password = $params['password'];
        if (empty($userName) || empty($password)) {
            return $this->apiReturn(static::ERROR, [], "账号或密码错误");
        }
        $user = SystemUser::where('mobile', $userName)->where('status', 1)->where('is_del', 0)->first();
        if (empty($user)) {
            return $this->apiReturn(static::ERROR, [], "当前手机号未注册");
        } else if ($user->password != md5($password . $user->password_salt)) {
            return $this->apiReturn(static::ERROR, [], "账号/密码错误,请修改");
        }
        $token = md5($user->password . '|' . time() . '|' .   $user->id . '|'.  rand());
        $user->token = $token;
        $user->token_time = time();
        $user->save();
        return $this->apiReturn(static::APP_OK, ["token" => $token]);
    }

    public function logout(Request $request) {
        if (!$this->checkSign($request)) {
            return $this->apiReturn(static::ERROR, [], '验签失败');
        }
        $token = $_SERVER['HTTP_X_API_TOKEN'];
        if (empty($token)) {
            return $this->apiReturn(static::ERROR, [], 'TOKEN不能为空');
        }
        $user = SystemUser::where('token', $token)->where('status', 1)->where('is_del', 0)->first();
        if (empty($user)) {
            return $this->apiReturn(static::APP_OK, [], "该用户已经退出");
        }
        $user->token = '';
        $user->save();
        return $this->apiReturn(static::APP_OK, [], "退出成功");
    }

    public function userinfo(Request $request) {
        if (!$this->checkSign($request)) {
            return $this->apiReturn(static::ERROR, [], '验签失败');
        }
        $token = $_SERVER['HTTP_X_API_TOKEN'];
        if (empty($token)) {
            return $this->apiReturn(static::ERROR, [], 'TOKEN不能为空');
        }
        $user = SystemUser::where('token', $token)->where('status', 1)->where('is_del', 0)->first();
        if (empty($user)) {
            return $this->apiReturn(static::ERROR, [], "获取用户信息失败,请重新登录");
        }
        $roles = implode(",", array_column((new SystemRole())->getRoleByUserId($user->id), 'name'));
        $team = SystemTeam::find(intval($user['team_id']));
        $data = [
            'user_name' => $user->name,
            'mobile' => substr($user->mobile, 0, 3) . '****'. substr($user->mobile, 7, 4),
            'role' => $roles,
            'team' => $team ? $team->name : ''
        ];
        return $this->apiReturn(static::APP_OK, $data);
    }

    public function calllist(Request $request) {
        if (!$this->checkSign($request)) {
            return $this->apiReturn(static::ERROR, [], '验签失败');
        }
        $token = $_SERVER['HTTP_X_API_TOKEN'];
        if (empty($token)) {
            return $this->apiReturn(static::ERROR, [], 'TOKEN不能为空');
        }
        $user = SystemUser::where('token', $token)->where('status', 1)->where('is_del', 0)->first();
        if (empty($user)) {
            return $this->apiReturn(static::ERROR, [], "获取用户信息失败,请重新登录");
        }
        $logModel = new CustomerLog();
        $list = $logModel->getLogListByUserIdAndType($user->id, CustomerLog::TYPE_CALL, 0, 200);
        $temp = [];
        foreach ($list as $item) {
            if ($temp[$item['customer_id']]) {continue;}
            $temp[$item['customer_id']] = $item;
        }
        $data = [];
        foreach ($temp as $item) {
            $customer = Customer::find($item['customer_id']);
            $data[] = [
                'name' => $customer->name,
                'mobile' => $customer->mobile,
                'call_time' => $item['create_time']
            ];
        }
        return $this->apiReturn(static::APP_OK, $data);
    }
}