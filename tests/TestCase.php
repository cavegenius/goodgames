<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createUserAndLogin() {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        return $user;
    }

    public function addGameForUser($user) {
        $game = factory(Game::class)->create([
            'userId' => $user->id
        ]);

        return $game->id;
    }
}
