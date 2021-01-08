<?php

namespace App\Uploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader implements UploaderInterface
{
    private const FILENAME_FORMAT = '%s_%s.%s';
    private const FILE_EXTENSION = ".pdf";

    private string $uploadsAbsoluteDir;
    private string $uploadsRelativeDir;
    private SluggerInterface $slugger;
    private RequestStack $requestStack;

    public function __construct(string $uploadsAbsoluteDir, string $uploadsRelativeDir, SluggerInterface $slugger, RequestStack $requestStack)
    {
        $this->uploadsAbsoluteDir = $uploadsAbsoluteDir;
        $this->uploadsRelativeDir = $uploadsRelativeDir;
        $this->slugger = $slugger;
        $this->requestStack = $requestStack;
    }

    public function upload(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName();

        if (str_contains(self::FILE_EXTENSION, $originalName)) {
            $originalName = substr($file->getClientOriginalName(), 0, -4); // Cut .pdf
        }

        $filename = sprintf(
            self::FILENAME_FORMAT,
            $this->slugger->slug($originalName), // Slugify img name
            uniqid('', true),
            $file->getClientOriginalExtension()
        );

        $file->move($this->uploadsAbsoluteDir, $filename);

        return $filename;
    }

    public function remove(string $path): void
    {
        $filesystem = new Filesystem();
        
        if ($filesystem->exists($path)) {
            $filesystem->remove($path);
        }
    }

    private function getCurrentDomain(): string
    {
        return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost(); // @TODO: try ?
    }
}