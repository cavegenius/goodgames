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
    public function add(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'platform' => 'required',
            'platformType' => 'required',
            'format' => 'required',
            'owned' => 'required'
        ]);

        $user = Auth::id();
        $game = new Game;
        $game->userId = $user;
        foreach( (array)$request->all() as $key=>$value ) {
            if( $key == '_token') { continue; }
            if( $key == 'notes' && !$value ) { $value='';}
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
            if( $key == 'notes' && !$value ) { $value='';}
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

        if($game->delete()) {
            $result = json_encode(['Status' => 'Success', 'Message' => 'Game Deleted Successfully']);
        } else {
            $result = json_encode(['Status' => 'Error', 'Message' => 'An Error has Occurred']);
        }

        return $result;
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
            $game->owned = strtolower($gamesArray[$i]['Status']) != 'wishlist' ? true : false;
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

    public function get_platform_list() {
        $platforms = ['32X','3DO','Acorn Archimedes','Acorn Electron','Amiga','Amiga CD32','Amstrad CPC','Amstrad GX4000','Android','APF-M1000','Apple II','Apple Arcade','Apple Bandai Pippin','Arcade','Atari 2600','Atari 5200','Atari 7800','Atari 8-bit','Atari ST','Bally Astrocade','Battle.Net','BBC Micro','Beamdog','Bethesda Launcher','Big Fish Games','Browser','Calculator','CD-i','CD32X','Coleco Adam','ColecoVision','Commodore 64','Commodore CDTV','Commodore Plus/4','Commodore VIC-20','Cougar Boy','Desura','Discord','DOS','DotEmu','Dragon 32/64','Dreamcast','DSiWare','Emerson Arcadia 2001','Epic Games Launcher','Fairchild Channel F','Family Computer','Famicom Disk System','FM Towns','Fujitsu Micro 7','Gamate','Game &amp; Watch','Game Gear','Game Boy','Game Boy/Color','Game Boy Advance','e-Reader','Game Wave Family Entertainment System','GameCube','GameFly','GamersGate','GameStop PC','Games For Windows','Game.com','Genesis / Mega Drive','GetGames','Gizmondo','GOG.com','Google Stadia','GP2X Wiz','Green Man Gaming','HTC Vive','Humble Bundle Store','HyperScan','IndieCity','Intellivision','iOS','iPad','iPod','iPhone','itch.io','Jaguar','Jaguar CD','LaserActive','Linux','Lynx','Mac','Magnavox Odyssey','Master System','Microvision','Miscellaneous','Mobile','MSX','N-Gage','NEC PC-8801','NEC PC-9801','Neo Geo','Neo Geo CD','Neo Geo Pocket/Color','Nintendo 3DS','3DS Downloads','Nintendo DS','Nintendo 64','Nintendo 64DD','Nintendo Entertainment System','Nintendo Switch','Switch Downloads','Nuon','Nuuvem','Oculus Rift','Odyssey² / Videopac','OnLive','Origin','OUYA','Pandora','PC','PC Downloads','PC-50X','PC-FX','Pinball','PlayStation','PlayStation 2','PlayStation 3','PlayStation 4','PlayStation Mobile','PlayStation Network','PSOne Classics','PS2 Classics','PlayStation minis','PlayStation Portable','PlayStation Vita','PlayStation VR','Plug-and-Play','PocketStation','Pokémon Mini','R-Zone','RCA Studio II','Rockstar Games Launcher','Super A\'Can','SAM Coupé','Saturn','Sega CD','Sega Pico','Sega SG-1000','Sharp X1','Sharp X68000','Steam','Super Nintendo Entertainment System','SuperGrafx','TI-99/4A','Tiger Handhelds','TurboDuo','TurboGrafx-16','TurboGrafx-CD','TRS-80 Color Computer','Twitch','Uplay','Vectrex','Virtual Boy','Virtual Console (Wii)','Virtual Console (3DS)','Watara Supervision','Wii','WiiWare','Wii U','Wii U Downloads','Virtual Console (WiiU)','Windows Phone 7','Windows Store','WonderSwan/Color','XaviXPORT','Xbox','Xbox 360','Xbox Game Pass','Xbox LIVE Arcade','XNA Indie Games','Xbox 360 Games on Demand','Xbox One','Xbox One Downloads','Zeebo','Zune','ZX Spectrum'];

        $response['Platforms'] = $platforms;
        $response['Status'] = 'Success';

        return json_encode($response); 
    }

}