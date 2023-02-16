<?php

namespace App\Services;


class SelectService
{
    /**
     * 构造前端的select 
     * 
     */
    public function genKv($data, $key, $value) {
        $returnData = [];
        foreach ($data as $item) {
            $returnData[$item[$key]] = $item[$value];
        }
        return $returnData;
    }

    /**
     * 构造前端的select 
     * 
     */
    public function genSelect($data, $value, $label) {
        $returnData = [];
        foreach ($data as $item) {
            $returnData[] = [
                'value' => $item[$value],
                'label' => $item[$label],
            ];
        }
        return $returnData;
    }

    /**
     * 构造前端的select 
     * 
     */
    public function genSelectByKV($data) {
        $returnData = [];
        foreach ($data as $key=>$value) {
            $returnData[] = [
                'value' => $key,
                'label' => $value,
            ];
        }
        return $returnData;
    }

    /**
     * 构造前端的select 
     * 
     */
    public function genSelectByVK($data) {
        $returnData = [];
        foreach ($data as $key=>$value) {
            $returnData[] = [
                'value' => $value,
                'label' => $key,
            ];
        }
        return $returnData;
    }


    /**
     * 构造前端的select 
     * 
     */
    public function genSelectByK($data, $k) {
        $returnData = [];
        foreach ($data as $value) {
            $returnData[] = [
                'value' => $value[$k],
                'label' => $value[$k],
            ];
        }
        return $returnData;
    }
}
