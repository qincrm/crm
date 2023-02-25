<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\CustomerBack;
use App\Models\CustomerLog;
use App\Models\Customer;
use App\Models\CustomerReport;
use App\Models\SystemDict;
use App\Models\SystemRole;
use App\Models\SystemTeam;
use App\Models\SystemUser;
use App\Services\CustomerService;
use App\Services\RightService;
use App\Services\SelectService;
use Illuminate\Http\Request;
use App\Services\ToolService;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    /**
     * 回款数据列表
     */
    public function backlist(Request $request) {

        $params = $request->all();
        $dict = new SystemDict();
        $userModel = new SystemUser();
        $model = new CustomerBack();
        $teammodel = new SystemTeam();
        $followTeamArray= $teammodel->getAllTeamMap();
        $followUserArray = $userModel->getAllUserWithDelMap();
        $followUserTeamArray = $userModel->getAllUserTeamWithDelMap();
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置

        $list = $model->getLists($params);
        foreach ($list as $key => $item) {
            $item['apply_amount'] = $item['apply_amount'] ? $item['apply_amount'] : '';
            $item['apply_date'] = $item['apply_date'] ? date('Y-m-d H:i:s', $item['apply_date']) : '';
            $item['date'] = $item['date'] ? date('Y-m-d H:i:s', $item['date']) : '';
            $item['fee'] = $item['fee'] . '%';
            $item['follow_user'] = $followUserArray[$item['follow_user_id']];
            $item['team'] = $followTeamArray[$followUserTeamArray[$item['follow_user_id']]];
            $item['source']  = $customGourpDict[$dict::TYPE_SOURCE][$item['source']];
            $item['user_from']  = $customGourpDict[$dict::TYPE_USER_FROM][$item['user_from']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $model->getCount($params);

        $data['allTeamListSelect'] = app(SelectService::class)->genSelectByKV($followTeamArray); // 转成前端的select
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 工作日志列表
     */
    public function worklist(Request $request) {

        $params = $request->all();
        $userModel = new SystemUser();
        $model = new CustomerLog();
        $teammodel = new SystemTeam();
        $roleModel = new SystemRole();
        $followTeamArray = $teammodel->getAllTeamMap();
        $followUserArray = $userModel->getAllUserWithDelMap();
        $followUserTeamArray = $userModel->getAllUserTeamWithDelMap();
        $query = $model;
        $query = ToolService::ifQueryEq($query, $params, 'user_id');
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('user_id', $userIds);
        }
        if ($params['timeType'] == 1) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 2) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-1 day")));
            $query = $query->where('create_time', '<', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 3) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-2 day")));
        }
        if ($params['timeType'] == 4) {
            $time = time();
            $monday = date('Y-m-d 00:00:00', ($time - ((date('w',$time) == 0 ? 7 : date('w',$time)) - 1) * 24 * 3600));
            $query = $query->where('create_time', '>', $monday);
        }
        if ($params['timeType'] == 5) {
            $query = $query->where('create_time', '>', date("Y-m-01 00:00:00"));
        }
        if ($params['timeType'] == 6) {
            $query = $query->where('create_time', '>', $params['times'][0]. ' 00:00:00');
            $query = $query->where('create_time', '<', $params['times'][1]. ' 23:59:59');
        }
        $list = $query->select('user_id', 'after', 'type', DB::raw('count(*) as cnt'))
        ->whereRaw(DB::raw('type in (8, 16)'))
        ->groupBy('user_id', 'after', 'type')->get();
        $query = new CustomerBack();
        $query = ToolService::ifQueryEq($query, $params, 'user_id', 'follow_user_id');
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('follow_user_id', $userIds);
        }
        if ($params['timeType'] == 1) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 2) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-1 day")));
            $query = $query->where('create_time', '<', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 3) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-2 day")));
        }
        if ($params['timeType'] == 4) {
            $time = time();
            $monday = date('Y-m-d 00:00:00', ($time - ((date('w',$time) == 0 ? 7 : date('w',$time)) - 1) * 24 * 3600));
            $query = $query->where('create_time', '>', $monday);
        }
        if ($params['timeType'] == 5) {
            $query = $query->where('create_time', '>', date("Y-m-01 00:00:00"));
        }
        if ($params['timeType'] == 6) {
            $query = $query->where('create_time', '>', $params['times'][0]. ' 00:00:00');
            $query = $query->where('create_time', '<', $params['times'][1]. ' 23:59:59');
        }
        $query->where('status', 1);
        $list2 = $query->select('follow_user_id', DB::raw('sum(real_amount) as real_amount, sum(amount) as amount'))->groupBy('follow_user_id')->get();
        $dataList = [];
        foreach ($list as $item) {
            if ($item['type'] == 8)  {
                $dataList[$item['user_id']][$item['after']] = $item['cnt'];
            } else if ($item['type'] == 16)  {
                $dataList[$item['user_id']]['zhuan'] = $item['cnt'];
            }
        }
        foreach ($list2 as $item) {
            $dataList[$item['follow_user_id']]['real_amount'] = $item['real_amount'];
            $dataList[$item['follow_user_id']]['amount'] = $item['amount'];
        }
        $query= $userModel->where('is_del', 0);
        if ($params['user_id']) {
            $query = $query->where('id', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('id', $userIds);
        }
        $list = $query->get();
        foreach ($list as $key => $user) {
            $userId = $user['id'];
            $item = $dataList[$userId];
            $user['team'] = $followTeamArray[$followUserTeamArray[$userId]];
            $roles = $roleModel->getRoleByUserId($userId);
            $user['roles'] = implode(",", app(ToolService::class)->objColumn($roles, 'name'));
            $user['yuejian'] = intval($item['7']);
            $user['qianyue'] = intval($item['9']);
            $user['zhuan'] = intval($item['zhuan']);
            $user['real_amount'] = floatval($item['real_amount']);
            $user['amount'] = floatval($item['amount']);
            $list[$key] = $user;
        }
        $data['list'] = $list;
        $data['allTeamListSelect'] = app(SelectService::class)->genSelectByKV($followTeamArray); // 转成前端的select
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 成本列表
     */
    public function costlist(Request $request) {

        $params = $request->all();
        $userModel = new SystemUser();
        $model = new CustomerLog();
        $teammodel = new SystemTeam();
        $roleModel = new SystemRole();

        $followTeamArray= $teammodel->getAllTeamMap();
        $followUserArray = $userModel->getAllUserWithDelMap();
        $followUserTeamArray = $userModel->getAllUserTeamWithDelMap();

        $query = $model->where('after', '>', 0);
        if ($params['user_id']) {
            $query = $query->where('after', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('after', $userIds);
        }
        if ($params['timeType'] == 1) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 2) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-1 day")));
            $query = $query->where('create_time', '<', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 3) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-2 day")));
        }
        if ($params['timeType'] == 4) {
            $time = time();
            $monday = date('Y-m-d 00:00:00', ($time - ((date('w',$time) == 0 ? 7 : date('w',$time)) - 1) * 24 * 3600));
            $query = $query->where('create_time', '>', $monday);
        }
        if ($params['timeType'] == 5) {
            $query = $query->where('create_time', '>', date("Y-m-01 00:00:00"));
        }
        if ($params['timeType'] == 6) {
            $query = $query->where('create_time', '>', $params['times'][0]. ' 00:00:00');
            $query = $query->where('create_time', '<', $params['times'][1]. ' 23:59:59');
        }
        $list = $query->select('after', 'type', DB::raw('customer_id'))
        ->whereRaw(DB::raw('type in (7, 15)'))
        ->get();
        $dataList = [];
        foreach ($list as $item) {
            if ($item['type'] == 15)  {
                $dataList[$item['after']]['new'][] = $item['customer_id'];
            } else if ($item['type'] == 7)  {
                $dataList[$item['after']]['old'][] = $item['customer_id'];
            }
            $dataList[$item['after']]['all'][] = $item['customer_id'];
        }
        $query= $userModel->where('is_del', 0);
        if ($params['user_id']) {
            $query = $query->where('id', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('id', $userIds);
        }
        $list = $query->get();
        foreach ($list as $key => $user) {
            $userId = $user['id'];
            $item = $dataList[$userId];
            $user['team'] = $followTeamArray[$followUserTeamArray[$userId]];
            $roles = $roleModel->getRoleByUserId($userId);
            $user['roles'] = implode(",", app(ToolService::class)->objColumn($roles, 'name'));
            $user['cost'] = 0;
            if ($item['new']) {
                $user['cost'] = Customer::whereIn('id', $item['new'])->sum('cost');
            }
            $user['all'] = $item['all'] ? count(array_unique($item['all'])) : 0;
            $user['new'] = $item['new'] ? count(array_unique($item['new'])) : 0;
            $user['old'] = $item['old'] ? count(array_unique($item['old'])) : 0;
            $list[$key] = $user;
        }
        $data['list'] = $list;
        $data['allTeamListSelect'] = app(SelectService::class)->genSelectByKV($followTeamArray); // 转成前端的select
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select
        return $this->apiReturn(static::OK, $data);
    }

}
