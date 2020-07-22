<?php

namespace App\Services\Google\Objects;

class Media
{
    private string $downloadId;
    private string $description;
    private string $fileName;
    private string $token;

    /**
     * @param string $downloadId
     * @param string $description
     * @param string $fileName
     * @param string $token
     */
    public function __construct(string $downloadId, string $description, string $fileName, string $token)
    {
        $this->downloadId = $downloadId;
        $this->description = $description;
        $this->fileName = $fileName;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getDownloadId(): string
    {
        return $this->downloadId;
    }
}
