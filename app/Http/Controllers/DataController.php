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
        $export = $params['export'];
        $dict = new SystemDict();
        $userModel = new SystemUser();
        $model = new CustomerBack();
        $teammodel = new SystemTeam();
        $allTeamListSelect = $teammodel->getAllTeam();
        $followTeamArray = collect($allTeamListSelect)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserTeamArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['team_id']];
        })->toArray();
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
        if ($export == 1) {
            app(ToolService::class)->csv(
                "回款数据",
                [
                    "name" =>         "客户姓名",
                    "apply_date"  =>   "申请时间",
                    "apply_amount" =>   "申请金额",
                    "date" =>         "放款时间",
                    "amount" =>       "放款金额",
                    "fee" =>        "点位"  ,
                    "real_amount" =>     "实际创收" ,
                    "remark" =>     "备注"  ,
                    "follow_user" =>    "跟进顾问" ,
                    "team" =>       "团队"  ,
                    "user_from" =>    "数据来源" ,
                    "source" =>     "渠道"   ,
                ],
                $list
            );
            return;
        }
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
        $allTeamListSelect = $teammodel->getAllTeam();
        $followTeamArray = collect($allTeamListSelect)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserTeamArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['team_id']];
        })->toArray();
        $query = $model;
        if ($params['user_id']) {
            $query = $query->where('user_id', $params['user_id']);
        }
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
        if ($params['user_id']) {
            $query = $query->where('follow_user_id', $params['user_id']);
        }
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
        $allTeamListSelect = $teammodel->getAllTeam();
        $followTeamArray = collect($allTeamListSelect)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $followUserTeamArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['team_id']];
        })->toArray();
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



    /**
     * 报表数据
     */
    public function reportlist(Request $request) {

        $userId = $request->session()->get('user_id');
        $userModel = new SystemUser();
        $params = $request->all();
        $export = $params['export'];
        $followUserArray = app(RightService::class)->getCustomViews($userId);
        if ($followUserArray == 'all') {
            $followUserList = $userModel->getAllUser();
            $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            })->toArray();
        } else {
            $params['users'] = array_keys($followUserArray);
        }

        $dict = new SystemDict();
        $sourceDict = $dict->getListByType($dict::TYPE_SOURCE); // 获取城市信息配置
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置
        $params = $request->all();
        if ($params['name']) {
            $params['source'] = array_flip($sourceDict)[$params['name']];
            if (empty($params['source'])) {
                $params['source'] = 9999999;
            }
        }
        $model = new CustomerReport();
        $list = $model->getListsGroupByChannel($params);
        $listtmp = [];
        foreach ($list as $item) {
            $listtmp[$item['user_id']][$item['star']] = $item['cnt'];
        }
        $listtmp2 = [];
        $list = $model->getListsGroupByChannel2($params);
        foreach ($list as $item) {
            $listtmp2[$item['user_id']][$item['follow_status1']] = $item['cnt'];
        }
        $model = new CustomerReport();
        $allList = $model->getAllUser($params);
        $data['list'] = [];
        foreach ($allList as $user) {
            $source = $user['id'];
            $star = (array)$listtmp[$source];
            $sum = $star[0] + $star[1] + $star[2] + $star[3] + $star[4] + $star[5] + $star[-1];
            $data['list'][] = [
                'name' => $user['name'],
                'channel' => $source,
                'follow_user_id' => $source,
                'star0_num' => intval($star[0]) + intval($star[-1]),
                'all_num' => intval($sum),
                'star0' => $sum == 0 ? 0 : round($star[0]/$sum*100, 2).'%',
                'star1_num' => intval($star[1]),
                'star1' => $sum == 0 ? 0 :round($star[1]/$sum*100, 2).'%',
                'star2_num' => intval($star[2]),
                'star2' => $sum == 0 ? 0 :round($star[2]/$sum*100, 2).'%',
                'star3_num' => intval($star[3]),
                'star3' => $sum == 0 ? 0 :round($star[3]/$sum*100, 2).'%',
                'star4_num' => intval($star[4]),
                'star4' => $sum == 0 ? 0 :round($star[4]/$sum*100, 2).'%',
                'star5_num' => intval($star[5]),
                'star5' => $sum == 0 ? 0 :round($star[5]/$sum*100, 2).'%',
                'star6_num' => intval($star[2] + $star[3] + $star[4] + $star[5]),
                'star6' => $sum == 0 ? 0 :round(intval($star[2] + $star[3] + $star[4] + $star[5])/$sum*100, 2).'%',
                'star7_num' => intval($star[3] + $star[4] + $star[5]),
                'star7' => $sum == 0 ? 0 :round(intval($star[3] + $star[4] + $star[5])/$sum*100, 2).'%',
                'star8_num' => intval($star[4] + $star[5]),
                'star8' => $sum == 0 ? 0 :round(intval($star[4] + $star[5])/$sum*100, 2).'%',
                'status0_num' => intval($listtmp2[$source][0]),
                'status0' => $sum == 0 ? 0 :round($listtmp2[$source][0]/$sum*100, 2).'%',
                'status1_num' => intval($listtmp2[$source][1]),
                'status1' => $sum == 0 ? 0 :round($listtmp2[$source][1]/$sum*100, 2).'%',
                'status2_num' => intval($listtmp2[$source][2]),
                'status2' => $sum == 0 ? 0 :round($listtmp2[$source][2]/$sum*100, 2).'%',
                'status3_num' => intval($listtmp2[$source][3]),
                'status3' => $sum == 0 ? 0 :round($listtmp2[$source][3]/$sum*100, 2).'%',
                'status4_num' => intval($listtmp2[$source][4]),
                'status4' => $sum == 0 ? 0 :round($listtmp2[$source][4]/$sum*100, 2).'%',
                'status5_num' => intval($listtmp2[$source][5]),
                'status5' => $sum == 0 ? 0 :round($listtmp2[$source][5]/$sum*100, 2).'%',
            ];
        }
        $dictModel = new SystemDict();
        $sourceList = $dictModel->getListByType($dictModel::TYPE_SOURCE);
        $data['sourceList'] = app(SelectService::class)->genSelectByKV($sourceList); // 渠道来源
        $teammodel = new SystemTeam();
        $allTeamListSelect = $teammodel->getAllTeam();
        $data['allTeamListSelect'] = app(SelectService::class)->genSelect($allTeamListSelect, 'id', 'name'); // 转成前端的select
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select
        $data['userFromList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dict::TYPE_USER_FROM]); // 用户来源
        if ($export == 1) {
            app(ToolService::class)->csv(
                $params['type'] == 'user' ?  '我的数据' : '渠道数据', [
                'name'=> $params['type'] == 'user' ?  '跟进顾问' : '渠道名称', 
                'all_num'=>'总计', 
                'star0_num'=>'0星',
                'star0'=>'0星占比',
                'star1_num'=>'1星',
                'star1'=>'1星占比',
                'star2_num'=>'2星',
                'star2'=>'2星占比',
                'star3_num'=>'3星',
                'star3'=>'3星占比',
                'star4_num'=>'4星',
                'star4'=>'4星占比',
                'star5_num'=>'5星',
                'star5'=>'5星占比',
                'star6_num'=>'2星以上',
                'star6'=>'2星以上占比',
                'star7_num'=>'3星以上',
                'star7'=>'3星以上占比',
                'star8_num'=>'4星以上',
                'star8'=>'4星以上占比',
                'status0_num'=>'未跟进',
                'status0'=>'未跟进占比',
                'status1_num'=>'已跟进',
                'status1'=>'已跟进占比',
            ], $data['list']);
            return;
        }
        return $this->apiReturn(static::OK, $data);
    }

    
    /**
     * 报表客户列表
     */
    public function reportdetail(Request $request)
    {
        $data = [];
        $dictModel = new SystemDict();
        $userModel = new SystemUser();
        $customModel = new Customer();
        $model = new CustomerReport();
        
        // 获取客户维度的码表
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        // 筛选项
        $params = $request->all();
        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();

        $data['title'] = "" ;

        // 加工显示项目
        $customIds = $model->getCustomerIds($params);
        $list = [];
        $count = 0;
        if ($customIds) {
            $customParams = ['custom_ids'=>$customIds, 'current'=>$params['current'], 'pageSize'=>$params['pageSize']];
            $list = $customModel->getLists($customParams);
            foreach ($list as $key => $item) {
                $item['notFollowTime'] = app(CustomerService::class)->getNotFollowTime($item);
                $item['apply_time'] = empty($item['apply_time']) ? '-' : date('Y-m-d H:i:s', $item['apply_time']);
                $item['follow_time'] = empty($item['follow_time']) ? '-' : date('Y-m-d H:i:s', $item['follow_time']);
                $item['assign_time'] = empty($item['assign_time']) ? '-' : date('Y-m-d H:i:s', $item['assign_time']);
                $item['zizhi'] = app(CustomerService::class)->genZizhi($item);
                $item['starcolor'] = Constants::TAG_COLOR[$item['star']] ?? '';
                $item['star'] = $customGourpDict[$dictModel::TYPE_STAR][$item['star']] ?? '';
                $item['fstatuscolor'] = Constants::TAG_COLOR[$item['follow_status']];
                $item['follow_status'] = $customGourpDict[$dictModel::TYPE_FOLLOW][$item['follow_status']] ?? '-';
                $item['user_from'] = $customGourpDict[$dictModel::TYPE_USER_FROM][$item['user_from']];
                $item['source'] = $customGourpDict[$dictModel::TYPE_SOURCE][$item['source']];
                $item['follow_user'] = $followUserArray[$item['follow_user_id']];
                $item['age']  = empty($item['age']) ? '' : $item['age'];
                $item['amount']  = $item['amount'] == 0 ? '' : intval($item['amount']);
                $item['city'] = $customGourpDict[$dictModel::TYPE_CITY][$item['city']] ?? '-';
                $list[$key] = $item;
            }
            $count = $customModel->getCount($customParams);
        }
        $data['list'] = $list;
        $data['total'] = $count;
        return $this->apiReturn(static::OK, $data);
    }

}
