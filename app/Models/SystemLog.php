<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_log';

    protected $guarded = [];

    public $timestamps = false;

    const TYPE_ASSIGN = 1; // 分配权限修改
    const TYPE_EDIT   = 2; // 基本资料修改


    public function saveLog($type, $id, $before, $after, $userId = 0, $remark = '') {
        $this->type = $type;
        $this->obj_id = $id;
        $this->before = $before;
        $this->after = $after;
        $this->remark = $remark;
        $this->user_id = $userId;
        $this->save();
    }


    public function getLogListById($type, $id, $params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->where('type', $type)->where('obj_id', $id)->orderBy("id", "desc")->skip($offset)->take($params['pageSize'])->get();
        return $list;
    }
    public function getLogCountById($type, $id, $params) {
        return $this->where('type', $type)->where('obj_id', $id)->count();
    }

}
