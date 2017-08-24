<?php

namespace App;

use DI\ContainerBuilder;
use Interop\Container\ContainerInterface;

class App extends \DI\Bridge\Slim\App
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $definitions = [
            'settings.displayErrorDetails' => true,

            \Slim\Views\Twig::class => function (ContainerInterface $container) {
                $view = new \Slim\Views\Twig(__DIR__ . '/../templates', [
                    'cache' => false,
                ]);

                $view->addExtension(new \Slim\Views\TwigExtension(
                    $container->get('router'),
                    $container->get('request')->getUri()
                ));

                return $view;
            },
            \App\Helpers\DataInterface::class => function (ContainerInterface $container) {
                return new \App\Helpers\DataInFile('../data/data_in_file.php');
            },
            \App\Helpers\DataLoginAttempts::class => function (ContainerInterface $container) {
                return new \App\Helpers\DataLoginAttempts('../data/data_login_attempts.php');
            },
        ];

        $builder->addDefinitions($definitions);
    }
}