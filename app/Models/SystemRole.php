<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_role';

    public static $statusMapping = [
        1 => '有效',
        0 => '无效',
    ];
    public $timestamps = false;


    public function getRoleByUserId($userId) {
        $sql = "SELECT c.* FROM system_role c, system_user_role d 
        where c.id = d.role_id and c.status = 1 and d.user_id = ? and c.status = 1 group by c.id";
        return app('db')->select($sql, [$userId]);
    }


    public function getUserByRoleId($roleId) {
        $sql = "SELECT c.* FROM system_user c, system_user_role d 
        where c.id = d.user_id and c.status = 1 and d.role_id = ? group by c.id";
        return app('db')->select($sql, [$roleId]);
    }

    public function getRoleSelect() {
        $list = $this->where('is_delete', 0)->where('status', 1)->get();
        $returnData = [ ];
        foreach ($list as $item) {
            $returnData[] = [
                'label' => $item['name'],
                'value' => $item['id'],
            ];
        }
        return $returnData;
    }



    private function _createWhere($params) {
        $query = $this;
        $query = $query->where(['is_delete' => 0]);
        if (isset($params['name']) && $params['name'] !== "") {
            $query = $query->where(['name' => $params['name']]);
        }
        if (isset($params['status']) && $params['status'] !== "") {
            $query = $query->where(['status' => $params['status']]);
        }
        return $query;
    }
    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        foreach ($list as $key => $item) {
            $item['statusname'] = static::$statusMapping[$item['status']];
            $list[$key] = $item;
        }
        return $list;
    }
    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }
}