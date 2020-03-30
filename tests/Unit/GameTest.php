<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Game;

class GameTest extends TestCase {
    use RefreshDatabase;

    public function testSearchTest() {
        $this->json('POST', '/games/search', ['name' => 'horizon zero dawn'])
             ->assertJsonStructure([
                // Verify all elements have an id and name
                'Games' => [
                    '*' => [
                        'id', 'name'
                    ]
                ]
            ]);
    }

    public function testAddGameFullTest() {
        $user = $this->createUserAndLogin();

        $data = [
            'name' => 'horizon zero dawn',
            'status' => 'None',
            'platform' => 'Playstation 4',
            'platformType' => 'Console',
            'favorite' => false,
            'rating' => '0',
            'format' => 'Physical',
            'notes' => 'This is a test Note'
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
            'format' => 'Physical'
        ];

        $response = $this->actingAs($user)->post('/games/add', $data, []);
        $response->assertExactJson(['Status'=>'Success','Message'=>'Game Added Successfully']);
    }

    public function testAddGameMissingRequiredTest() {
        $user = $this->createUserAndLogin();
        $data = [
            'name' => 'horizon zero dawn',
            'status' => 'None',
            'favorite' => false,
            'rating' => '0',
            'format' => 'Physical',
            'notes' => 'This is a test Note'
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
        $response->assertJsonStructure(['Game' => ['name','status','favorite','rating','format','notes']]);
    }

    public function testShowGameNotFoundTest() {
        $user = $this->createUserAndLogin();

        $data = [
            'id' => 100000000,
        ];
        $response = $this->actingAs($user)->post('/games/showOne', $data, []);
        $response->assertJson(['Status'=>'Error','Message'=>'Game Not Found']);
    }

    public function testShowListTest() {
        $user = $this->createUserAndLogin();

        // Add multiple games for the user
        $this->addGameForUser($user);
        $this->addGameForUser($user);
        $this->addGameForUser($user);
        $data = [
            'list' => 'all',
        ];

        $response = $this->actingAs($user)->post('/games/showList', $data, []);
        $response->assertJsonStructure([ 'Games'=> [ '*' => ['name','status','favorite','rating','format','notes']]]);
    }

    public function testShowListNoGamesTest() {
        $user = $this->createUserAndLogin();
        $data = [
            'list' => 'all',
        ];

        $response = $this->actingAs($user)->post('/games/showList', $data, []);
        $response->assertJson(['Status'=>'Error','Message'=>'No Games Found']);
    }

    public function testUpdateGameTest() {
        $user = $this->createUserAndLogin();
        $id = $this->addGameForUser($user);

        $data = [
            'id' => $id,
            'name' => 'new name'
        ];
        $response = $this->actingAs($user)->post('/games/update', $data, []);
        $response->assertJson(['Status' => 'Success', 'Message' => 'Game Updated Successfully']);
        $this->markTestIncomplete('Add assertion to check the game has the new data.');
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
        $response = $this->actingAs($user2)->post('/games/update', $data, []);
        $response->assertJson(['Status' => 'Error', 'Message' => 'Invalid Game']);
    }

    public function testImportCSVSuccessfulTest() {
        $user = $this->createUserAndLogin();
        $stub = __DIR__.'/files/importFile.csv';
        $name = str_random(8).'.csv';
        $path = sys_get_temp_dir().'/'.$name;

        copy($stub, $path);

        $file = new UploadedFile(
            $path,
            $name,
            'text/csv',
            filesize($path),
            null,
            TRUE
        );
        $response = $this->actingAs($user)->call('POST', '/games/importCSV', [], [], ['csvFile' => $file], ['Accept' => 'application/json']);
        $response->assertExactJson(['Status'=>'Success','Message'=>'Games Successfully Imported']);

        // Remove the file
        $uploaded = 'uploads'.DIRECTORY_SEPARATOR.$name;
        @unlink($uploaded);
    }

}
