<?php

namespace Tests\Feature;

use App\Game;
use App\Steam;
use App\User;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GameLibraryTest extends TestCase
{
    use RefreshDatabase;

    private function gameData(array $overrides = []): array
    {
        return array_merge([
            'rank' => 0,
            'name' => 'Horizon Zero Dawn',
            'status' => 'None',
            'platform' => 'PlayStation 4',
            'platformType' => 'Console',
            'favorite' => false,
            'rating' => '0',
            'format' => 'Physical',
            'genre' => 'Adventure',
            'notes' => '',
        ], $overrides);
    }

    public function test_game_library_requires_a_verified_user(): void
    {
        $this->get('/games')->assertRedirect('/login');

        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)->get('/games')->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_open_the_library(): void
    {
        $this->actingAs(User::factory()->create())->get('/games')
            ->assertOk()
            ->assertViewIs('games');
    }

    public function test_game_creation_validates_required_fields_and_persists_game(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/games/add', $this->gameData())
            ->assertExactJson(['Status' => 'Success', 'Message' => 'Game Added Successfully']);

        $this->assertDatabaseHas('games', [
            'userId' => $user->id,
            'name' => 'Horizon Zero Dawn',
            'platform' => 'PlayStation 4',
            'rank' => 0,
        ]);

        $this->actingAs($user)->postJson('/games/add', ['name' => 'Missing fields'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['platform', 'platformType', 'format', 'rank']);
    }

    public function test_duplicate_game_on_same_platform_is_rejected_but_another_platform_is_allowed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/games/add', $this->gameData())->assertOk();

        $this->postJson('/games/add', $this->gameData())
            ->assertExactJson(['Status' => 'Error', 'Message' => 'This game already exists for this platform. Please update your existing game.']);

        $this->postJson('/games/add', $this->gameData(['platform' => 'PC']))
            ->assertExactJson(['Status' => 'Success', 'Message' => 'Game Added Successfully']);
        $this->assertDatabaseCount('games', 2);
    }

    public function test_game_detail_update_and_delete_enforce_ownership(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $game = Game::factory()->create(['userId' => $owner->id, 'name' => 'Original']);

        $this->actingAs($other)->postJson('/games/showOne', ['id' => $game->id])
            ->assertJson(['Status' => 'Error', 'Message' => 'Game Not Found']);
        $this->postJson('/games/update', ['id' => $game->id, 'name' => 'Changed'])
            ->assertJson(['Status' => 'Error', 'Message' => 'Invalid Game']);
        $this->postJson('/games/delete', ['id' => $game->id])
            ->assertJson(['Status' => 'Error', 'Message' => 'Invalid Game']);
        $this->assertDatabaseHas('games', ['id' => $game->id, 'name' => 'Original']);

        $this->actingAs($owner)->postJson('/games/showOne', ['id' => $game->id])
            ->assertJsonPath('Game.name', 'Original');
        $this->postJson('/games/update', ['id' => $game->id, 'name' => 'Changed'])
            ->assertJson(['Status' => 'Success', 'Message' => 'Game Updated Successfully']);
        $this->assertDatabaseHas('games', ['id' => $game->id, 'name' => 'Changed']);
        $this->postJson('/games/delete', ['id' => $game->id])
            ->assertJson(['Status' => 'Success', 'Message' => 'Game Deleted Successfully']);
        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    public function test_game_lists_are_scoped_sorted_and_can_be_filtered(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Game::factory()->create(['userId' => $user->id, 'name' => 'Zelda', 'status' => 'Wishlist', 'favorite' => true]);
        Game::factory()->create(['userId' => $user->id, 'name' => 'Astro', 'status' => 'Completed', 'favorite' => false]);
        Game::factory()->create(['userId' => $anotherUser->id, 'name' => 'Hidden']);

        $this->actingAs($user)->postJson('/games/showList', [
            'list' => 'all', 'sortCol' => 'name', 'sortOrder' => 'asc',
        ])->assertJsonPath('Status', 'Success')
            ->assertJsonCount(2, 'Games')
            ->assertJsonPath('Games.0.name', 'Astro');

        $this->postJson('/games/showList', [
            'list' => 'Wishlist', 'sortCol' => 'name', 'sortOrder' => 'asc',
        ])->assertJsonCount(1, 'Games')
            ->assertJsonPath('Games.0.name', 'Zelda');

        $filters = json_encode([
            'favorite' => ['true'], 'status' => [], 'platform' => [], 'platformType' => [],
            'format' => [], 'genre' => [], 'rating' => [],
        ]);
        $this->postJson('/games/showList', [
            'list' => 'all', 'sortCol' => 'name', 'sortOrder' => 'asc', 'filtered' => $filters,
        ])->assertJsonCount(1, 'Games')
            ->assertJsonPath('Games.0.name', 'Zelda');
    }

    public function test_ranked_lists_assign_and_close_ranks(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = $this->gameData(['status' => 'Wishlist', 'rank' => 0]);
        $this->postJson('/games/add', $data)->assertOk();
        $this->postJson('/games/add', $this->gameData(['name' => 'Second', 'status' => 'Wishlist', 'rank' => 0]))->assertOk();
        $this->assertDatabaseHas('games', ['name' => 'Horizon Zero Dawn', 'rank' => 1]);
        $this->assertDatabaseHas('games', ['name' => 'Second', 'rank' => 2]);

        $second = Game::where('name', 'Second')->firstOrFail();
        $this->postJson('/games/delete', ['id' => $second->id])->assertOk();
        $this->assertDatabaseHas('games', ['name' => 'Horizon Zero Dawn', 'rank' => 1]);
    }

    public function test_platform_and_genre_catalogs_return_successful_lists(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/games/get_platform_list')
            ->assertJsonPath('Status', 'Success')->assertJsonPath('Platforms.0', '32X');
        $this->postJson('/games/get_genre_list')
            ->assertJsonPath('Status', 'Success')->assertJsonPath('Genres.0', 'Not Set');
    }

    public function test_csv_import_and_export_preserve_user_library_data(): void
    {
        $user = User::factory()->create();
        $csv = UploadedFile::fake()->createWithContent('games.csv', implode("\n", [
            'Name,PlatformType,Platform,Status,Genre,Favorite,Rating,Format,Notes',
            'Imported Game,PC,Steam,Backlog,Indie,1,4,Digital,"Worth playing"',
        ]));

        $this->actingAs($user)->postJson('/games/importCSV', ['csvFile' => $csv])
            ->assertExactJson(['Status' => 'Success', 'Message' => 'Games Successfully Imported']);
        $this->assertDatabaseHas('games', [
            'userId' => $user->id, 'name' => 'Imported Game', 'platform' => 'Steam',
            'status' => 'Backlog', 'notes' => 'Worth playing', 'platformType' => 'PC',
        ]);

        $export = $this->get('/games/exportCSV')->assertOk();
        $export->assertHeader('content-disposition', 'attachment; filename=file.csv');
        $this->assertStringContainsString('Imported Game', $export->streamedContent());
        $this->assertStringNotContainsString('Hidden Game', $export->streamedContent());
    }

    public function test_import_template_is_downloadable(): void
    {
        $this->actingAs(User::factory()->create())->get('/games/getImportTemplate')->assertOk();
    }

    public function test_steam_import_creates_only_new_games_with_default_library_values(): void
    {
        $user = User::factory()->create();
        $client = \Mockery::mock(ClientInterface::class);
        $client->shouldReceive('get')->twice()->andReturn(
            new Response(200, [], json_encode(['response' => ['games' => [['name' => 'Portal 2'], ['name' => 'Celeste']]]])),
            new Response(200, [], json_encode(['response' => ['games' => [['name' => 'Portal 2'], ['name' => 'Celeste']]]]))
        );
        $steam = new Steam();
        $steam->setHttpClient($client);
        $this->app->instance(Steam::class, $steam);

        $this->actingAs($user)->postJson('/games/steamImport', ['steamId' => '76561198166200918'])
            ->assertJsonPath('Status', 'Success')
            ->assertJsonPath('Games', ['Portal 2', 'Celeste']);
        $this->assertDatabaseHas('games', [
            'userId' => $user->id, 'name' => 'Portal 2', 'platform' => 'Steam',
            'platformType' => 'PC', 'format' => 'Digital', 'status' => 'None',
        ]);

        $this->postJson('/games/steamImport', ['steamId' => '76561198166200918'])
            ->assertJsonPath('Games', []);
        $this->assertDatabaseCount('games', 2);
    }
}
