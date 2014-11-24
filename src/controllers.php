<?php

use Keradus\Graphics\Comparator;
use Keradus\Graphics\Image;
use Keradus\Graphics\ImageFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match(
        '/',
        function (Request $request) use ($app) {
            ImageFileLoader::registerBuiltInParsers();
            $algorithms = Comparator::getAllowedInstanceNames();

            $form = $app['form.factory']->createBuilder('form', [])
                ->add('file', 'file', [
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\Image([
                            'mimeTypes'        => ImageFileLoader::getAvailableTypes(),
                            'mimeTypesMessage' => 'Nieprawidłowy plik obrazu',
                        ]),
                    ]
                ])
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

            if ($form->isSubmitted()) {
                $limitField = $form->get('limit');
                $limitFieldValue = $limitField->getData();
                if (null !== $limitFieldValue && (!is_int($limitFieldValue) || $limitFieldValue <= 0)) {
                    $limitField->addError(new Symfony\Component\Form\FormError('Wartość musi być dodatnią liczbą całkowitą'));
                }

                if ($form->isValid()) {
                    $data = $form->getData();

                    $fileId = uniqid();
                    $img = new Image(new ImageFileLoader($data['file']->getRealPath()));
                    $img->saveToGD2($app['imgGrep.cache'] . '/input-' . $fileId . '.gd2');

                    $data['file'] = $fileId;
                    $data['gallery'] = $app['imgGrep.galleries'][$data['gallery']];

                    $config = [
                        'galleriesDir' => $app['imgGrep.galleriesDir.web'],
                        'galleries'    => $app['imgGrep.galleries'],
                        'params'       => $data,
                    ];

                    $files = [];
                    foreach (Finder::create()->files()->in($app['imgGrep.galleriesDir.server']) as $file) {
                        if ("" !== $file->getExtension()) {
                            $files[] = $file->getRelativePathname();
                        }
                    }

                    return $app['twig']->render('index_result.twig', ['config' => json_encode($config), 'files' => $files]);
                }
            }

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
