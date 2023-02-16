<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notice;
use App\Models\SystemDict;
use Illuminate\Http\Request;
use App\Services\RightService;

class NoticeController extends Controller
{
    /**
     * 获取通知信息
     */
    public function unreadlist(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $noticeModel = new Notice();
        $dict = new SystemDict();
        $list = $noticeModel->getUnRead($userId);
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置
        $returnData = [];
        foreach ($list as $item) {
            $custom = Customer::find($item['custom_id']);
            $returnData[] = [
                'id' => $item['id'],
                'type' => 'notice',
                'title' => $customGourpDict[$dict::TYPE_NOTICE][$item['type']]."(".$custom['name'].")",
                'ntype' => $item['type'],
                'content' => $item['remark'],
                'custom_id' => $item['custom_id'],
                'time' => date('Y-m-d H:i:s', $item['date']),
            ];
        }
        return $this->apiReturn(static::OK, $returnData);
    }

    /**
     * 获取通知信息
     */
    public function list(Request $request)
    {
        $params = $request->all();
        $userId = $request->session()->get('user_id');
        $params['userId'] = $userId;
        $noticeModel = new Notice();
        $dict = new SystemDict();
        $list = $noticeModel->getLists($params);
        $customGourpDict = $dict->getListByGroup($dict::GROUP_CUSTOM); // 获取城市信息配置
        $returnData = [];
        foreach ($list as $key => $item) {
            $custom = Customer::find($item->custom_id);
            $item['name'] = $custom->name;
            $item['type'] = $customGourpDict[$dict::TYPE_NOTICE][$item->type];
            $item['time'] = date('Y-m-d H:i:s', $item->date);
            $item['can_read'] = $item->date > time() ? false : true;
            $list[$key] = $item;
        }
        $returnData['list'] = $list;
        $returnData['total'] = $noticeModel->getCount($params);
        return $this->apiReturn(static::OK, $returnData);
    }

    /**
     * 读信息
     */
    public function read(Request $request)
    {
        $userId = $request->all();
        $noticeModel = new Notice();
        $noticeModel->read($request->get('ids'));
        return $this->apiReturn(static::OK);
    }
}
