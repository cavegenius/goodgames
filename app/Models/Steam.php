<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Steam extends Model {

    private $api_key = '975BA8BA56DE5FCCFF8DC2B392BE526A';
    private $api_url = 'http://api.steampowered.com/';

    public function import( $user ) {
        $client = new \GuzzleHttp\Client();
        $gamesList = [];

        $response = $client->get($this->api_url.'IPlayerService/GetOwnedGames/v0001/?key='.$this->api_key.'&steamid='.$user.'&include_appinfo=1&format=json');
        $games = json_decode($response->getBody()->getContents());
        $games = $games->response->games;

        foreach( $games as $game ) {
            $gamesList[] = $game->name;
        }
        return $gamesList;
    }
    
}