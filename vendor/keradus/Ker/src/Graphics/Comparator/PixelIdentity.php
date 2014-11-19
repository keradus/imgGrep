<?php

namespace Ker\Graphics\Comparator;

// wiecej = gorzej
class PixelIdentity extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\PixelBasedTrait;

    protected function compareColorPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return (
            (int) ($_a->getRed() !== $_b->getRed())      +
            (int) ($_a->getGreen() !== $_b->getGreen())  +
            (int) ($_a->getBlue() !== $_b->getBlue())    +
            (int) ($_a->getAlpha() !== $_b->getAlpha())
        ) / 4;
    }

    protected function compareGreyPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return (int) ($_a->getGrey() !== $_b->getGrey());
    }

    protected function computeResult($_diff)
    {
        $this->isIdentical = !$_diff;
        $this->ratio = $_diff / ($this->imageA->getWidth() * $this->imageA->getHeight());
    }
}
