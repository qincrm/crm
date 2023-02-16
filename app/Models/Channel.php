<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'channel';
    public $timestamps = false;
    protected $guarded = [];

}
