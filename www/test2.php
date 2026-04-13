<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Helpers.php';
session();
request();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once __DIR__ . '/app/Core/Database.php';

try {
    $quotation = \App\Models\Quotation::with(['client.user', 'briefing', 'items'])->first();
    if(!$quotation) {
        echo "No quotation found\n";
        exit;
    }
    echo "Found quotation ID " . $quotation->id . "\n";
    $html = \App\Core\View::render('admin.quotations.show', ['quotation' => $quotation]);
    echo "HTML Length: " . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "ERROR CATCHED:\n";
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
