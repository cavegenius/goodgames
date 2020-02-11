<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Game;
use App\Igdb;
use App\Steam;

class GamesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index() {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::id();
        //$games = Game::index($user);

        return view('games');
        //return view('games')->with('games', $games);
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function addGame(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'platform' => 'required',
            'platformType' => 'required',
            'format' => 'required',
            'owned' => 'required',
            'wishlist' => 'required',
            'backlog' => 'required',
        ]);

        $user = Auth::id();
        $game = new Game;
        $game->userId = $user;
        foreach( (array)$request->all() as $key=>$value ) {
            if( $key == '_token') { continue; }
            $game->$key = $value;
        }

        if($game->save()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Game Added Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }

    public function search(Request $request) {
        $name = $request->all('name'); // This will get all the request data.

        $game = new Igdb;
        $games = $game->search($name['name']);
        
        return $games; 
    }

    public function importSteam(Request $request) {
        $user = $request->all('user'); // This will get all the request data.

        $game = new Steam;
        $games = $game->import($user['user']);
        
        return $games; 
        //return view('games.results')->with('games', $games);
    }

}