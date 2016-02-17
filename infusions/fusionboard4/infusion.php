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

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}

$inf_title = $locale ['fb4_title'];
$inf_description = $locale ['fb4_desc'];
$inf_version = "4.02";
$inf_developer = "php-Invent Team";
$inf_email = "ianunruh@gmail.com";
$inf_weburl = "http://www.php-invent.com";

$inf_folder = "fusionboard4";

$inf_adminpanel [1] = array ("title" => $locale ['fb4_title'], "image" => "image.gif", "panel" => "admin.php", "rights" => "FB4" );
?>