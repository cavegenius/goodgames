<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Xbox extends Model {

    private $api_url = 'https://xboxapi.com/v2/';

    public function getUid( $gamerTag ) {
        $client = new \GuzzleHttp\Client();
        $header = ['X-AUTH' => config('keys.xboxKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'xuid/'.$gamerTag, ['headers' => $header]);

        return $response->getBody()->getContents();
    }

    public function import( $gamerTag ) {
        $games['xbox360'] = [];
        $gamesList['xboxOne'] = [];
        $uid = $this->getUid( $gamerTag );
        $client = new \GuzzleHttp\Client();
        $header = ['X-AUTH' => config('keys.xboxKey') , 'Content-Type'=>'text/plain'];

        $response = $client->get($this->api_url.$uid.'/xbox360games', ['headers' => $header]);
        $games['xbox360'] = $response->getBody()->getContents();
        
        foreach ((array)json_decode($games['xbox360'])->titles as $game) {
            $gamesList['xbox360'][] = $game->name;
        }

        $response= $client->get($this->api_url.$uid.'/xboxonegames', ['headers' => $header]);
        $games['xboxOne'] = $response->getBody()->getContents();
        foreach ((array)json_decode($games['xboxOne'])->titles as $game) {
            $gamesList['xboxOne'][] = $game->name;
        }
        
        return $gamesList;
    }
    
}