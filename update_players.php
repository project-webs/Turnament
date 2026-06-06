<?php
use App\Models\Player;

$femaleNames = [
    'Anna Rose',
    'Asih Nariastuti',
    'Avi',
    'Ellyanli',
    'Evi',
    'Heni Rusmanto',
    'Iis H.',
    'Lina W.',
    'Marni Darmawan',
    'Mustangimah',
    'Nada Marnada',
    'Patlina',
    'Pudji Xiexie',
    'Rosika K.',
    'Shofi',
    'Widy',
    'Wiwik Soeyatno',
    'Yolan M.',
    'Yusri Heni'
];

$players = Player::all();
$updated = 0;

foreach ($players as $player) {
    $player->itr_rating = 500;
    
    if (in_array($player->name, $femaleNames)) {
        $player->gender = 'Perempuan';
    } else {
        $player->gender = 'Laki-laki';
    }
    
    $player->save();
    $updated++;
}

echo "Updated $updated players.\n";
