<?php

/** @var \Bramus\Router\Router $router */

$router->get('/sse/stream', 'App\Controllers\SSEController@stream');

$router->get('/', function() {
    echo \App\Core\View::render('welcome');
});

$router->mount('/cliente', function() use ($router) {
    $router->get('/login', 'App\Controllers\Client\AuthController@loginForm');
    $router->post('/login', 'App\Controllers\Client\AuthController@login');
    $router->post('/login/magic-link', 'App\Controllers\Client\AuthController@requestMagicLink');
    
    $router->get('/dashboard', 'App\Controllers\Client\DashboardController@index');
    
    // Configurações do Perfil e Senha
    $router->get('/perfil', 'App\Controllers\Client\ProfileController@index');
    $router->post('/perfil/senha', 'App\Controllers\Client\ProfileController@updatePassword');
    
    // Client Briefings
    $router->get('/briefings/(\d+)', 'App\Controllers\Client\BriefingController@show');
    $router->post('/briefings/(\d+)/save', 'App\Controllers\Client\BriefingController@save');

    // Client Tickets (Support)
    $router->get('/suporte', 'App\Controllers\Client\TicketController@index');
    $router->get('/suporte/novo', 'App\Controllers\Client\TicketController@create');
    $router->post('/suporte/store', 'App\Controllers\Client\TicketController@store');
    $router->get('/suporte/(\d+)', 'App\Controllers\Client\TicketController@show');
    $router->post('/suporte/(\d+)/reply', 'App\Controllers\Client\TicketController@reply');
});

$router->mount('/admin', function() use ($router) {
    $router->get('/login', 'App\Controllers\Admin\AuthController@loginForm');
    $router->post('/login', 'App\Controllers\Admin\AuthController@login');
    
    $router->get('/dashboard', 'App\Controllers\Admin\DashboardController@index');
    
    // Notifications
    $router->get('/notifications', 'App\Controllers\Admin\NotificationController@index');
    $router->get('/notifications/(\d+)/read', 'App\Controllers\Admin\NotificationController@markAsRead');
    $router->get('/notifications/read-all', 'App\Controllers\Admin\NotificationController@markAllAsRead');

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

    // Admin Tickets (Support)
    $router->get('/tickets', 'App\Controllers\Admin\TicketController@index');
    $router->get('/tickets/(\d+)', 'App\Controllers\Admin\TicketController@show');
    $router->post('/tickets/(\d+)/reply', 'App\Controllers\Admin\TicketController@reply');
    $router->post('/tickets/(\d+)/status', 'App\Controllers\Admin\TicketController@updateStatus');

    // Admin Email Settings & Queue

    $router->get('/settings/email', 'App\Controllers\Admin\EmailSettingsController@index');
    $router->post('/settings/email', 'App\Controllers\Admin\EmailSettingsController@save');
    $router->get('/queue', 'App\Controllers\Admin\QueueManagerController@index');
    $router->post('/queue/(\d+)/retry', 'App\Controllers\Admin\QueueManagerController@retry');
});
