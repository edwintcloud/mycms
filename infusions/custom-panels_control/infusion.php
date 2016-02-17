<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| CVS Version: 1.00 RC1
| Author: WEC
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

include INFUSIONS."custom-panels_control/infusion_db.php";

// Check if locale file is available matching the current site locale setting.
if (file_exists(INFUSIONS."custom-panels_control/locale/".$settings['locale'].".php")) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS."custom-panels_control/locale/".$settings['locale'].".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS."custom-panels_control/locale/English.php";
}

// Infusion general information
$inf_title = $locale['cpac_title'];
$inf_description = $locale['cpac_desc'];
$inf_version = "1.00";
$inf_developer = "WEC";
$inf_email = "wechallenge@lycos.co.uk";
$inf_weburl = "";

$inf_folder = "custom-panels_control";

// Delete any items not required here.
$inf_newtable[1] = "";
$inf_insertdbrow[1] = "";
$inf_droptable[1] = "";
$inf_altertable[1] = "";
$inf_deldbrow[1] = "";

$inf_adminpanel[1] = array(
	"title" => $locale['cpac_title'],
	"image" => "infusion_panel.gif",
	"panel" => "custom-panels_control_admin.php",
	"rights" => "CPAC"
);

/* Disabled. No site link to be created on infusion
$inf_sitelink[1] = array(
	"title" => $locale['xxx_link1'],
	"url" => "filename.php",
	"visibility" => "0"
);
*/

// Change of core table below.
if (isset($_POST['infuse']) && isset($_POST['infusion'])) { 
$result = dbquery("ALTER TABLE ".DB_PANELS." ADD (panel_url_list TEXT NOT NULL DEFAULT '', panel_url_list_exclude TINYINT(1) UNSIGNED NOT NULL DEFAULT '0')");
}

// Restore of core table below.
else if (isset($_GET['defuse']) && isnum($_GET['defuse'])) {
$result = dbquery("ALTER TABLE ".DB_PANELS." DROP panel_url_list, DROP panel_url_list_exclude");
}
?>


