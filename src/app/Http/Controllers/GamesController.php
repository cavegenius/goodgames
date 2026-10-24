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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|string|max:100',
            'platformType' => 'required|string|in:PC,Console,Other',
            'status' => 'required|string|in:Abandoned,Backlog,Completed,In Progress,Might Play,None,Paused,Replayable,Wishlist,Wont Play',
            'rank' => 'required|integer|min:0',
            'favorite' => 'sometimes|boolean',
            'rating' => 'sometimes|integer|between:0,5',
            'format' => 'required|string|in:Physical,Digital,Not Set',
            'genre' => 'sometimes|string|in:Adventure,Board Game,Fighting,Indie,Music,Not Set,Other,Quiz/Trivia,Role-playing,Shooter,Simulator,Strategy,Tactical',
            'notes' => 'nullable|string|max:255',
        ]);
        $status = $data['status'];
        $max = 0;
        if($status=='Wishlist'|| $status=='Backlog') {
            $max = $this->model->where('rank', '>', 0)->where('status', '=', $status)->where('userId', $user )->max('rank')+1;
        }
        $this->validate($request, ['rank' => 'integer|max:'.$max]);

        $existinggame = null;
        $gameCheck = new Game;
        $existinggame = $gameCheck->where('userId', '=', Auth::id())->where('name', '=',$request->get('name') )->where('platform', '=',$request->get( 'platform') )->first();
        if ($existinggame !== null) {
            return json_encode(['Status' => 'Error', 'Message' => 'This game already exists for this platform. Please update your existing game.']);
        } 

        $game = new Game;
        $game->userId = $user;
        $data['rank'] = in_array($status, ['Wishlist', 'Backlog'], true)
            ? ($data['rank'] ?: $this->getNextRank($status))
            : 0;
        if ($data['rank'] > 0 && in_array($status, ['Wishlist', 'Backlog'], true)) {
            $this->moveRanksDown($data['rank'], $status);
        }
        $game->fill($data);

        if($game->save()) {
            $response = json_encode(['Status' => 'Success', 'Message' => 'Game Added Successfully']);
        } else {
            $response = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $response;
    }

    public function search(Request $request, Igdb $game) {
        $name = $request->validate(['name' => 'required|string|max:100'])['name'];

        $games = $game->search($name);
        if (!is_array($games)) $games = [];

        foreach($games as $key => $oneGame) {
            // Create Image URL
            if (property_exists($oneGame, 'cover')) {
                $id = $game->getCover($oneGame->cover);
                $games[$key]->cover = 'https://images.igdb.com/igdb/image/upload/t_thumb/'.$id[0]->image_id.'.jpg';
            }

            // Platforms
            if (property_exists($oneGame, 'platforms')) {
                $platforms = [];
                foreach ($oneGame->platforms as $platform) $platforms[] = $game->getPlatform($platform)[0]->name;
                $games[$key]->platforms = $platforms;
            }

            // Genres
            if (property_exists($oneGame, 'genres')) {
                $genres = [];
                foreach ($oneGame->genres as $genre) $genres[] = $game->getGenre($genre)[0]->name;
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
        $params = $request->validate([
            'list' => 'sometimes|string|in:all,backlog,wishlist,In Progress,Backlog,Wishlist',
            'sortCol' => 'sometimes|string|in:name,rank,status,platform,platformType,format,genre,rating,created_at,updated_at',
            'sortOrder' => 'sometimes|string|in:asc,desc',
            'filtered' => 'nullable|string|max:10000',
            'searchTerm' => 'nullable|string|max:100',
        ]);
        $list = ['backlog' => 'Backlog', 'wishlist' => 'Wishlist'][$params['list'] ?? ''] ?? ($params['list'] ?? 'all');
        $sortCol = $params['sortCol'] ?? 'name';
        $sortOrder = $params['sortOrder'] ?? 'asc';
        $filters = json_decode($params['filtered'] ?? '', true);
        $filterKeys = ['favorite', 'status', 'platform', 'platformType', 'format', 'genre', 'rating'];
        $filters = array_merge(array_fill_keys($filterKeys, []), is_array($filters) ? array_intersect_key($filters, array_flip($filterKeys)) : []);
        foreach ($filterKeys as $key) {
            $filters[$key] = is_array($filters[$key])
                ? array_slice(array_values(array_filter($filters[$key], 'is_string')), 0, 100)
                : [];
        }
        $search = $params['searchTerm'] ?? '';

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
        $id = $request->validate(['id' => 'required|integer'])['id'];
        $game = $this->model->where('userId', $user)->find($id);

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
        $data = $request->validate([
            'id' => 'required|integer',
            'name' => 'sometimes|required|string|max:255',
            'platform' => 'sometimes|required|string|max:100',
            'platformType' => 'sometimes|required|string|in:PC,Console,Other',
            'status' => 'sometimes|required|string|in:Abandoned,Backlog,Completed,In Progress,Might Play,None,Paused,Replayable,Wishlist,Wont Play',
            'rank' => 'sometimes|integer|min:0',
            'favorite' => 'sometimes|boolean',
            'rating' => 'sometimes|integer|between:0,5',
            'format' => 'sometimes|required|string|in:Physical,Digital,Not Set',
            'genre' => 'sometimes|string|in:Adventure,Board Game,Fighting,Indie,Music,Not Set,Other,Quiz/Trivia,Role-playing,Shooter,Simulator,Strategy,Tactical',
            'notes' => 'nullable|string|max:255',
        ]);
        $game = $this->model->where('userId', $user)->find($data['id']);
        if (!$game) {
            return json_encode(['Status' => 'Error', 'Message' => 'Invalid Game']);
        }

        $oldStatus = $game->status;
        $status = $data['status'] ?? $oldStatus;
        unset($data['id']);
        if (isset($data['status']) && $status !== $oldStatus && !array_key_exists('rank', $data)) {
            $data['rank'] = 0;
        }
        if (array_key_exists('rank', $data)) {
            if (in_array($status, ['Wishlist', 'Backlog'], true)) {
                $maxRank = Game::where('userId', $user)->where('status', $status)->where('rank', '>', 0)->max('rank') + 1;
                validator(['rank' => $data['rank']], ['rank' => 'integer|min:0|max:'.$maxRank])->validate();
                $data['rank'] = $data['rank'] ?: $this->getNextRank($status);
                if ($data['rank'] > 0) $this->moveRanksDown($data['rank'], $status);
            } else {
                $data['rank'] = 0;
            }
        }
        $game->fill($data);

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
        $id = $request->validate(['id' => 'required|integer'])['id'];
        $game = $this->model->where('userId', $user)->find($id);
        $updateRanks = false;
        if (!$game) {
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
        $file = $request->validate(['csvFile' => 'required|file|mimes:csv,txt|max:10240'])['csvFile'];
        $gamesArray = $this->csvToArray($file->getRealPath());
        $allowedColumns = ['Name', 'Rank', 'PlatformType', 'Platform', 'Status', 'Genre', 'Favorite', 'Rating', 'Format', 'Notes'];
        if (!is_array($gamesArray) || $gamesArray === [] || count($gamesArray) > 1000) {
            return response()->json(['Status' => 'Error', 'Message' => 'The CSV file is invalid or has too many rows.'], 422);
        }
        if ($gamesArray !== [] && (array_diff(array_keys($gamesArray[0]), $allowedColumns)
            || array_diff(['Name', 'Platform', 'Status'], array_keys($gamesArray[0])))) {
            return response()->json(['Status' => 'Error', 'Message' => 'The CSV contains unsupported columns.'], 422);
        }
        foreach ($gamesArray as $row) {
            $rowValidator = validator($row, [
                'Name' => 'required|string|max:255',
                'Rank' => 'nullable|integer|min:0',
                'PlatformType' => 'nullable|in:PC,Console,Other',
                'Platform' => 'required|string|max:100',
                'Status' => 'required|in:Abandoned,Backlog,Completed,In Progress,Might Play,None,Paused,Replayable,Wishlist,Wont Play',
                'Genre' => 'nullable|in:Adventure,Board Game,Fighting,Indie,Music,Not Set,Other,Quiz/Trivia,Role-playing,Shooter,Simulator,Strategy,Tactical',
                'Favorite' => 'nullable|boolean',
                'Rating' => 'nullable|integer|between:0,5',
                'Format' => 'nullable|in:Physical,Digital,Not Set',
                'Notes' => 'nullable|string|max:255',
            ]);
            if ($rowValidator->fails()) {
                return response()->json(['Status' => 'Error', 'Message' => 'The CSV contains invalid game data.'], 422);
            }
        }

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

                $attribute = $key === 'platformtype' ? 'platformType' : $key;
                if (in_array($attribute, ['name', 'rank', 'platformType', 'platform', 'status', 'genre', 'favorite', 'rating', 'format', 'notes'], true)) {
                    $game->$attribute = $value;
                }
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
            "Cache-Control" => "private, no-store, max-age=0",
            "Expires" => "0"
        );
    
        $games = $this->model->allGamesForListByUser( Auth::id() );
        $columns = array('Rank','Name','PlatformType','Platform','Status','Genre','Favorite','Rating','Format','Notes');
    
        $callback = function() use ($games, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
    
            foreach($games as $game) {
                fputcsv($file, array_map([$this, 'safeCsvCell'], [$game->rank, $game->name, $game->platformType, $game->platform, $game->status, $game->genre, $game->favorite, $game->rating, $game->format, $game->notes]));
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    private function safeCsvCell($value): string
    {
        $value = (string) ($value ?? '');
        return preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $value) ? "'".$value : $value;
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
                    if (count($header) !== count($row)) return false;
                    if (count(array_unique($header)) !== count($header)) return false;
                    $record = array_combine($header, $row);
                    if ($record === false) return false;
                    $data[] = $record;
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
        $existingGame = $games->where('userId', Auth::id())->where('rank', '=', $rank)->where('status', '=', $list)->first();
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
            'steamId' => 'required|regex:/^\d{17}$/'
        ];
        $messages = [
            'steamId.regex' => 'Please use the 17 digit numeric Steam ID'
        ];

        $this->validate($request, $rules, $messages);

        $addedGames = array();

        $user = Auth::id();

        $steamId = $request->get('steamId');
        $steam = app(Steam::class);
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
            $game->platformType = 'PC';
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
