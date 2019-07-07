<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PSN;
use App\Models\Xbox;
use App\Models\Steam;

class ImportTest extends TestCase
{
/*
    public function testImportPSNTest() {
        $psn = new Psn;
        $games = $psn->import( 'cavegenius' );
        
        $this->assertArrayHasKey(3, $games);
    }

    public function testImportXboxTest() {
        $xbox = new Xbox;
        $games = $xbox->import('theoneandonly19');

        $this->assertArrayHasKey('xbox360', $games);
        $this->assertArrayHasKey('xboxOne', $games);
    }
*/
    public function testImportSteamTest() {
        $steam = new Steam;
        $games = $steam->import('76561198166200918');

        $this->assertArrayHasKey(3, $games);
    }
}
