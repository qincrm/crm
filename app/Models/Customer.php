<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer';

    protected $guarded = [];

    public $timestamps = false;


    private function _createWhere($params) {
        $query = $this->when(isset($params['name']) && $params['name'] !== "", function ($query) use ($params) {
            return $query->where('name', 'like', '%'.$params['name'].'%');
        })->when(isset($params['mobile']) && $params['mobile'] !== "", function ($query) use ($params) {
            return $query->where('mobile',  $params['mobile']);
        })->when(isset($params['city']) && $params['city'] !== "", function ($query) use ($params) {
            return $query->where('city',  $params['city']);
        })->when(isset($params['followUserId']) && $params['followUserId'] !== "", function ($query) use ($params) {
            return $query->where('follow_user_id',  $params['followUserId']);
        })->when(isset($params['followStatus']) && $params['followStatus'] !== "", function($query) use ($params) {
            return $query->where('follow_status',  $params['followStatus']);
        })->when(isset($params['star']) && $params['star'] !== "", function($query) use ($params) {
            if ($params['star'] == 6) {
                return $query->where('star', '>=', '2');
            } else if ($params['star'] == 7) {
                return $query->where('star', '>=', '3');
            } else if ($params['star'] == 8) {
                return $query->where('star', '>=', '4');
            } else {
                return $query->where('star',  $params['star']);
            }
        })->when($params['custom_ids'], function($query) use ($params) {
            return $query->whereIn('id', $params['custom_ids']);
        })->when(isset($params['userFrom']) && $params['userFrom'] !== "", function($query) use ($params) {
            return $query->where('user_from',  $params['userFrom']);
        })->when(isset($params['source']) && $params['source'] !== "", function($query) use ($params) {
            return $query->where('source',  $params['source']);
        })->when(isset($params['channel']) && $params['channel'] !== "", function($query) use ($params) {
            return $query->where('source',  $params['channel']);
        })->when(isset($params['zizhi']) && is_array($params['zizhi']) && in_array(1, $params['zizhi']), function($query) use ($params) {
            return $query->where('house', "!=", 1);
        })->when(isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(2, $params['zizhi']), function($query) use ($params) {
            return $query->where('car',  "!=", 1);
        })->when(isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(3, $params['zizhi']), function($query) use ($params) {
            return $query->where('policy',  "!=", 1);
        })->when(isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(4, $params['zizhi']), function($query) use ($params) {
            return $query->where('insurance',   "!=",1);
        })->when(isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(5, $params['zizhi']), function($query) use ($params) {
            return $query->where('funds',   "!=",1);
        })->when(isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(6, $params['zizhi']), function($query) use ($params) {
            return $query->where('credit', 1);
        })->when(isset($params['notFollow']) && $params['notFollow']!= '', function($query) use ($params) {
            return $query->where('follow_user_id',  '>', 0)
                        ->where('assign_time', '<', time() - ($params['notFollow'] * 24 *3600))
                        ->where('follow_time', '<', time() - ($params['notFollow'] * 24 *3600));
        })->when(isset($params['createTime']) && !empty($params['createTime']) && is_array($params['createTime']), function($query) use ($params ) {
            $query = $query->where('apply_time',  '>', strtotime($params['createTime'][0]));
            return $query->where('apply_time',  '<', strtotime($params['createTime'][1]));
        })->when(isset($params['followTime']) && !empty($params['followTime']) && is_array($params['followTime']), function($query) use ($params ) {
            $query = $query->where('follow_time',  '>', strtotime($params['followTime'][0]));
            return $query->where('follow_time',  '<', strtotime($params['followTime'][1]));
        })->when($params['type'] == 'CustomerList', function($query) use ($params) {
            return $query->where('follow_user_id', '>' , 0);
        })->when($params['type'] == 'CustomerImportList', function($query) use ($params) {
            $query = $query->where('follow_user_id', '>' , 0);
            return $query->where('important',  1);
        })->when($params['team_id'], function($query) use ($params) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            return $query->whereIn('follow_user_id', $userIds);
        })->when($params['type'] == 'CustomerNewList', function($query) use ($params) {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            return $query->where('user_from',  1);
        })->when($params['type'] == 'CustomerInnerList', function($query) use ($params) {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            return $query->where('user_from', '!=', 1);
        })->when(isset($params['users']) && in_array($params['type'], ['CustomerImportList', 'CustomerInnerList', 'CustomerNewList', 'CustomerList']), function($query) use ($params) {
            if ($params['users']) {
                return $query->whereIn('follow_user_id',  $params['users']);
            } else {
                return $query->where('follow_user_id', 99999999);
            }
        })->when($params['type'] == 'CustomerNewpool', function($query) use ($params) {
            $query = $query->where('follow_user_id',  0);
            return $query->where('user_from',  1);
        })->when($params['type'] == 'CustomerPool', function($query) use ($params) {
            $query = $query->where('follow_user_id',  0);
            return $query->where('user_from', '!=', 1);
        })->when($params['timeType'] == 1, function($query) use ($params) {
            return $query->where('create_time', '>', date('Y-m-d 00:00:00'));
        })->when($params['timeType'] == 2, function($query) use ($params) {
            return $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-1 day")));
        })->when($params['timeType'] == 3, function($query) use ($params) {
            return $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-2 day")));
        })->when($params['timeType'] == 4, function($query) use ($params) {
            $time = time();
            $monday = date('Y-m-d 00:00:00', ($time - ((date('w',$time) == 0 ? 7 : date('w',$time)) - 1) * 24 * 3600));
            return $query->where('create_time', '>', $monday);
        })->when($params['timeType'] == 5, function($query) use ($params) {
            return $query->where('create_time', '>', date("Y-m-01 00:00:00"));
        })->when($params['timeType'] == 6, function($query) use ($params) {
            $query = $query->where('create_time', '>', $params['times'][0]. ' 00:00:00');
            return $query->where('create_time', '<', $params['times'][1]. ' 00:00:00');
        });
        if ($params['type'] == 'CustomerUnvalid') {
            $query = $query->where('status',  0);
        } else {
            $query = $query->where('status',  1);
        }
        return $query;
    }

    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->select('*', DB::raw('
            (CASE WHEN follow_time > assign_time THEN 1 ELSE 0 END) as is_follow,
            (CASE WHEN follow_time > assign_time THEN id ELSE assign_time END) as order_field 
        '))->orderByRaw("is_follow , order_field desc")->skip($offset)->take($params['pageSize'])->get()->toArray();
        return $list;
    }

    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }

    public function _createChannelWhere($params) {
        $query = $this;
        if ($params['users']) {
            $query = $query->whereIn('follow_user_id', $params['users']);
        }
        if ($params['user_id']) {
            $query = $query->where('follow_user_id', $params['user_id']);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('follow_user_id', $userIds);
        }
        if ($params['source']) {
            $query = $query->where('source',  $params['source']);
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
        return $query;
    }
    public function getListsGroupByChannel($params) {
        $list = $this->_createChannelWhere($params)->select('source', 'star', DB::raw('count(*) as cnt'))->groupBy('source', 'star')->get();
        return $list;
    }
    public function getListsGroupByChannel2($params) {
        $groupby = 'source';
        if ($params['type'] == 'user') {
            $groupby = 'follow_user_id';
        }
        $list = $this->_createChannelWhere($params)->select($groupby, DB::raw('CASE follow_status WHEN 0 THEN 0 ELSE 1 END as follow_status1'), DB::raw('count(*) as cnt'))->groupBy($groupby, 'follow_status1')->get();
        return $list;
    }

    public function getMyCount($params) {
        $query = $this;
        if ($params['users']) {
            $query = $query->whereIn('follow_user_id', $params['users']);
        }
        if ($params['important']) {
            $query = $query->where('important', $params['important']);
        }
        if ($params['today']) {
            $query = $query->where('assign_time', '>', strtotime(date('Y-m-d')));
        }
        if (isset($params['follow_status']) && $params['follow_status'] !== "") {
            $query = $query->where('follow_status',  $params['follow_status']);
        }
        if ($params['new']) {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            $query = $query->where('user_from', 1);
        }
        if ($params['inner']) {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            $query = $query->where('user_from', '!=', 1);
        }
        return $query->count();
    }
    public function getCustomerByChannelId($channel, $channelId) {
        $list = $this->where('source', $channel)->get();
        return $list;
    }
 

    public function getWaitForFollow($userId) {
        $sql = "select count(*) as cnt from customer where follow_user_id = ? and assign_time > follow_time and status = 1 and user_from = 1 and important = 0 and status = 1";
        return intval(app('db')->select($sql, [$userId])[0]->cnt);
    }
 
}
