<?php

$_blockSize = 2;
$blocks = [];
$blocks2 = [];

$matrix = [];
$matrix[0] = [11, 12, 13, 14, 15, ];
$matrix[1] = [21, 22, 23, 24, 25, ];
$matrix[2] = [31, 32, 33, 34, 35, ];


$width = count($matrix);
$height = count($matrix[0]);

$sizeX = ceil($width / $_blockSize);
$sizeY = ceil($height / $_blockSize);

$xBlocks = array_chunk($matrix, $_blockSize);

for ($x = 0; $x < $sizeX; ++$x) {
    $blocks[$x] = [];

    for ($y = 0; $y < $sizeY; ++$y) {
        $blocks[$x][$y] = [];
        $blocks2["$x-$y"] = [];

        foreach ($xBlocks[$x] as $xBlock) {
            $yBlocks = array_chunk($xBlock, $_blockSize);
            $blocks[$x][$y][] = $yBlocks[$y];
            $blocks2["$x-$y"][] = $yBlocks[$y];
        }
    }
}

var_dump(json_encode($blocks2, JSON_PRETTY_PRINT));