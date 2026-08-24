<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'lila.setiyani.krw@horizon.ac.id';
$user = \App\Models\User::where('email', $email)->first();
if ($user) {
    echo "Lila Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
} else {
    echo "Lila not found\n";
}

$email2 = 'muhammad.jomantara.krw@horizon.ac.id';
$user2 = \App\Models\User::where('email', $email2)->first();
if ($user2) {
    echo "Muhammad Roles: " . $user2->roles->pluck('name')->join(', ') . "\n";
} else {
    echo "Muhammad not found\n";
}
