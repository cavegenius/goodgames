<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Filter extends Model {

    public function filterList( $user ) {
        $filters = DB::table('filters')
            ->where('userId', $user )
            ->get();

        return $filters;
    }

}
