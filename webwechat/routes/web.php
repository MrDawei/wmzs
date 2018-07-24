<?php

/**
 * 生成微信码
 */
//Route::get('/', 'LoginController@login');

/**
 * 生成微信码
 */
Route::get('/create/web/wechat/image/login', 'LoginController@login');

/**
 * 生成微信码
 */
Route::get('/create/web/wechat/image/login/result', 'LoginController@loginResult');

/**
 * 轮询发送
 */
Route::post('/api/service/web/wechat/check/login', 'LoginController@apiCheckLogin');


/**
 * 解析 xml 初始化数据
 */
Route::post('/api/service/web/wechat/get/xml/init/data', 'LoginController@apiGetXmlInitData');

/**
 * 进入用户界面
 */
Route::get('/wmzs/webwechat/view/user/index', 'UserController@index');

/**
 * 清理好友
 */
Route::post('/api/service/web/wechat/post/clear/contacts/{wmzs_webwechat_users_id}', 'UserController@apiClearContacts');

/**
 * 轮询发送
 */
Route::post('/api/service/web/wechat/sync/check/key/{wmzs_webwechat_users_id}', 'UserController@apiSyncCheckKey');

/**
 * 轮询发送
 */
Route::post('/api/service/web/wechat/sync/key/{wmzs_webwechat_users_id}', 'UserController@apiSynKey');

/**
 * 发送图片信息
 */
Route::get('/api/service/web/wechat/send/image/message/{wmzs_webwechat_users_id}', 'UserController@apiSendImage');

/**
 * 发送文字信息
 */
Route::post('/api/service/web/wechat/send/text/message/{wmzs_webwechat_users_id}', 'UserController@apiSendMsg');


/**
 * 好友分页获取
 */
Route::post('/api/service/web/wechat/get/page/{wmzs_webwechat_users_id}', 'UserController@apiGetPage');

/**
 * 清粉订单更新
 */
Route::post('/api/service/web/wechat/post/update/order/{wmzs_webwechat_users_id}', 'UserController@apiUpdateOrder');


	

