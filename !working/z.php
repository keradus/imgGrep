<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "vendor\keradus\Psr4Autoloader\src\Keradus\Psr4Autoloader.php";

$psr4autoloader = new \Keradus\Psr4Autoloader();
$psr4autoloader->register();
$psr4autoloader->addNamespace("Ker", "vendor\keradus\Ker\src");

$image = new \Ker\Image("baloons.jpg");

$res = $image->getResource();


$blocks = \Ker\Image\Block::chunkImage($image, 4);

$sizeX = count($blocks);
$sizeY = count($blocks[0]);

$res = imagecreatetruecolor($sizeX, $sizeY);

for ($x = 0; $x < $sizeX; ++$x) {
    for ($y = 0; $y < $sizeY; ++$y) {
        $color = $blocks[$x][$y]->getAverageColor();
        imagesetpixel($res, $x, $y, imagecolorallocatealpha($res, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()));
    }
}

imagejpeg($res, "x.jpg", 100);