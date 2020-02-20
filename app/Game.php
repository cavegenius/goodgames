<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    public function allGamesByUser( $user ) {
        $games = DB::table('games')
            ->where('userId', $user )
            ->get();
        
        return $games;
    }
}
