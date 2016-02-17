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


/*					-FOOTER NAVIGATION LINKS-
->Add links by increasing the count and making a new config line of the same format with the next trailing number in the name.
Ex: Configuration::Set('navbar.link7',	'Unstuck Tool'	.'|'.	'Player Tools/unstuck.php'		);
->To exclude links set them blank or just reduce the link count.
Ex: Configuration::Set('navbar.link1',	''		.'|'.	''			);																*/
Configuration::Set('navbar.link.count',6);
Configuration::Set('navbar.link1',	'Home'		.'|'.	'news.php'			);
Configuration::Set('navbar.link2',	'FAQ'		.'|'.	'faq.php'			);
Configuration::Set('navbar.link3',	'Forums'	.'|'.	'forum/index.php'	);
Configuration::Set('navbar.link4',	'Downloads'	.'|'.	'downloads.php'		);
Configuration::Set('navbar.link5',	'Search'	.'|'.	'search.php'		);
Configuration::Set('navbar.link6',	'Contact'	.'|'.	'contact.php'		);


/*					-NeonBlue_V7 THEME-
->Flash Banner - 0 to disable, 1 to enable
	+Location - Enter the location of the banner from the Base Dir
->Lettering Message - Enter the lettering message to be displayed
	+Type - 1 for woltk style letters, 2 for tbc, 3 for oversized classic letters
	+Begin Spaces - Amount of spaces added before the message to center it															*/
Configuration::Set('use.flash.banner',1);
Configuration::Set('flash.banner.location',	'images/banners/logo.swf'	);
Configuration::Set('lettering.message',		'My Server'					);
Configuration::Set('lettering.type',1);
Configuration::Set('lettering.begin.spaces',11);


/*					-NON FLASH BANNER-
->Bloody Theme Banner Path - The path of the banner for this theme from the Base Dir
	+Header Path - The path of the left navbar header for this theme from the Base Dir
->Neon Theme Banner Path - If use flash banner is disabled, set the path of the graphic banner here. 								*/
Configuration::Set('bloody.theme.banner.path',	'images/banners/main.png'	);
Configuration::Set('bloody.theme.header.path',	'images/banners/header.png'	);
Configuration::Set('neon.theme.banner.path',	''							);

?>