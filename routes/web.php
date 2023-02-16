<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/api', function () use ($router) {
    return $router->app->version();
});

// 渠道
$router->post('/api/ssd/check_user', 'Third\SsdController@checkUser');
$router->post('/api/ssd/apply', 'Third\SsdController@apply');
$router->get('/api/ssd/test', 'Third\SsdController@test');
$router->post('/api/ssd/query_state', 'Third\SsdController@queryState');

$router->post('/api/pdh/check_user', 'Third\PdhController@checkUser');

$router->post('/api/yx/order', 'Third\YxController@order');
$router->post('/api/yx/check_user', 'Third\YxController@checkUser');
$router->post('/api/hl/order', 'Third\HlController@order');
$router->post('/api/hl/check_user', 'Third\HlController@checkUser');

$router->post('/api/user/login', 'UserController@login');
$router->post('/api/customer/push', 'ApiController@push');
$router->post('/api/customer/check', 'ApiController@check');
$router->get('/api/user/test', 'UserController@test');
$router->get('/api/captcha', 'UserController@captcha');
$router->get('/api/donothing', 'UserController@donothing');
$router->post('/api/user/logout', 'UserController@logout');
$router->get('/api/user/menu', 'UserController@menu');
$router->get('/api/notice/list', 'NoticeController@list');
$router->get('/api/custom/follownum', 'CustomController@follownum');

$router->group(['prefix' => 'api', 'middleware'=>'auth'], function () use ($router) {
    $router->post('user/info', 'UserController@info');
    $router->post('user/menu', 'UserController@menu');
    $router->post('user/resetpassword', 'UserController@resetpassword');
    $router->get('user/dashboard', 'UserController@dashboard');
    $router->post('user/online', 'SystemUserController@online');

    $router->get('system/user/list', 'SystemUserController@list');
    $router->post('system/user/edit', 'SystemUserController@edit');
    $router->post('system/user/lock', 'SystemUserController@lock');
    $router->post('system/user/info', 'SystemUserController@info');
    $router->post('system/user/role', 'SystemUserController@role');
    $router->post('system/user/resetpwd', 'SystemUserController@resetpwd');
    $router->post('system/user/delete', 'SystemUserController@delete');

    $router->get('system/role/list', 'SystemRoleController@list');
    $router->post('system/role/edit', 'SystemRoleController@edit');
    $router->post('system/role/lock', 'SystemRoleController@lock');
    $router->post('system/role/info', 'SystemRoleController@info');
    $router->post('system/role/delete', 'SystemRoleController@delete');

    $router->get('system/team/list', 'SystemTeamController@list');
    $router->post('system/team/edit', 'SystemTeamController@edit');
    $router->post('system/team/info', 'SystemTeamController@info');
    $router->post('system/team/del', 'SystemTeamController@del');


    $router->get('system/product/list', 'ProductController@list');
    $router->post('system/product/edit', 'ProductController@edit');
    $router->post('system/product/info', 'ProductController@info');
    $router->post('system/product/del', 'ProductController@del');

    $router->get('system/setting', 'SystemUserController@setting');
    $router->post('system/setting', 'SystemUserController@setting');

    $router->get('custom/list', 'CustomController@list');
    $router->post('custom/edit', 'CustomController@edit');
    $router->post('custom/info', 'CustomController@info');
    $router->post('custom/lahei', 'CustomController@lahei');
    $router->post('custom/assign', 'CustomController@assign');
    $router->post('custom/important', 'CustomController@important');
    $router->post('custom/lock', 'CustomController@lock');
    $router->post('custom/giveup', 'CustomController@giveup');
    $router->post('custom/batchgiveup', 'CustomController@batchgiveup');
    $router->post('custom/batchget', 'CustomController@batchget');
    $router->get('custom/assignlist', 'CustomController@assignlist');
    $router->get('custom/followlist', 'CustomController@followlist');
    $router->get('custom/starlist', 'CustomController@starlist');
    $router->post('custom/upload', 'CustomController@upload');
    $router->post('custom/getnoticelist', 'CustomController@getNoticeList');
    $router->post('custom/addnotices', 'CustomController@addNotices');
    $router->post('custom/submitback', 'CustomController@addBack');
    $router->post('custom/dianping', 'CustomController@addDianping');
    $router->post('custom/editback', 'CustomController@editBack');
    $router->post('custom/delback', 'CustomController@delBack');
    $router->post('custom/get', 'CustomController@get');
    $router->get('custom/backlist', 'CustomController@backlist');

    $router->get('message/list', 'NoticeController@list');
    $router->get('message/unreadlist', 'NoticeController@unreadlist');
    $router->post('message/read', 'NoticeController@read');

    $router->get('channel/list', 'ChannelController@list');
    $router->get('channel/detail', 'ChannelController@detail');
    $router->get('data/backlist', 'DataController@backlist');
    $router->get('data/reportlist', 'DataController@reportlist');
    $router->get('data/reportdetail', 'DataController@reportdetail');

    $router->post('operate/assign/edit', 'AssignController@edit');
    $router->get('operate/assign/log', 'AssignController@log');
    $router->get('operate/assign/config', 'AssignController@config');
    $router->post('operate/assign/editrule', 'AssignController@editrule');
    $router->post('operate/assign/setstatus', 'AssignController@setstatus');
    $router->get('operate/channel/config', 'ChannelController@config');
    $router->post('operate/channel/info', 'ChannelController@info');
    $router->post('operate/channel/edit', 'ChannelController@edit');
    $router->post('operate/channel/setstatus', 'ChannelController@setstatus');

    $router->get('work/list', 'DataController@worklist');
    $router->get('cost/list', 'DataController@costlist');

    $router->get('approve/list', 'ApproveController@list');
    $router->post('approve/cancel', 'ApproveController@cancel');
    $router->post('approve/pass', 'ApproveController@pass');
    $router->post('approve/view', 'ApproveController@view');
});
