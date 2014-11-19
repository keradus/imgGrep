<?php

namespace Ker\Graphics\Comparator;

class BlockIdentity extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\BlockBasedTrait;

    protected function compareColorBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b)
    {
        $colorA = $_a->getAverageColor();
        $colorB = $_b->getAverageColor();

        return $_a->getSize() * (
            (int) ($colorA->getRed() !== $colorB->getRed())      +
            (int) ($colorA->getGreen() !== $colorB->getGreen())  +
            (int) ($colorA->getBlue() !== $colorB->getBlue())    +
            (int) ($colorA->getAlpha() !== $colorB->getAlpha())
        ) / 4;
    }

    protected function compareGreyBlock(\Ker\Graphics\Block $_a, \Ker\Graphics\Block $_b)
    {
        $colorA = $_a->getAverageColor();
        $colorB = $_b->getAverageColor();

        return $_a->getSize() * (int) ($colorA->getGrey() !== $colorB->getGrey());
    }

    protected function computeResult($_diff)
    {
        $this->isIdentical = !$_diff;
        $this->ratio = $_diff / ($this->imageA->getWidth() * $this->imageA->getHeight());
    }
}
