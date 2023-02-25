<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustomerService;

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
        foreach ($data as $item) {
            $customService->addChannelCustomer($item);
        }
    }


}
