<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product';

    public $timestamps = false;

    public function getAllProduct() {
        return $this->where('status', 1)->get();
    }

    public function getAllBank() {
        return $this->select('bank')->groupBy('bank')->get();
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
