<?php

namespace App\Http\Controllers;

use App\Models\SystemTeam;
use App\Models\SystemUser;
use App\Services\SelectService;
use Illuminate\Http\Request;

class SystemTeamController extends Controller
{
    /**
     * 用户列表
     */
    public function list(Request $request) {
        $model = new SystemTeam();
        $list = $model->getLists($request->all());
        $usermodel = new SystemUser();
        $allUserListSelect = $usermodel->getAllUserWithDel();
        $allUserListSelect = app(SelectService::class)->genKv($allUserListSelect, 'id', 'name'); // 转成前端的select
        $data['total'] = $model->getCount($request->all());
        foreach ($list as $key => $item) {
            $item['manager'] = $allUserListSelect[$item['manager_id']];
            $item['usercount'] = $usermodel->getUserCountByTeamid($item['id']);
            $list[$key] = $item;
        }
        $data['list'] = $list;
        return $this->apiReturn(static::OK, $data);
    }

    /**
     * 新增或编辑用户
     */
    public function edit(Request $request) {
        $params = $request->all();
        $model = new SystemTeam();
        if (isset($params['id'])) {
            $model = $model->find($params['id']);
        }
        $model->name = $params['name'];
        $model->manager_id = $params['manager_id'];
        $model->save();
        return $this->apiReturn(static::OK);
    }

    /**
     * 获取用户信息
     */
    public function info(Request $request) {
        $params = $request->all();
        $model = new SystemTeam();
        if (isset($params['id'])) {
            $model = $model->find($params['id']);
            $model['manager_id'] = empty($model['manager_id']) ? "" : intval($model['manager_id']);
        }
        $usermodel = new SystemUser();
        $allUserListSelect = $usermodel->getAllUser();
        $allUserListSelect = app(SelectService::class)->genSelect($allUserListSelect, 'id', 'name'); // 转成前端的select
        return $this->apiReturn(static::OK, ['teamInfo'=>$model, 'allUserListSelect' => $allUserListSelect]);
    }


    /**
     * 新增或编辑用户
     */
    public function del(Request $request) {
        $params = $request->all();
        $model = new SystemTeam();
        $usermodel = new SystemUser();
        if (empty($params['id'])) {
            return $this->apiReturn(static::OK);
        }
        $usercount = $usermodel->getUserCountByTeamid($params['id']);
        if ($usercount > 0) {
            return $this->apiReturn(static::ERROR, [], '该团队下还有用户,无法删除!');
        }
        $model = $model->find($params['id']);
        $model->status = 0;
        $model->save();
        return $this->apiReturn(static::OK);
    }
}
