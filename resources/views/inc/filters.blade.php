<ul class="nav flex-column">
    <h3>Filters</h3>
    <div class="btn-group mr-2">
        <button type="button" value="" class="btn btn-sm btn-outline-secondary clearFilters">Clear Filters</button>
    </div>
    <!--Name Search-->
    <li class="nav-item">
        Search Your Games:
    </li>
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <input type="text" id="inventorySearch" name="searchInventory" value="" class="" />
        </li>
    </ul>
    <!--Favorites-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="favoriteFilters">Favorites:</span>
    </li>
    <ul class="nav flex-column mb-2 favoriteFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="favorite" value="true" class="filterItem" /> Favorited
        </li>
        <li>    
            <input type="checkbox" name="favorite" value="false" class="filterItem"/> Not Favorited
        </li>
    </ul>
    <!--Statuses-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="statusFilters">Status:</span>
    </li>
    <ul class="nav flex-column mb-2 statusFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="status" value="Abandoned" class="filterItem" /> Abandoned
        </li>
        <li>    
            <input type="checkbox" name="status" value="Backlog" class="filterItem"/> Backlog
        </li>
        <li class="nav-item">
            <input type="checkbox" name="status" value="Completed" class="filterItem" /> Completed
        </li>
        <li>    
            <input type="checkbox" name="status" value="In Progress" class="filterItem"/> In Progress
        </li>
        <li class="nav-item">
            <input type="checkbox" name="status" value="Might Play" class="filterItem" /> Might Play
        </li>
        <li>    
            <input type="checkbox" name="status" value="None" class="filterItem"/> None
        </li>
        <li class="nav-item">
            <input type="checkbox" name="status" value="Paused" class="filterItem" /> Paused
        </li>
        <li>    
            <input type="checkbox" name="status" value="Replayable" class="filterItem"/> Replayable
        </li>
        <li class="nav-item">
            <input type="checkbox" name="status" value="Wishlist" class="filterItem" /> Wishlist
        </li>
        <li>    
            <input type="checkbox" name="status" value="Wont Play" class="filterItem"/> Wont Play
        </li>
    </ul>
    <!--Platforms-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="platformFilters">Platform:</span>
    </li>
    <ul class="nav flex-column mb-2 platformFilters hide-on-load">
    <li class="nav-item">
        <input type="checkbox" name="platform" value="32X" class="filterItem" /> 32X
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="3DO" class="filterItem" /> 3DO
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Acorn Archimedes" class="filterItem" /> Acorn Archimedes
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Acorn Electron" class="filterItem" /> Acorn Electron
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Amiga" class="filterItem" /> Amiga
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Amiga CD32" class="filterItem" /> Amiga CD32
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Amstrad CPC" class="filterItem" /> Amstrad CPC
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Amstrad GX4000" class="filterItem" /> Amstrad GX4000
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Android" class="filterItem" /> Android
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="APF-M1000" class="filterItem" /> APF-M1000
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Apple II" class="filterItem" /> Apple II
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Apple Arcade" class="filterItem" /> Apple Arcade
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Apple Bandai Pippin" class="filterItem" /> Apple Bandai Pippin
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Arcade" class="filterItem" /> Arcade
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Atari 2600" class="filterItem" /> Atari 2600
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Atari 5200" class="filterItem" /> Atari 5200
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Atari 7800" class="filterItem" /> Atari 7800
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Atari 8-bit" class="filterItem" /> Atari 8-bit
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Atari ST" class="filterItem" /> Atari ST
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Bally Astrocade" class="filterItem" /> Bally Astrocade
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Battle.Net" class="filterItem" /> Battle.Net
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="BBC Micro" class="filterItem" /> BBC Micro
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Beamdog" class="filterItem" /> Beamdog
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Bethesda Launcher" class="filterItem" /> Bethesda Launcher
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Big Fish Games" class="filterItem" /> Big Fish Games
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Browser" class="filterItem" /> Browser
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Calculator" class="filterItem" /> Calculator
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="CD-i" class="filterItem" /> CD-i
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="CD32X" class="filterItem" /> CD32X
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Coleco Adam" class="filterItem" /> Coleco Adam
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="ColecoVision" class="filterItem" /> ColecoVision
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Commodore 64" class="filterItem" /> Commodore 64
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Commodore CDTV" class="filterItem" /> Commodore CDTV
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Commodore Plus/4" class="filterItem" /> Commodore Plus/4
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Commodore VIC-20" class="filterItem" /> Commodore VIC-20
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Cougar Boy" class="filterItem" /> Cougar Boy
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Desura" class="filterItem" /> Desura
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Discord" class="filterItem" /> Discord
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="DOS" class="filterItem" /> DOS
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="DotEmu" class="filterItem" /> DotEmu
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Dragon 32/64" class="filterItem" /> Dragon 32/64
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Dreamcast" class="filterItem" /> Dreamcast
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="DSiWare" class="filterItem" /> DSiWare
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Emerson Arcadia 2001" class="filterItem" /> Emerson Arcadia 2001
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Epic Games Launcher" class="filterItem" /> Epic Games Launcher
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Fairchild Channel F" class="filterItem" /> Fairchild Channel F
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Family Computer" class="filterItem" /> Family Computer
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Famicom Disk System" class="filterItem" /> Famicom Disk System
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="FM Towns" class="filterItem" /> FM Towns
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Fujitsu Micro 7" class="filterItem" /> Fujitsu Micro 7
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Gamate" class="filterItem" /> Gamate
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game &amp; Watch" class="filterItem" /> Game &amp; Watch
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game Gear" class="filterItem" /> Game Gear
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game Boy" class="filterItem" /> Game Boy
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game Boy/Color" class="filterItem" /> Game Boy/Color
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game Boy Advance" class="filterItem" /> Game Boy Advance
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="e-Reader" class="filterItem" /> e-Reader
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game Wave Family Entertainment System" class="filterItem" /> Game Wave Family Entertainment System
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GameCube" class="filterItem" /> GameCube
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GameFly" class="filterItem" /> GameFly
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GamersGate" class="filterItem" /> GamersGate
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GameStop PC" class="filterItem" /> GameStop PC
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Games For Windows" class="filterItem" /> Games For Windows
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Game.com" class="filterItem" /> Game.com
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Genesis / Mega Drive" class="filterItem" /> Genesis / Mega Drive
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GetGames" class="filterItem" /> GetGames
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Gizmondo" class="filterItem" /> Gizmondo
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GOG.com" class="filterItem" /> GOG.com
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Google Stadia" class="filterItem" /> Google Stadia
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="GP2X Wiz" class="filterItem" /> GP2X Wiz
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Green Man Gaming" class="filterItem" /> Green Man Gaming
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="HTC Vive" class="filterItem" /> HTC Vive
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Humble Bundle Store" class="filterItem" /> Humble Bundle Store
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="HyperScan" class="filterItem" /> HyperScan
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="IndieCity" class="filterItem" /> IndieCity
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Intellivision" class="filterItem" /> Intellivision
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="iOS" class="filterItem" /> iOS
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="iPad" class="filterItem" /> iPad
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="iPod" class="filterItem" /> iPod
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="iPhone" class="filterItem" /> iPhone
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="itch.io" class="filterItem" /> itch.io
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Jaguar" class="filterItem" /> Jaguar
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Jaguar CD" class="filterItem" /> Jaguar CD
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="LaserActive" class="filterItem" /> LaserActive
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Linux" class="filterItem" /> Linux
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Lynx" class="filterItem" /> Lynx
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Mac" class="filterItem" /> Mac
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Magnavox Odyssey" class="filterItem" /> Magnavox Odyssey
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Master System" class="filterItem" /> Master System
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Microvision" class="filterItem" /> Microvision
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Miscellaneous" class="filterItem" /> Miscellaneous
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Mobile" class="filterItem" /> Mobile
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="MSX" class="filterItem" /> MSX
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="N-Gage" class="filterItem" /> N-Gage
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="NEC PC-8801" class="filterItem" /> NEC PC-8801
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="NEC PC-9801" class="filterItem" /> NEC PC-9801
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Neo Geo" class="filterItem" /> Neo Geo
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Neo Geo CD" class="filterItem" /> Neo Geo CD
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Neo Geo Pocket/Color" class="filterItem" /> Neo Geo Pocket/Color
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo 3DS" class="filterItem" /> Nintendo 3DS
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="3DS Downloads" class="filterItem" /> 3DS Downloads
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo DS" class="filterItem" /> Nintendo DS
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo 64" class="filterItem" /> Nintendo 64
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo 64DD" class="filterItem" /> Nintendo 64DD
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo Entertainment System" class="filterItem" /> Nintendo Entertainment System
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nintendo Switch" class="filterItem" /> Nintendo Switch
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Switch Downloads" class="filterItem" /> Switch Downloads
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nuon" class="filterItem" /> Nuon
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Nuuvem" class="filterItem" /> Nuuvem
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Oculus Rift" class="filterItem" /> Oculus Rift
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Odyssey² / Videopac" class="filterItem" /> Odyssey² / Videopac
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="OnLive" class="filterItem" /> OnLive
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Origin" class="filterItem" /> Origin
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Other'" class="filterItem" /> Other'
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="OUYA" class="filterItem" /> OUYA
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Pandora" class="filterItem" /> Pandora
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PC" class="filterItem" /> PC
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PC Downloads" class="filterItem" /> PC Downloads
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PC-50X" class="filterItem" /> PC-50X
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PC-FX" class="filterItem" /> PC-FX
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Pinball" class="filterItem" /> Pinball
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation" class="filterItem" /> PlayStation
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation 2" class="filterItem" /> PlayStation 2
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation 3" class="filterItem" /> PlayStation 3
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation 4" class="filterItem" /> PlayStation 4
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation Mobile" class="filterItem" /> PlayStation Mobile
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation Network" class="filterItem" /> PlayStation Network
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PSOne Classics" class="filterItem" /> PSOne Classics
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PS2 Classics" class="filterItem" /> PS2 Classics
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation minis" class="filterItem" /> PlayStation minis
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation Portable" class="filterItem" /> PlayStation Portable
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation Vita" class="filterItem" /> PlayStation Vita
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PlayStation VR" class="filterItem" /> PlayStation VR
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Plug-and-Play" class="filterItem" /> Plug-and-Play
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="PocketStation" class="filterItem" /> PocketStation
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Pokémon Mini" class="filterItem" /> Pokémon Mini
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="R-Zone" class="filterItem" /> R-Zone
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="RCA Studio II" class="filterItem" /> RCA Studio II
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Rockstar Games Launcher" class="filterItem" /> Rockstar Games Launcher
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="SAM Coupe" class="filterItem" /> SAM Coupe
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Saturn" class="filterItem" /> Saturn
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Sega CD" class="filterItem" /> Sega CD
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Sega Pico" class="filterItem" /> Sega Pico
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Sega SG-1000" class="filterItem" /> Sega SG-1000
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Sharp X1" class="filterItem" /> Sharp X1
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Sharp X68000" class="filterItem" /> Sharp X68000
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Steam" class="filterItem" /> Steam
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Super Nintendo Entertainment System" class="filterItem" /> Super Nintendo Entertainment System
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="SuperGrafx" class="filterItem" /> SuperGrafx
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="TI-99/4A" class="filterItem" /> TI-99/4A
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Tiger Handhelds" class="filterItem" /> Tiger Handhelds
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="TurboDuo" class="filterItem" /> TurboDuo
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="TurboGrafx-16" class="filterItem" /> TurboGrafx-16
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="TurboGrafx-CD" class="filterItem" /> TurboGrafx-CD
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="TRS-80 Color Computer" class="filterItem" /> TRS-80 Color Computer
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Twitch" class="filterItem" /> Twitch
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Uplay" class="filterItem" /> Uplay
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Vectrex" class="filterItem" /> Vectrex
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Virtual Boy" class="filterItem" /> Virtual Boy
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Virtual Console (Wii)" class="filterItem" /> Virtual Console (Wii)
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Virtual Console (3DS)" class="filterItem" /> Virtual Console (3DS)
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Watara Supervision" class="filterItem" /> Watara Supervision
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Wii" class="filterItem" /> Wii
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="WiiWare" class="filterItem" /> WiiWare
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Wii U" class="filterItem" /> Wii U
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Wii U Downloads" class="filterItem" /> Wii U Downloads
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Virtual Console (WiiU)" class="filterItem" /> Virtual Console (WiiU)
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Windows Phone 7" class="filterItem" /> Windows Phone 7
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Windows Store" class="filterItem" /> Windows Store
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="WonderSwan/Color" class="filterItem" /> WonderSwan/Color
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="XaviXPORT" class="filterItem" /> XaviXPORT
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox" class="filterItem" /> Xbox
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox 360" class="filterItem" /> Xbox 360
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox Game Pass" class="filterItem" /> Xbox Game Pass
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox LIVE Arcade" class="filterItem" /> Xbox LIVE Arcade
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="XNA Indie Games" class="filterItem" /> XNA Indie Games
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox 360 Games on Demand" class="filterItem" /> Xbox 360 Games on Demand
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox One" class="filterItem" /> Xbox One
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Xbox One Downloads" class="filterItem" /> Xbox One Downloads
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Zeebo" class="filterItem" /> Zeebo
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="Zune" class="filterItem" /> Zune
    </li>
    <li class="nav-item">
        <input type="checkbox" name="platform" value="ZX Spectrum" class="filterItem" /> ZX Spectrum
    </li>
    </ul>
    <!--Platform Types-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="platformTypeFilters">Platform Type:</span>
    </li>
    <ul class="nav flex-column mb-2 platformTypeFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="platformType" value="other" class="filterItem" /> Other
        </li>
        <li>    
            <input type="checkbox" name="platformType" value="Console" class="filterItem"/> Console
        </li>
        <li>    
            <input type="checkbox" name="platformType" value="PC" class="filterItem"/> PC
        </li>
    </ul>
    <!--Format-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="formatFilters">Format:</span>
    </li>
    <ul class="nav flex-column mb-2 formatFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="format" value="Not Set" class="filterItem" /> Not Set
        </li>
        <li>    
            <input type="checkbox" name="format" value="Physical" class="filterItem"/> Physical
        </li>
        <li>    
            <input type="checkbox" name="format" value="Digital" class="filterItem"/> Digital
        </li>
    </ul>
    <!--Genre-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="genreFilters">Genre:</span>
    </li>
    <ul class="nav flex-column mb-2 genreFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="genre" value="Adventure" class="filterItem" /> Adventure
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Fighting" class="filterItem"/> Fighting
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Indie" class="filterItem"/> Indie
        </li>
        <li class="nav-item">
            <input type="checkbox" name="genre" value="Music" class="filterItem" /> Music
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Not Set" class="filterItem"/> Not Set
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Other" class="filterItem"/> Other
        </li>
        <li class="nav-item">
            <input type="checkbox" name="genre" value="Quiz/Trivia" class="filterItem" /> Quiz/Trivia
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Role-playing" class="filterItem"/> Role-playing
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Shooter" class="filterItem"/> Shooter
        </li>
        <li class="nav-item">
            <input type="checkbox" name="genre" value="Simulator" class="filterItem" /> Simulator
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Strategy" class="filterItem"/> Strategy
        </li>
        <li>    
            <input type="checkbox" name="genre" value="Tactical" class="filterItem"/> Tactical
        </li>
    </ul>
    <!--Rating-->
    <li class="nav-item">
        <span class="filterHeader filterClosed" data-section="ratingFilters">Rating:</span>
    </li>
    <ul class="nav flex-column mb-2 ratingFilters hide-on-load">
        <li class="nav-item">
            <input type="checkbox" name="rating" value="0" class="filterItem" /> 0 Stars
        </li>
        <li>    
            <input type="checkbox" name="rating" value="1" class="filterItem"/> 1 Star
        </li>
        <li>    
            <input type="checkbox" name="rating" value="2" class="filterItem"/> 2 Stars
        </li>
        <li class="nav-item">
            <input type="checkbox" name="rating" value="3" class="filterItem" /> 3 Stars
        </li>
        <li class="nav-item">
            <input type="checkbox" name="rating" value="4" class="filterItem" /> 4 Stars
        </li>
        <li class="nav-item">
            <input type="checkbox" name="rating" value="5" class="filterItem" /> 5 Stars
        </li>
    </ul>







    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="file"></span>
        
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="shopping-cart"></span>
        
        </a>
    </li>
    </ul>

    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
    <span></span>
    <a class="d-flex align-items-center text-muted" href="#">
        <span data-feather="plus-circle"></span>
    </a>
    </h6>
    <ul class="nav flex-column mb-2">
    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="file-text"></span>
        
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="file-text"></span>
        
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="file-text"></span>
        
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
        <span data-feather="file-text"></span>
        
        </a>
    </li>
</ul>