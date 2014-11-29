<?php

use Keradus\Graphics\Comparator;
use Keradus\Graphics\Image;
use Keradus\Graphics\ImageFileLoader;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

$app = new Application();
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider());

$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    return $twig;
}));

$app['imgGrep.compare'] = $app->protect(function (array $_params) {
    ImageFileLoader::registerBuiltInParsers();

    $comparator = Comparator::createInstance(
        $_params['algorithm'],
        [
            'imgA' => new Image(new ImageFileLoader($_params['fileA'])),
            'imgB' => new Image(new ImageFileLoader($_params['fileB'])),
        ]
    );

    $comparator->allowResize = !!$_params['resize'];
    $comparator->useGreyscale = !!$_params['grey'];
    $comparator->process();

    return [
        'wasCompared' => $comparator->wasCompared(),
        'isIdentical' => $comparator->isIdentical(),
        'ratio'       => $comparator->getSimilarityRatio(),
    ];
});

return $app;
