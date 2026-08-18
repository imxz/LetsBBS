<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->set404Override();
$routes->get('health/live', 'Health::live');
$routes->get('health/ready', 'Health::ready');
$routes->match(['GET', 'POST'], 'install', 'Install::index');
$routes->get('captcha', 'Auth::captcha');
$routes->get('/', 'Forum::index');
$routes->get('node', 'Forum::nodes');
$routes->get('recent/(:num)', 'Forum::recent/$1');
$routes->get('recent', 'Forum::recent/1');
$routes->get('search/(:num)', 'Forum::search/$1');
$routes->get('search', 'Forum::search/1');
$routes->get('topic/(:num)', 'Forum::topic/$1');
$routes->get('node/(:num)/(:num)', 'Forum::node/$1/$2');
$routes->get('node/(:num)', 'Forum::node/$1/1');
$routes->get('member/(:segment)', 'Member::show/$1');
$routes->get('member/(:segment)/topics/(:num)', 'Member::topics/$1/$2');
$routes->get('member/(:segment)/comments/(:num)', 'Member::comments/$1/$2');
$routes->match(['GET', 'POST'], 'register', 'Auth::register');
$routes->match(['GET', 'POST'], 'login', 'Auth::login');
$routes->post('logout', 'Auth::logout', ['filter' => 'session']);
$routes->get('topic/new', 'Topic::create', ['filter' => 'session']);
$routes->post('topic', 'Topic::store', ['filter' => 'session']);
$routes->post('topic/(:num)/comment', 'Topic::comment/$1', ['filter' => 'session']);
$routes->post('topic/(:num)/delete', 'Topic::delete/$1', ['filter' => 'session']);
$routes->post('topic/(:num)/follow', 'Follow::topic/$1', ['filter' => 'session']);
$routes->post('node/(:num)/follow', 'Follow::node/$1', ['filter' => 'session']);
$routes->post('member/(:segment)/follow', 'Follow::member/$1', ['filter' => 'session']);
$routes->post('media/images', 'Media::image', ['filter' => 'session']);
$routes->get('notification/(:num)', 'Notification::index/$1', ['filter' => 'session']);
$routes->get('notification', 'Notification::index/1', ['filter' => 'session']);
$routes->post('notification/read', 'Notification::readAll', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings', 'Member::settings', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings/password', 'Member::password', ['filter' => 'session']);
$routes->post('settings/avatar', 'Member::avatar', ['filter' => 'session']);
$routes->group('admin', ['filter' => 'group:admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin::index');
    $routes->post('node', 'Admin::node');
    $routes->post('topic/(:num)/moderate', 'Admin::moderate/$1');
    $routes->post('comment/(:num)/moderate', 'Admin::moderateComment/$1');
    $routes->post('member/(:num)/mute', 'Admin::mute/$1');
    $routes->post('settings', 'Admin::settings');
});
