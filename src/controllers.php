<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Keradus\Graphics\ImageFileLoader;
use \Keradus\Graphics\Comparator;

$app->get(
        '/',
        function (Request $request) use ($app) {
            $algorithms = Comparator::getAllowedInstanceNames();

            //ImageFileLoader::registerBuiltInParsers();
            //var_dump(ImageFileLoader::getAvailableTypes());

            $defaults = [];

            $form = $app['form.factory']->createBuilder('form', $defaults)
//                ->add('file', 'file')
                ->add('resize', 'checkbox', [
                    'label'    => 'Zezwól na dopasowanie rozmiarów',
                    'required' => false,
                ])
                ->add('grey', 'checkbox', [
                    'label'    => 'Sprawdzaj w skali szarości',
                    'required' => false,
                ])
                ->add('identical', 'checkbox', [
                    'label'    => 'Pokaż tylko identyczne',
                    'required' => false,
                ])
                ->add('limit', 'integer', [
                    'label'     => 'Limit wyników',
                    'precision' => 0,
                    'required'  => false,
                ])
                ->add('algorithm', 'choice', [
                    'label'   => 'Algorytm',
                    'choices' => array_combine($algorithms, $algorithms),
                ])
                ->add('gallery', 'choice', [
                    'label'   => 'Baza obrazków',
                    'choices' => array_combine(array_keys($app['imgGrep.galleries']), array_keys($app['imgGrep.galleries'])),
                ])
                ->add('save', 'submit', [
                    'label' => 'Szukaj',
                ])
                ->getForm();

            $form->handleRequest($request);

            return $app['twig']->render('index.twig', ['form' => $form->createView()]);
        }
    )
    ->bind('index')
;

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $templates = [
        'errors/' . $code . '.twig',
        'errors/' . substr($code, 0, 2) . 'x.twig',
        'errors/' . substr($code, 0, 1) . 'xx.twig',
        'errors/default.twig',
    ];

    return new Response($app['twig']->resolveTemplate($templates)->render(['code' => $code]), $code);
});
