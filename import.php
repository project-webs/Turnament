<?php
use App\Models\Player;
use App\Models\Iuran;
use App\Models\User;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    return;
}

$data = [
    ['name' => 'Adi Noer S.', 'payments' => []],
    ['name' => 'Agus C.', 'payments' => ['Juni' => 25000, 'Juli' => 25000]],
    ['name' => 'Alaudin', 'payments' => []],
    ['name' => 'Anna Rose', 'payments' => []],
    ['name' => 'Andi Sofrany', 'payments' => []],
    ['name' => 'Aphin Christanto S.', 'payments' => []],
    ['name' => 'Arko', 'payments' => []],
    ['name' => 'Asih Nariastuti', 'payments' => []],
    ['name' => 'Avi', 'payments' => []],
    ['name' => 'Aziz Azmi', 'payments' => []],
    ['name' => 'Bambang P. Icon', 'payments' => []],
    ['name' => 'Barakah A.', 'payments' => []],
    ['name' => 'Boybul', 'payments' => []],
    ['name' => 'Dwikantun', 'payments' => []],
    ['name' => 'Dunes Sukoco', 'payments' => ['Juni' => 25000, 'Juli' => 25000, 'Agt' => 25000, 'Sep' => 25000]],
    ['name' => 'Ellyanli', 'payments' => []],
    ['name' => 'Evi', 'payments' => []],
    ['name' => 'Fahrizal', 'payments' => []],
    ['name' => 'Giarno', 'payments' => []],
    ['name' => 'Hadi', 'payments' => []],
    ['name' => 'Hartono S.', 'payments' => []],
    ['name' => 'Heni Rusmanto', 'payments' => []],
    ['name' => 'Hery Sairan', 'payments' => []],
    ['name' => 'I Ketut P.', 'payments' => []],
    ['name' => 'Iis H.', 'payments' => []],
    ['name' => 'Ikhsan VDS', 'payments' => []],
    ['name' => 'Irul', 'payments' => []],
    ['name' => 'Ismu H.', 'payments' => ['Juni' => 25000, 'Juli' => 25000]],
    ['name' => 'Iyus Suwanda', 'payments' => []],
    ['name' => 'Joko Sumanto', 'payments' => []],
    ['name' => 'Kussigit S.', 'payments' => []],
    ['name' => 'Lina W.', 'payments' => []],
    ['name' => 'M. Zen', 'payments' => []],
    ['name' => 'M. Ridho', 'payments' => ['Juni' => 25000, 'Juli' => 25000]],
    ['name' => 'Marni Darmawan', 'payments' => []],
    ['name' => 'Marsono', 'payments' => ['Juni' => 25000, 'Juli' => 25000, 'Agt' => 25000, 'Sep' => 25000, 'Okt' => 25000]],
    ['name' => 'Maulana', 'payments' => []],
    ['name' => 'Mustangimah', 'payments' => ['Juni' => 25000, 'Juli' => 25000, 'Agt' => 25000, 'Sep' => 25000, 'Okt' => 25000]],
    ['name' => 'Nada Marnada', 'payments' => ['SP' => 50000, 'Juni' => 25000, 'Juli' => 25000]],
    ['name' => 'Nazwir', 'payments' => []],
    ['name' => 'Patlina', 'payments' => []],
    ['name' => 'Pudji Xiexie', 'payments' => []],
    ['name' => 'Rosika K.', 'payments' => []],
    ['name' => 'Rudy Hartono', 'payments' => []],
    ['name' => 'Shofi', 'payments' => []],
    ['name' => 'Sjofjan Salim', 'payments' => []],
    ['name' => 'Sugeng W.', 'payments' => []],
    ['name' => 'Sukoco R.', 'payments' => ['SP' => 50000, 'Juni' => 25000, 'Juli' => 25000]],
    ['name' => 'Supandi', 'payments' => []],
    ['name' => 'Suryadi', 'payments' => []],
    ['name' => 'Teguh Iman', 'payments' => ['Juni' => 25000, 'Juli' => 25000, 'Agt' => 25000, 'Sep' => 25000]],
    ['name' => 'Wahyu KL', 'payments' => []],
    ['name' => 'Widy', 'payments' => []],
    ['name' => 'Wiwik Soeyatno', 'payments' => []],
    ['name' => 'Yolan M.', 'payments' => ['Juni' => 25000, 'Juli' => 25000, 'Agt' => 25000, 'Sep' => 25000]],
    ['name' => 'Yono', 'payments' => []],
    ['name' => 'Yusri Heni', 'payments' => []],
];

$months = [
    'SP' => null, // Special case
    'Juni' => '2026-06-01',
    'Juli' => '2026-07-01',
    'Agt'  => '2026-08-01',
    'Sep'  => '2026-09-01',
    'Okt'  => '2026-10-01',
    'Nov'  => '2026-11-01',
    'Des'  => '2026-12-01',
];

$countPlayers = 0;
$countIurans = 0;

foreach ($data as $item) {
    // Find or create player
    $player = Player::where('name', $item['name'])->where('user_id', $user->id)->first();
    if (!$player) {
        $player = new Player();
        $player->name = $item['name'];
        $player->user_id = $user->id;
        // Try to guess gender roughly for existing female names, else leave null or Laki-laki
        // For simplicity, we just save them. We'll leave gender null, user can update later.
        $player->save();
        $countPlayers++;
    }

    foreach ($item['payments'] as $month => $amount) {
        $period = $months[$month] ?? '2026-01-01'; // Default if not found
        $notes = '';
        if ($month === 'SP') {
            $period = '2026-05-01'; // Give it a month before June
            $notes = 'Sumbangan Pokok';
        } else {
            $notes = "Iuran Bulan $month";
        }
        
        // check if iuran already exists
        $exists = Iuran::where('player_id', $player->id)
            ->where('period', $period)
            ->where('notes', $notes)
            ->first();
            
        if (!$exists) {
            Iuran::create([
                'player_id' => $player->id,
                'tanggal' => date('Y-m-d'), // Set to today's date for record entry
                'period' => $period,
                'amount' => $amount,
                'notes' => $notes
            ]);
            $countIurans++;
        }
    }
}

echo "Added/Found players. New players: $countPlayers\n";
echo "Added new iurans: $countIurans\n";
