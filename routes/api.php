<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DiskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/', function(){
    return view('index');
});


// папки
Route::post('/getFolders', [FolderController::class, "getFoldersFromSys"]);
// никнейм
Route::post('/addNickname', [FolderController::class, 'addNickname']);
Route::post('/updateNickname', [FilmController::class, 'updateNickname']);
Route::post('/deleteNickname', [FolderController::class, "deleteNickname"]);
// файлы
Route::post('/createFile', [FolderController::class, "createFile"]);                   // создать
Route::post('/deleteFile', [FolderController::class, "deleteFile"]);                   // удалить
Route::get('/download/{filePath}', 'FileController@download')->name('downloadFile');   // скачать
// переименование
Route::post('/rename', [FolderController::class, "renameFileorFolder"]);




//диски
Route::get('/getDisks', [DiskController::class, "getDisks"]);






Route::post('/updateNickname', [FolderController::class, 'updateNickname']);  // не реализовано

//test
Route::post('/getNickname', [FolderController::class, "getNicknameInStorage"]);
Route::post('/showPath', [FolderController::class, "showPath"]);

// // путь
// Route::post('/givePath', [FolderController::class, "givePath"]);
// Route::get('/getPath', [FolderController::class, "getPath"]);




