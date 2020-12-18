<?php

namespace App\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file): string;

    /**
     * @param string $path
     */
    public function remove(string $path): void;
}