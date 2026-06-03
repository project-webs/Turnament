<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

echo "1. Membuat Turnamen Test...\n";
$tournament = $user->tournaments()->create([
    'name' => 'Turnamen Simulasi ITR',
    'type' => 'single_elimination',
    'status' => 'pending'
]);

echo "2. Mendaftarkan 4 Pemain dari Master Data...\n";
$players = $user->players()->take(4)->get();
foreach($players as $p) {
    $tournament->participants()->create([
        'player_id' => $p->id,
        'name' => $p->name
    ]);
}

echo "3. Generate Bracket (Mulai Turnamen)...\n";
$service = app(\App\Services\BracketService::class);
$service->generate($tournament);
echo "Turnamen status: " . $tournament->fresh()->status . "\n";

echo "4. Mensimulasikan Hasil Pertandingan...\n";
$matches = $tournament->matches()->get();
foreach($matches as $m) {
    if($m->participant1_id && $m->participant2_id && !$m->is_bye) {
        // Player 1 wins
        $m->update([
            'score1' => 3,
            'score2' => 1,
            'winner_id' => $m->participant1_id,
            'status' => 'finished'
        ]);
        echo "Match " . $m->id . " selesai. Pemenang: " . $m->participant1->name . "\n";
    }
}

echo "5. Memanggil Fitur Kalkulasi ITR...\n";
$controller = new \App\Http\Controllers\PlayerController();
// Fake request
$controller->calculateRating();

echo "6. Mengecek ITR Rating Terbaru:\n";
foreach($players as $p) {
    echo "- " . $p->name . " (ITR Baru: " . $p->fresh()->itr_rating . ")\n";
}

echo "7. Membersihkan Data Test...\n";
$tournament->delete();
echo "Selesai!\n";
