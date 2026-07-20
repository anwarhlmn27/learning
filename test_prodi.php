<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$prodis = App\Models\Prodi::with('fakultas.univ')->get();
$mapped = $prodis->map(function($p) {
    return [
        'id' => $p->id,
        'name' => $p->nama_prodi . ' (' . ($p->fakultas->nama_fakultas ?? '-') . ')'
    ];
})->values()->toArray();

echo json_encode($mapped);
