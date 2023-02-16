<?php
 
namespace App\Models;

use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Model;

class CustomerRuleConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_rule_config';

    protected $guarded = [];
    public $timestamps = false;

}
