<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Helpers.php';
session();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require_once __DIR__ . '/app/Core/Database.php';

try {
    $data = [
        'client_id' => 1,
        'briefing_id' => 1,
        'title' => 'test test',
        'descriptions' => ['test item'],
        'quantities' => [2],
        'unit_prices' => ['1 000,50']
    ];

    $quotation = \App\Models\Quotation::create([
        'client_id' => $data['client_id'],
        'briefing_id' => !empty($data['briefing_id']) ? $data['briefing_id'] : null,
        'title' => $data['title'],
        'status' => 'draft',
        'valid_until' => date('Y-m-d H:i:s', strtotime('+15 days')), 
        'total_amount' => 0 
    ]);

    $grandTotal = 0;
    foreach ($data['descriptions'] as $index => $desc) {
        $quantity = isset($data['quantities'][$index]) ? (int)$data['quantities'][$index] : 1;
        $unitPriceRaw = $data['unit_prices'][$index] ?? '0';
        $unitPrice = floatval(str_replace(['.', ','], ['', '.'], str_replace(['R$', ' '], '', $unitPriceRaw)));
        $totalLined = $quantity * $unitPrice;

        \App\Models\QuotationItem::create([
            'quotation_id' => $quotation->id,
            'description' => trim($desc),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $totalLined
        ]);

        $grandTotal += $totalLined;
    }

    $quotation->update(['total_amount' => $grandTotal]);
    echo "SUCCESS: " . $quotation->id . "\n";

} catch (\Throwable $e) {
    echo "ERROR CATCHED:\n";
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
