<?php

namespace Ker\Graphics;

class Color
{
    use \Ker\InaccessiblePropertiesProtectorTrait;
    use \Ker\InnerClassCacheTrait;

    protected $alpha;
    protected $blue;
    protected $green;
    protected $red;

    public function __construct(array $_rgba)
    {
        $this->alpha = $_rgba["alpha"];
        $this->blue = $_rgba["blue"];
        $this->green = $_rgba["green"];
        $this->red = $_rgba["red"];
    }

    public function getAlpha()
    {
        return $this->alpha;
    }

    public function getBlue()
    {
        return $this->blue;
    }

    public function getGreen()
    {
        return $this->green;
    }

    public function getGrey()
    {
        if (isset($this->cache["grey"])) {
            return $this->cache["grey"];
        }

        $grey = (
            0.2126 * $this->red +
            0.7152 * $this->green +
            0.0722 * $this->blue
        );

        $this->cache["grey"] = $grey;

        return $grey;
    }

    public function getRed()
    {
        return $this->red;
    }

    public function getRGBA()
    {
        return [
            "red" => $this->red,
            "green" => $this->green,
            "blue" => $this->blue,
            "alpha" => $this->alpha,
        ];
    }
}
