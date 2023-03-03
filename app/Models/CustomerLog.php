<?php
 
namespace App\Models;

use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Model;

class CustomerLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_log';

    protected $guarded = [];

    public $timestamps = false;

    const TYPE_IN = 1; // 录入客户 
    const TYPE_IMPORTANT = 2; // 设置为重要
    const TYPE_NOT_IMPORTANT = 3; // 设置为非重要
    const TYPE_LOCK = 4; // 设置为锁定
    const TYPE_UNLOCK = 5; // 设置为解锁
    const TYPE_GIVEUP = 6; // 移入公海
    const TYPE_ASSIGN = 7; // 分配
    const TYPE_FOLLOW = 8; // 跟进
    const TYPE_STAR   = 9; // 星级变化
    const TYPE_BACK = 10; // 星级变化
    const TYPE_GET  = 11; // 星级变化
    const TYPE_UNVALID = 12; //
    const TYPE_NOT_UNVALID = 13; //
    const TYPE_EDIT = 14; //
    const TYPE_ASSIGN_NEW = 15; // 新数据分配
    const TYPE_INTRO = 16; // 转介绍
    const TYPE_DIANPING = 17; //
    const TYPE_CALL = 18; //

    const TYPE_MAPPING = [
        self::TYPE_IN => '录入客户',
        self::TYPE_IMPORTANT => '设置重要客户',
        self::TYPE_NOT_IMPORTANT => '取消重要客户',
        self::TYPE_LOCK => '锁定客户',
        self::TYPE_UNLOCK => '解锁客户',
        self::TYPE_GIVEUP =>  '移入公海',
        self::TYPE_ASSIGN => '客户重分配',
        self::TYPE_ASSIGN_NEW => '新数据分配',
        self::TYPE_FOLLOW => '跟进',
        self::TYPE_STAR   => '星级变化',
        self::TYPE_BACK => '回款成功',
        self::TYPE_GET  => '客户认领',
        self::TYPE_EDIT => '客户资料修改',
        self::TYPE_UNVALID=> '设为无效',
        self::TYPE_NOT_UNVALID=> '设为有效',
        self::TYPE_INTRO => '转介绍',
        self::TYPE_DIANPING => '主管点评',
        self::TYPE_CALL => '拨打电话',
    ];


    public function saveLog($type, $id, $before, $after, $userId = 0, $remark = '') {
        $this->type = $type;
        $this->customer_id = $id;
        $this->before = $before;
        $this->after = $after;
        $this->remark = $remark;
        $this->user_id = $userId;
        return $this->save();
    }


    public function getLogListByCustomerId($customerId, $limit) {
        $dictModel = new SystemDict();
        $userModel = new SystemUser();
        $followUserArray = $userModel->getAllUserWithDelMap();
        $dict = $dictModel->getListByGroup($dictModel::GROUP_CUSTOM);
        $list = $this->where('customer_id', $customerId)->whereRaw('type not in ('.static::TYPE_CALL.')')->orderBy("id", "desc")->take($limit)->get();
        foreach ($list as $key => $item) {
            switch($item['type']) {
                case static::TYPE_STAR:
                    $item['remark'] .= "(".$dict[$dictModel::TYPE_STAR][$item['before']]."->".$dict[$dictModel::TYPE_STAR][$item['after']].")";
                    break;
                case static::TYPE_FOLLOW:
                    $item['remark'] .= "(".$dict[$dictModel::TYPE_FOLLOW][$item['before']]."->".$dict[$dictModel::TYPE_FOLLOW][$item['after']].")";
                    break;
                case static::TYPE_ASSIGN:
                    $item['remark'] = CustomerService::ASSIGN_TYPE_MAPPING[$item['remark']] . "(".$followUserArray[$item['before']]."->".$followUserArray[$item['after']].")";
                    break;
                case static::TYPE_ASSIGN_NEW:
                    $item['remark'] = CustomerService::ASSIGN_TYPE_MAPPING[$item['remark']] . "(".$followUserArray[$item['before']]."->".$followUserArray[$item['after']].")";
                    break;
                case static::TYPE_GET:
                    $item['remark'] = CustomerService::ASSIGN_TYPE_MAPPING[$item['remark']] . "(".$followUserArray[$item['before']]."->".$followUserArray[$item['after']].")";
                    break;
                case static::TYPE_INTRO:
                    $item['remark'] = $item['remark'];
                    break;
                case static::TYPE_FOLLOW:
                    $custom = Customer::find($item['before']);
                    $item['remark'] = '转介绍('.$item['before'].'-'.$custom['name'].')';
                    break;
            }
            $item['username'] = $item['user_id'] ? $followUserArray[$item['user_id']] : '系统';
            $list[$key] = $item;
        }
        return $list;
    }


    public function getLogListByCustomerIdAndType($customerId, $type, $params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->where('customer_id', $customerId)->where('type', $type)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }

    public function getCountByCustomerIdAndType($customerId, $type) {
        return $this->where('customer_id', $customerId)->where('type', $type)->count();
    }

    public function getCountByUserIdAndTime($userId, $type, $startTime, $endTime) {
        $sql = "SELECT  count(distinct customer_id) as cnt, max(id) as id FROM customer_log where type = ? 
            and `after` = ? and create_time >= ? and create_time < ?";
        return app('db')->select($sql, [$type, $userId, $startTime, $endTime]);
    }
    public function getLogListByUserIdAndType($userId, $type, $offset, $pageSize) {
        $list = $this->where('user_id', $userId)->where('type', $type)->orderBy("id", "desc")->skip($offset)->take($pageSize)->get();
        return $list;
    }
}
