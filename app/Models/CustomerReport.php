<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_report';

    protected $guarded = [];
    public $timestamps = false;

    public function _createChannelWhere($params) {
        $query = $this->join('customer', 'customer_report.customer_id', '=', 'customer.id');
        if ($params['users']) {
            $query = $query->whereIn('customer_report.user_id', $params['users']);
        }
        if ($params['user_id']) {
            $query = $query->where('customer_report.user_id', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('customer_report.user_id', $userIds);
        }
        if ($params['userFrom']) {
            $query = $query->where('customer_report.type2',  $params['userFrom']);
        } else {
            $query = $query->where('customer_report.type2',  99);
        }
        if ($params['reportType'] == 'month' && $params['time2']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time2'].'%');
        } else if ($params['reportType'] == 'week' && $params['time1']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time1'].'%');
        } else {
            $query = $query->where('user_id', '=', 0);
        }
        return $query;
    }

    public function getListsGroupByChannel($params) {
        $list = $this->_createChannelWhere($params)->select('customer_report.user_id', 'customer_report.star', DB::raw('count(*) as cnt'))->groupBy('customer_report.user_id', 'customer_report.star')->get();
        return $list;
    }

    public function getListsGroupByChannel2($params) {
        $list = $this->_createChannelWhere($params)->select(
            'customer_report.user_id', 
            DB::raw('CASE customer_report.follow_status WHEN 0 THEN 0 ELSE 1 END as follow_status1'),
            DB::raw('count(*) as cnt')
        )->groupBy('customer_report.user_id', 'follow_status1')->get();
        return $list;
    }
    public function getAllUser($params) {
        $query = $this->leftJoin('system_user', 'customer_report.user_id', '=', 'system_user.id');
        if ($params['users']) {
            $query = $query->whereIn('customer_report.user_id', $params['users']);
        }
        if ($params['user_id']) {
            $query = $query->where('customer_report.user_id', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('customer_report.user_id', $userIds);
        }
        $query = $query->where('customer_report.type2',  98);
        if ($params['reportType'] == 'month' && $params['time2']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time2'].'%');
        } else if ($params['reportType'] == 'week' && $params['time1']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time1'].'%');
        } else {
            $query = $query->where('user_id', '=', 0);
        }
        $list = $query->select('system_user.*')->get();
        return $list;
    }

    public function getCustomerIds($params) {
        $query = $this;
        $query = $query->where('customer_report.user_id', $params['user_id']);
        if ($params['star']  || $params['star'] !== "") {
            if ($params['star'] == 6) {
                $query = $query->where('star', '>=', '2');
            } else if ($params['star'] == 7) {
                $query = $query->where('star', '>=', '3');
            } else if ($params['star'] == 8) {
                $query = $query->where('star', '>=', '4');
            } else {
                $query = $query->where('star',  $params['star']);
            }
        }
        if ($params['status']  || $params['status'] !== "") {
            if ($params['status'] == 0) {
                $query = $query->where('customer_report.follow_status', 0);
            } else {
                $query = $query->where('customer_report.follow_status', '>', 0);
            }
        }
        if ($params['userFrom']) {
            $query = $query->where('customer_report.type2',  $params['userFrom']);
        } else {
            $query = $query->where('customer_report.type2',  99);
        }
        if ($params['reportType'] == 'month' && $params['time2']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time2'].'%');
        } else if ($params['reportType'] == 'week' && $params['time1']) {
            $query = $query->where('type1', $params['reportType'])->where('day', 'like', '%'.$params['time1'].'%');
        } else {
            $query = $query->where('user_id', '=', 0);
        }
        $list = array_column($query->select('*')->get()->toArray(), 'customer_id');
        return $list;
    }
}