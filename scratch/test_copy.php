<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sourceClass = App\Models\ClassRoom::whereHas('topics')->first();
if (!$sourceClass) {
    echo "No source class found with topics.\n";
    exit;
}
echo "Source class: " . $sourceClass->id . " - " . $sourceClass->nama_kelas . "\n";
echo "Topics count: " . $sourceClass->topics()->count() . "\n";

$targetClass = App\Models\ClassRoom::where('id', '!=', $sourceClass->id)->first();
if (!$targetClass) {
    echo "No target class found.\n";
    exit;
}
echo "Target class: " . $targetClass->id . " - " . $targetClass->nama_kelas . "\n";

$controller = new App\Http\Controllers\ClassRoomController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('performDeepCopyTopics');
$method->setAccessible(true);

$copiedCount = $method->invokeArgs($controller, [$sourceClass, $targetClass]);
echo "Copied count: " . $copiedCount . "\n";
echo "Target topics count now: " . $targetClass->topics()->count() . "\n";

$topics = $targetClass->topics()->get();
foreach ($topics as $t) {
    echo "Topic: " . $t->title . " (Session: " . $t->session_number . ", Type: " . $t->type . ", Content ID: " . $t->content_id . ")\n";
}
