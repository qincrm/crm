<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Constants;
use App\Models\Customer;
use App\Models\SystemDict;
use App\Models\SystemTeam;
use App\Models\SystemUser;
use App\Services\CustomerService;
use App\Services\ToolService;
use App\Services\RightService;
use App\Services\SelectService;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * 渠道数据列表
     */
    public function list(Request $request) {

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
        $params = $request->all();
        if ($params['name']) {
            $params['source'] = array_flip($sourceDict)[$params['name']];
            if (empty($params['source'])) {
                $params['source'] = 9999999;
            }
        }
        $model = new Customer();
        $list = $model->getListsGroupByChannel($params);
        $listtmp = [];
        $key = $params['type'] == 'user' ? 'follow_user_id' : 'source';
        foreach ($list as $item) {
            $listtmp[$item[$key]][$item['star']] = $item['cnt'];
        }
        $listtmp2 = [];
        $list = $model->getListsGroupByChannel2($params);
        foreach ($list as $item) {
            $listtmp2[$item[$key]][$item['follow_status1']] = $item['cnt'];
        }
        $data['list'] = [];
        foreach ($listtmp as $source => $star) {
            $sum = $star[0] + $star[1] + $star[2] + $star[3] + $star[4] + $star[5] + $star[-1];
            $data['list'][] = [
                'name' => $params['type'] == 'user' ? ($followUserArray[$source] ?? '公海'): ($sourceDict[$source] ?? '其他'),
                'channel' => $source,
                'follow_user_id' => $source,
                'star0_num' => intval($star[0]) + intval($star[-1]),
                'all_num' => intval($sum),
                'star0' => $sum == 0 ? 0 : round(($star[0]+ intval($star[-1]))/$sum*100, 2).'%',
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
                'status1_num'=>'待跟进',
                'status1'=>'待跟进占比',
                'status2_num'=>'跟进中',
                'status2'=>'跟进中占比',
                'status3_num'=>'已放款',
                'status3'=>'已放款占比',
                'status4_num'=>'已上门',
                'status4'=>'已上门占比',
                'status5_num'=>'已签约',
                'status5'=>'已签约占比',
            ], $data['list']);
            return;
        }
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 渠道详细客户列表
     */
    public function detail(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $timeTypes = [1=>'今日', 2=>'昨日', 3=>'近三天', 4=>'本周', 5=>'本月',];
        $data = [];
        $dictModel = new SystemDict();
        $userModel = new SystemUser();
        $customModel = new Customer();
        
        // 获取客户维度的码表
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        // 筛选项
        $params = $request->all();
        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();

        $data['title'] = empty($params['channel']) ? '无渠道' : $data['sourceList'][$params['channel']];
        $data['title'] .= " | ". $params['star'] . "星" ;
        $data['title'] .= " | [" ;
        if ($params['timeType'] !=6) {
            $data['title'] .= $timeTypes[$params['timeType']] ;
        } else {
            $data['title'] .= $params['times'][0] . ' ~ ' . $params['times'][1];
        }
        $data['title'] .= "]" ;

        // 加工显示项目
        $list = $customModel->getLists($params);
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
        $data['list'] = $list;
        $data['total'] = $customModel->getCount($params);
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 渠道配置
     */
    public function config(Request $request) {
        $query = new Channel();
        $params = $request->all();
        $dictModel = new SystemDict();
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        $sourceMap = $customGourpDict[$dictModel::TYPE_SOURCE]; // 渠道来源
        if (isset($params['name']) && $params['name'] !== "") {
            $query = $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['status']) && $params['status'] !== "") {
            $query = $query->where('status',  $params['status']);
        }
        $list = $query->get();
        foreach ($list as $key => $item) {
            $item['type'] = $item['type'] == 1 ? "渠道接入CRM" : "CRM接入渠道";
            $item['show_name'] = $sourceMap[$item['id']];
            $item['app_id'] = $item['id'] + 10030;
            $list[$key] = $item;
        }
        $data['list'] = $list;
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 获取渠道信息
     */
    public function info(Request $request) {
        $model = new Channel();
        $params = $request->all();
        $dictModel = new SystemDict();
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        $sourceMap = $customGourpDict[$dictModel::TYPE_SOURCE]; // 渠道来源
        $info = [];
        if ($params['id']) {
            $info = $model->find($params['id']);
            $info['show_name'] = $sourceMap[$params['id']];
            $info['type'] = strval($info['type']);
        }
        return $this->apiReturn(static::OK, ['data'=>$info], '修改成功');
    }

    /**
     * 修改流转规则
     */
    public function edit(Request $request) {
        $model = new Channel();
        $dictModel = new SystemDict();
        $params = $request->all();
        $showName = $params['show_name'];
        unset($params['show_name']);
        if ($params['id']) {
            $model = $model->find($params['id']);
            $model->update($params);
            $channelId = $params['id'];
            $dictModel->where('type', $dictModel::TYPE_SOURCE)->where('tid', $channelId)->update(['name' => $showName]);
        } else {
            $params['cost'] = floatval($params['cost']);
            $channel = $model->create($params);
            $channelId = $channel->id;
            $dictModel->create(
                [
                    'type' => $dictModel::TYPE_SOURCE,
                    'tid' => $channelId,
                    'name' => $showName,
                    'groups' => $dictModel::GROUP_CUSTOM
                ]
            );
        }
        return $this->apiReturn(static::OK, [], $channelId);
    }


    /**
     * 锁定用户
     */
    public function setstatus(Request $request) {
        $params = $request->all();
        $model = Channel::find($params['id']);
        $model->status= $params['status'];
        $model->save();
        return $this->apiReturn(static::OK, [], '操作成功');
    }
    
}
