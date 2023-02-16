<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class ApproveDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'approve_detail';
    public $timestamps = false;
    protected $guarded = [];

    private function _createWhere($params) {
        $query = $this->where('user_id', $params['user_id']);
        //if (isset($params['type']) && $params['type'] !== "") {
        //    $query = $query->where('type', $params['type']);
        //}
        if (isset($params['status']) && $params['status'] !== "") {
            $query = $query->where('status',  $params['status']);
        }
        return $query;
    }

    public function getLists($params) {
        $offset = ($params['current'] - 1) * $params['pageSize'];
        $list = $this->_createWhere($params)->select('*')->skip($offset)->take($params['pageSize'])->orderby('id','desc')->get()->toArray();
        return $list;
    }

    public function getCount($params) {
        return $this->_createWhere($params)->count();
    }
}
