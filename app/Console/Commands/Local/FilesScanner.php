<?php

namespace App\Console\Commands\Local;

use App\Console\BaseCommand;
use App\Models\FilesIndexing;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\File;

class FilesScanner extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:filesscanner {task=fully} {--path=} {--extensions=mkv,flv,avi,ts,mov,wmv,mp4,mpeg,mpg,m4v}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Local files scanner';

    public function fully()
    {
        if (!$path = $this->option('path')) {
            return false;
        }

        if (!$path = realpath($path)) {
            return false;
        }

        $allowedExtensions = explode(',', $this->option('extensions'));

        foreach (File::allFiles($path) as $file) {
            $ext = $file->getExtension();

            if (!in_array(strtolower($ext), $allowedExtensions)) {
                continue;
            }

            try {
                $media = FFProbe::create()->streams($file->getPathname())
                    ->videos()
                    ->first()
                    ->all();
            } catch (\Exception $exception) {
                continue;
            }

            FilesIndexing::firstOrCreate(
                [
                    'path' => $file->getPath(),
                    'filename' => $file->getFilename(),
                    'extension' => $file->getExtension(),
                    'filesize' => $file->getSize()
                ],
                [
                    'codec_name' => $media['codec_name'] ?? null,
                    'codec_long_name' => $media['codec_long_name'] ?? null,
                    'width' => $media['width'] ?? null,
                    'height' => $media['height'] ?? null,
                    'duration' => $media['duration'] ?? null,
                    'bit_rate' => $media['bit_rate'] ?? null,
                    'bits_per_raw_sample' => $media['bits_per_raw_sample'] ?? null,
                    'nb_frames' => $media['nb_frames'] ?? null
                ]
            );
        }

        return true;
    }
}
