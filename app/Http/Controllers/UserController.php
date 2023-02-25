<?php

namespace App\Http\Controllers;

use App\Models\Captcha;
use App\Models\Customer;
use App\Models\CustomerBack;
use App\Models\Notice;
use App\Models\SystemDict;
use App\Models\SystemRight;
use App\Models\SystemTeam;
use App\Models\SystemUser;
use App\Services\ApproveService;
use App\Services\Hook\LoginHook;
use App\Services\RightService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 登录
     */
    public function login(Request $request)
    {
        $params = $request->all();
        $userName = $params['username'];
        $password = $params['password'];
        $captcha = $params['captcha'];
        if ($captcha != $request->session()->get('captcha')) {
            return $this->apiReturn(static::ERROR, [], '验证码不正确');
        }
        if (empty($userName) || empty($password)) {
            return $this->apiReturn(static::ERROR, [], "账号或密码错误");
        }
        $user = SystemUser::where('mobile', $userName)->where('status', 1)->where('is_del', 0)->first();
        if (empty($user)) {
            return $this->apiReturn(static::ERROR, [], "账号或密码错误");
        } else if ($user->password != md5($password . $user->password_salt)) {
            return $this->apiReturn(static::ERROR, [], "账号或密码错误");
        }
        if (!LoginHook::canLogin($user->id, $request)){
            return $this->apiReturn(static::ERROR, [], "非法IP, 请联系管理员");
        }
        $page = LoginHook::getPage($user->id);
        $request->session()->put('user_id', $user->id);
        return $this->apiReturn(static::OK, ["token" => 12345, 'page' => $page]);
    }


    /**
     * 退出
     */
    public function logout(Request $request)
    {
        $request->flush();
        return $this->apiReturn(static::OK);
    }

    /**
     * 用户基本信息
     */
    public function info(Request $request)
    {
        $teamModel = new SystemTeam();
        $userModel = new SystemUser();
        $noticeModel = new Notice();

        $userId = $request->session()->get('user_id');
        $userArray = $userModel->getAllUserMap();
        $teamArray = $teamModel->getAllTeamMap();

        $user = SystemUser::where('id', $userId)->first();
        $data = [
            "name" => $user->name,
            "mobile" => $user->mobile,
            "department" => $teamArray[$user->team_id],
            "leader" => $userArray[$user->parent_id],
            "avatar" => '/resource/av.png',
            "online" => $user->online == 1 ? true : false,
            'noticeCount' => $noticeModel->getUnReadCount($userId),
        ];
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 用户菜单
     */
    public function menu(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $service = app(\App\Services\RightService::class);
        $realMenus = ($service->getRightTree($userId)['tree']);
        // 给一些通过的功能默认加上权限
        $realMenus = array_merge($realMenus, $service->getDefaultRight());
        return $this->apiReturn(static::OK, $realMenus);
    }

    /**
     * 登录
     */
    public function resetpassword(Request $request)
    {
        $params = $request->all();
        $userId = $request->session()->get('user_id');
        $oldpassword = $params['oldpassword'];
        $newpassword = $params['newpassword'];
        $user = SystemUser::find($userId);
        if (empty($user)) {
            return $this->apiReturn(static::ERROR, [], "操作异常，请刷新重试");
        } else if ($user->password != md5($oldpassword . $user->password_salt)) {
            return $this->apiReturn(static::ERROR, [], "旧密码输入错误");
        }
        $user->password = md5($newpassword . $user->password_salt);
        $user->save();
        return $this->apiReturn(static::OK);
    }

    /**
     * 首页
     */
    public function dashboard(Request $request)
    {
        $dict = new SystemDict();
        $customGourpDict = $dict->getListByType($dict::TYPE_NOTICE); // 获取城市信息配置
        $color = ['', 'green', 'blue', 'purple', 'pinkpurple', 'red'];
        $userId = $request->session()->get('user_id');
        $model = new Notice();
        $noticeList = $model->getListByUserId($userId, 10);
        foreach ($noticeList as $key => $notice) {
            $custom = Customer::find($notice->custom_id);
            $notice->color = $color[$notice->type];
            $notice->type = $customGourpDict[$notice->type];
            $notice->day = date('Y-m-d H:i:s', $notice->date);
            $notice->remark = $notice->remark . " (客户姓名:" .$custom['name'] . ")";
            $noticeList[$key] = $notice;
        }
        $users = [];
        $followUserArray = app(RightService::class)->getCustomViews($userId);
        if ($followUserArray != 'all') {
            $users = array_keys($followUserArray);
        }
        $customModel = new Customer();
        $backModel = new CustomerBack();
        $paihangbang = $backModel->getPaihangbang();
        $paihangbang2 = $backModel->getPaihangbang2();
        $chartData = [];
        foreach ($paihangbang2 as $item) {
            $chartData [] = [
                'value' => [$item->cnt],
                'name' => $item->name
            ];
        }
        $index = 1;
        $rightModel = new SystemRight();
        $rights = $rightModel->getRightByUserId($userId);
        $rights = array_column($rights, 'router');
        foreach ($paihangbang as $key=>$item) {
            $item->index = $index;
            $paihangbang[$key] = $item;
            if (!in_array('YejiAmount', $rights)) {
                $item->amount = '';
            }
            if (!in_array('YejiRealAmount', $rights)) {
                $item->real_amount = '';
            }
            $index ++;
        }
        return $this->apiReturn(static::OK, [
            'customer' => $customModel->getMyCount(['users' => $users]),
            'important' => $customModel->getMyCount(['users' => $users, 'important' => 1]),
            'newcustomer' => $customModel->getMyCount(['users' => $users, 'new' => 1]),
            'innercustomer' => $customModel->getMyCount(['users' => $users, 'inner' => 1]),
            'followcustomer' => $customModel->getMyCount(['users' => $users, 'follow_status' => 1]),
            'paihangbang' => $paihangbang,
            'chartData' => $chartData,
            'yeji_list' => [],
            'order_list' => [],
            'notice_list' => $noticeList
        ]);
    }

    /**
     * 验证码
     */
    public function captcha(Request $request) {
        $c = new Captcha();
        $c->create($request);
    }

    /**
     * donoting
     */
    public function donothing(Request $request) {
    }

}
