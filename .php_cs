<?php

use Symfony\CS\Config\Config;

$config = Config::create()
    // use default level and extra fixers:
    ->fixers([
        '-psr0',
        '-concat_without_spaces',
        'align_double_arrow',
        'concat_with_spaces',
        'ordered_use',
        'short_array_syntax',
        'strict',
        'strict_param',
    ])
    ->setUsingCache(true)
    ->setUsingLinter(false);

$config->getFinder()
    ->exclude(['var'])
    ->in(__DIR__);

return $config;
