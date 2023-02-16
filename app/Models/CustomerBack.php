<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class CustomerBack extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_backs';

    protected $guarded = [];

    public $timestamps = false;

    private function _createWhere($params) {
        $query = $this->leftJoin('customer', 'customer_backs.custom_id', '=', 'customer.id')->where('customer_backs.status', 1);
        if (isset($params['customId']) && !empty($params['customId'])) {
            $query = $query->where('customer_backs.custom_id', $params['customId']);
        }
        if (isset($params['name']) && !empty($params['name'])) {
            $query = $query->where('customer.name', $params['name']);
        }
        if (isset($params['mobile']) && !empty($params['mobile'])) {
            $query = $query->where('customer.mobile', $params['mobile']);
        }
        if (isset($params['follow_user_id']) && !empty($params['follow_user_id'])) {
            $query = $query->where('customer_backs.follow_user_id', $params['follow_user_id']);
        }
        if (isset($params['team_id']) && !empty($params['team_id'])) {
            $teamModel = new SystemUser();
            $userIds = $teamModel->getUserByTeamid($params['team_id'])->map(function($item) {return $item['id'];});
            $query = $query->whereIn('customer_backs.follow_user_id', $userIds);
        }
        if (isset($params['applyTime']) && !empty($params['applyTime']) && is_array($params['applyTime']) ) {
            $query = $query->where('apply_date',  '>', strtotime($params['applyTime'][0]));
            $query = $query->where('apply_date',  '<', strtotime($params['applyTime'][1]));
        }
        if (isset($params['backTime']) && !empty($params['backTime']) && is_array($params['backTime']) ) {
            $query = $query->where('date',  '>', strtotime($params['backTime'][0]));
            $query = $query->where('date',  '<', strtotime($params['backTime'][1]));
        }
        return $query;
    }

    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->select("customer_backs.*", "customer.name", "customer.source", "customer.user_from")->orderBy("customer_backs.id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }

    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }

    /**
     * 排行榜
     */
    public function getPaihangbang() {
        $sql = "SELECT a.follow_user_id,sum(a.amount) as amount,sum(a.real_amount) real_amount, b.name user_name, c.name team_name FROM `customer_backs` a,
            system_user b left join system_team c on b.team_id = c.id  
            where a.follow_user_id = b.id and a.status = 1 and a.create_time > '".date("Y-m-01")."' group by a.follow_user_id order by real_amount desc limit 10";
        return app('db')->select($sql);
    }


    /**
     * 排行榜
     */
    public function getPaihangbang2() {
        $sql = "SELECT a.product_id, b.name, count(*) as cnt FROM `customer_backs` a,
            product b   
            where a.product_id = b.id and a.status = 1 and a.create_time > '".date("Y-m-01")."' group by a.product_id";
        return app('db')->select($sql);
    }
}
