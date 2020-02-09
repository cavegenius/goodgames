<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Game;

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
        $this->json('POST', '/games/add', [
            'userId' => 1,
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
        ])
        ->assertJsonStructure([
           // Verify Success Message
            'Status', 'Message'
       ]);
    }
}
