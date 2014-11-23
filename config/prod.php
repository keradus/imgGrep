<?php

// configure your app for the production environment

$app['twig.path'] = [__DIR__ . '/../templates'];
$app['twig.options'] = ['cache' => __DIR__ . '/../var/cache/twig'];

$app['imgGrep.galleries'] = [
    'exampleGallery' => __DIR__ . '/../web/galleries/exampleGallery',
];
$app['imgGrep.cache'] = __DIR__ . '/../var/cache/imgGrep';
