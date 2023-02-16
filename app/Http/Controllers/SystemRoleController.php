<?php

namespace App\Http\Controllers;

use App\Models\SystemFields;
use App\Models\SystemRight;
use App\Models\SystemRole;
use App\Models\SystemRoleRight;
use App\Models\SystemUser;
use App\Models\SystemUserRole;
use App\Services\RightService;
use App\Services\SelectService;
use App\Services\ToolService;
use Illuminate\Http\Request;

class SystemRoleController extends Controller
{
    /**
     * 角色列表
     */
    public function list(Request $request) {
        $model = new SystemRole();
        $data['list'] = $model->getLists($request->all());
        $data['total'] = $model->getCount($request->all());
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 新增或编辑用户
     */
    public function edit(Request $request) {
        $params = $request->all();
        $model = new SystemRole();
        $rightModel = new SystemRight();
        $rolerightModel = new SystemRoleRight();
        if (isset($params['id'])) {
            $model = $model->find($params['id']);
        }
        $model->name = $params['name'];
        $model->views = $params['views'] ? $params['views'] : 1;
        $model->fields = implode(",", array_merge((array)$params['fields1'] , (array)$params['fields2'] , (array)$params['fields3']));
        $model->save();
        // 处理权限
        // 获取当当前的权限
        $rights = $rightModel->getRightByRoleId($model->id);
        $oldRights = [];
        foreach ($rights as $right) {
            $oldRights[] = $right->id;
        }
        // 获取新的权限
        $newRights = array_merge((array)$params['rights'], (array)$params['halfrights']);
        // 该新增的权限，改移除的权限
        $delRights = array_diff($oldRights, $newRights);
        $addRights = array_diff($newRights, $oldRights);
        // 删除权限
        $rolerightModel->deleteRights($model->id, $delRights);
        $rolerightModel->addRights($model->id, $addRights);
        return $this->apiReturn(static::OK);
    }

    /**
     * 锁定角色
     */
    public function lock(Request $request) {
        $params = $request->all();
        $model = SystemRole::find($params['id']);
        $model->status = $params['status'];
        $model->save();
        return $this->apiReturn(static::OK);
    }

    /**
     * 获取用户信息
     */
    public function info(Request $request) {
        $params = $request->all();
        $model = new SystemRole();
        $rightModel = new SystemRight();
        $fieldsModel = new SystemFields();
        $selectFieldList = $fieldsModel->getSelect();
        $service = app(RightService::class);
        $selectRightList = $service->getRightTree();
        if (isset($params['id']) && $params['id']) {
            $model = $model->find($params['id']);
            $model['fields1'] = $model['fields2'] = $model['fields3'] = [];
            if ($model['fields']) {
                $fields = explode(',', $model['fields']);
                foreach ($fields as $field) {
                    if (array_key_exists($field, $selectFieldList[1])) {
                        $fields1[] = $field;
                    } else if (array_key_exists($field, $selectFieldList[2])) {
                        $fields2[] = $field;
                    } else if (array_key_exists($field, $selectFieldList[3])) {
                        $fields3[] = $field;
                    }
                } 
                if ($fields1) {$model['fields1'] = $fields1;}
                if ($fields2) {$model['fields2'] = $fields2;}
                if ($fields3) {$model['fields3'] = $fields3;}
            }
            ($selectRightList['leafs'][] = 31);
            $rights = app(ToolService::class)->objColumn($rightModel->getRightByRoleId($params['id']), 'id');
            $model['rights']= array_values(array_intersect($rights, $selectRightList['leafs']));
            $model['halfrights']= array_values(array_diff($rights, $selectRightList['leafs']));
        }
        return $this->apiReturn(static::OK, [
            'roleInfo'=>$model, 
            'selectFieldList1' => app(SelectService::class)->genSelectByKV($selectFieldList[1]), // 转成前端的select
            'selectFieldList2' => app(SelectService::class)->genSelectByKV($selectFieldList[2]), // 转成前端的select
            'selectFieldList3' => app(SelectService::class)->genSelectByKV($selectFieldList[3]), // 转成前端的select
            'selectRightList'=>$selectRightList['tree']
        ]);
    }

    /**
     * 删除用户
     */
    public function delete(Request $request) {
        $params = $request->all();
        $model = new SystemUserRole();
        $count = $model->where('role_id', $params['id'])->count();
        if ($count > 0) {
            return $this->apiReturn(static::ERROR, [], '该账号还有'.$count.'个用户，请转移后再删除');
        }
        $model = SystemRole::find($params['id']);
        $model->is_delete = 1;
        $model->save();
        return $this->apiReturn(static::OK);
    }
}
