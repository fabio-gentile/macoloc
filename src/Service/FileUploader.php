<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileUploader
{
    private string $targetDirectory;

    public function __construct(
        private string $housingsDirectory,
        private string $tenantsDirectory,
        private string $usersDirectory,
        private string $defaultDirectory,
        private SluggerInterface $slugger,
        string $directoryType
    ) {
        $this->setTargetDirectory($directoryType);
    }

    /**
     * Upload a file
     * @param UploadedFile $file
     * @return array
     */
    public function upload(UploadedFile $file): array
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new \RuntimeException(sprintf('Could not move the file "%s" to the directory "%s"', $file->getClientOriginalName(), $this->getTargetDirectory()));
        }

        return [
            'fileName' => $fileName,
            'originalFilename' => $originalFilename,
            'mimeType' => $file->getClientMimeType(),
        ];
    }

    /**
     * Remove a file
     * @param string $filename
     * @return bool
     */
    public function remove(string $filename): bool
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
                return true;
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf('Could not delete the file "%s" from the directory "%s"', $fileName, $this->getTargetDirectory()));
            }
        } else {
            throw new \RuntimeException(sprintf('File "%s" does not exist in the directory "%s"', $filename, $this->getTargetDirectory()));
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    private function setTargetDirectory(string $directoryType): void
    {
        if ($directoryType === 'housings') {
            $this->targetDirectory = $this->housingsDirectory;
        } elseif ($directoryType === 'tenants') {
            $this->targetDirectory = $this->tenantsDirectory;
        } elseif ($directoryType === 'users') {
            $this->targetDirectory = $this->usersDirectory;
        } elseif ($directoryType === 'default') {
            $this->targetDirectory = $this->defaultDirectory;
        } else {
            throw new \InvalidArgumentException("Invalid directory type");
        }
    }
}
