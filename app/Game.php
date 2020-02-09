<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    public static function index( $user ) {
        $games = DB::table('games')
            ->where('userId', $user )
            ->get();
        
        return $games;
    }
}
