<?php

namespace Ker\Graphics\Comparator;

class BlockSimilarity extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\BlockBasedTrait;

    protected function compareColorBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b)
    {
        $colorA = $_a->getAverageColor();
        $colorB = $_b->getAverageColor();

        return $_a->getSize() * (
            abs($colorA->getRed() - $colorB->getRed())      +
            abs($colorA->getGreen() - $colorB->getGreen())  +
            abs($colorA->getBlue() - $colorB->getBlue())    +
            abs($colorA->getAlpha() - $colorB->getAlpha())
        ) / 4;
    }

    protected function compareGreyBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b)
    {
        $colorA = $_a->getAverageColor();
        $colorB = $_b->getAverageColor();

        return $_a->getSize() * abs($colorA->getGrey() - $colorB->getGrey());
    }

    protected function computeResult($_diff)
    {
        $this->isIdentical = !$_diff;
        $this->ratio = $_diff / ($this->imageA->getWidth() * $this->imageA->getHeight() * 255);
    }
}
