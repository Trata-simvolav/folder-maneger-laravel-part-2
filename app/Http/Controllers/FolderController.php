<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Models\FileModal;

class FolderController extends BaseController
{
//----------------------никнейм--------------------------------------
    public function addNickname(Request $request)
    {
        $path = $request->input('pathToFile');
        $nickname = $request->input('nickname');
        $filename = $request->input('filename');
        $type = $request->input('type');

        $newFile = new FileModal;
        $newFile->pathToFile = $path;
        $newFile->nickname = $nickname;
        $newFile->filename = $filename;
        $newFile->type = $type;
        $newFile->save();

        return "Никнейм присвоен!";
    }
    
    public function updateNickname(Request $request)
    {
        $id = $request->input('path');
        $filename = $request->input('filename');
        $newName = $request->input('nickname');

        $fileToUpdate = FileModal::where('path', $path)
                            ->where('filename', $filename)
                            ->first();

        $fileToUpdate->nickname = $newName;
        $fileToUpdate->save();

        return "File name updated!";
    }

    public function deleteNickname(Request $request)
    {
        $path = $request->input('pathToFile');
        $filename = $request->input('filename');

        $fileToDelete = FileModal::where('pathToFile', $path)
                            ->where('filename', $filename)
                            ->first();
        $fileToDelete->delete();

        return "Nickname deleted!";
    }
//----------------------импорт папок и файлов--------------------------------------
    public function getNicknameInStorage($path)
    {
        // $path = $request->input('pathToFile');  Request $request
        $filesInStorage = FileModal::where('pathToFile', $path)->get();
        $fileData = [];
    
        foreach ($filesInStorage as $file) {
            $fileData[] = [
                'id' => $file->id,
                'pathToFile' => $file->pathToFile,
                'nickname' => $file->nickname,
                'filename' => $file->filename,
                'type' => $file->type,
            ];
        }
    
        return $fileData;
    }

    public function showPath($path) {
    $files = scandir($path);
    $contents = [];
    $id = 0;

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '$RECYCLE.BIN' && $file !== 'System Volume Information') {
            $fileType = is_dir($path . '/' . $file) ? 'folder' : pathinfo($path . $file, PATHINFO_EXTENSION);

            $id++;
            $contents[] = [
                'id' => $id,
                'name' => $file,
                'type' => $fileType,
                'path' => strlen($path) >= 3 ? $path . $file : $path . '/' . $file,
            ];
        }
    }

    return $contents;
}

function getFoldersFromSys(Request $request){
    $path = $request->input('pathToFile');
    

    $foldersAndFiles = $this->showPath($path);
    $nicknames = $this->getNicknameInStorage($path);

    foreach ($foldersAndFiles as $obj1) {
        foreach ($nicknames as $obj2) {
            if ($obj1['name'] == $obj2['filename'] && $obj1['type'] == $obj2['type']) {
                $key = array_search($obj1, $foldersAndFiles, true);
                $foldersAndFiles[$key]['nickname'] = $obj2['nickname'];
            }
        }
    }

    return $foldersAndFiles;
}
//----------------------создание и удаление и скачивание--------------------------------------
function createFile(Request $request){
    $request->validate([
        'sourcePath' => 'required',
        'destinationPath' => 'required',
        'filename' => 'required',
    ]);

    $sourcePath = $request->input('sourcePath');
    $destinationPath = $request->input('destinationPath');
    $filename = $request->input('filename');

    if (!File::exists($sourcePath)) {
        return "Source file does not exist:" . $sourcePath;
    }

    if (!File::isDirectory($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }

    try {
        File::copy($sourcePath.'/'.$filename, $destinationPath.'/'.$filename);
        return "All good!";
    } catch (ErrorException $e) {
        return $e->getMessage();
    }
}
function deleteFile(Request $request){
    $request->validate([
        'pathToTarget' => 'required',
        'nameTarget' => 'required',
        'typeTarget' => 'required',
    ]);

    $pathToTarget = $request->input('pathToTarget');
    $nameTarget = $request->input('nameTarget');
    $typeTarget = $request->input('typeTarget');

    $fullPath = $pathToTarget  . '/' . $nameTarget;

    if (file_exists($fullPath)) {
        if ($typeTarget !== 'folder') {
            unlink($fullPath);
            return 'Файл успешно удален';
        } elseif ($typeTarget == 'folder') {
            rmdir($fullPath);
            return 'Папка успешно удалена';
        } else {
            return 'Некорректный тип удаляемого объекта';
        }
    } else {
        return 'Файл или папка не существует';
    }
}

public function downloadByPath(Request $request)
{
    $absolutePath = $request->input('typeTarget');

    // Проверяем существование файла
    if (file_exists($absolutePath)) {
        // Открываем файл и отправляем его как поток
        $fileStream = fopen($absolutePath, 'r');

        // Возвращаем файл в виде скачиваемого контента
        return response()->stream(
            function () use ($fileStream) {
                fclose($fileStream);
            },
            200,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    } else {
        // Если файл не существует, возвращаем 404
        abort(404);
    }
}

//----------------------переименование--------------------------------------

function renameFileorFolder(Request $request){
    $oldFileOrFolder = $request->input('oldFileOrFolder');
    $newFileOrFolder = $request->input('newFileOrFolder');

    if (File::exists($oldFileOrFolder)) {
        File::move($oldFileOrFolder, $newFileOrFolder);
        return "Успешно переименована.";
    } else {
        return "Указанного не существует.";
    }
}


}
