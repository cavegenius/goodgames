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

        return view('games');
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function store(Request $request) {
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

    /**
     * Return all games for a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAll() {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $model = new Game;
        $games = $model->allGamesByUser($user);

        return count($games) > 0 ? json_encode($games) : json_encode(['Status'=> 'Error', 'Message'=>'No Games Found']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showOne(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id');
        $id = $id['id'];

        $game = Game::find($id);

        // If the game does not belong to this user
        if( $game && $user != $game['userId'] ){
            $game=false;
        }
        return $game ? json_encode($game) : json_encode(['Status'=> 'Error', 'Message'=>'Game Not Found']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id');
        $id = $id['id'];

        $game = Game::find($id);
        // If the game does not belong to this user
        if( $game && $user != $game['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Game']);
        }

        foreach( (array)$request->all() as $key=>$value ) {
            if( $key == '_token') { continue; }
            $game->$key = $value;
        }

        if($game->save()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Game Updated Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        //
    }

    /**
     *  Import Games from CSV File
     */
    public function importCSV() {

    }

    /**
     * Export to a CSV
     */
    public function exportCSV() {

    }

}