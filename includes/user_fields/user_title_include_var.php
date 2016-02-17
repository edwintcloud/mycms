<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if (!defined("IN_FUSION")) { die("Access Denied"); }

// Check if locale file is available matching the current site locale setting.
if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS."fusionboard4/locale/English.php";
}

$user_field_name = $locale['uf_title'];
$user_field_desc = $locale['uf_title_desc'];
$user_field_dbname = "user_title";
$user_field_group = 2;
$user_field_dbinfo = "VARCHAR(100) NOT NULL DEFAULT ''";
?>