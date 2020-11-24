<?php
declare(strict_types=1);

namespace App\Request;

use App\Entity\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ExportPoints
{
    protected Collection $points;

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): void
    {
        if ($this->points->contains($point)) {
            return;
        }

        $this->points->add($point);
    }
}