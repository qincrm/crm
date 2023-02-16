<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SystemRoleRight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_role_right';

    public function deleteRights($roleId, $rightIds) {
        if (empty($rightIds)){
            return true;
        }
        return $this->where('role_id', $roleId)->wherein('right_id', $rightIds)->delete();
    }



    public function addRights($roleId, $rightIds){
        if (empty($rightIds)){
            return true;
        }
        $rights = [];
        foreach ($rightIds as $rightId) {
            $rights[] = [
                'role_id'=>$roleId,
                'right_id'=>$rightId
            ];
        }
        return $this->insert($rights);
    }
}
