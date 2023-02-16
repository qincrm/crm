<?php
 
namespace App\Models;

use App\Services\ToolService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\SkippedTest;

class SystemUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_user';

    public $timestamps = false;

    public static $statusMapping = [
        1 => '有效',
        0 => '无效',
    ];

    private function _createWhere($params) {
        $query = $this;
        $query = $query->where(['is_del' => 0]);
        if (isset($params['name']) && $params['name'] !== "") {
            $query = $query->where(['name' => $params['name']]);
        }
        if (isset($params['team_id']) && $params['team_id'] !== "") {
            $query = $query->where(['team_id' => $params['team_id']]);
        }
        if (isset($params['status']) && $params['status'] !== "") {
            $query = $query->where(['status' => $params['status']]);
        }
        if (isset($params['online']) && $params['online'] !== "") {
            $query = $query->where(['online' => $params['online']]);
        }
        if (isset($params['mobile']) && $params['mobile'] !== "") {
            $query = $query->where(['mobile' => $params['mobile']]);
        }
        return $query;
    }
    public function getLists($params) {
        $roleModel = new SystemRole();
        $teamModel = new SystemTeam();
        $teams = $teamModel->getAllTeam();
        $teams = collect($teams)->mapWithKeys(function($item){return [$item['id']=>$item['name']];});
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        foreach ($list as $key => $item) {
            $roles = $roleModel->getRoleByUserId($item['id']);
            $item['roles'] = implode(",", app(ToolService::class)->objColumn($roles, 'name'));
            $item['team'] = $teams[$item->team_id];
            $item['statusname'] = static::$statusMapping[$item['status']];
            unset($item['password']);
            $list[$key] = $item;
        }
        return $list;
    }
    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }

    public function getAllUserWithDel($userId = 0) {
        return $this->where('id', '!=', $userId)->get();
    }

    public function getAllUser($userId = 0) {
        return $this->where('status', 1)->where('is_del', 0)->where('id', '!=', $userId)->get();
    }

    public function getAllUserByParentId($parentId = 0) {
        return $this->where('status', 1)->where('is_del', 0)->where('parent_id', '=', $parentId)->get();
    }

    public function getUserCountByTeamid($teamId) {
        return $this->where('team_id', $teamId)->where('is_del', 0)->count();
    }

    public function getUserByTeamid($teamId) {
        return $this->where('team_id', $teamId)->where('is_del', 0)->get();
    }
}