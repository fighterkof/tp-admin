<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//Route::domain('api', 'api');
/*
Route::get('code/:time/:random/:token', 'code/getCode');
Route::get('code/sms/:time/:random/:token', 'code/getSms');
Route::get('vcode/v/:time/:random/:token', 'vcode/getCode');
Route::PUT('vcode/v/:time/:random/:token', 'vcode/getCode');

return [
];
 */
/*****
 * RESTful 风格设计  Api的查询（get）、增加（post）、修改（put）、删除（delete）
 */

Route::group('Banner', function () {
    Route::get('index/:time/:random/:token', 'api/Banner/index');
    Route::miss('api/pub/miss');
});

Route::group('My', function () {
    Route::post('perfect', 'api/My/perfect');
    Route::miss('api/pub/miss');
});

Route::group('Notice', function () {
    Route::get('details/:time/:random/:token/:notice_id', 'api/Notice/details');
    Route::get('index/:time/:random/:token', 'api/Notice/index');
    Route::miss('api/pub/miss');
});

Route::group('Members', function () {
    Route::post('information', 'api/Members/information');
    Route::miss('api/pub/miss');
});
