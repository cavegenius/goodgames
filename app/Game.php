<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $fillable = ['name','platformType','platform','status','genre','favorite','rating','format','notes'];

    public function allGamesForListByUser( $user, $list ) {
        if( $list == 'backlog' ){
            $games = DB::table('games')
                ->where('userId', $user )
                ->where('status', 'backlog')
                ->orderBy('name', 'asc')
                ->get();
        } else if($list == 'wishlist') {
            $games = DB::table('games')
                ->where('userId', $user )
                ->where('status', 'wishlist')
                ->orderBy('name', 'asc')
                ->get();
        } else {
            $games = DB::table('games')
                ->where('userId', $user )
                ->where('owned', 1)
                ->orderBy('name', 'asc')
                ->get();
        }

        return $games;
    }
}
