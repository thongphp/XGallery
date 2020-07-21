<?php

namespace App\Exceptions\Truyenchon;

use Exception;

class TruyenchonChapterDownloadException extends Exception
{
    /**
     * @param string $filePath
     * @param string $chapterUrl
     * @param string $responseMessage
     */
    public function __construct(string $filePath, string $chapterUrl, string $responseMessage)
    {
        $message = sprintf(
            '[%s] Fail on Download [filePath=%s] [chapterUrl=%s] [%s]',
            'TruyenchonChapterDownloadException',
            $filePath,
            $chapterUrl,
            $responseMessage
        );

        parent::__construct($message);
    }
}
