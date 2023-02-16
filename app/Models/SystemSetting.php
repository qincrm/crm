<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SystemSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_setting';

    protected $guarded = [];

    public $timestamps = false;

}
