<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "./vendor/autoload.php";

\Keradus\Graphics\ImageFileLoader::registerBuiltInParsers();

$galleryDir = "exampleGallery";
$testFile = "exampleGallery/park.jpg";

function getImageFileLoader($_file)
{
    static $loaders = [];

    if (!isset($loaders[$_file])) {
        $loaders[$_file] = new \Keradus\Graphics\ImageFileLoader($_file);
    }

    return $loaders[$_file];
}

function compare(array $_params)
{
    $comparator = \Keradus\Graphics\Comparator::createInstance(
        $_params["algorithm"],
        [
            "imgA" => new \Keradus\Graphics\Image(getImageFileLoader($_params["fileA"])),
            "imgB" => new \Keradus\Graphics\Image(getImageFileLoader($_params["fileB"])),
        ]
    );

    $comparator->allowResize = ($_params["resize"] ? true : false);
    $comparator->useGreyscale = ($_params["gray"] ? true : false);
    $comparator->process();

    return [
        "wasCompared" => $comparator->wasCompared(),
        "isIdentical" => $comparator->isIdentical(),
        "ratio" => $comparator->getSimilarityRatio(),
    ];
};

$files = [];

foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($galleryDir)) as $item) {
    if ($item->isFile() && $item->getExtension() !== "") {
        $files[] = $item->getPathname();
    }
}

foreach (array_merge([$testFile], $files) as $file) {
    getImageFileLoader($file)->process();
}

$sortFnc = function ($_a, $_b) {
    if ($_a["isIdentical"]) {
        return -1;
    }

    if ($_b["isIdentical"]) {
        return 1;
    }

    return $_a["ratio"] > $_b["ratio"];
};

$algorithms = \Keradus\Graphics\Comparator::getAllowedInstanceNames();

$params = [
    "gray" => false,
    "resize" => true,
    "fileA" => $testFile,
    "fileB" => null,
    "algorithm" => null,
];

$result = [];

$time = [];
$time["start"] = microtime(true);

foreach ($algorithms as $algorithm) {
    $algorithmResult = [];
    $params["algorithm"] = $algorithm;

    foreach ($files as $file) {
        $shortName = substr($file, strlen($galleryDir)+1);
        echo "Compare [$algorithm]: " . $shortName;
        $params["fileB"] = $file;
        $algorithmResult[$shortName] = compare($params);
        echo "\n";

        if (count($algorithmResult) === 2) {
            break;
        }
    }

    uasort($algorithmResult, $sortFnc);
    $result[$algorithm] = $algorithmResult;
}
$time["end"] = microtime(true);
$time["diff"] = $time["end"] - $time["start"];
echo json_encode($result, JSON_PRETTY_PRINT);
var_dump($time);
