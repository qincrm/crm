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
        $query = $this;
        if (isset($params['name']) && $params['name'] !== "") {
            $query = $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['mobile']) && $params['mobile'] !== "") {
            $query = $query->where('mobile',  $params['mobile']);
        }
        if (isset($params['city']) && $params['city'] !== "") {
            $query = $query->where('city',  $params['city']);
        }
        if (isset($params['followUserId']) && $params['followUserId'] !== "") {
            $query = $query->where('follow_user_id',  $params['followUserId']);
        }
        
        if (isset($params['followStatus']) && $params['followStatus'] !== "") {
            $query = $query->where('follow_status',  $params['followStatus']);
        }
        if (isset($params['star']) && $params['star'] !== "") {
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
        if ($params['custom_ids']) {
            $query = $query->whereIn('id', $params['custom_ids']);
        }
        if (isset($params['userFrom']) && $params['userFrom'] !== "") {
            $query = $query->where('user_from',  $params['userFrom']);
        }
        if (isset($params['source']) && $params['source'] !== "") {
            $query = $query->where('source',  $params['source']);
        }
        if (isset($params['channel']) && $params['channel'] !== "") {
            $query = $query->where('source',  $params['channel']);
        }
        if (isset($params['zizhi']) && is_array($params['zizhi']) && in_array(1, $params['zizhi'])) {
            $query = $query->where('house', "!=", 1);
        }
        if (isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(2, $params['zizhi'])) {
            $query = $query->where('car',  "!=", 1);
        }
        if (isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(3, $params['zizhi'])) {
            $query = $query->where('policy',  "!=", 1);
        }
        if (isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(4, $params['zizhi'])) {
            $query = $query->where('insurance',   "!=",1);
        }
        if (isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(5, $params['zizhi'])) {
            $query = $query->where('funds',   "!=",1);
        }
        if (isset($params['zizhi'])  && is_array($params['zizhi']) && in_array(6, $params['zizhi'])) {
            $query = $query->where('credit', 1);
        }
        if (isset($params['notFollow']) && $params['notFollow']!= '') {
            $query = $query->where('follow_user_id',  '>', 0)
                        ->where('assign_time', '<', time() - ($params['notFollow'] * 24 *3600))
                        ->where('follow_time', '<', time() - ($params['notFollow'] * 24 *3600));
        }
        if (isset($params['createTime']) && !empty($params['createTime']) && is_array($params['createTime']) ) {
            $query = $query->where('apply_time',  '>', strtotime($params['createTime'][0]));
            $query = $query->where('apply_time',  '<', strtotime($params['createTime'][1]));
        }
        if (isset($params['followTime']) && !empty($params['followTime']) && is_array($params['followTime']) ) {
            $query = $query->where('follow_time',  '>', strtotime($params['followTime'][0]));
            $query = $query->where('follow_time',  '<', strtotime($params['followTime'][1]));
        }
        if ($params['type'] == 'CustomerList') {
            $query = $query->where('follow_user_id', '>' , 0);
        }
        if ($params['type'] == 'CustomerImportList') {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  1);
        }
        if ($params['type'] == 'CustomerUnvalid') {
            $query = $query->where('status',  0);
        } else {
            $query = $query->where('status',  1);
        }
        if ($params['team_id']) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('follow_user_id', $userIds);
        }
        if ($params['type'] == 'CustomerNewList') {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            $query = $query->where('user_from',  1);
        } 
        if ($params['type'] == 'CustomerInnerList') {
            $query = $query->where('follow_user_id', '>' , 0);
            $query = $query->where('important',  0);
            $query = $query->where('user_from', '!=', 1);
        } 
        if (isset($params['users']) && in_array($params['type'], ['CustomerImportList', 'CustomerInnerList', 'CustomerNewList', 'CustomerList'])) {
            if ($params['users']) {
                $query = $query->whereIn('follow_user_id',  $params['users']);
            } else {
                $query = $query->where('follow_user_id', 99999999);
            }
        }
        if ($params['type'] == 'CustomerNewpool') {
            $query = $query->where('follow_user_id',  0);
            $query = $query->where('user_from',  1);
        }
        if ($params['type'] == 'CustomerPool') {
            $query = $query->where('follow_user_id',  0);
            $query = $query->where('user_from', '!=', 1);
        }
        if ($params['timeType'] == 1) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00'));
        }
        if ($params['timeType'] == 2) {
            $query = $query->where('create_time', '>', date('Y-m-d 00:00:00', strtotime("-1 day")));
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
            $query = $query->where('create_time', '<', $params['times'][1]. ' 00:00:00');
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
        $groupby = 'source';
        if ($params['type'] == 'user') {
            $groupby = 'follow_user_id';
        }
        $list = $this->_createChannelWhere($params)->select($groupby, 'star', DB::raw('count(*) as cnt'))->groupBy($groupby, 'star')->get();
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
