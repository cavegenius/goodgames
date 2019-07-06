<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \GuzzleHttp\Psr7\Request;

class Game extends Model {
    
    public function search( $search ) {
        $client = new \GuzzleHttp\Client();

        $url = 'https://api-v3.igdb.com/search';
        $data = 'fields *; search "'.$search.'"; limit 50;';
        $header = ['user-key' => '0d5c13224126d70a2eb7a941bfa26161', 'Content-Type'=>'text/plain'];
        $response = $client->post($url, ['headers' => $header, 'body'=>$data]);
        
        //$response = $client->request( 'POST', $url, $header, $data );
        return $response;
    }
}
