<?php

/** @var \Bramus\Router\Router $router */

$router->get('/', function() {
    echo \App\Core\View::render('welcome');
});

$router->mount('/cliente', function() use ($router) {
    $router->get('/login', 'App\Controllers\Client\AuthController@loginForm');
    $router->post('/login', 'App\Controllers\Client\AuthController@login');
    
    $router->get('/dashboard', 'App\Controllers\Client\DashboardController@index');
    
    // Client Briefings
    $router->get('/briefings/(\d+)', 'App\Controllers\Client\BriefingController@show');
    $router->post('/briefings/(\d+)/save', 'App\Controllers\Client\BriefingController@save');
});

$router->mount('/admin', function() use ($router) {
    $router->get('/login', 'App\Controllers\Admin\AuthController@loginForm');
    $router->post('/login', 'App\Controllers\Admin\AuthController@login');
    
    $router->get('/dashboard', 'App\Controllers\Admin\DashboardController@index');

    // Admin Clients Routes
    $router->get('/clients', 'App\Controllers\Admin\ClientController@index');
    $router->get('/clients/create', 'App\Controllers\Admin\ClientController@create');
    $router->post('/clients/store', 'App\Controllers\Admin\ClientController@store');
    $router->get('/clients/(\d+)/edit', 'App\Controllers\Admin\ClientController@edit');
    $router->post('/clients/(\d+)/update', 'App\Controllers\Admin\ClientController@update');
    $router->post('/clients/(\d+)/generate-magic-link', 'App\Controllers\Admin\ClientController@generateMagicLink');

    // Admin Briefing Templates Routes
    $router->get('/templates', 'App\Controllers\Admin\BriefingTemplateController@index');
    $router->get('/templates/create', 'App\Controllers\Admin\BriefingTemplateController@create');
    $router->post('/templates/store', 'App\Controllers\Admin\BriefingTemplateController@store');
    $router->get('/templates/(\d+)/edit', 'App\Controllers\Admin\BriefingTemplateController@edit');
    $router->post('/templates/(\d+)/update', 'App\Controllers\Admin\BriefingTemplateController@update');

    // Admin Client Briefings Routes (Projects)
    $router->get('/briefings', 'App\Controllers\Admin\ClientBriefingController@index');
    $router->get('/briefings/create', 'App\Controllers\Admin\ClientBriefingController@create');
    $router->post('/briefings/store', 'App\Controllers\Admin\ClientBriefingController@store');
    $router->get('/briefings/(\d+)', 'App\Controllers\Admin\ClientBriefingController@show');
    $router->post('/briefings/(\d+)/status', 'App\Controllers\Admin\ClientBriefingController@updateStatus');
});
