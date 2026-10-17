<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pipeline = new App\Services\NLP\SentimentPipeline();

$res = $pipeline->predictSingle('aplikasi jelek banget pesanan dibatalkan sepihak driver tidak mau antar rugi uang saya');
echo "=== Real Negative Review in PHP ===" . PHP_EOL;
print_r($res);
