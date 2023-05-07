<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\Customer;
use App\Models\CustomerBack;
use App\Models\CustomerLog;
use App\Models\CustomerRemarkLog;
use App\Models\Excel\CustomerModel;
use App\Models\Notice;
use App\Models\SystemDict;
use App\Models\SystemTeam;
use App\Models\SystemUser;
use App\Services\CustomerEditService;
use App\Services\CustomerService;
use App\Services\RightService;
use App\Services\ToolService;
use App\Services\SelectService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CustomController extends Controller
{
    /**
     * 客户列表
     */
    public function list(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $data = [];
        $dictModel = new SystemDict();
        $userModel = new SystemUser();
        $customModel = new Customer();
        $customerRemarkLog = new CustomerRemarkLog();
        
        $teammodel = new SystemTeam();
        $allTeamListSelect = $teammodel->getAllTeam();
        $followTeamArray = collect($allTeamListSelect)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        // 获取客户维度的码表
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        $data['cityList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_CITY]); // 城市
        $data['followStatusList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_FOLLOW]); // 跟进状态
        $data['starList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_STAR]); // 星级
        $data['userFromList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_USER_FROM]); // 用户来源
        $data['sourceList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_SOURCE]); // 渠道来源
        $data['zizhiList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_QUALIFICATION]); // 资质说明
        $data['teamList'] = app(SelectService::class)->genSelectByKV($followTeamArray); // 转成前端的select

        // 筛选项
        $params = $request->all();
        $export = $params['export'];
        $data['master'] = 0;
        $followUserArray = app(RightService::class)->getCustomViews($userId);
        if ($followUserArray == 'all') {
            $followUserList = $userModel->getAllUser();
            $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            })->toArray();
            $data['master'] = 1;
        } else {
            $params['users'] = array_keys($followUserArray);
        }
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select


        // 加工显示项目
        $list = $customModel->getLists($params);
        $customIds = [];
        foreach ($list as $key => $item) {
            $customIds[] = $item['id'];
            $item['notFollowTime'] = app(CustomerService::class)->getNotFollowTime($item);
            $item['apply_time'] = empty($item['apply_time']) ? '-' : date('Y-m-d H:i:s', $item['apply_time']);
            $item['follow_time'] = empty($item['follow_time']) ? '-' : date('Y-m-d H:i:s', $item['follow_time']);
            $item['assign_time'] = empty($item['assign_time']) ? '-' : date('Y-m-d H:i:s', $item['assign_time']);
            $item['zizhi'] = app(CustomerService::class)->genZizhi($item);
            if ($item['star'] == -1) {
                $item['starcolor'] = 'lime';
            } else {
                $item['starcolor'] = Constants::TAG_COLOR[$item['star']] ?? '';
            }
            $item['star'] = $customGourpDict[$dictModel::TYPE_STAR][$item['star']] ?? '';
            $item['fstatuscolor'] = Constants::TAG_COLOR[$item['follow_status']];
            $item['follow_status'] = $customGourpDict[$dictModel::TYPE_FOLLOW][$item['follow_status']] ?? '-';
            $item['user_from'] = $customGourpDict[$dictModel::TYPE_USER_FROM][$item['user_from']];
            $item['source_id'] = $item['source'];
            $item['source'] = $customGourpDict[$dictModel::TYPE_SOURCE][$item['source']];
            $item['follow_user'] = $followUserArray[$item['follow_user_id']];
            $item['age']  = empty($item['age']) ? '' : $item['age'];
            $item['amount']  = $item['amount'] == 0 ? '' : intval($item['amount']);
            $item['city'] = $customGourpDict[$dictModel::TYPE_CITY][$item['city']] ?? '-';
            $remarks = $customerRemarkLog->getLogListByCustomerId($item['id'], $export ? 100 : 3);
            $item['remark'] = implode($export ? "\n":"<br/>", collect($remarks)->map(function($item) use ($followUserArray, $export) {
                return $item->remark . ($export ? "[".$item->create_time.":".$followUserArray[$item['user_id']]."]" : "");
            })->toArray());
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['customIds'] = base64_encode(implode('-', $customIds));
        $data['total'] = $customModel->getCount($params);
        if ($export == 1) {
            app(ToolService::class)->csv(
                "我的客户",
                [
                    "id" => "id",
                    "name" => "姓名",
                    "mobile" => "手机号",
                    "star" => "星级",
                    "source" => "渠道",
                    "follow_status" => "跟进状态",
                    "remark" => "跟进备注"  ,
                    "follow_time" => "最新跟进时间" ,
                ],
                $list
            );
        }
        return $this->apiReturn(static::OK, $data);
    }


    /**
     * 获取用户信息
     */
    public function info(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $color = ['', 'green', 'blue', 'purple', 'pinkpurple', 'red'];
        $params = $request->all();

        $dictModel = new SystemDict();
        $customModel = new Customer();

        // 获取客户维度的码表
        $customGourpDict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM); 
        $data['cityList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_CITY]); // 城市
        $data['followStatusList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_FOLLOW]); // 跟进状态
        $data['starList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_STAR]); // 星级
        $data['userFromList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_USER_FROM]); // 用户来源
        $data['sourceList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_SOURCE]); // 渠道来源
        $data['zizhiList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_QUALIFICATION]); // 资质说明
        $data['workList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_WORK]); // 工作类型
        $data['noticesList'] = app(SelectService::class)->genSelectByKV($customGourpDict[$dictModel::TYPE_NOTICE]); // 通知类型
        $logList = [];

        if (isset($params['id']) && !empty($params['id'])) {
            $model = $customModel->find($params['id']);
            $followUserArray = app(RightService::class)->getCustomViews($userId);
            // 防止越权
            if (!in_array($model->follow_user_id, array_keys($followUserArray))) {
                return $this->apiReturn(static::ERROR, [], '无客户权限');
            }
            ToolService::unsetEmptyField($model, ['sex', 'age', 'marry', 'work', 'live_area', 'household_area', 'income', 'amount', 'follow_status', 'star', 'city']);
            unset($model['remark']);

            // 客户操作日志大杂烩
            $logmodel = new CustomerLog();
            $logList = $logmodel->getLogListByCustomerId($params['id'], 100);
            foreach ($logList as $key => $log) {
                $log->color = $color[($log->type % 5) + 1];
                $log->type = $logmodel::TYPE_MAPPING[$log->type]." (".$log->username.")";
                $log->day = $log->create_time;
                $logList[$key] = $log;
            }
        } else {
            $model = new Customer();
        }
        $data['customInfo'] = $model;
        $data['logList'] = $logList;

        return $this->apiReturn(static::OK, $data);
    }


    /**
     * 修改用户信息
     */
    public function edit(Request $request)
    {
        $params = $request->all();

        unset($params['notices']);
        unset($params['update_time']);

        $operUserId = $request->session()->get('user_id');

        $service = app(CustomerEditService::class);

        DB::beginTransaction();

        if (isset($params['id'])) {
            $result = $service->edit($params, $operUserId);
        } else {
            $result = $service->add($params, $operUserId);
        }

        if ($result['status'] == 1) {
            DB::commit();
        } else {
            DB::rollBack();
            return $this->apiReturn(static::ERROR, [], $result['error']);
        }

        return $this->apiReturn(static::OK, [], "操作成功");
    }


    /**
     * 客户分配
     */
    public function assign(Request $request)
    {
        $params = $request->all();
        $operUserId = $request->session()->get('user_id');

        $followUserId = $params['followUserId'];
        $customIds = $params['customIds'];

        $customService = app(CustomerService::class);

        foreach ($customIds as $customId) {
            if (!$customService->canAssign($customId)) {
                $model = new Customer();
                $custom = $model->find($customId);
                return $this->apiReturn(static::ERROR, [], $custom['name']."(".$customId.")正在跟进中，暂时无法分配");
            }
            $flag = $customService->assign($customId, $followUserId, $operUserId);
            if (!$flag) {
                return $this->apiReturn(static::ERROR, [], "部分客户分配失败,请刷新重试");
            }
        }

        return $this->apiReturn(static::OK);
    }

    /**
     * 客户认领
     */
    public function get(Request $request)
    {
        $params = $request->all();
        $operUserId = $request->session()->get('user_id');
        $customId = $params['id'];

        $customService = app(CustomerService::class);
        $userService = app(UserService::class);

        $leftCount = $userService->getGetLeftTime($operUserId);
        if ($leftCount <= 0) {
            return $this->apiReturn(static::ERROR, [], "客户认领失败,超过今日认领上限");
        }
        if (!$customService->canAssign($customId)) {
            $model = new Customer();
            $custom = $model->find($customId);
            return $this->apiReturn(static::ERROR, [], $custom['name']."(".$customId.")正在跟进中，暂时无法分配");
        }

        $flag = $customService->get($customId, $operUserId, $operUserId);
        if (!$flag) {
            return $this->apiReturn(static::ERROR, [], "客户认领失败,请刷新重试");
        }

        return $this->apiReturn(static::OK);
    }


    /**
     * 分配记录LIST
     */
    public function assignlist(Request $request)
    {

        $params = $request->all();
        $userModel = new SystemUser();
        $logModel = new CustomerLog();

        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = ($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();

        $list = $logModel->getLogListByCustomerIdAndType($params['customId'], $logModel::TYPE_ASSIGN, $params);
        foreach ($list as $key => $item) {
            $item['old_user'] = $followUserArray[$item['before']];
            $item['new_user'] = $followUserArray[$item['after']];
            $item['oper_user'] = $followUserArray[$item['user_id']];
            $item['type'] = CustomerService::ASSIGN_TYPE_MAPPING[$item['remark']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $logModel->getCountByCustomerIdAndType($params['customId'], $logModel::TYPE_ASSIGN);
        return $this->apiReturn(static::OK, $data);
    }



    /**
     * 跟进记录LIST
     */
    public function followlist(Request $request)
    {

        $params = $request->all();
        $dict = new SystemDict();
        $userModel = new SystemUser();
        $logModel = new CustomerLog();

        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();

        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置

        $list = $logModel->getLogListByCustomerIdAndType($params['customId'], $logModel::TYPE_FOLLOW, $params);
        foreach ($list as $key => $item) {
            $item['old_status'] = $customGourpDict[$dict::TYPE_FOLLOW][$item['before']];
            $item['new_status'] = $customGourpDict[$dict::TYPE_FOLLOW][$item['after']];
            $item['oper_user'] = $followUserArray[$item['user_id']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $logModel->getCountByCustomerIdAndType($params['customId'], $logModel::TYPE_FOLLOW);
        return $this->apiReturn(static::OK, $data);
    }



    /**
     * 星级变化记录
     */
    public function starlist(Request $request)
    {

        $params = $request->all();
        $dict = new SystemDict();
        $userModel = new SystemUser();
        $logModel = new CustomerLog();

        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置

        $list = $logModel->getLogListByCustomerIdAndType($params['customId'], $logModel::TYPE_STAR, $params);
        foreach ($list as $key => $item) {
            $item['old_value'] = $customGourpDict[$dict::TYPE_STAR][$item['before']];
            $item['new_value'] = $customGourpDict[$dict::TYPE_STAR][$item['after']];
            $item['oper_user'] = $followUserArray[$item['user_id']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $logModel->getCountByCustomerIdAndType($params['customId'], $logModel::TYPE_STAR);
        return $this->apiReturn(static::OK, $data);
    }



    /**
     * 锁定
     */
    public function lock(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        DB::beginTransaction();
        $model = Customer::find($params['id']);
        $model->lock = $params['lock'];
        $model->save();
        $logmodel = new CustomerLog();
        $logmodel->saveLog($model->lock == 1 ? $logmodel::TYPE_LOCK: $logmodel::TYPE_UNLOCK, $params['id'], '', '', $userId, '');
        DB::commit();
        return $this->apiReturn(static::OK);
    }

    /**
     * 移入公海 
     */
    public function giveup(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $customService = app(CustomerService::class);
        $customService->giveup($params['id'], $userId, '移入公海');
        return $this->apiReturn(static::OK);
    }


    /**
     * 批量移入公海 
     */
    public function batchgiveup(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $customIds = $params['customIds'];
        DB::beginTransaction();
        foreach ($customIds as $customId) {
            $customService = app(CustomerService::class);
            $customService->giveup($customId, $userId, '移入公海');
        }
        DB::commit();
        return $this->apiReturn(static::OK);
    }


    /**
     * 批量领取
     */
    public function batchget(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $customIds = $params['customIds'];
        $customService = app(CustomerService::class);
        $userService = app(UserService::class);
        $leftCount = $userService->getGetLeftTime($userId);
        if ($leftCount < count($customIds)) {
            return $this->apiReturn(static::ERROR, [], "认领失败,超过今日认领上限,今日剩余".$leftCount."认领名额");
        }
        DB::beginTransaction();
        foreach ($customIds as $customId) {
            if (!$customService->canAssign($customId)) {
                $model = new Customer();
                $custom = $model->find($customId);
                return $this->apiReturn(static::ERROR, [], $custom['name']."(".$customId.")正在跟进中，暂时无法分配");
            }
            $customService->get($customId, $userId, $userId);
        }
        DB::commit();
        return $this->apiReturn(static::OK);
    }

    /**
     * 设为重要
     */
    public function important(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        DB::beginTransaction();
        $model = Customer::find($params['id']);
        $model->important = $params['important'];
        $model->save();
        $logmodel = new CustomerLog();
        $logmodel->saveLog($model->important == 1 ? $logmodel::TYPE_IMPORTANT : $logmodel::TYPE_NOT_IMPORTANT, $params['id'], '', '', $userId, '');
        DB::commit();
        return $this->apiReturn(static::OK);
    }

    /**
     * 设为无效
     */
    public function lahei(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $model = Customer::find($params['id']);
        $model->status = $params['status'];
        $model->save();
        $logmodel = new CustomerLog();
        $logmodel->saveLog($model->status == 1 ? $logmodel::TYPE_NOT_UNVALID : $logmodel::TYPE_UNVALID, $params['id'], '', '', $userId, '');
        return $this->apiReturn(static::OK);
    }

    /**
     * 批量导入用户
     */
    public function upload(Request $request) {
        $files = ($request->file());
        $fileName = array_keys($files)[0];
        if (!in_array($fileName, ['CustomerPool', 'CustomerNewpool'])) {
            return $this->apiReturn(static::ERROR, [], "入口异常,请刷新重试");
        }
        try {
            $data = Excel::toArray(new CustomerModel, $request->file($fileName))[0];
        } catch (\Exception $e) {
            return $this->apiReturn(static::ERROR, [], "文件格式异常请重试");
        }
        array_shift($data);
        if (empty($data)) {
            return $this->apiReturn(static::ERROR, [], "客户信息为空");
        }
        $service = app(\App\Services\CustomerService::class);
        $error = [];
        $customs = [];
        $dict = new SystemDict();
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置
        $index = 1;
        $mobiles = [];
        $times = 0;
        $success = 0;
        foreach ($data as $row) {
            $customCheck = $service->genCustomByExcelRow($row, $customGourpDict);
            if ($customCheck['status'] == 1) {
                if ($fileName == 'CustomerNewpool') {
                    $customCheck['custom']['user_from'] = 1;
                } else if ($fileName == 'CustomerPool') {
                    $customCheck['custom']['user_from'] = 2;
                }
                $customCheck['custom']['mobile_md5'] = md5($customCheck['custom']['mobile']);
                $customCheck['custom']['source'] = intval(Constants::EXPORT_CHANNEL);
                if (in_array($customCheck['custom']['mobile'], $mobiles) || count(Customer::where('mobile', $customCheck['custom']['mobile'])->get()) > 0) {
                    $times++;
                } else {
                    $success++;
                    $customs[] = $customCheck['custom'];
                }
            } else {
                $error[] = "第".$index."行:".implode(PHP_EOL, $customCheck['error']);
            }
            $index++;
        }
        if (empty($error)) {
            (new Customer())->insert($customs);
            return $this->apiReturn(static::OK, [], '导入成功'.$success.'个客户, 重复'.$times.'个');
        } else {
            return $this->apiReturn(static::ERROR, [], implode(PHP_EOL, $error));
        }
        
    }

    /**
     * 待办事项列表
     */
    public function getNoticeList (Request $request) {
        $params = $request->all();
        $userId = $request->session()->get('user_id');
        $customId = $params['id'];
        $notice = new Notice();
        $list = $notice->getListByUserIdAndCustomer($customId, $userId, 3);
        $color = ['', 'green', 'blue', 'purple', 'pinkpurple', 'red'];
        $dict = new SystemDict();
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置
        foreach ($list as $key => $item) {
            $item['color'] = $color[$item['type']];
            $item['type'] = $customGourpDict[$dict::TYPE_NOTICE][$item['type']];
            $item['day'] = date('Y-m-d H:i:s', $item['date']);
            $list[$key] = $item;
        }
        return $this->apiReturn(static::OK, ['list'=>$list]);
    }

    /**
     * 增加代办事项
     */
    public function addNotices(Request $request) {
        $userId = $request->session()->get('user_id');
        $params = $request->all();
        $customId = $params['id'];
        $notices = $params['notices'];
        DB::beginTransaction();
        foreach ($notices as $notice) {
            if (empty($notice['date'])) {
                DB::rollBack();
                return $this->apiReturn(static::ERROR, [], "请选择日期");
            }
            if (empty($notice['remark'])) {
                DB::rollBack();
                return $this->apiReturn(static::ERROR, [], "请输入备注");
            }
            $noticeModel = new Notice();
            $noticeModel->date = strtotime($notice['date']);
            $noticeModel->custom_id = $customId;
            $noticeModel->follow_user_id = $userId;
            $noticeModel->type = $notice['type'];
            $noticeModel->remark = $notice['remark'];
            $flag = $noticeModel->save();
            if (!$flag) {
                DB::rollBack();
                return $this->apiReturn(static::ERROR, [], "日程写入失败");
            }
        }
        DB::commit();
        return $this->apiReturn(static::OK);

    }
    
    /**
     * 添加点评
     */
    public function addDianping(Request $request) {
        $params = $request->all();
        $customId = $params['id'];
        $userId = $request->session()->get('user_id');
        $logmodel = new CustomerLog();
        $logmodel->saveLog($logmodel::TYPE_DIANPING, $params['id'], '', '', $userId, $params['remark']);
        $custom = Customer::find($customId);
        $noticeModel = new Notice();
        $noticeModel->date = time();
        $noticeModel->custom_id = $customId;
        $noticeModel->follow_user_id = $custom['follow_user_id'];
        $noticeModel->type = 5;
        $noticeModel->remark = $params['remark'];
        $noticeModel->save();
        DB::commit();
        return $this->apiReturn(static::OK);
    }

    /**
     * 添加回款
     */
    public function addBack(Request $request) {
        $params = $request->all();
        $userId = $request->session()->get('user_id');
        $customId = $params['id'];
        $model = Customer::find($params['id']);
        $backmodel = new CustomerBack();
        if ($errorMsg = ToolService::checkParams($params, [
            'date' => '放款时间',
            'amount' => '放款金额',
            'agency_fee' => '点位',
            'realmoney' => '实际创收',
        ])) {
            return $this->apiReturn(static::ERROR, [], $errorMsg);
        }
        $backmodel->custom_id = $params['id'];
        $backmodel->date = strtotime($params['date']);
        $backmodel->amount = $params['amount'];
        $backmodel->fee = $params['agency_fee'];
        $backmodel->real_amount = $params['realmoney'];
        $backmodel->remark = $params['remark'];
        $backmodel->cost = $params['cost'];
        $backmodel->quanzheng = $params['quanzheng'];
        $backmodel->apply_date = intval($model->apply_time);
        $backmodel->apply_amount = floatval($model->amount);
        $backmodel->follow_user_id = intval($model->follow_user_id);
        $backmodel->oper_user_id = $userId;
        $backmodel->hetong = $params['hetong'];
        $backmodel->product_id= intval($params['product_id']);
        $backmodel->status = 1;
        $backmodel->save();
        $logModel = new CustomerLog();
        $logModel->saveLog($logModel::TYPE_BACK, $params['id'], '', '', $userId, "金额:".$params['amount']);
        return $this->apiReturn(static::OK);

    }


    /**
     * 编辑回款
     */
    public function editBack(Request $request) {
        $params = $request->all();
        $userId = $request->session()->get('user_id');
        $backmodel = new CustomerBack();
        $backmodel = $backmodel->find($params['id']);
        if (empty($params['id']) || empty($backmodel)) {
            return $this->apiReturn(static::ERROR, [], "参数错误,请刷新重试");
        }
        if ($errorMsg = ToolService::checkParams($params, [
            'date' => '放款时间',
            'amount' => '放款金额',
            'agency_fee' => '点位',
            'realmoney' => '实际创收',
        ])) {
            return $this->apiReturn(static::ERROR, [], $errorMsg);
        }
        $backmodel->date = strtotime($params['date']);
        $backmodel->amount = $params['amount'];
        $backmodel->fee = $params['agency_fee'];
        $backmodel->real_amount = $params['realmoney'];
        $backmodel->remark = $params['remark'];
        $backmodel->cost = $params['cost'];
        $backmodel->hetong = $params['hetong'];
        $backmodel->product_id= intval($params['product_id']);
        $backmodel->save();
        return $this->apiReturn(static::OK);

    }

    /**
     * 编辑回款
     */
    public function delBack(Request $request) {
        $params = $request->all();
        $backmodel = new CustomerBack();
        $backmodel = $backmodel->find(intval($params['id']));
        $backmodel->delete();
        return $this->apiReturn(static::OK);

    }
    /**
     * 回款列表
     */
    public function backlist(Request $request)
    {

        $params = $request->all();
        $dict = new SystemDict();
        $userModel = new SystemUser();
        $model = new CustomerBack();

        $followUserList = $userModel->getAllUserWithDel();
        $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置

        $list = $model->getLists($params);
        foreach ($list as $key => $item) {
            $item['apply_amount'] = $item['apply_amount'] ? $item['apply_amount'] : '';
            $item['fee'] = $item['fee'] . '%';
            $item['apply_date'] = $item['apply_date'] ? date('Y-m-d H:i:s', $item['apply_date']) : '';
            $item['date'] = $item['date'] ? date('Y-m-d H:i:s', $item['date']) : '';
            $item['new_value'] = $customGourpDict[$dict::TYPE_STAR][$item['after']];
            $item['oper_user'] = $followUserArray[$item['oper_user_id']];
            $list[$key] = $item;
        }
        $data['list'] = $list;
        $data['total'] = $model->getCount($params);
        return $this->apiReturn(static::OK, $data);
    }
    

    /**
     * 认领后未跟进
     */
    public function follownum(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $data = ['custom_num'=>0, 'approve_num'=>0];
        if ($userId > 0) {
            $model = new Customer();
            $data['custom_num'] =  $model->getWaitForFollow($userId);
        }
        return $this->apiReturn(static::OK, $data);
    }



     /**
     * 统计数据 
     */
    public function data(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $userModel = new SystemUser();
        $customModel = new Customer();
        $data = [];
        
        // 筛选项
        $params = $request->all();
        $data['master'] = 0;
        $followUserArray = app(RightService::class)->getCustomViews($userId);
        if ($followUserArray == 'all') {
            $followUserList = $userModel->getAllUser();
            $followUserArray = collect($followUserList)->mapWithKeys(function ($item) {
                return [$item['id'] => $item['name']];
            })->toArray();
            $data['master'] = 1;
        } else {
            $params['users'] = array_keys($followUserArray);
        }
        $data['followUserList'] = app(SelectService::class)->genSelectByKV($followUserArray); // 转成前端的select

        $data['num1'] = $customModel->getCount(array_merge($params, ['notFollow'=>1]));
        $data['num2'] = $customModel->getCount(array_merge($params, ['notFollow'=>3, 'star'=>6]));
        $data['num3'] = $customModel->getCount(array_merge($params, ['notFollow'=>5, 'star'=>6]));
        $data['num4'] = $customModel->getCount(array_merge($params, ['notFollow'=>7, 'star'=>6]));
        $data['num5'] = $customModel->getCount(array_merge($params, ['notFollow'=>1, 'star'=>6]));
        return $this->apiReturn(static::OK, $data);
    }
}
