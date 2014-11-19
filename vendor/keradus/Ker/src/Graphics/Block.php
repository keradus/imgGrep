<?php

namespace Ker\Graphics;

class Block
{
    use \Ker\InaccessiblePropertiesProtectorTrait;

    public static function chunkImage(\Ker\Graphics\Image $_img, $_blockSize)
    {
        $blocks = [];

        $width = $_img->getWidth();
        $height = $_img->getHeight();

        $sizeX = ceil($width / $_blockSize);
        $sizeY = ceil($height / $_blockSize);

        $xBlocks = array_chunk($_img->getColorMatrix(), $_blockSize);

        for ($x = 0; $x < $sizeX; ++$x) {
            $blocks[$x] = [];

            foreach ($xBlocks[$x] as $k => $xBlock) {
                $xBlocks[$x][$k] = array_chunk($xBlock, $_blockSize);
            }

            for ($y = 0; $y < $sizeY; ++$y) {
                $tmp = [];

                foreach ($xBlocks[$x] as $yBlocks) {
                    $tmp[] = $yBlocks[$y];
                }

                $blocks[$x][$y] = new static($tmp);
            }
        }

        return $blocks;
    }

    protected $cells;

    public function __construct(array $_cells)
    {
        $this->cells = $_cells;
    }

    public function getCells()
    {
        return $this->cells;
    }

    public function getSize()
    {
        return (count($this->cells) * count($this->cells[0]));
    }

    public function getAverageColor()
    {
        $r = [];
        $g = [];
        $b = [];
        $a = [];

        foreach ($this->cells as $row) {
            foreach ($row as $cell) {
                $r[] = $cell->getRed();
                $g[] = $cell->getGreen();
                $b[] = $cell->getBlue();
                $a[] = $cell->getAlpha();
            }
        }

        return new \Ker\Graphics\Color([
            "red" => round(array_sum($r) / count($r)),
            "green" => round(array_sum($g) / count($g)),
            "blue" => round(array_sum($b) / count($b)),
            "alpha" => round(array_sum($a) / count($a)),
        ]);
    }
}
