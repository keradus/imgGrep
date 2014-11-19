<?php

namespace Ker\Graphics\Comparator;

trait BlockBasedTrait
{
    public $chunkSize = 8;
    protected $chunksQuantity;

    public function compare()
    {
        $blocksA = \Ker\Graphics\Block::chunkImage($this->imageA, $this->chunkSize);
        $blocksB = \Ker\Graphics\Block::chunkImage($this->imageB, $this->chunkSize);

        $sizeX = count($blocksA);
        $sizeY = count($blocksA[0]);

        $this->chunksQuantity = ["x" => $sizeX, "y" => $sizeY, ];

        $diff = 0;

        for ($x = 0; $x < $sizeX; ++$x) {
            for ($y = 0; $y < $sizeY; ++$y) {
                $diff += $this->compareBlock($blocksA[$x][$y], $blocksB[$x][$y]);

                if ($this->wasCompared) {
                    return;
                }
            }
        }

        $this->computeResult($diff);
    }

    abstract protected function compareColorBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b);
    abstract protected function compareGreyBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b);

    protected function compareBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b)
    {
        if ($this->useGreyscale) {
            return $this->compareGreyBlock($_a, $_b);
        }

        return $this->compareColorBlock($_a, $_b);
    }
}
