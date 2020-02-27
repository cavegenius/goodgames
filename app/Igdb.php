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
    
}