<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Igdb;

class IgdbTest extends TestCase
{

    public function testIgdbSearchTest() {
        $igdb = new Igdb;
        $games = $igdb->search( 'Horizon Zero Dawn' );
        
        $this->assertArrayHasKey(3, $games);
    }

}