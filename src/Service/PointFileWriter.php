<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Point;
use Doctrine\Common\Collections\Collection;
use League\Csv\Reader;
use League\Csv\Writer;

class PointFileWriter
{
    /**
     * @param Collection|Point[] $points
     *
     * @return string
     */
    public function write(Collection $points): string
    {
        $writer = Writer::createFromString();
        $writer->setOutputBOM(Writer::BOM_UTF8);
        $writer->setDelimiter(';');
        // Add header
        $writer->insertOne(['identifier', 'x', 'y', 'z']);

        // Add points
        foreach ($points as $point) {
            $writer->insertOne([$point->getIdentifier(), $point->getX(), $point->getY(), $point->getZ()]);
        }

        return $writer->getContent();
    }
}