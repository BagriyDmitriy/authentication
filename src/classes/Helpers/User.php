<?php

namespace App\Helpers;

use Interop\Container\ContainerInterface;
use App\Models\User as UserModel;

class User
{
    protected $request;
    protected $response;
    protected $container;
    protected $userModel;

    public function __construct(ContainerInterface $container, UserModel $userModel)
    {
         $this->container = $container;
         $this->userModel = $userModel;
         $this->request = $container->get('request');
         $this->response = $container->get('response');
    }

    private function sessionStart()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn()
    {
        $this->sessionStart();

        if (!isset($_SESSION['username'])) {
            return false;
        }
        return true;
    }

    public function login()
    {
        $this->sessionStart();

        if (isset($_SESSION['username'])) {
            return true;
        } else if ($this->request->isPost()) {
            $data = $this->request->getParsedBody();
            $username = $data['username'];
            $password = $data['password'];

            $passwordHash = $this->userModel->getPasswordByName($username);

            if (password_verify($password, $passwordHash)) {
                $_SESSION['username'] = $username;

                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        $this->sessionStart();

        if (isset($_SESSION['username'])) {
            unset($_SESSION['username']);
        }
    }
}