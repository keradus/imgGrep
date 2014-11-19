<?php

namespace Ker\Graphics\Comparator;

// wiecej = lepiej
class PixelPSNR extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\PixelBasedTrait;

    const INVERT_SIMILARITY_RATIO = true;

    protected function compareColorPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return (
            pow($_a->getRed() - $_b->getRed(), 2)       +
            pow($_a->getGreen() - $_b->getGreen(), 2)   +
            pow($_a->getBlue() - $_b->getBlue(), 2)     +
            pow($_a->getAlpha() - $_b->getAlpha(), 2)
        ) / 4;
    }

    protected function compareGreyPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        return pow($_a->getGrey() - $_b->getGrey(), 2);
    }

    protected function computeResult($_diff)
    {
        if (!$_diff) {
            $this->isIdentical = true;

            return;
        }

        $this->ratio = 10 * log10(pow(255, 2) * $this->imageA->getWidth() * $this->imageA->getHeight() / $_diff);
    }
}
