<?php

namespace Tests\Unit;

use App\Steam;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class SteamTest extends TestCase
{
    public function test_import_returns_game_names_from_steam_owned_games_response(): void
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('get')->once()->withArgs(function ($url) {
            return str_contains($url, 'steamid=76561198166200918')
                && str_contains($url, 'include_appinfo=1');
        })->andReturn(new Response(200, [], json_encode([
            'response' => [
                'games' => [
                    ['appid' => 620, 'name' => 'Portal 2'],
                    ['appid' => 504230, 'name' => 'Celeste'],
                ],
            ],
        ])));

        $steam = new Steam();
        $steam->setHttpClient($client);

        $this->assertSame(['Portal 2', 'Celeste'], $steam->import('76561198166200918'));
    }
}
