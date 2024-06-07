<?php

namespace App\Factory;

use App\Service\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderFactory
{
    public function __construct(
        private string $housingsDirectory,
        private string $tenantsDirectory,
        private string $usersDirectory,
        private string $defaultDirectory,
        private SluggerInterface $slugger
    ) {}

    /**
     * @param string $directoryType
     * @return FileUploader
     */
    public function createUploader(string $directoryType): FileUploader
    {
        return new FileUploader(
            $this->housingsDirectory,
            $this->tenantsDirectory,
            $this->usersDirectory,
            $this->defaultDirectory,
            $this->slugger,
            $directoryType
        );
    }
}
