<?php

namespace Ker\Graphics;

/**
 * Klasa z narzędziami do ladowania obrazow z plikow.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @copyright Copyright (c) Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ImageFileLoader
{
    use \Ker\InaccessiblePropertiesProtectorTrait;

    protected static $parsers = array();

    public static function registerBuiltInParsers()
    {
        static $registered = false;

        if ($registered) {
            return;
        }

        $registered = true;

        $parsers = [
            "image/gif" => function($_filename) {
                return @imagecreatefromgif($_filename);
            },
            "image/png" => function($_filename) {
                return @imagecreatefrompng($_filename);
            },
            "image/bmp" => function($_filename) {
                return @\Ker\Graphics\ExtLib::imageCreateFromBmp($_filename);
            },
            "image/jpeg" => function($_filename) {
                return @imagecreatefromjpeg($_filename);
            },
            "application/octet-stream" => function($_filename) {
                return @imagecreatefromgd2($_filename);
            },
        ];
        
        foreach ($parsers as $type => $parser) {
            static::registerParser($type, $parser);
        }
    }

    public static function registerParser($_type, callable $_parser)
    {
        static::$parsers[$_type] = $_parser;
    }

    public static function getAvailableTypes()
    {
        return array_keys(static::$parsers);
    }

    protected $filename;
    protected $mimeType;
    protected $resource;

    public function __construct($_filename)
    {
        $this->filename = $_filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function process()
    {
        // if already processed - return
        if ($this->resource) {
            return;
        }

        $file = $this->filename;

        if (!filesize($file)) {
            throw new \DomainException("empty file");
        }

        $imageType = exif_imagetype($file);
        $mimeType = image_type_to_mime_type($imageType);
        $this->mimeType = $mimeType;

        if (!isset(static::$parsers[$mimeType])) {
            throw new \DomainException("unknown mime type: $mimeType");
        }

        $parser = static::$parsers[$mimeType];

        $this->resource = $parser($file);

        if (!$this->resource) {
            throw new \DomainException("malformed image");
        }
    }
}
