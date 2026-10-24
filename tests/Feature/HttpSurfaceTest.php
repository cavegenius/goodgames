<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Igdb;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class HttpSurfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_auth_pages_and_health_endpoint_are_available(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertViewIs('auth.login');
        $this->get('/register')->assertOk()->assertViewIs('auth.register');
        $this->get('/password/reset')->assertOk();
        $this->get('/api/healthz')->assertOk()->assertExactJson(['status' => 'ok']);
        $this->getJson('/api/user')->assertUnauthorized();
    }

    public function test_registration_persists_user_and_sends_verification_notification(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        $this->post('/register', [
            'name' => 'New Player', 'email' => 'new-player@example.test',
            'password' => 'a-secure-password', 'password_confirmation' => 'a-secure-password',
        ])->assertRedirect('/games');

        $user = User::where('email', 'new-player@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }

    public function test_login_logout_and_password_reset_flows(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);

        $this->from('/login')->post('/login', [
            'email' => $user->email, 'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect('/games')->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/')->assertGuest();

        $this->post('/password/email', ['email' => $user->email])->assertSessionHas('status');
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \Illuminate\Auth\Notifications\ResetPassword::class
        );
    }

    public function test_igdb_search_formats_cover_platform_and_genre_metadata(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('post')->once()->andReturn(new Response(200, [], '[{"id":14,"name":"Test Game","cover":4,"platforms":[1],"genres":[2]}]'));
        $client->shouldReceive('get')->times(3)->andReturn(
            new Response(200, [], '[{"image_id":"cover-id"}]'),
            new Response(200, [], '[{"name":"PC"}]'),
            new Response(200, [], '[{"name":"Adventure"}]')
        );
        $igdb = new Igdb();
        $igdb->setHttpClient($client);
        $this->app->instance(Igdb::class, $igdb);

        $this->postJson('/games/search', ['name' => 'Test Game'])
            ->assertJsonPath('Status', 'Success')
            ->assertJsonPath('Games.0.cover', 'https://images.igdb.com/igdb/image/upload/t_thumb/cover-id.jpg')
            ->assertJsonPath('Games.0.platforms.0', 'PC')
            ->assertJsonPath('Games.0.genres.0', 'Adventure');
    }
}
