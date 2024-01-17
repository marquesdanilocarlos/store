<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{

    public function __construct(
        private string $uploadDir,
        private SluggerInterface $slugger,
        private Filesystem $filesystem
    ) {
    }

    public function upload($files, $targetFolder = ''): array
    {
        $uploadDir = "{$this->uploadDir}/{$targetFolder}";

        if (is_array($files)) {
            $newFiles = [];
            foreach ($files as $file) {
                /**
                 * @var UploadedFile $file ;
                 */
                $newFiles[] = $this->move($uploadDir, $file);
            }
            return $newFiles;
        }

        return [$this->move($uploadDir, $files)];
    }

    private function createNewName(UploadedFile $file): string
    {
        $originalFilename = $file->getClientOriginalName();
        $safeFilename = $this->slugger->slug($originalFilename);
        return $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
    }

    private function move(string $uploadDir, UploadedFile $file): string
    {
        $newName = $this->createNewName($file);
        $file->move($uploadDir, $newName);

        return $newName;
    }

    public function remove(string $fileName): void
    {
        if (!$this->filesystem->exists($fileName)) {
            return;
        }

        $this->filesystem->remove($fileName);
    }

}