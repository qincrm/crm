<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SystemUser;
use App\Services\SelectService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 用户列表
     */
    public function list(Request $request) {
        $model = new Product();
        $list = $model->getLists($request->all());
        $data['total'] = $model->getCount($request->all());
        foreach ($list as $key=>$item) {
            if ($item['amount1'] == 0) {$item['amount1'] = '?';}
            if ($item['amount2'] == 0) {$item['amount2'] = '?';}
            $item['amount'] = $item['amount1'] . ' - '. $item['amount2'];
        }
        $data['list'] = $list;
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 新增或编辑用户
     */
    public function edit(Request $request) {
        $params = $request->all();
        $model = new Product();
        if (isset($params['id'])) {
            $model = $model->find($params['id']);
        }
        if ($params['amount1']&& $params['amount2'] && $params['amount1'] > $params['amount2']) {
            return $this->apiReturn(static::ERROR, [], '最大额度必须大于最小额度');
        }
        $model->name = $params['name'];
        $model->bank= $params['bank'];
        $model->remark= $params['remark'];
        $model->amount1 = floatval($params['amount1']);
        $model->amount2 = floatval($params['amount2']);
        $model->save();
        return $this->apiReturn(static::OK);
    }

    /**
     * 获取用户信息
     */
    public function info(Request $request) {
        $params = $request->all();
        $model = new Product();
        $allUserListSelect = $model->getAllBank();
        if (isset($params['id'])) {
            $model = $model->find($params['id']);
            if ($model['amount'] == 0) {$model['amount'] = '';}
            if ($model['amount1'] == 0) {$model['amount1'] = '';}
            if ($model['amount2'] == 0) {$model['amount2'] = '';}
        }
        $allUserListSelect = app(SelectService::class)->genSelectByK($allUserListSelect, 'bank'); // 转成前端的select
        return $this->apiReturn(static::OK, ['teamInfo'=>$model, 'allUserListSelect' => $allUserListSelect]);
    }


    /**
     * 新增或编辑用户
     */
    public function del(Request $request) {
        $params = $request->all();
        $model = new Product();
        $usermodel = new SystemUser();
        if (empty($params['id'])) {
            return $this->apiReturn(static::OK);
        }
        $usercount = $usermodel->getUserCountByProductid($params['id']);
        if ($usercount > 0) {
            return $this->apiReturn(static::ERROR, [], '该团队下还有用户,无法删除!');
        }
        $model = $model->find($params['id']);
        $model->status = 0;
        $model->save();
        return $this->apiReturn(static::OK);
    }
}
