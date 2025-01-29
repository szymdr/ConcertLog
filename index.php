<?php
session_start();

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url( $path, PHP_URL_PATH);

$router = new Router();

Router::get('', 'DefaultController');
Router::get('feed', 'DefaultController');
Router::get('changepassword', 'DefaultController');
Router::get('friends', 'DefaultController');
Router::get('addconcert', 'DefaultController');
Router::get('profile', 'DefaultController');
Router::get('settings', 'DefaultController');

Router::post('login', 'SecurityController');
Router::post('signup', 'SecurityController');
Router::get('logout', 'SecurityController');

Router::post('createConcert', 'ConcertController');

Router::run($path);