<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tournament;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@turnament.com'],
            [
                'name'     => 'Admin Turnamen',
                'password' => Hash::make('password'),
            ]
        );

        // Create demo tournament
        $tournament = Tournament::create([
            'user_id'           => $user->id,
            'name'              => 'Turnamen Tenis Meja HUT RI ke-80',
            'slug'              => 'turnamen-tenis-meja-hut-ri-ke-80-demo01',
            'description'       => 'Turnamen tahunan dalam rangka memperingati HUT Republik Indonesia ke-80. Diikuti oleh seluruh karyawan.',
            'type'              => 'single_elimination',
            'status'            => 'pending',
            'third_place_match' => true,
            'seeded'            => false,
        ]);

        // Add 8 participants
        $names = [
            'Ahmad Fauzi', 'Budi Santoso', 'Citra Dewi', 'Dian Pratama',
            'Eko Wahyudi', 'Fitri Andini', 'Gilang Ramadhan', 'Hendra Kusuma',
        ];

        foreach ($names as $name) {
            Participant::create([
                'tournament_id' => $tournament->id,
                'name'          => $name,
            ]);
        }

        $this->command->info('Demo data created!');
        $this->command->info('Login: admin@turnament.com / password');
    }
}
