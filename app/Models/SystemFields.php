<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemFields extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_field';

    public $timestamps = false;

    public function getSelect() {
        $list = $this->get();
        $returnData = [ ];
        foreach ($list as $item) {
            $returnData[$item['type']][$item['name']] =$item['name_cn'] ;
        }
        return $returnData;
    }
}
