<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "vendor\keradus\Psr4Autoloader\src\Keradus\Psr4Autoloader.php";

$psr4autoloader = new \Keradus\Psr4Autoloader();
$psr4autoloader->register();
$psr4autoloader->addNamespace("Ker", "vendor\keradus\Ker\src");

function compare (array $_params) {
    $comparator = \Ker\Graphics\Comparator::createInstance(
        $_params["algorithm"],
        [
            "imgA" => new \Ker\Graphics\Image(new \Ker\Graphics\ImageFileLoader($_params["fileA"])),
            "imgB" => new \Ker\Graphics\Image(new \Ker\Graphics\ImageFileLoader($_params["fileB"])),
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

$response = [];
try {
    $response["result"] = compare($_POST["params"]);
} catch (\Exception $e) {
    $response["error"] = true;
    $response["error_descr"] = $e->getMessage();
}

echo json_encode($response);
