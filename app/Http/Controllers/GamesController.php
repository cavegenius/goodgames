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
            $response = json_encode(['Status' => 'Success', 'Message' => 'Game Added Successfully']);
        } else {
            $response = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $response;
    }

    public function search(Request $request) {
        $name = $request->all('name')['name']; // This will get all the request data.

        $game = new Igdb;
        $games = $game->search($name);

        $response['Games'] = $games;
        $response['Status'] = 'Success';

        return json_encode($response); 
    }

    public function importSteam(Request $request) {
        $user = $request->all('user')['user']; // This will get all the request data.

        $game = new Steam;
        $games = $game->import($user);
        
        $response['Games'] = $games;
        $response['Status'] = 'Success';

        return json_encode($response);
    }

    /**
     * Return all games for a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function showList(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $list = $request->all('list')['list'];
        $model = new Game;
        $games = $model->allGamesForListByUser($user, $list);

        if(count($games) > 0) {
            $response['Games'] = $games;
            $response['Status'] = 'Success';
        } else {
            $response['Status'] = 'Error';
            $response['Message'] ='No Games Found';
        }

        return json_encode($response);
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
        $id = $request->all('id')['id'];

        $game = Game::find($id);

        // If the game does not belong to this user
        if( $game && $user != $game['userId'] ){
            $response['Status'] = 'Error';
            $response['Message'] ='Game Not Found For This User';
        } else if( $game && $user == $game['userId'] ) {
            $response['Game'] = $game;
            $response['Status'] = 'Success';
        } else { 
            $response['Status'] = 'Error';
            $response['Message'] ='Game Not Found';
        }

        return json_encode($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // TODO: Add fields that can not be edited and also required fields?
    public function update(Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->all('id')['id'];

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
    public function importCSV(Request $request) {
        $file = $request->file('csvFile');

        $gamesArray = $this->csvToArray($file);

        for ($i = 0; $i < count($gamesArray); $i++) {
            $game = new Game;
            $game->userId = Auth::id();
            $game->owned = true;
            $game->backlog = false;
            $game->wishlist = false;
            foreach( $gamesArray[$i] as $key=>$value ) {
                $key = strtolower($key );
                $game->$key  = $value;
                $game->save();
            }
        }

        $result = json_encode(['Status' => 'Success', 'Message' => 'Games Successfully Imported']);
        return $result;
    }

    /**
     * Export to a CSV
     */
    public function exportCSV() {

    }

    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header){
                    // Remove the byte order mark some applications add to the beginning
                    $bom = pack('H*','EFBBBF');
                    $row[0] = preg_replace("/^$bom/", '', $row[0]);
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

}