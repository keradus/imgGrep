<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

// http://silex.sensiolabs.org/doc/providers/form.html
$app->get(
        '/',
        function (Request $request) use ($app) {
            $defaults = [];

            $form = $app['form.factory']->createBuilder('form', $defaults)
                ->add('file', 'file')
                ->add('resize', 'checkbox', [
                    'label'    => 'Zezwól na dopasowanie rozmiarów',
                    'required' => false,
                ])
                ->add('grey', 'checkbox', [
                    'label' => 'Sprawdzaj w skali szarości',
                ])
                ->add('identical', 'checkbox', [
                    'label' => 'Pokaż tylko identyczne',
                ])
                ->add('limit', 'integer', [
                    'label' => 'Limit wyników',
                ])
                ->add('algorithm', 'choice', [
                    'label'   => 'Algorytm',
                    'choices' => [
                        1 => 'jakaś algorytm',
                    ],
                ])
                ->add('gallery', 'choice', [
                    'label'   => 'Baza obrazków',
                    'choices' => [
                        1 => 'jakaś galeria',
                    ],
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
