<?php

use Illuminate\Http\Request;

use App\Controllers\Common;
use App\Controllers\Upload;
use App\Service\Route\RouteService;
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

//小程序用户端
Route::post('/common', function (Request $request) {

    $result = RouteService::checkRoute($request);
    $action = $request->get("action", "");
    if ($result === true) {

        $controller = new Common\HandleController();
        return $controller->$action();

    } else {
        return $result;
    }

})->middleware('checkToken');
//->middleware('requestBefore', "checkSign", "checkToken","LogRecorder");//中间件
//上传图片入口
Route::post('/upload', function (Request $request) {

    $result = RouteService::checkRoute($request);
    $action = $request->get("action", "");
    if ($result === true) {

        $controller = new Upload\HandleController();
        return $controller->$action();

    } else {
        return $result;
    }

});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
