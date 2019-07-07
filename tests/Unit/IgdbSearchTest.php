<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Igdb;

class IgdbTest extends TestCase
{

    public function testIgdbSearchTest() {
        $igdb = new Igdb;
        $games = $igdb->search( 'Horizon Zero Dawn' );
        var_dump($games);
        $this->assertArrayHasKey(3, $games);
    }

}