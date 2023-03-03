<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    const OK = 20000;
    const ERROR = 50000;
    const APP_OK = 0;

    protected function apiReturn($code = 20000, $data = [], $msg = '', $httpCode = 200) {
        return  response()->json([
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ], $httpCode);
    }

}
