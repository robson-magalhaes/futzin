<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Payout;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@futzin.com'],
            [
                'name' => 'Admin Futzin',
                'password' => Hash::make('password'),
                'phone' => '11999999999',
                'position' => 'Goleiro',
            ]
        );

        Subscription::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'plan' => 'premium',
                'starts_at' => now(),
                'ends_at' => now()->addMonths(12),
                'price' => 59.90,
                'status' => 'active',
            ]
        );

        $positions = ['Goleiro', 'Zagueiro', 'Lateral', 'Meio-campo', 'Atacante'];
        $players = collect(range(1, 10))->map(function ($index) use ($positions) {
            return User::updateOrCreate(
                ['email' => "player{$index}@futzin.com"],
                [
                    'name' => "Jogador {$index}",
                    'password' => Hash::make('password'),
                    'phone' => '1199000000'.$index,
                    'position' => $positions[($index - 1) % count($positions)],
                ]
            );
        });

        foreach ($players as $player) {
            Subscription::updateOrCreate(
                ['user_id' => $player->id],
                [
                    'plan' => 'basic',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths(1),
                    'price' => 29.90,
                    'status' => 'active',
                ]
            );
        }

        $group = Group::updateOrCreate(
            ['user_id' => $admin->id, 'name' => 'Futebol da Terça'],
            [
                'description' => 'Partidas toda terça à noite no parque',
                'monthly_fee' => 50.00,
                'status' => 'active',
            ]
        );

        $admin->groups()->syncWithoutDetaching([
            $group->id => ['role' => 'admin'],
        ]);

        $players->each(function ($player) use ($group) {
            $group->members()->syncWithoutDetaching([
                $player->id => ['role' => 'player'],
            ]);
        });

        foreach ($players as $player) {
            Payout::updateOrCreate(
                [
                    'user_id' => $player->id,
                    'group_id' => $group->id,
                    'status' => 'pending',
                ],
                [
                    'due_date' => now()->addDays(7),
                    'amount' => 50.00,
                ]
            );
        }
    }
}
