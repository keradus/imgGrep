<?php

namespace Ker\Graphics;

/**
 * Klasa obsługująca modyfikowanie obrazów. Implementuje wzorzec projektowy adapter dla biblioteki GD.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @license MIT
 */
class Image
{
    use \Ker\InaccessiblePropertiesProtectorTrait;
    use \Ker\InnerClassCacheTrait;

    protected $height;

    protected $resource;

    protected $mimeType;

    protected $width;

    public function __construct(\Ker\Graphics\ImageFileLoader $_imageFileLoader)
    {
        $_imageFileLoader->process();

        $this->resource = $_imageFileLoader->getResource();
        $this->mimeType = $_imageFileLoader->getMimeType();
        $this->width = imagesx($this->resource);
        $this->height = imagesy($this->resource);

        $this->resource = $this->cloneResource(); // by modyfikacje zasobu nie wplywaly na oryginal z ImageFileLoader
    }

    public function __destruct()
    {
        imagedestroy($this->resource);
        $this->clearCache();
    }

    public function __clone()
    {
        $this->resource = $this->cloneResource();
    }

    public function cloneResource()
    {
        $res = imagecreatetruecolor($this->width, $this->height);
        imagecopy($res, $this->resource, 0, 0, 0, 0, $this->width, $this->height);

        return $res;
    }

    public function getChannels()
    {
        if (isset($this->cache["channels"])) {
            return $this->cache["channels"];
        }

        $channels = [
            "red" => [],
            "green" => [],
            "blue" => [],
            "alpha" => [],
            "grey" => [],
        ];

        $channelNames = array_keys($channels);

        $channelMethods = [];
        foreach ($channelNames as $channelName) {
            $channelMethods[$channelName] = "get" . ucfirst($channelName);
        }

        $matrix = $this->getColorMatrix();

        $width = $this->width;
        $height = $this->height;

        for ($x = 0; $x < $width; ++$x) {
            foreach ($channelNames as $channelName) {
                $channels[$channelName][$x] = [];
            }

            for ($y = 0; $y < $height; ++$y) {
                $color = $this->getColor($x, $y);

                foreach ($channelNames as $channelName) {
                    $channels[$channelName][$x][$y] = $color->{$channelMethods[$channelName]}();
                }
            }
        }

        $this->cache["channels"] = $channels;

        return $channels;
    }

    public function getColor($_x, $_y)
    {
        if ($_x < 0 || $_y < 0 || $_x >= $this->width || $_y >= $this->height) {
            throw new \OutOfRangeException();
        }

        $cacheKey = "color_{$_x}_{$_y}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $col = imagecolorat($this->resource, $_x, $_y);
        $rgba = imagecolorsforindex($this->resource, $col);

        $color = new \Ker\Graphics\Color($rgba);

        $this->cache[$cacheKey] = $color;

        return $color;
    }

    public function getColorMatrix()
    {
        if (isset($this->cache["colorMatrix"])) {
            return $this->cache["colorMatrix"];
        }

        $colorMatrix = [];

        $width = $this->width;
        $height = $this->height;

        for ($x = 0; $x < $width; ++$x) {
            $colorMatrix[$x] = [];

            for ($y = 0; $y < $height; ++$y) {
                $colorMatrix[$x][$y] = $this->getColor($x, $y);
            }
        }

        $this->cache["colorMatrix"] = $colorMatrix;

        return $colorMatrix;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function resize($_width, $_height)
    {
        if ($_width <= 0 || $_height <= 0) {
            throw new \InvalidArgumentException("invalid size");
        }

        if ($_width === $this->width AND $_height === $this->height) {
            return;
        }

        $imageNew = imagecreatetruecolor($_width, $_height);
        imagecopyresampled($imageNew, $this->resource, 0, 0, 0, 0, $_width, $_height, $this->width, $this->height);
        imagedestroy($this->resource);
        $this->resource = & $imageNew;
        $this->width = $_width;
        $this->height = $_height;

        $this->clearCache();
    }

    public function resizeProportional($_maxWidth, $_maxHeight)
    {
        if ($_maxWidth <= 0 || $_maxHeight <= 0) {
            throw new \InvalidArgumentException("invalid size");
        }

        $proportion = $this->width / $this->height;
        $proportionMax = $_maxWidth / $_maxHeight;

        $width = $_maxWidth;
        $height = $_maxHeight;

        if ($proportionMax > $proportion) {
            $width = min($width, (int) round($_maxHeight * $proportion));
        } else {
            $height = min($height, (int) round($_maxWidth / $proportion));
        }

        $this->resize($width, $height);
    }

    public function saveToGD2($_filename)
    {
        if (file_exists($_filename)) {
            unlink($_filename);
        }

        $result = imagegd2($this->resource, $_filename);

        if ($result === false) {
            throw new \UnexpectedValueException("fail to save");
        }
    }

    public function saveToJpg($_filename, $_quality = 75)
    {
        if (file_exists($_filename)) {
            unlink($_filename);
        }

        $result = imagejpeg($this->resource, $_filename, $_quality);

        if ($result === false) {
            throw new \UnexpectedValueException("fail to save");
        }
    }

    public function saveToPng($_filename, $_compression = 9)
    {
        if (file_exists($_filename)) {
            unlink($_filename);
        }

        $result = imagepng($this->resource, $_filename, $_compression);

        if ($result === false) {
            throw new \UnexpectedValueException("fail to save");
        }
    }
}
