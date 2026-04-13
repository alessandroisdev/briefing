<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Helpers.php';
session();
request();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once __DIR__ . '/app/Core/Database.php';

$_POST = [
    'client_id' => 1,
    'briefing_id' => 1,
    'title' => 'test test',
    'descriptions' => ['test item'],
    'quantities' => [2],
    'unit_prices' => ['10,00']
];

try {
    $c = new \App\Controllers\Admin\QuotationController();
    $c->store();
} catch (\Throwable $e) {
    echo "ERROR CATCHED:\n";
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
