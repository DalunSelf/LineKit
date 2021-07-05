<?php

use Ryan\LineKit\Http\Controllers\Backend\LineChannelController;
use Ryan\LineKit\Http\Controllers\Backend\LineChatController;
use Ryan\LineKit\Http\Controllers\Backend\LineFlexTemplateController;
use Ryan\LineKit\Http\Controllers\Backend\LineKeywordController;
use Ryan\LineKit\Http\Controllers\Backend\LineMemberController;
use Ryan\LineKit\Http\Controllers\Backend\LinePushActionRecordController;
use Ryan\LineKit\Http\Controllers\Backend\LinePushController;
use Ryan\LineKit\Http\Controllers\Backend\LineRichMenuController;
use Ryan\LineKit\Http\Controllers\Backend\LineTagController;
use Ryan\LineKit\Http\Middleware\LineRequestMiddleware;
use Ryan\LineKit\Http\Middleware\OrganizationMiddleware;
use Ryan\LineKit\Http\Middleware\SettingLogMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 測試路由開通狀態
Route::get('/test-linekit', function (Request $request) {
    return response()->json('ok', 200);
});

// 後台管理
Route::group(['middleware' => ['auth:api', SettingLogMiddleware::class]], function () {
    Route::group(['middleware' => [OrganizationMiddleware::class]], function () {
        /**
        官方帳號 LINE BOT 專區 (機器人)
         */
        Route::apiResource('channels', LineChannelController::class)->withoutMiddleware(OrganizationMiddleware::class);
        Route::apiResource('flex-templates', LineFlexTemplateController::class)->only(['index', 'store', 'show', 'update'])->withoutMiddleware(OrganizationMiddleware::class);
        Route::group(['middleware' => LineRequestMiddleware::class], function () {
            Route::apiResource('chats', LineChatController::class)->only(['index', 'store', 'show']);
            Route::apiResource('members', LineMemberController::class)->only(['index']);
            Route::apiResource('pushs', LinePushController::class);
            Route::apiResource('push-action-records', LinePushActionRecordController::class)->only(['index']);
            Route::apiResource('tags', LineTagController::class)->only(['index']);
            Route::apiResource('rich-menus', LineRichMenuController::class);
            Route::apiResource('keywords', LineKeywordController::class);
            Route::post('/keywords-switch/{id}', [LineKeywordController::class, 'updateKeywordSwitch']);

            /**
            共用
             */
            // 取得標籤所有選項
            Route::get('/tags-option', [LineTagController::class, 'getAllTag']);
        });
    });
});