<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Igdb extends Model {

    private $api_key = '0d5c13224126d70a2eb7a941bfa26161';
    private $api_url = 'https://api-v3.igdb.com/';

    public function search( $search ) {
        $client = new \GuzzleHttp\Client();
        
        $data = 'fields *; search "'.$search.'"; limit 50;';
        $header = ['user-key' => $this->api_key , 'Content-Type'=>'text/plain'];
        $response = $client->post($this->api_url.'search', ['headers' => $header, 'body'=>$data]);
        
        return $response;
    }
    
}