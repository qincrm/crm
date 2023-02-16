<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemUserRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_user_role';

    public $timestamps = false;

    public function getRoleByUserId($userId) {
        return $this->where('user_id', $userId)->get()->toArray();
    }

    public function delRoles($userId, $roleIds){
        if (empty($roleIds)){
            return true;
        }
        return $this->where('user_id', $userId)->where('role_id', $roleIds)->delete();
    }

    public function addRoles($userId, $roleIds){
        if (empty($roleIds)){
            return true;
        }
        $roles = [];
        foreach ($roleIds as $roleId) {
            $roles[] = [
                'role_id'=>$roleId,
                'user_id'=>$userId
            ];
        }
        return $this->insert($roles);
    }
}