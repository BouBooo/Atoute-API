<?php

namespace App\Uploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader implements UploaderInterface
{
    private const FILENAME_FORMAT = '%s_%s.%s';

    private string $uploadsAbsoluteDir;
    private string $uploadsRelativeDir;
    private SluggerInterface $slugger;

    public function __construct(string $uploadsAbsoluteDir, string $uploadsRelativeDir, SluggerInterface $slugger)
    {
        $this->uploadsAbsoluteDir = $uploadsAbsoluteDir;
        $this->uploadsRelativeDir = $uploadsRelativeDir;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $filename = sprintf(
            self::FILENAME_FORMAT,
            $this->slugger->slug($file->getClientOriginalName()), // Slugify img name
            uniqid('', true),
            $file->getClientOriginalExtension()
        );

        $file->move($this->uploadsAbsoluteDir, $filename);

        return $this->uploadsRelativeDir.$filename;
    }

    public function remove(string $path): void
    {
        $filesystem = new Filesystem();
        
        if($filesystem->exists($path)) {
            $filesystem->remove($path);
        }
    }
}