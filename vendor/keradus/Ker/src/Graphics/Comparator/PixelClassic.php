<?php

namespace Ker\Graphics\Comparator;

// ratio = null
class PixelClassic extends \Ker\Graphics\Comparator
{
    use \Ker\Graphics\Comparator\PixelBasedTrait;

    protected function compareColorPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        // jesli napotkalismy na rozne pixele - konczymy porownywanie obrazow
        $this->wasCompared = (
            $_a->getRed() !== $_b->getRed()     ||
            $_a->getGreen() !== $_b->getGreen() ||
            $_a->getBlue() !== $_b->getBlue()   ||
            $_a->getAlpha() !== $_b->getAlpha()
        );
    }

    protected function compareGreyPixels(\Ker\Graphics\Color $_a, \Ker\Graphics\Color $_b)
    {
        // jesli napotkalismy na rozne pixele - konczymy porownywanie obrazow
        $this->wasCompared = ($_a->getGrey() !== $_b->getGrey());
    }

    protected function computeResult($_diff)
    {
        // obrazy sa identyczne jesli nie nastapilo przerwanie porownywania
        $this->isIdentical = !$this->wasCompared;

        // w tym algorytmie nie wyznaczamy wspolczynnika podobienstwa
        unset($_diff);
    }
}
