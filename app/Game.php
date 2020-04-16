<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $fillable = ['name','platformType','platform','status','genre','favorite','rating','format','notes'];

    public function allGamesForListByUser( $user, $list, $sortCol='name', $sortOrder='asc') {
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

    public function filteredList( $user, $list, $sortCol='name', $sortOrder='asc', $filters=[], $search = '' ) {

        // Favorites is getting strings but needs booleans. 
        if(count($filters['favorite']) == 1) {
            if($filters['favorite'][0] == "true" ) {
                $favorites = [true];
            } else {
                $favorites = [false];
            }
        } else {
            $favorites = [true,false];
        }

        if( $list == 'backlog' ){
             $games = DB::table('games')
                 ->where('userId', $user )
                 ->where('status', 'backlog')
                 ->whereIn('favorite',$favorites)
                 ->whereIn('status',count($filters['status'])>0 ? $filters['status'] : ['Abandoned','Backlog','Completed','In Progress','Might Play','None','Paused','Replayable','Wishlist','Wont Play'])
                 ->whereIn('platform',count($filters['platform'])>0 ? $filters['platform'] : ['32X','3DO','Acorn Archimedes','Acorn Electron','Amiga','Amiga CD32','Amstrad CPC','Amstrad GX4000','Android','APF-M1000','Apple II','Apple Arcade','Apple Bandai Pippin','Arcade','Atari 2600','Atari 5200','Atari 7800','Atari 8-bit','Atari ST','Bally Astrocade','Battle.Net','BBC Micro','Beamdog','Bethesda Launcher','Big Fish Games','Browser','Calculator','CD-i','CD32X','Coleco Adam','ColecoVision','Commodore 64','Commodore CDTV','Commodore Plus/4','Commodore VIC-20','Cougar Boy','Desura','Discord','DOS','DotEmu','Dragon 32/64','Dreamcast','DSiWare','Emerson Arcadia 2001','Epic Games Launcher','Fairchild Channel F','Family Computer','Famicom Disk System','FM Towns','Fujitsu Micro 7','Gamate','Game &amp; Watch','Game Gear','Game Boy','Game Boy/Color','Game Boy Advance','e-Reader','Game Wave Family Entertainment System','GameCube','GameFly','GamersGate','GameStop PC','Games For Windows','Game.com','Genesis / Mega Drive','GetGames','Gizmondo','GOG.com','Google Stadia','GP2X Wiz','Green Man Gaming','HTC Vive','Humble Bundle Store','HyperScan','IndieCity','Intellivision','iOS','iPad','iPod','iPhone','itch.io','Jaguar','Jaguar CD','LaserActive','Linux','Lynx','Mac','Magnavox Odyssey','Master System','Microvision','Miscellaneous','Mobile','MSX','N-Gage','NEC PC-8801','NEC PC-9801','Neo Geo','Neo Geo CD','Neo Geo Pocket/Color','Nintendo 3DS','3DS Downloads','Nintendo DS','Nintendo 64','Nintendo 64DD','Nintendo Entertainment System','Nintendo Switch','Switch Downloads','Nuon','Nuuvem','Oculus Rift','Odyssey² / Videopac','OnLive','Origin','Other','OUYA','Pandora','PC','PC Downloads','PC-50X','PC-FX','Pinball','PlayStation','PlayStation 2','PlayStation 3','PlayStation 4','PlayStation Mobile','PlayStation Network','PSOne Classics','PS2 Classics','PlayStation minis','PlayStation Portable','PlayStation Vita','PlayStation VR','Plug-and-Play','PocketStation','Pokémon Mini','R-Zone','RCA Studio II','Rockstar Games Launcher','SAM Coupe','Saturn','Sega CD','Sega Pico','Sega SG-1000','Sharp X1','Sharp X68000','Steam','Super Nintendo Entertainment System','SuperGrafx','TI-99/4A','Tiger Handhelds','TurboDuo','TurboGrafx-16','TurboGrafx-CD','TRS-80 Color Computer','Twitch','Uplay','Vectrex','Virtual Boy','Virtual Console (Wii)','Virtual Console (3DS)','Watara Supervision','Wii','WiiWare','Wii U','Wii U Downloads','Virtual Console (WiiU)','Windows Phone 7','Windows Store','WonderSwan/Color','XaviXPORT','Xbox','Xbox 360','Xbox Game Pass','Xbox LIVE Arcade','XNA Indie Games','Xbox 360 Games on Demand','Xbox One','Xbox One Downloads','Zeebo','Zune','ZX Spectrum'])
                 ->whereIn('platformType',count($filters['platformType'])>0 ? $filters['platformType'] : ['PC', 'Console', 'Other'])
                 ->whereIn('format',count($filters['format'])>0? $filters['format'] : ['Physical', 'Digital', 'Not Set'])
                 ->whereIn('genre',count($filters['genre'])>0 ? $filters['genre'] : ["Adventure", "Fighting", "Indie", "Music", "Not Set", "Other", "Quiz/Trivia", "Role-playing", "Shooter", "Simulator", "Strategy", "Tactical"])
                 ->whereIn('rating',count($filters['rating'])>0 ? $filters['rating'] : ['0','1','2','3','4','5'])
                 ->where('name','like', '%'.$search.'%')
                 ->orderBy($sortCol, $sortOrder)
                 ->get();
         } else if($list == 'wishlist') {
             $games = DB::table('games')
                 ->where('userId', $user )
                 ->where('status', 'wishlist')
                 ->whereIn('favorite',$favorites)
                 ->whereIn('status',count($filters['status'])>0 ? $filters['status'] : ['Abandoned','Backlog','Completed','In Progress','Might Play','None','Paused','Replayable','Wishlist','Wont Play'])
                 ->whereIn('platform',count($filters['platform'])>0 ? $filters['platform'] : ['32X','3DO','Acorn Archimedes','Acorn Electron','Amiga','Amiga CD32','Amstrad CPC','Amstrad GX4000','Android','APF-M1000','Apple II','Apple Arcade','Apple Bandai Pippin','Arcade','Atari 2600','Atari 5200','Atari 7800','Atari 8-bit','Atari ST','Bally Astrocade','Battle.Net','BBC Micro','Beamdog','Bethesda Launcher','Big Fish Games','Browser','Calculator','CD-i','CD32X','Coleco Adam','ColecoVision','Commodore 64','Commodore CDTV','Commodore Plus/4','Commodore VIC-20','Cougar Boy','Desura','Discord','DOS','DotEmu','Dragon 32/64','Dreamcast','DSiWare','Emerson Arcadia 2001','Epic Games Launcher','Fairchild Channel F','Family Computer','Famicom Disk System','FM Towns','Fujitsu Micro 7','Gamate','Game &amp; Watch','Game Gear','Game Boy','Game Boy/Color','Game Boy Advance','e-Reader','Game Wave Family Entertainment System','GameCube','GameFly','GamersGate','GameStop PC','Games For Windows','Game.com','Genesis / Mega Drive','GetGames','Gizmondo','GOG.com','Google Stadia','GP2X Wiz','Green Man Gaming','HTC Vive','Humble Bundle Store','HyperScan','IndieCity','Intellivision','iOS','iPad','iPod','iPhone','itch.io','Jaguar','Jaguar CD','LaserActive','Linux','Lynx','Mac','Magnavox Odyssey','Master System','Microvision','Miscellaneous','Mobile','MSX','N-Gage','NEC PC-8801','NEC PC-9801','Neo Geo','Neo Geo CD','Neo Geo Pocket/Color','Nintendo 3DS','3DS Downloads','Nintendo DS','Nintendo 64','Nintendo 64DD','Nintendo Entertainment System','Nintendo Switch','Switch Downloads','Nuon','Nuuvem','Oculus Rift','Odyssey² / Videopac','OnLive','Origin','Other','OUYA','Pandora','PC','PC Downloads','PC-50X','PC-FX','Pinball','PlayStation','PlayStation 2','PlayStation 3','PlayStation 4','PlayStation Mobile','PlayStation Network','PSOne Classics','PS2 Classics','PlayStation minis','PlayStation Portable','PlayStation Vita','PlayStation VR','Plug-and-Play','PocketStation','Pokémon Mini','R-Zone','RCA Studio II','Rockstar Games Launcher','SAM Coupe','Saturn','Sega CD','Sega Pico','Sega SG-1000','Sharp X1','Sharp X68000','Steam','Super Nintendo Entertainment System','SuperGrafx','TI-99/4A','Tiger Handhelds','TurboDuo','TurboGrafx-16','TurboGrafx-CD','TRS-80 Color Computer','Twitch','Uplay','Vectrex','Virtual Boy','Virtual Console (Wii)','Virtual Console (3DS)','Watara Supervision','Wii','WiiWare','Wii U','Wii U Downloads','Virtual Console (WiiU)','Windows Phone 7','Windows Store','WonderSwan/Color','XaviXPORT','Xbox','Xbox 360','Xbox Game Pass','Xbox LIVE Arcade','XNA Indie Games','Xbox 360 Games on Demand','Xbox One','Xbox One Downloads','Zeebo','Zune','ZX Spectrum'])
                 ->whereIn('platformType',count($filters['platformType'])>0 ? $filters['platformType'] : ['PC', 'Console', 'Other'])
                 ->whereIn('format',count($filters['format'])>0? $filters['format'] : ['Physical', 'Digital', 'Not Set'])
                 ->whereIn('genre',count($filters['genre'])>0 ? $filters['genre'] : ["Adventure", "Fighting", "Indie", "Music", "Not Set", "Other", "Quiz/Trivia", "Role-playing", "Shooter", "Simulator", "Strategy", "Tactical"])
                 ->whereIn('rating',count($filters['rating'])>0 ? $filters['rating'] : ['0','1','2','3','4','5'])
                 ->where('name','like', '%'.$search.'%')
                 ->orderBy($sortCol, $sortOrder)
                 ->get();
         } else {
             $games = DB::table('games')
                 ->where('userId', $user )
                 ->whereIn('favorite',$favorites)
                 ->whereIn('status',count($filters['status'])>0 ? $filters['status'] : ['Abandoned','Backlog','Completed','In Progress','Might Play','None','Paused','Replayable','Wishlist','Wont Play'])
                 ->whereIn('platform',count($filters['platform'])>0 ? $filters['platform'] : ['32X','3DO','Acorn Archimedes','Acorn Electron','Amiga','Amiga CD32','Amstrad CPC','Amstrad GX4000','Android','APF-M1000','Apple II','Apple Arcade','Apple Bandai Pippin','Arcade','Atari 2600','Atari 5200','Atari 7800','Atari 8-bit','Atari ST','Bally Astrocade','Battle.Net','BBC Micro','Beamdog','Bethesda Launcher','Big Fish Games','Browser','Calculator','CD-i','CD32X','Coleco Adam','ColecoVision','Commodore 64','Commodore CDTV','Commodore Plus/4','Commodore VIC-20','Cougar Boy','Desura','Discord','DOS','DotEmu','Dragon 32/64','Dreamcast','DSiWare','Emerson Arcadia 2001','Epic Games Launcher','Fairchild Channel F','Family Computer','Famicom Disk System','FM Towns','Fujitsu Micro 7','Gamate','Game &amp; Watch','Game Gear','Game Boy','Game Boy/Color','Game Boy Advance','e-Reader','Game Wave Family Entertainment System','GameCube','GameFly','GamersGate','GameStop PC','Games For Windows','Game.com','Genesis / Mega Drive','GetGames','Gizmondo','GOG.com','Google Stadia','GP2X Wiz','Green Man Gaming','HTC Vive','Humble Bundle Store','HyperScan','IndieCity','Intellivision','iOS','iPad','iPod','iPhone','itch.io','Jaguar','Jaguar CD','LaserActive','Linux','Lynx','Mac','Magnavox Odyssey','Master System','Microvision','Miscellaneous','Mobile','MSX','N-Gage','NEC PC-8801','NEC PC-9801','Neo Geo','Neo Geo CD','Neo Geo Pocket/Color','Nintendo 3DS','3DS Downloads','Nintendo DS','Nintendo 64','Nintendo 64DD','Nintendo Entertainment System','Nintendo Switch','Switch Downloads','Nuon','Nuuvem','Oculus Rift','Odyssey² / Videopac','OnLive','Origin','Other','OUYA','Pandora','PC','PC Downloads','PC-50X','PC-FX','Pinball','PlayStation','PlayStation 2','PlayStation 3','PlayStation 4','PlayStation Mobile','PlayStation Network','PSOne Classics','PS2 Classics','PlayStation minis','PlayStation Portable','PlayStation Vita','PlayStation VR','Plug-and-Play','PocketStation','Pokémon Mini','R-Zone','RCA Studio II','Rockstar Games Launcher','SAM Coupe','Saturn','Sega CD','Sega Pico','Sega SG-1000','Sharp X1','Sharp X68000','Steam','Super Nintendo Entertainment System','SuperGrafx','TI-99/4A','Tiger Handhelds','TurboDuo','TurboGrafx-16','TurboGrafx-CD','TRS-80 Color Computer','Twitch','Uplay','Vectrex','Virtual Boy','Virtual Console (Wii)','Virtual Console (3DS)','Watara Supervision','Wii','WiiWare','Wii U','Wii U Downloads','Virtual Console (WiiU)','Windows Phone 7','Windows Store','WonderSwan/Color','XaviXPORT','Xbox','Xbox 360','Xbox Game Pass','Xbox LIVE Arcade','XNA Indie Games','Xbox 360 Games on Demand','Xbox One','Xbox One Downloads','Zeebo','Zune','ZX Spectrum'])
                 ->whereIn('platformType',count($filters['platformType'])>0 ? $filters['platformType'] : ['PC', 'Console', 'Other'])
                 ->whereIn('format',count($filters['format'])>0? $filters['format'] : ['Physical', 'Digital', 'Not Set'])
                 ->whereIn('genre',count($filters['genre'])>0 ? $filters['genre'] : ["Adventure", "Fighting", "Indie", "Music", "Not Set", "Other", "Quiz/Trivia", "Role-playing", "Shooter", "Simulator", "Strategy", "Tactical"])
                 ->whereIn('rating',count($filters['rating'])>0 ? $filters['rating'] : ['0','1','2','3','4','5'])
                 ->where('name','like', '%'.$search.'%')
                 ->orderBy($sortCol, $sortOrder)
                 ->get();
         }
 
         return $games;
     }
}
