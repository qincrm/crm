<?php

namespace App\Services;


/**
 * 客户分配相关的service
 */
class AssignService
{

    const TYPE_MAPPING = [
        "public" => "公共池领取数据",
        "inner"  => "内部流转分配数据",
        "new"    => "新数据分配"
    ];

    /**
     * 通过json生成文案 
     * 
     */
    public function genText($json) {
        $data = json_decode($json, true);
        $returnText = "";
        if (empty($data)) {
            return "";
        }
        foreach ($data  as $key => $value) {
            $returnText .= static::TYPE_MAPPING[$key] . "(每日上限:".$value."); ";
        }
        return $returnText;
    }

}
