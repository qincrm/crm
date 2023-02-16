<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class Test extends Command
{
    protected $signature = 'test:test';
    protected $description = '上传教材';

    /**
        *      * 执行控制台命令
        *           */
    public function handle()
    {
        $service = app(\App\Services\CustomerService::class);
        $service->diff(
            new Customer(['name'=>1]),
            new Customer(['name'=>2])
        );
    }
}
