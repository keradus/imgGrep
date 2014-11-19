<?php

// http://www.qfs.de/qftest/manual/en/tech_imagealgorithmdetails.html
// http://blog.jeffterrace.com/2012/09/comparing-image-comparison-algorithms.html
// http://en.wikipedia.org/wiki/Histogram_matching
// http://stackoverflow.com/questions/843972/image-comparison-fast-algorithm

namespace Ker\Graphics;

abstract class Comparator
{
    use \Ker\InaccessiblePropertiesProtectorTrait;

    const INVERT_SIMILARITY_RATIO = false;

    protected static $instancesDefiniotion = null;

    public static function __constructStatic()
    {
        static $isInitialized = false;

        if ($isInitialized) {
            return;
        }

        $isInitialized = true;

        $class = __CLASS__;
        $subclasses = ["BlockIdentity", "BlockSimilarity", "PixelIdentity", "PixelPSNR", "PixelSimilarity", "PixelClassic", ];
        $subclassesMap = [];

        foreach ($subclasses as $subclass) {
            $subclassesMap[$subclass] = "$class\\$subclass";
        }

        static::$instancesDefiniotion = $subclassesMap;
    }

    public static function createInstance($_name, $_params)
    {
        return new static::$instancesDefiniotion[$_name]($_params);
    }

    public static function getAllowedInstanceNames()
    {
        $names = array_keys(static::$instancesDefiniotion);
        sort($names);

        return $names;
    }
// TODO: czy ta metoda jest uzywana ???
    public static function registerInstanceDefinition($_name, $_class)
    {
        if (!class_exists($_class)) {
            throw new \InvalidArgumentException("Class $_class do not exists");
        }

        if (!is_subclass_of($_class, __CLASS__)) {
            throw new \LogicException("Class $_class is not subclass of " . __CLASS__);
        }

        static::$instancesDefiniotion[$_name] = $_class;
    }

    protected $imageA;
    protected $imageB;
    protected $isIdentical = false;
    protected $ratio = null;
    protected $wasCompared = false;

    public $allowResize = false;
    public $useGreyscale = false;

    public function __construct(array $_)
    {
        if (empty($_["imgA"]) || !($_["imgA"] instanceof \Ker\Graphics\Image)) {
            throw new \InvalidArgumentException("Invalid parameter imgA");
        }

        if (empty($_["imgB"]) || !($_["imgB"] instanceof \Ker\Graphics\Image)) {
            throw new \InvalidArgumentException("Invalid parameter imgB");
        }

        $this->imageA = $_["imgA"];
        $this->imageB = $_["imgB"];
    }

    protected function adjustSizes()
    {
        if ($this->sizesAreEqual()) {
            return;
        }

        $width = min($this->imageA->getWidth(), $this->imageB->getWidth());
        $height = min($this->imageA->getHeight(), $this->imageB->getHeight());

        $this->imageA = clone $this->imageA;
        $this->imageB = clone $this->imageB;

        $this->imageA->resize($width, $height);
        $this->imageB->resize($width, $height);
    }

    public function isIdentical()
    {
        return $this->isIdentical;
    }

    // mniej = podobniej
    public function getSimilarityRatio()
    {
        if (static::INVERT_SIMILARITY_RATIO) {
            return -1 * $this->ratio;
        }

        return $this->ratio;
    }

    public function wasCompared()
    {
        return $this->wasCompared;
    }

    public function sizesAreEqual()
    {
        return (
            $this->imageA->getWidth() === $this->imageB->getWidth()     &&
            $this->imageA->getHeight() === $this->imageB->getHeight()
        );
    }

    public function process()
    {
        if (!$this->sizesAreEqual()) {
            if (!$this->allowResize) {
                return;
            }

            $this->adjustSizes();
        }

        $this->compare();
        $this->wasCompared = true;
    }

    abstract public function compare();
    abstract protected function computeResult($_diff);
}

Comparator::__constructStatic();
