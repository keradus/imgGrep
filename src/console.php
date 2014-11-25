<?php

use Keradus\Graphics\Comparator;
use Keradus\Graphics\Image;
use Keradus\Graphics\ImageFileLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

$console = new Application('imgGrep', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('compare')
    ->setDefinition([
        new InputArgument('base', InputArgument::REQUIRED, 'Image base', null),
        new InputArgument('dest', InputArgument::REQUIRED, 'Image to compare or directory with images', null),
        new InputOption('algorithm', null, InputOption::VALUE_REQUIRED, 'Algorithm'),
        new InputOption('grey', null, InputOption::VALUE_NONE, 'Compare in grey channel'),
        new InputOption('resize', null, InputOption::VALUE_NONE, 'Allow to resize'),
        new InputOption('iterations', null, InputOption::VALUE_REQUIRED, 'Number of iteration', 1),

    ])
    ->setDescription('Compare images')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $arguments = $input->getArguments();
        $options   = $input->getOptions();

        if (empty($options['algorithm'])) {
            $output->writeln('<error>Missing algorithm.</error>');

            return 1;
        }

        $allowedAlgorithms = Comparator::getAllowedInstanceNames();

        if (!in_array($options['algorithm'], $allowedAlgorithms)) {
            $output->writeln('<error>Unknown algorithm. Use one of ' . join(', ', $allowedAlgorithms) . '.</error>');

            return 1;
        }

        if (!is_file($arguments['base'])) {
            $output->writeln('<error>Invalid base file.</error>');

            return 1;
        }

        if (!is_file($arguments['dest']) && !is_dir($arguments['dest'])) {
            $output->writeln('<error>Invalid dest file/dir.</error>');

            return 1;
        }

        $compareSingleFile = is_file($arguments['dest']);

        $filesToCompare = [];
        if ($compareSingleFile) {
            $filesToCompare[] = $arguments['dest'];
        } else {
            foreach (Finder::create()->files()->in($arguments['dest']) as $file) {
                if ("" !== $file->getExtension()) {
                    $filesToCompare[] = $file->getPathname();
                }
            }
        }

        ImageFileLoader::registerBuiltInParsers();

        $compareResults = [];

        foreach ($filesToCompare as $file) {
            $result = $app['imgGrep.compare']([
                'fileA'     => $arguments['base'],
                'fileB'     => $file,
                'algorithm' => $options['algorithm'],
                'grey'      => $options['grey'],
                'resize'    => $options['resize'],
            ]);
            $result['file'] = $file;
            $compareResults[] = $result;
            $output->write('.');
        }
        $output->write("\n");

        $compareResults = array_filter($compareResults, function ($item) { return $item['wasCompared']; });

        usort($compareResults, function ($a, $b) {
            if ($a['isIdentical']) {
                return -1;
            }

            if ($b['isIdentical']) {
                return 1;
            }

            return $a['ratio'] - $b['ratio'];
        });

        $output->writeln(json_encode($compareResults, JSON_PRETTY_PRINT));
    })
;

return $console;
