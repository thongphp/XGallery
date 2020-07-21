<?php

namespace App\Exceptions\Truyenchon;

use Exception;

class TruyenchonChapterDownloadWritePDFException extends Exception
{
    /**
     * @param string $chapterUrl
     * @param string $pdfFile
     */
    public function __construct(string $chapterUrl, string $pdfFile)
    {
        $message = sprintf(
            '[%s] Fail on write to PDF file [filePath=%s] [chapterUrl=%s]',
            __CLASS__,
            $pdfFile,
            $chapterUrl
        );

        parent::__construct($message);
    }
}
