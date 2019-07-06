<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;

class GamesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::id();
        //$recipes = Recipe::index($user);
        // TODO: get the units to send to the form for options and send to view
        return view('games');
        //return view('games')->with('games', $games);
    }

    public function search(Request $request) {
        $name = $request->all('name'); // This will get all the request data.

        $game = new Game;
        $games = $game->search($name['name']);
        
        return $games; // This will dump and die
        //return view('games.results')->with('games', $games);
    }

}