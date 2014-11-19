<?php

namespace Ker\Graphics\Comparator;

// wiecej = gorzej
class PixelSimilarity extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\PixelBasedTrait;

    protected function compareColorPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return (
            abs($_a->getRed() - $_b->getRed())      +
            abs($_a->getGreen() - $_b->getGreen())  +
            abs($_a->getBlue() - $_b->getBlue())    +
            abs($_a->getAlpha() - $_b->getAlpha())
        ) / 4;
    }

    protected function compareGreyPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return abs($_a->getGrey() - $_b->getGrey());
    }

    protected function computeResult($_diff)
    {
        $this->isIdentical = !$_diff;
        $this->ratio = $_diff / ($this->imageA->getWidth() * $this->imageA->getHeight() * 255);
    }
}
