<?php
/*
This is the Main Configuration File! Any file using the
maincore as an include/require can access these settings.

Syntax:
Configuration::Set('nameofsetting','setting')
Configuration::Get(''nameofsetting')
Thank Kirth from MMOwned for giving me this idea :P ..
and his configuration class.

Note: Do Not Change the first field in the Set unless
you plan of modifying the declarations.

Subconfiguration files follow the same format, this
includes all files in the Configuration directory
excluding declarations.php and class.php
*/

/* 	++REQUIRED CONFIGURATION CLASS AND SUBCONFIGS++ 																								*/
require_once "class.php";
require_once "sqlconfig.php";
require_once "styleconfig.php";
require_once "modulesconfig.php";
/*	 ++DO NOT DELETE THESE LINES++ 																									*/


/*					-AGREEMENT PAGE-
1 - Users will be redirected to the agreement page upon entering the site  0 - disable 												*/
Configuration::Set('use.agreement',1);


/*					-EMULATOR SELECTION-
0 - Disable Game Acc Functions  1 - ArcEmu  2 - Mangos 																				*/
Configuration::Set('emulator',2);


/*					-REALMLIST-
->Realmlist - Input the realmlist of your server, it will be displayed upon user registration										*/
Configuration::Set('realmlist',		'server.myserver.info'	);

?>