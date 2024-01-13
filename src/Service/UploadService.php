<?php

namespace App\Service;

class UploadService
{

    public function __construct(
        private string $uploadDir
    ) {
    }

    public function upload()
    {
        return "Realização de upload para: {$this->uploadDir}";
    }

}