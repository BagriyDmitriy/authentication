<?php

$app->map(['GET', 'POST'], '/', ['\App\Controllers\LoginController', 'index'])->setName('loginForm');

$app->get('/profile', ['\App\Controllers\UserController', 'index'])->setName('profile');

$app->get('/logout', ['\App\Controllers\LoginController', 'logout'])->setName('logout');