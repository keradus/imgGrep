<?php

// configure your app for the production environment

$app['twig.path'] = [__DIR__ . '/../templates'];
$app['twig.options'] = ['cache' => __DIR__ . '/../var/cache/twig'];

$app['imgGrep.galleriesDir.web'] = 'galleries';
$app['imgGrep.galleriesDir.server'] = __DIR__ . '/../web/galleries';
$app['imgGrep.galleries'] = [
    'exampleGallery' => 'exampleGallery',
];
$app['imgGrep.cache'] = __DIR__ . '/../var/cache/imgGrep';
