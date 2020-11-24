<?php
declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportPoints
{
    protected ?UploadedFile $file = null;

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }
}