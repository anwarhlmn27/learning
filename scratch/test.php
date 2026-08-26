<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$classes = App\Models\ClassRoom::whereHas('topics')->get();
echo "Classes with topics: " . count($classes) . "\n";
foreach($classes as $c) {
    echo $c->id . " - " . $c->nama_kelas . " (Topics: " . $c->topics()->count() . ")\n";
}

$topics = App\Models\ClassTopic::limit(2)->get();
print_r($topics->toArray());
