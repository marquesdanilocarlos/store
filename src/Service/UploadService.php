<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class UploadService
{

    public function __construct(
        private string $uploadDir,
        private bool $hasLog,
        private LoggerInterface $logger
    ) {
    }

    public function upload()
    {
        dump($this->logger);

        if ($this->hasLog) {
            $this->logger->info('Log vindo da classe de UPLOAD SERVICE');
        }

        $hasLog = $this->hasLog ? 'Sim' : 'Não';
        return "Realização de upload para: {$this->uploadDir}, Tem log? {$hasLog}";
    }

}