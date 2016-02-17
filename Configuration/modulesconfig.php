<?php
/*
This is a Sub-Configuration File! Any file using the
maincore as an include/require can access these settings.

Syntax:
Configuration::Set('nameofsetting','setting')
Configuration::Get(''nameofsetting')
Thank Kirth from MMOwned for giving me this idea :P ..
and his configuration class.

Note: Do Not Change the first field in the Set unless
you plan of modifying the declarations.
*/


/*					-SERVER STATUS PAGE-
->Stats XML Location - Location of the stats.xml file dumped by your server (can be http address)
Note : This page is designed for ArcEmu																								*/
Configuration::Set('stats.xml.location',	'stats.xml'	);


/*					-GAME ACCOUNT REGISTRATION-
->Game Account Level - Access level given to acount upon creation
Note : 0 is for player access level, you should stick with this on a regular server													*/
Configuration::Set('game.acc.level',	'0'	);

?>