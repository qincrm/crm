<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class Mobile extends Command
{
    protected $signature = 'mobile';
    protected $description = '手机号重复判断策略';

    /**
     * 给系统里的客户，手机号相同的打标记，表示客户第几次出现
     */
    public function handle() {
        // 获取上次判断到的id
        $id = intval(@file_get_contents("/www/wwwlogs/mobilelog.txt"));
        // 取到所有规则
        $model = new Customer();
        $mobiles = $model->select('id', 'mobile')->where('id', '>', $id)->orderby('id')->get();
        foreach ($mobiles as $mobile) {
            $id = $mobile->id;
            $x = $model->select('anum')->where('id', '<', $mobile->id)->where('mobile', $mobile->mobile)->orderby('id' ,'desc')->take(1)->get()->toArray();
            if ($x && $x[0]) {
                if ($x[0]['anum'] == 0) {
                    $num = $x[0]['anum'] + 2;
                } else {
                    $num = $x[0]['anum'] + 1;
                }
                $model->where('id', $id)->update(['anum' => $num]);
            }
        }
        file_put_contents("/www/wwwlogs/mobilelog.txt", $id);
    }
}
