<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User as User;
use App\Helpers\User as UserHelper;
use Interop\Container\ContainerInterface;

class LoginController
{
    public function index(Request $request, Response $response, View $view, User $user, ContainerInterface $container, UserHelper $userHelper)
    {
        if ($userHelper->isLoggedIn()) {
            $url = $container->get('router')->pathFor('profile');

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        $authorizationStatus = array('errors' => false, 'text'   => '');

        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $authorizationStatus = $user->processAttemptsStatus($data['username']);

            if (!$authorizationStatus['errors'] && $userHelper->login()) {
                $user->emptyUserAttempts($data['username']);
                $url = $container->get('router')->pathFor('profile');

                return $response->withStatus(302)->withHeader('Location', $url);
            } else {
                if ($authorizationStatus['errors'] === false) {
                    $authorizationStatus['errors'] = true;
                    $authorizationStatus['text'] = 'Login or password incorrect';
                }
            }
        }

        return $view->render($response, 'login_form.twig', array('status' => $authorizationStatus));
    }

    public function logout(Response $response, ContainerInterface $container, UserHelper $userHelper)
    {
        $userHelper->logout();

        $url = $container->get('router')->pathFor('loginForm');
        return $response->withStatus(302)->withHeader('Location', $url);
    }
}