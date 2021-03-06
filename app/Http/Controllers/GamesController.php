<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Game;
use App\Igdb;
use App\Steam;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class GamesController extends Controller {
    private $model;

    public function __construct() {
        $this->model = new Game;
    }

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

    public function add(Request $request) {
        $user = Auth::id();
        $status = $request->get('status');
        $max = 0;
        if($status=='Wishlist'|| $status=='Backlog') {
            $max = $this->model->where('rank', '>', 0)->where('status', '=', $status)->where('userId', $user )->max('rank')+1;
        }
        $this->validate($request, [
            'name' => 'required',
            'platform' => 'required',
            'platformType' => 'required',
            'rank' => 'required|numeric|min:0|max:'.$max,
            'format' => 'required'
        ]);

        $existinggame = null;
        $gameCheck = new Game;
        $existinggame = $gameCheck->where('userId', '=', Auth::id())->where('name', '=',$request->get('name') )->where('platform', '=',$request->get( 'platform') )->first();
        if ($existinggame !== null) {
            return json_encode(['Status' => 'Error', 'Message' => 'This game already exists for this platform. Please update your existing game.']);
        } 

        $game = new Game;
        $game->userId = $user;

        foreach( (array)$request->all() as $key=>$value ) {
            if( $key == '_token') { continue; }
            if( $key == 'notes' && !$value ) { $value='';}

            if($key == 'rank' && ($status == 'Wishlist' || $status == 'Backlog')) {
                if($value==0) {
                    $value = $this->getNextRank($status);
                } else {
                    $this->moveRanksDown($value, $status );
                }
            } else if($key == 'rank') {
                $value = 0;
            }

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
        $name = $request->get('name'); // This will get all the request data.

        $game = new Igdb;
        $games = $game->search($name);

        foreach($games as $key => $oneGame) {
            // Create Image URL
            if(property_exists($oneGame, 'cover')){
                $id = $game->getCover( $oneGame->cover);
                $games[$key]->cover = 'https://images.igdb.com/igdb/image/upload/t_thumb/'.$id[0]->image_id.'.jpg';
            }

            // Platforms
            if(property_exists($oneGame, 'platforms')){
                $platforms = array();
                foreach($oneGame->platforms as $platform) {
                    $result = $game->getPlatform($platform);
                    $platforms[] = $result[0]->name;
                }
                $games[$key]->platforms = $platforms;
            }

            // Genres
            if(property_exists($oneGame, 'genres')){
                $genres = array();
                foreach($oneGame->genres as $genre) {
                    $result = $game->getGenre($genre);
                    $genres[] = $result[0]->name;
                }
                $games[$key]->genres = $genres;
            }
        }

        $response['Games'] = $games;
        $response['Status'] = 'Success';

        return json_encode($response); 
    }

    public function importSteam(Request $request) {
        $user = $request->get('user'); // This will get all the request data.

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
        $list = $request->get('list');
        $sortCol = $request->get('sortCol');
        $sortOrder = $request->get('sortOrder');
        $filters = json_decode($request->get('filtered'), TRUE );
        $search = $request->get('searchTerm');

        $model = new Game;
        if($filters || $search){
            $games = $model->filteredList($user, $list, $sortCol, $sortOrder, $filters,  $search);
        } else {
            $games = $model->allGamesForListByUser($user, $list, $sortCol, $sortOrder);
        }

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
        $id = $request->get('id');

        $game = $this->model->find($id);

        // Verifying the game is found and belongs to this user
        if( $game && $user == $game['userId'] ) {
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
        $id = $request->get('id');

        $game = $this->model->find($id);
        // If the game does not belong to this user
        if( $game && $user != $game['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Game']);
        }

        $oldStatus = $game->status;
        $status = ucfirst( $request->get('status') );
        foreach( (array)$request->all() as $key=>$value ) {
            if( $key == '_token') { continue; }
            if( $key == 'notes' && !$value ) { $value='';}

            if($key == 'rank' && ($status == 'Wishlist' || $status == 'Backlog')) {
                if($status != $oldStatus) {
                    $value=0;
                }
                if($value==0) {
                    $value = $this->getNextRank($status);
                } else {
                    $this->moveRanksDown($value, $status );
                }
            } else if($key == 'rank') {
                $value = 0;
            }

            $game->$key = $value;
        }

        if($game->save()) {
            if(($oldStatus == 'Wishlist' || $oldStatus == 'Backlog' ) && ($status != 'Wishlist' || $status != 'Backlog') ) {
                $this->closeRankGaps($oldStatus);
            }
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
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $user = Auth::id();
        $id = $request->get('id');
        $game = $this->model->find($id);
        $updateRanks = false;
        // If the game does not belong to this user
        if( $game && $user != $game['userId'] ){
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Game']);
        }

        $status = $game->status;
        if($status == 'Wishlist' || $status == 'Backlog') {
            $updateRanks = true;
        }

        if($game->delete()) {
            if($updateRanks) { $this->closeRankGaps($status); }
            $result = json_encode(['Status' => 'Success', 'Message' => 'Game Deleted Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
    }

    public function getImportTemplate()
{
    //PDF file is stored under project/public/download/info.pdf
    $file= public_path(). "/download/ImportTemplate.xlsx";

    $headers = array(
              'Content-Type: application/xlsx',
            );

            return response()->download($file, 'ImportTemplate.xlsx', $headers);
}


    /**
     *  Import Games from CSV File
     */
    public function importCSV(Request $request) {
        $file = $request->file('csvFile');

        $gamesArray = $this->csvToArray($file);

        for ($i = 0; $i < count($gamesArray); $i++) {
            $existinggame = null;
            $gameCheck = new Game;
            $existinggame = $gameCheck->where('userId', '=', Auth::id())->where('name', '=',$gamesArray[$i]['Name'])->where('platform', '=',$gamesArray[$i]['Platform'])->first();
            if ($existinggame !== null) {
                $game = $existinggame;
            } else {
                $game = new Game;
            }

            $game->userId = Auth::id();
            foreach( $gamesArray[$i] as $key=>$value ) {
                $key = strtolower($key );
                if( $key == 'genre' && !$value ) { $value='Not Set';}
                if( $key == 'favorite' && !$value ) { $value=0;}
                if( $key == 'rating' && !$value ) { $value='0';}
                if( $key == 'format' && !$value ) { $value='Not Set';}
                if( $key == 'rank' && !$value ) { $value=0;}
                //Logic for backlog and wishlist ranking

                if($key == 'rank' && ($gamesArray[$i]['Status'] == 'Wishlist' || $gamesArray[$i]['Status'] == 'Backlog')) {
                    if($value==0) {
                        $value = $this->getNextRank($gamesArray[$i]['Status']);
                    } else {
                        $max = $this->model->where('rank', '>', 0)->where('status', '=', $gamesArray[$i]['Status'])->where('userId', Auth::id() )->max('rank')+1;
                        if($value > $max) {
                            $value = $max;
                        }
                        $this->moveRanksDown($value, $gamesArray[$i]['Status'] );
                        $this->closeRankGaps($gamesArray[$i]['Status']);
                    }
                } else if($key == 'rank') {
                    $value = 0;
                }

                $game->$key  = $value;
            }
            $game->save();
        }

        $result = json_encode(['Status' => 'Success', 'Message' => 'Games Successfully Imported']);
        return $result;
    }

    /**
     * Export to a CSV
     */
    public function exportCSV() {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
    
        $games = $this->model->allGamesForListByUser( Auth::id() );
        $columns = array('Rank','Name','PlatformType','Platform','Status','Genre','Favorite','Rating','Format','Notes');
    
        $callback = function() use ($games, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach($games as $game) {
                fputcsv($file, array($game->rank, $game->name, $game->platformType, $game->platform, $game->status, $game->genre, $game->favorite, $game->rating, $game->format, $game->notes));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
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

    public function get_platform_list() {
        $platforms = ['32X','3DO','Acorn Archimedes','Acorn Electron','Amiga','Amiga CD32','Amstrad CPC','Amstrad GX4000','Android','APF-M1000','Apple II','Apple Arcade','Apple Bandai Pippin','Arcade','Atari 2600','Atari 5200','Atari 7800','Atari 8-bit','Atari ST','Bally Astrocade','Battle.Net','BBC Micro','Beamdog','Bethesda Launcher','Big Fish Games','Browser','Calculator','CD-i','CD32X','Coleco Adam','ColecoVision','Commodore 64','Commodore CDTV','Commodore Plus/4','Commodore VIC-20','Cougar Boy','Desura','Discord','DOS','DotEmu','Dragon 32/64','Dreamcast','DSiWare','Emerson Arcadia 2001','Epic Games Launcher','Fairchild Channel F','Family Computer','Famicom Disk System','FM Towns','Fujitsu Micro 7','Gamate','Game &amp; Watch','Game Gear','Game Boy','Game Boy/Color','Game Boy Advance','e-Reader','Game Wave Family Entertainment System','GameCube','GameFly','GamersGate','GameStop PC','Games For Windows','Game.com','Genesis / Mega Drive','GetGames','Gizmondo','GOG.com','Google Stadia','GP2X Wiz','Green Man Gaming','HTC Vive','Humble Bundle Store','HyperScan','IndieCity','Intellivision','iOS','iPad','iPod','iPhone','itch.io','Jaguar','Jaguar CD','LaserActive','Linux','Lynx','Mac','Magnavox Odyssey','Master System','Microvision','Miscellaneous','Mobile','MSX','N-Gage','NEC PC-8801','NEC PC-9801','Neo Geo','Neo Geo CD','Neo Geo Pocket/Color','Nintendo 3DS','3DS Downloads','Nintendo DS','Nintendo 64','Nintendo 64DD','Nintendo Entertainment System','Nintendo Switch','Switch Downloads','Nuon','Nuuvem','Oculus Rift','Odyssey² / Videopac','OnLive','Origin','OUYA','Pandora','PC','PC Downloads','PC-50X','PC-FX','Pinball','PlayStation','PlayStation 2','PlayStation 3','PlayStation 4','PlayStation Mobile','PlayStation Network','PSOne Classics','PS2 Classics','PlayStation minis','PlayStation Portable','PlayStation Vita','PlayStation VR','Plug-and-Play','PocketStation','Pokémon Mini','R-Zone','RCA Studio II','Rockstar Games Launcher','SAM Coup','Saturn','Sega CD','Sega Pico','Sega SG-1000','Sharp X1','Sharp X68000','Steam','Super Nintendo Entertainment System','SuperGrafx','TI-99/4A','Tiger Handhelds','TurboDuo','TurboGrafx-16','TurboGrafx-CD','TRS-80 Color Computer','Twitch','Uplay','Vectrex','Virtual Boy','Virtual Console (Wii)','Virtual Console (3DS)','Watara Supervision','Wii','WiiWare','Wii U','Wii U Downloads','Virtual Console (WiiU)','Windows Phone 7','Windows Store','WonderSwan/Color','XaviXPORT','Xbox','Xbox 360','Xbox Game Pass','Xbox LIVE Arcade','XNA Indie Games','Xbox 360 Games on Demand','Xbox One','Xbox One Downloads','Zeebo','Zune','ZX Spectrum', 'Other'];

        $response['Platforms'] = $platforms;
        $response['Status'] = 'Success';

        return json_encode($response); 
    }

    public function get_genre_list() {
        $genres = ["Not Set","Adventure", "Board Game", "Fighting", "Indie", "Music", "Other", "Quiz/Trivia", "Role-playing", "Shooter", "Simulator", "Strategy", "Tactical"];

        $response['Genres'] = $genres;
        $response['Status'] = 'Success';

        return json_encode($response); 
    }

    private function getNextRank($list) {
        $value = $this->model->where('rank', '>', 0)->where('status', '=', $list)->where('userId', Auth::id() )->max('rank')+1;

        return $value;
    }

    private function moveRanksDown($rank, $list) {
        $existingGame = null;
        $games = null;
        $games = new Game;
        $existingGame = $games->where('rank', '=', $rank)->where('status', '=', $list)->first();
        //Log::stack(['single', 'slack'])->info($existingGame);
        //Log::channel('single')->info($rank);

        if ($existingGame !== null) {
            $newRank = $rank+1;
            $this->moveRanksDown($newRank, $list);
            $existingGame->rank = $newRank;
            $existingGame->save();
        }

        return true;
    }

    private function closeRankGaps($list) {
        // Need to select all the games on the specific list sorted by rank asc.
        // look for any case where the current lines rank is not exactly one higher than the previous.
        // If that happens set it to the previous +1
        // then keep going and any ones after that should automatically be fixed.
        $model = new Game;
        $user = Auth::id();
        $games = $model->allGamesForListByUser( $user, strtolower( $list ), 'rank', 'asc');
//        $prev = 0;
        //Log::stack(['single', 'slack'])->info($existingGame);
        // view in logs/laravel.log
        $gameslist = json_decode($games, true);
        //Log::channel('single')->info(print_r($gameslist));

        foreach($gameslist as $key=>$game) {
            if($game['rank'] != $key+1) {
                $gameUpdate = $this->model->find($game['id']);
                $gameUpdate->rank = $key+1;
                $gameUpdate->save();
            }
        }
    }

    public function steamImport( Request $request) {
        if (!Auth::check()) {
            return json_encode(['Status'=>'Error','Message'=>'User Not Logged In', 'Logout'=> true]);
        }

        $rules = [
            'steamId' => 'required|regex:/\d{17}/'
        ];
        $messages = [
            'steamId.regex' => 'Please use the 17 digit numeric Steam ID'
        ];

        $this->validate($request, $rules, $messages);

        $addedGames = array();

        $user = Auth::id();

        $steamId = $request->get('steamId');
        $steam = new Steam;
        $games = $steam->import($steamId);

        // Take each game and loop through them using the save function?
        // If its a new game add it to the return list
        foreach($games as $gameName) {
            $existinggame = null;
            $gameCheck = new Game;
            $existinggame = $gameCheck->where('userId', '=', Auth::id())->where('name', '=', $gameName )->where('platform', '=', 'Steam' )->first();
            if ($existinggame !== null) {
                continue;
            }

            $game = new Game;
            $game->userId = $user;
            $game->name = $gameName;
            $game->status = 'None';
            $game->platform = 'Steam';
            $game->platformtype = 'PC';
            $game->format = 'Digital';
            $game->favorite = 0;
            $game->rating = '0';
            $game->rank = 0;
            $game->genre = 'Not Set';
            $game->notes = '';

            if($game->save()) {
                $addedGames[] = $game->name;
            }
        }

        $response['Games'] = $addedGames;
        $response['Status'] = 'Success';

        return json_encode($response);
    }
}
