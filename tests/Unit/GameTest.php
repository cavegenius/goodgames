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
        $user = $this->createUserAndLogin();

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
        $user = $this->createUserAndLogin();

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
        $user = $this->createUserAndLogin();
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

    public function testShowGameTest() {
        $user = $this->createUserAndLogin();
        $id = $this->addGameForUser($user);

        $data = [
            'id' => $id,
        ];
        $response = $this->actingAs($user)->post('/games/showOne', $data, []);
        $response->assertJsonStructure(['name','igdbId','status','favorite','rating','format','notes','owned','wishlist','backlog']);
    }

    public function testShowGameNotFoundTest() {
        $user = $this->createUserAndLogin();

        $data = [
            'id' => 100000000,
        ];
        $response = $this->actingAs($user)->post('/games/showOne', $data, []);
        $response->assertJson(['Status'=>'Error','Message'=>'Game Not Found']);
    }

    public function testShowAllTest() {
        $user = $this->createUserAndLogin();

        // Add multiple games for the user
        $this->addGameForUser($user);
        $this->addGameForUser($user);
        $this->addGameForUser($user);

        $response = $this->actingAs($user)->post('/games/showAll', [], []);
        $response->assertJsonStructure(['*' => ['name','igdbId','status','favorite','rating','format','notes','owned','wishlist','backlog']]);
    }

    public function testShowAllNoGamesTest() {
        $user = $this->createUserAndLogin();

        $response = $this->actingAs($user)->post('/games/showAll', [], []);
        $response->assertJson(['Status'=>'Error','Message'=>'No Games Found']);
    }

    public function testUpdateGameTest() {
        $user = $this->createUserAndLogin();
        $id = $this->addGameForUser($user);

        $data = [
            'id' => $id,
            'name' => 'new name'
        ];
        $response = $this->actingAs($user)->post('/games/edit', $data, []);
        $response->assertJson(['Status' => 'Success', 'Message' => 'Game Updated Successfully']);
    }

    public function testUpdateGameInavalidUserTest() {
        $user = $this->createUserAndLogin();
        $id = $this->addGameForUser($user);
        $response = $this->get('/logout');
        $user2 = $this->createUserAndLogin();
        $data = [
            'id' => $id,
            'name' => 'new name'
        ];
        $response = $this->actingAs($user2)->post('/games/edit', $data, []);
        $response->assertJson(['Status' => 'Error', 'Message' => 'Invalid Game']);
    }

}
