<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if (! defined ( "IN_FUSION" )) {
	die ( "Access Denied" );
}

if (! defined ( "DB_INFUSION_TABLE" )) {
	define ( "DB_INFUSION_TABLE", DB_PREFIX . "fb_forums" );
}
?>