<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class GoogleDrive
 * @package App\Services
 */
class GoogleDrive
{
    public function dirExists(string $dir, string $dirname)
    {
        return collect(Storage::cloud()->listContents($dir))
            ->where('type', '=', 'dir')
            ->where('name', '=', $dirname)
            ->first();
    }

    public function put(string $dir, string $filePath)
    {
        $fileName = basename($filePath);

        if ($this->fileExist($dir, $fileName)) {
            return true;
        }

        Storage::cloud()->put($dir.'/'.$fileName, File::get($filePath));

        return true;
    }

    public function fileExist(string $dir, string $fileName)
    {
        return collect(Storage::cloud()->listContents($dir))
            ->where('type', '=', 'file')
            ->where('name', '=', $fileName)
            ->first();
    }
}
