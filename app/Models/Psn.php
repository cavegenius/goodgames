<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;
use PlayStation\Client;

class Psn extends Model {

    public $psnRefreshToken = '35ead7ed-5859-4a18-9f68-92e752b0ac01';

    public function firstLogin($uuid,$token) {
        // Full directions at https://tusticles.com/psn-php/first_login.html
        $client = new Client();

        $client->login($uuid, $token);

        $refreshToken = $client->refreshToken();
        return $refreshToken;
    }

    public function login() {
        $client = new Client();

        $client->login( $this->psnRefreshToken );

        $refreshToken = $client->refreshToken();
        return $client;
    }
    
    public function import( $user ) {
        $client = $this->login( );
        
        $games = $client->user( $user )->games();
        $gamesList =[];

        foreach ($games as $game) {
            if ($game->hasTrophies()) {
                $trophyGroups = $game->trophyGroups();
        
                foreach ($trophyGroups as $trophyGroup) {
                    $gamesList[] = $trophyGroup->name(); 
                }
            }
        }

        return $gamesList;
    }
    
}