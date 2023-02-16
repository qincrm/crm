<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;

class SystemDict extends Model
{

    const TYPE_CITY = 1;   // 城市
    const TYPE_WORK = 2;   // 工作类型
    const TYPE_FOLLOW = 3; // 跟进类型
    const TYPE_STAR = 4; // 星级类型
    const TYPE_USER_FROM = 5; // 用户来源
    const TYPE_SOURCE = 6; // 渠道来源
    const TYPE_QUALIFICATION = 7; // 资质
    const TYPE_NOTICE = 8; // 日程

    const GROUP_CUSTOM = 1; // 客户相关

    protected $table = 'dict';
    protected $guarded = [];

    public $timestamps = false;

    public function getListByType($type) {
        $list = $this->where(['type'=>$type, 'status'=>1])->get();
        $returnData = [];
        foreach ($list as $item) {
            $returnData[$item['tid']] = $item['name'];
        }
        return $returnData;
    }

    public function getListByGroup($group) {
        $list = $this->where(['groups'=>$group, 'status'=>1])->get();
        $returnData = [];
        foreach ($list as $item) {
            $returnData[$item['type']][$item['tid']] = $item['name'];
        }
        return $returnData;
    }


}
