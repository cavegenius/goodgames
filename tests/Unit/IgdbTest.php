<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Igdb;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;

class IgdbTest extends TestCase
{
    public function testIgdbSearchTest() {
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('post')->once()->andReturn(new Response(200, [], '[{"id":3,"name":"Horizon Zero Dawn"}]'));

        $igdb = new Igdb;
        $igdb->setHttpClient($client);
        $games = $igdb->search( 'Horizon Zero Dawn' );
        
        $this->assertSame('Horizon Zero Dawn', $games[0]->name);
    }
}
