<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Igdb extends Model {

    private $api_url = 'https://api-v3.igdb.com/';

    public function search( $search ) {
        $client = new \GuzzleHttp\Client();

        $data = 'fields name,cover,genres,platforms,release_dates,summary,url; search "'.$search.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->post($this->api_url.'games', ['headers' => $header, 'body'=>$data]);

        return json_decode($response->getBody()->getContents());
    }

    public function getCover( $id ) {
        $client = new \GuzzleHttp\Client();

        //$data = 'fields image_id; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'covers?fields=image_id&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }

    public function getGenre( $id ) {
        $client = new \GuzzleHttp\Client();

        //$data = 'fields name; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'genres?fields=name&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }

    public function getPlatform( $id ) {
        $client = new \GuzzleHttp\Client();

        //$data = 'fields name; filter[id][eq] "'.$id.'"; limit 50;';
        $header = ['user-key' => config('keys.igdbKey') , 'Content-Type'=>'text/plain'];
        $response = $client->get($this->api_url.'platforms?fields=name&filter[id][eq]='.$id, ['headers' => $header]);

        return json_decode($response->getBody()->getContents());
    }
}
