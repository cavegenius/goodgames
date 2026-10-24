<?php

namespace App;

use \GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class Igdb {

    private $api_url = 'https://api-v3.igdb.com/';
    private ?ClientInterface $httpClient = null;

    public function setHttpClient(ClientInterface $client): void
    {
        $this->httpClient = $client;
    }

    private function client(): ClientInterface
    {
        return $this->httpClient ??= new Client();
    }

    public function search( $search ) {
        $client = $this->client();

        $data = 'fields name,cover,genres,platforms,release_dates,summary,url; search "'.$search.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->post($this->api_url.'games', ['headers' => $header, 'body'=>$data]);

        return json_decode($response->getBody()->getContents());
    }

    public function getCover( $id ) {
        $client = $this->client();

        //$data = 'fields image_id; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'covers?fields=image_id&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }

    public function getGenre( $id ) {
        $client = $this->client();

        //$data = 'fields name; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'genres?fields=name&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }

    public function getPlatform( $id ) {
        $client = $this->client();

        //$data = 'fields name; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'platforms?fields=name&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }
}
