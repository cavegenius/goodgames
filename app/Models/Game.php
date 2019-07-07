<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;
use App\Models\Igdb;
use app\Models\Steam;
use app\Models\Psn;
use app\Models\Xbox;

class Game extends Model {
    
    public function searchIGDB( $search ) {
        
        $igdb = new Igdb;
        $response = $igdb->search( $search );
        
        return $response;
    }

    public function importSteam( $user ) {
        $steam = new Steam;

        $response = $steam->import( $user );
        return $response;
    }

    public function importPSN( $user ) {
        $psn = new Psn;

        $response = $psn->import( $user );
        return $response;
    }

    public function importXbox( $user ) {
        $xbox = new Xbox;

        $response = $xbox->import( $user );
        return $response;
    }
}
