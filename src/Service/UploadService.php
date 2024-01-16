<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{

    public function __construct(
        private string $uploadDir,
        private SluggerInterface $slugger
    ) {
    }

    public function upload($files, $targetFolder = ''): void
    {
        $uploadDir = "{$this->uploadDir}/{$targetFolder}";

        if (is_array($files)) {
            foreach ($files as $file) {
                /**
                 * @var UploadedFile $file ;
                 */
                $file->move($uploadDir, $this->createNewName($file));
            }
            return;
        }

        $files->move($uploadDir, $this->createNewName($files));
    }

    private function createNewName(UploadedFile $photo): string
    {
        $originalFilename = $photo->getClientOriginalName();
        $safeFilename = $this->slugger->slug($originalFilename);
        return $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
    }

}