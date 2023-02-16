<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerRuleConfig;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\UserService;

class Channel extends Command
{
    protected $signature = 'channel {--name=}';
    protected $description = '拉取渠道数据';

    /**
     ** 执行控制台命令
     **/
    public function handle() {
        $channelName  = $this->option('name');
        $className = 'App\Services\Channel\\'.ucfirst($channelName).'Channel';
        $channel = new $className();
        $data = $channel->getData();
        $customService = app(CustomerService::class);
        $userService = app(UserService::class);
        foreach ($data as $item) {
            $hasInfo = Customer::where('source', $item['source'])->where('channel_id', $item['channel_id'])->get()->toArray();
            if (empty($hasInfo)) {
                $item['mobile_md5'] = md5($item['mobile']);
                $id = (new Customer())->insertGetId($item);
                $users = $userService->getAllAssignUser();
                echo "新数据分配的所有用户" . json_encode($users).PHP_EOL;
                foreach ($users as $userId => $leftCount) {
                    $userIdStr = $userId;
                    $userId = intval($userId);
                    if ($leftCount <= 0) {
                        continue;
                    }
                    echo "分配" . $id . ' --> ' . $userId .PHP_EOL;
                    $flag = $customService->assign($id, $userId, 0, CustomerService::ASSIGN_TYPE_NEW);
                    break;
                }
            }
        }
    }


}
