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
$routes->get('latest/(:num)', 'Forum::latest/$1');
$routes->get('latest', 'Forum::latest/1');
$routes->get('search/(:num)', 'Forum::search/$1');
$routes->get('search', 'Forum::search/1');
$routes->get('topic/show/(:segment)', 'Forum::show/$1');
$routes->get('topic/(:num)', 'Forum::topic/$1');
$routes->get('node/follow/(:num)', 'Legacy::nodeAction/$1');
$routes->get('node/unfollow/(:num)', 'Legacy::nodeAction/$1');
$routes->get('node/(:num)/(:num)', 'Forum::legacyNode/$1/$2');
$routes->get('node/(:num)', 'Forum::legacyNode/$1/1');
$routes->get('node/(:segment)/(:num)', 'Forum::node/$1/$2');
$routes->get('node/(:segment)', 'Forum::node/$1/1');
$routes->get('member/(:segment)', 'Member::show/$1');
$routes->get('member/(:segment)/topics/(:num)', 'Member::topics/$1/$2');
$routes->get('member/(:segment)/comments/(:num)', 'Member::comments/$1/$2');
$routes->get('member/(:segment)/topic/(:num)', 'Member::topics/$1/$2');
$routes->get('member/(:segment)/comment/(:num)', 'Member::comments/$1/$2');
$routes->match(['GET', 'POST'], 'register', 'Auth::register');
$routes->match(['GET', 'POST'], 'reg', 'Auth::register');
$routes->match(['GET', 'POST'], 'login', 'Auth::login', ['as' => 'login']);
$routes->get('login/magic-link', 'Recovery::loginView', [
    'as' => 'magic-link',
]);
$routes->post('login/magic-link', 'Recovery::loginAction', [
    'filter' => 'auth-rates',
]);
$routes->get('login/verify-magic-link', 'Recovery::verify', [
    'as' => 'verify-magic-link',
]);
$routes->post('logout', 'Auth::logout', ['filter' => 'session']);
$routes->get('topic/new', 'Topic::create', ['filter' => 'session']);
$routes->get('topic/add', 'Topic::create', ['filter' => 'session']);
$routes->post('topic', 'Topic::store', ['filter' => 'session']);
$routes->post('topic/(:num)/comment', 'Topic::comment/$1', ['filter' => 'session']);
$routes->post('topic/(:num)/delete', 'Topic::delete/$1', ['filter' => 'session']);
$routes->post('topic/(:num)/follow', 'Follow::topic/$1', ['filter' => 'session']);
$routes->post('node/(:num)/follow', 'Follow::node/$1', ['filter' => 'session']);
$routes->post('member/(:segment)/follow', 'Follow::member/$1', ['filter' => 'session']);
$routes->get('topic/follow/(:num)', 'Legacy::topicAction/$1');
$routes->get('topic/unfollow/(:num)', 'Legacy::topicAction/$1');
$routes->get('member/follow/(:num)', 'Legacy::memberAction/$1');
$routes->get('member/unfollow/(:num)', 'Legacy::memberAction/$1');
$routes->post('media/images', 'Media::image', ['filter' => 'session']);
$routes->get('notification/(:num)', 'Notification::index/$1', ['filter' => 'session']);
$routes->get('notification', 'Notification::index/1', ['filter' => 'session']);
$routes->post('notification/read', 'Notification::readAll', ['filter' => 'session']);
$routes->get('my/content', 'Workspace::content/1', ['filter' => 'session']);
$routes->get('my/content/(:num)', 'Workspace::content/$1', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'my/(:segment)/(:num)/edit', 'Workspace::edit/$1/$2', ['filter' => 'session']);
$routes->get('my/subscriptions', 'Workspace::subscriptions/1', ['filter' => 'session']);
$routes->get('my/subscriptions/(:num)', 'Workspace::subscriptions/$1', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings/notifications', 'Workspace::preferences', ['filter' => 'session']);
$routes->post('topic/(:num)/report', 'Workspace::report/$1', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings', 'Member::settings', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings/profile', 'Member::settings', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings/avatar', 'Member::avatar', ['filter' => 'session']);
$routes->match(['GET', 'POST'], 'settings/password', 'Member::password', ['filter' => 'session']);
$routes->group('admin', ['filter' => 'group:admin'], static function (RouteCollection $routes): void {
    $routes->get('comment', 'Admin::comments/1');
    $routes->get('comment/page/(:num)', 'Admin::comments/$1');
    $routes->post('topic/(:num)/flags', 'Admin::topicFlags/$1');
    $routes->get('logs', 'Admin::logs/1');
    $routes->get('logs/(:num)', 'Admin::logs/$1');
    $routes->get('reports', 'Admin::reports/1');
    $routes->get('reports/(:num)', 'Admin::reports/$1');
    $routes->post('report/(:num)/resolve', 'Admin::resolveReport/$1');
    $routes->get('/', 'Admin::index');
    $routes->get('topic', 'Admin::topics/1');
    $routes->get('topic/page/(:num)', 'Admin::topics/$1');
    $routes->get('topic/verify', 'Admin::topicVerify/1');
    $routes->get('topic/verify/(:num)', 'Admin::topicVerify/$1');
    $routes->match(['GET', 'POST'], 'topic/(:num)/edit', 'Admin::topicEdit/$1');
    $routes->get('topic/edit/(:num)', 'Legacy::adminTopic/$1');
    $routes->post('topic/(:num)/moderate', 'Admin::moderate/$1');
    $routes->post('comment/(:num)/moderate', 'Admin::moderateComment/$1');
    $routes->get('user', 'Admin::users/1');
    $routes->get('user/page/(:num)', 'Admin::users/$1');
    $routes->get('user/banned', 'Admin::bannedUsers/1');
    $routes->get('user/banned/(:num)', 'Admin::bannedUsers/$1');
    $routes->match(['GET', 'POST'], 'user/(:num)/edit', 'Admin::userEdit/$1');
    $routes->get('user/edit/(:num)', 'Legacy::adminUser/$1');
    $routes->post('user/(:num)/mute', 'Admin::mute/$1');
    $routes->get('node', 'Admin::nodes');
    $routes->match(['GET', 'POST'], 'node/add', 'Admin::nodeCreate');
    $routes->match(['GET', 'POST'], 'node/(:num)/edit', 'Admin::nodeEdit/$1');
    $routes->get('node/edit/(:num)', 'Legacy::adminNode/$1');
    $routes->match(['GET', 'POST'], 'settings/site', 'Admin::settings');
    $routes->match(['GET', 'POST'], 'settings/verify', 'Admin::verifySettings');
});
