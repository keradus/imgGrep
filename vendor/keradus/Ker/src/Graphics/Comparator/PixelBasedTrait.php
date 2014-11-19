<?php

namespace Ker\Graphics\Comparator;

trait PixelBasedTrait
{
    public function compare()
    {
        $imgA = $this->imageA;
        $imgB = $this->imageB;
        $width = $imgA->getWidth();
        $height = $imgA->getHeight();
        $diff = 0;

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $diff += $this->comparePixels($imgA->getColor($x, $y), $imgB->getColor($x, $y));

                if ($this->wasCompared) {
                    return;
                }
            }
        }

        $this->computeResult($diff);
    }

    abstract protected function compareColorPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b);
    abstract protected function compareGreyPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b);

    protected function comparePixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        if ($this->useGreyscale) {
            return $this->compareGreyPixels($_a, $_b);
        }

        return $this->compareColorPixels($_a, $_b);
    }
}
