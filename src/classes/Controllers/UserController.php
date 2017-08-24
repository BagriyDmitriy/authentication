<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\User as User;
use App\Helpers\User as UserHelper;
use Interop\Container\ContainerInterface;

class UserController
{
    public function index(Response $response, View $view, User $user, UserHelper $userHelper, ContainerInterface $container)
    {
        if (!$userHelper->isLoggedIn()) {
            $url = $container->get('router')->pathFor('loginForm');
            return $response->withStatus(302)->withHeader('Location', $url);
        }

        return $view->render($response, 'profile.twig', array('username' => $user->getUserName()));
    }
}