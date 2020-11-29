<?php
declare(strict_types=1);

namespace App\Service;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\TabularDataReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PointFileReader
{
    public function read(UploadedFile $file): TabularDataReader
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setOutputBOM(Reader::BOM_UTF8);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        return (new Statement())->process($csv);
    }
}