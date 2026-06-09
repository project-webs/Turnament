<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;

class PlayerImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'A. Sidieq',
            'Adi Noer S.',
            'Adhi Susanto',
            'Agus C.',
            'Alauddin L.',
            'Anna Rose',
            'Andi Sofrany',
            'Aphin Christanto S.',
            'Arko',
            'Asih Nariastuti',
            'Avi',
            'Aziz Azmi',
            'Bambang P. Icon',
            'Barokah A.',
            'Boybul',
            'David K.',
            'Dwikantun',
            'Dunes Sukoco',
            'Ellyanli',
            'Evi',
            'Fahrizal',
            'Giarno',
            'Hadi',
            'Heni Rusmanto',
            'Hery Sairan',
            'I Ketut P.',
            'Iis Haryati',
            'Ikhsan VDS',
            'Ilham W.',
            'Irbah Erna',
            'Irul',
            'Ismu H.',
            'Iyus Suwanda',
            'Joko Sumanto',
            'Kussigit S.',
            'Lina W.',
            'M. Jen',
            'M. Ridho',
            'Marni Darmawan',
            'Marsono',
            'Masrukan',
            'Maulana',
            'Mustangimah',
            'Nada Marnada',
            'Nazwir',
            'Patlina',
            'Pudji Xiexie',
            'Rienie Wisnu',
            'Rosika K.',
            'Rudy Hartono',
            'Shofi',
            'Sjofjan Salim',
            'Sugeng W.',
            'Suhartono S.',
            'Sukoco R.',
            'Supandi',
            'Suryadi',
            'Teguh Iman',
            'Wahyu KL',
            'Widy',
            'Wisnu A.',
            'Wiwik Soeyatno',
            'Yolan M.',
            'Yono',
            'Yusri Heni'
        ];

        $userId = 1;

        foreach ($names as $name) {
            Player::firstOrCreate([
                'user_id' => $userId,
                'name' => trim($name),
            ]);
        }
    }
}
