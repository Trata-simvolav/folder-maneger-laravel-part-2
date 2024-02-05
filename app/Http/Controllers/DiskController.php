<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DiskController extends Controller
{
    function getDisks(){
    // Проверка наличия диска D:
    exec('wmic logicaldisk where DeviceID="D:" get DeviceID', $outputD, $returnValueD);
    if ($returnValueD === 0 && strpos(implode("\n", $outputD), 'D:') !== false) {
        return 'D:';
    }

    // Проверка наличия диска C:
    exec('wmic logicaldisk where DeviceID="C:" get DeviceID', $outputC, $returnValueC);
    if ($returnValueC === 0 && strpos(implode("\n", $outputC), 'C:') !== false) {
        return 'C:';
    }

    return "К сожалению, нам не удалось найти доступные диски";
    }
}
