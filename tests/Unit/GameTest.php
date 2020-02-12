<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Game;

class GameTest extends TestCase {
    use RefreshDatabase;

    public function testSearchTest() {
        $this->json('POST', '/games/search', ['name' => 'horizon zero dawn'])
             ->assertJsonStructure([
                // Verify all elements have an id and name
                '*' => [
                    'id', 'name'
                ]
            ]);
    }

    public function testAddGameFullTest() {
        $user=$this->createUserAndLogin();

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
        $response->assertExactJson(['Status'=>'Success','Message'=>'Game Added Successfully']);
    }

    public function testAddGamepartialTest() {
        $user = $user=$this->createUserAndLogin();

        $data = [
            'name' => 'horizon zero dawn',
            'platform' => 'Playstation 4',
            'platformType' => 'Console',
            'format' => 'Physical',
            'owned' => 1,
            'wishlist' => 0,
            'backlog' => 0
        ];

        $response = $this->actingAs($user)->post('/games/add', $data, []);
        $response->assertExactJson(['Status'=>'Success','Message'=>'Game Added Successfully']);
    }

    public function testAddGameMissingRequiredTest() {
        $user = $user=$this->createUserAndLogin();
        $data = [
            'name' => 'horizon zero dawn',
            'igdbId' => 0,
            'status' => 'None',
            'favorite' => 'No',
            'rating' => '0',
            'format' => 'Physical',
            'notes' => 'This is a test Note',
            'owned' => 1,
            'wishlist' => 0,
            'backlog' => 0
        ];
        $response = $this->actingAs($user)->post('/games/add', $data, []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'platform' => 'The platform field is required.',
            'platformType' => 'The platform type field is required.'
        ]);
    }
}

