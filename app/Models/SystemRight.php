<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SystemRight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_right';

    public function getRightByUserId($userId) {
        $sql = "SELECT a.* FROM `system_right` a, system_role_right b, system_role c, system_user_role d , system_user e
        where a.id =b.right_id and b.role_id = c.id and c.id = d.role_id and d.user_id = e.id and e.status = 1 and is_del = 0 and c.status = 1 and d.user_id = ? and c.status = 1 and a.status = 1 group by a.id order by a.parent_id, a.orders desc";
        return app('db')->select($sql, [$userId]);
    }

    public function getRightByRoleId($roleId) {
        $sql = "SELECT a.* FROM `system_right` a, system_role_right b where a.id =b.right_id and a.status = 1 and b.role_id = ? group by a.id ";
        return app('db')->select($sql, [$roleId]);
    }


    public function getAllRight() {
        $sql = "SELECT a.* FROM `system_right` a where a.status = 1 order by a.parent_id, a.orders desc";
        return app('db')->select($sql);
    }


}
