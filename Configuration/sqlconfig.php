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


/*					-WEBSITE DATABASE-
->Input the connection details used for your website database. 																		*/
Configuration::Set('website.db.host',	'localhost'	);
Configuration::Set('website.db.user',	'root'		);
Configuration::Set('website.db.pass',	'bigfoot1'	);
Configuration::Set('website.db.name',	'mycms'		);
Configuration::Set('website.db.prefix',	'arcsite_'	);

/*					-LOGON DATABASE-
->Input the connection details used for your logon/realmd database. 																*/
Configuration::Set('logon.db.host',		'db.wowaddict.info'	);
Configuration::Set('logon.db.user',		'redscust_website'	);
Configuration::Set('logon.db.pass',		'wowaddict22184'	);
Configuration::Set('logon.db.name',		'redscust_realmd'	);

/*					-CHARACTERS DATABASE-
->Input the connection details of the database that contains your character tables													*/
Configuration::Set('characters.db.host',		'db.wowaddict.info'	);
Configuration::Set('characters.db.user',		'redscust_website'	);
Configuration::Set('characters.db.pass',		'wowaddict22184'	);
Configuration::Set('characters.db.name',		'redscust_realm1'	);

/*					-WORLD DATABASE-
->Input the connection details used for your world/mangos database. 																*/
Configuration::Set('world.db.host',		'localhost'	);
Configuration::Set('world.db.user',		'root'		);
Configuration::Set('world.db.pass',		'bigfoot1'	);
Configuration::Set('world.db.name',		'mang'		);

?>