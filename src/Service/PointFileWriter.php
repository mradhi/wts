<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Point;
use Doctrine\Common\Collections\Collection;
use League\Csv\Writer;
use SplFileObject;

class PointFileWriter
{
    /**
     * @param Collection|Point[] $points
     */
    public function write(Collection $points): string
    {
        $writer = Writer::createFromString();

        // Add header
        $writer->insertOne(['identifier', 'x', 'y', 'z']);

        // Add points
        foreach ($points as $point) {
            $writer->insertOne([$point->getIdentifier(), $point->getX(), $point->getY(), $point->getZ()]);
        }

        return $writer->getContent();
    }
}