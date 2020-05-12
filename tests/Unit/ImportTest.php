<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\PSN;
use App\Xbox;
use App\Steam;

class ImportTest extends TestCase
{

    public function testImportPSNTest() {
        $this->markTestIncomplete('This test has not been implemented yet.');
        /*$psn = new Psn;
        $games = $psn->import( 'cavegenius' );
        
        $this->assertArrayHasKey(3, $games);*/
    }

    public function testImportXboxTest() {
        $this->markTestIncomplete('This test has not been implemented yet.');
        /*$xbox = new Xbox;
        $games = $xbox->import('theoneandonly19');

        $this->assertArrayHasKey('xbox360', $games);
        $this->assertArrayHasKey('xboxOne', $games);*/
    }

    public function testImportSteamTest() {
        $steam = new Steam;
        $games = $steam->import('76561198166200918');

        $this->assertArrayHasKey(3, $games);
    }
}
