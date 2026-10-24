<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Steam extends Model {

    private $api_url = 'https://api.steampowered.com/';
    private ?ClientInterface $httpClient = null;

    public function setHttpClient(ClientInterface $client): void
    {
        $this->httpClient = $client;
    }

    public function import( $user ) {
        $client = $this->httpClient ?? new Client();
        $gamesList = [];

        $response = $client->get($this->api_url.'IPlayerService/GetOwnedGames/v0001/', [
            'query' => [
                'key' => config('keys.steamKey'),
                'steamid' => $user,
                'include_appinfo' => 1,
                'format' => 'json',
            ],
            'connect_timeout' => 3,
            'timeout' => 10,
        ]);
        $games = json_decode($response->getBody()->getContents());
        $games = $games->response->games;

        foreach( $games as $game ) {
            $gamesList[] = $game->name;
        }

        return $gamesList;
    }
    
}
