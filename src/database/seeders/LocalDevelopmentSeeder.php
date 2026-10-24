<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LocalDevelopmentSeeder extends Seeder
{
    public const EMAIL = 'demo@goodgames.test';

    /** A small, deterministic library suitable for UI and API development. */
    public const GAMES = [
        ['name' => 'Halo: The Master Chief Collection', 'status' => 'Completed', 'platform' => 'Windows Store', 'platformType' => 'PC', 'favorite' => true, 'format' => 'Digital', 'genre' => 'Shooter', 'rating' => '5'],
        ['name' => 'Final Fantasy VII Remake', 'status' => 'Completed', 'platform' => 'PlayStation 4', 'platformType' => 'Console', 'favorite' => false, 'format' => 'Physical', 'genre' => 'Role-playing', 'rating' => '4'],
        ['name' => 'Ace Combat 04: Shattered Skies', 'status' => 'Might Play', 'platform' => 'PlayStation 2', 'platformType' => 'Console', 'favorite' => false, 'format' => 'Physical', 'genre' => 'Shooter', 'rating' => '0'],
        ['name' => 'Age of Wonders III', 'status' => 'Backlog', 'platform' => 'Steam', 'platformType' => 'PC', 'favorite' => false, 'format' => 'Digital', 'genre' => 'Strategy', 'rating' => '0'],
        ['name' => 'Apex Legends', 'status' => 'In Progress', 'platform' => 'Origin', 'platformType' => 'PC', 'favorite' => false, 'format' => 'Digital', 'genre' => 'Shooter', 'rating' => '0'],
        ['name' => 'Burnout Revenge', 'status' => 'Replayable', 'platform' => 'PlayStation 2', 'platformType' => 'Console', 'favorite' => true, 'format' => 'Physical', 'genre' => 'Other', 'rating' => '4'],
        ['name' => 'DiRT Rally', 'status' => 'Paused', 'platform' => 'Steam', 'platformType' => 'PC', 'favorite' => false, 'format' => 'Digital', 'genre' => 'Simulator', 'rating' => '5'],
        ['name' => 'Crash Bandicoot 4', 'status' => 'Wishlist', 'platform' => 'PlayStation 4', 'platformType' => 'Console', 'favorite' => false, 'format' => 'Physical', 'genre' => 'Adventure', 'rating' => '0'],
        ['name' => 'Hearthstone', 'status' => 'Abandoned', 'platform' => 'Battle.Net', 'platformType' => 'PC', 'favorite' => false, 'format' => 'Digital', 'genre' => 'Strategy', 'rating' => '0'],
        ['name' => 'Mario Kart 8', 'status' => 'Completed', 'platform' => 'Nintendo Switch', 'platformType' => 'Console', 'favorite' => true, 'format' => 'Physical', 'genre' => 'Other', 'rating' => '5'],
        ['name' => 'Portal 2', 'status' => 'Backlog', 'platform' => 'Steam', 'platformType' => 'PC', 'favorite' => false, 'format' => 'Digital', 'genre' => 'Adventure', 'rating' => '0'],
        ['name' => 'Rocket League', 'status' => 'Replayable', 'platform' => 'Steam', 'platformType' => 'PC', 'favorite' => true, 'format' => 'Digital', 'genre' => 'Other', 'rating' => '5'],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::updateOrCreate(
                ['email' => self::EMAIL],
                ['name' => 'Local Demo', 'password' => Hash::make('password'), 'email_verified_at' => now()]
            );

            DB::table('filters')->where('userId', $user->id)->delete();
            DB::table('games')->where('userId', $user->id)->delete();

            $now = now();
            $games = [];
            foreach (self::GAMES as $rank => $game) {
                $games[] = array_merge([
                    'userId' => $user->id,
                    'rank' => 0,
                    'notes' => '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $game);
            }
            DB::table('games')->insert($games);

            DB::table('filters')->insert([
                ['userId' => $user->id, 'name' => 'Steam', 'filter' => '{"platform":["Steam"]}', 'searchTerm' => '', 'created_at' => $now, 'updated_at' => $now],
                ['userId' => $user->id, 'name' => 'PlayStation 4', 'filter' => '{"platform":["PlayStation 4"]}', 'searchTerm' => '', 'created_at' => $now, 'updated_at' => $now],
            ]);
        });
    }
}
