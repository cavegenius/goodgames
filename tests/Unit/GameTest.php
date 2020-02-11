<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Game;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GameTest extends TestCase {
    public function testSearchTest() {
        $this->json('POST', '/games/search', ['name' => 'horizon zero dawn'])
             ->assertJsonStructure([
                // Verify all elements have an id and name
                '*' => [
                    'id', 'name'
                ]
            ]);
    }

    public function testAddGameTest() {
        $user = User::find(1);

        $data = [
            'name' => 'horizon zero dawn',
            'igdbId' => 0,
            'status' => 'None',
            'platform' => 'Playstation 4',
            'platformType' => 'Console',
            'favorite' => 'No',
            'rating' => '0',
            'format' => 'Physical',
            'notes' => 'This is a test Note',
            'owned' => 1,
            'wishlist' => 0,
            'backlog' => 0
        ];

        $response = $this->actingAs($user)->post('/games/add', $data, []);
        $response->assertJsonStructure([
            'Status', 
            'Message'
       ]);
    }
}

