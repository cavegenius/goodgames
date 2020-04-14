<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $fillable = ['name','platformType','platform','status','genre','favorite','rating','format','notes'];

    public function allGamesForListByUser( $user, $list, $sortCol='name', $sortOrder='asc' ) {
        if( $list == 'backlog' ){
            $games = DB::table('games')
                ->where('userId', $user )
                ->where('status', 'backlog')
                ->orderBy($sortCol, $sortOrder)
                ->get();
        } else if($list == 'wishlist') {
            $games = DB::table('games')
                ->where('userId', $user )
                ->where('status', 'wishlist')
                ->orderBy($sortCol, $sortOrder)
                ->get();
        } else {
            $games = DB::table('games')
                ->where('userId', $user )
                ->orderBy($sortCol, $sortOrder)
                ->get();
        }

        return $games;
    }
}
