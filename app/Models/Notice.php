<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notice';

    protected $guarded = [];

    public $timestamps = false;

    public function getUnRead($userId) {
        $data1 = $this->where('follow_user_id', $userId)->where('type', '!=', 5)->where('date', '<', time() + 24 * 3600)->where('date', '>', time())->where('is_read', 0)->take(10)->get()->toArray();
        $data2 = $this->where('follow_user_id', $userId)->where('type', 5)->where('is_read', 0)->take(10)->get()->toArray();
        return array_merge($data1, $data2);
    }

    public function read($ids) {
        $this->wherein('id', $ids)->update(['is_read' => 1]);
        return true;
    }

    public function getUnReadCount($userId) {
        $num1 = $this->where('follow_user_id', $userId)->where('type', '!=', 5)->where('date', '<', time() + 24 * 3600)->where('date', '>', time())->where('is_read', 0)->count();
        $num2 = $this->where('follow_user_id', $userId)->where('type', 5)->where('is_read', 0)->count();
        return $num1 + $num2;
    }


    private function _createWhere($params) {
        $query = $this;
        if (isset($params['userId']) && !empty($params['userId'])) {
            $query = $query->where('follow_user_id', $params['userId']);
        }
        if (isset($params['name']) && !empty($params['name'])) {
            $customer = Customer::where('name', $params['name'])->get()->toArray()[0];
            if ($customer && $customer['id']) {
                $query = $query->where('custom_id', $customer['id']);
            } else {
                $query = $query->where('custom_id', 999999999999);
            }
        }
        if (isset($params['time']) && !empty($params['time']) && is_array($params['time']) ) {
            $query = $query->where('date',  '>', strtotime($params['time'][0]));
            $query = $query->where('date',  '<', strtotime($params['time'][1]));
        }
        return $query;
    }

    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }

    public function getCount($params = []) {
        return $this->_createWhere($params)->count();
    }


    public function getListByUserId($userId, $limit) {
        $list = $this->where('follow_user_id', $userId)->orderBy("date", "desc")->take($limit)->get();
        return $list;
    }

    public function getListByUserIdAndCustomer($customId, $userId, $limit) {
        $list = $this->where('follow_user_id', $userId)->where('custom_id', $customId)->where('date', '>', time())->orderBy("date", "desc")->take($limit)->get();
        return $list;
    }
}
