<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $fillable = ['name','platformType','platform','status','genre','favorite','rating','format','notes'];

    public function allGamesForListByUser( $user, $list ) {
        $games = DB::table('games')
            ->where('userId', $user )
            ->where($list, 1)
            ->get();
        
        return $games;
    }
}
