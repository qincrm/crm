<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemTeam extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_team';

    public $timestamps = false;

    /**
     * 获取所有团队
     */
    public function getAllTeam() {
        return $this->where('status', 1)->get();
    }

    /**
     * 获取所有团队转成KV
     */
    public function getAllTeamMap() {
        $list = $this->getAllTeam();
        return collect($list)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        })->toArray();
    }

    /**
     * 通用查询
     */
    private function _createWhere($params) {
        $query = $this->where('status', 1);
        if (isset($params['name']) && $params['name'] !== "") {
            $query = $query->where(['name' => $params['name']]);
        }
        if (isset($params['status']) && $params['status'] !== "") {
            $query = $query->where(['status' => $params['status']]);
        }
        return $query;
    }

    /**
     * 通用查询
     */
    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }

    /**
     * 通用查询
     */
    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }
}
