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

    public function getAllTeam() {
        return $this->where('status', 1)->get();
    }

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

    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }
    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }
}
