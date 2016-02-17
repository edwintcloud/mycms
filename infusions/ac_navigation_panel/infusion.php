<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
| Author: INSERT NAME HERE
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

include INFUSIONS."ac_navigation_panel/infusion_db.php";

if (file_exists(INFUSIONS."ac_navigation_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."ac_navigation_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."ac_navigation_panel/locale/German.php";
}

$inf_title = $locale['acn_title'];
$inf_description = $locale['acn_desc'];
$inf_version = "2.0";
$inf_developer = "ptown67";
$inf_email = "info@ptown67.de";
$inf_weburl = "http://www.ptown67.de";
$inf_folder = "ac_navigation_panel";

$inf_newtable[1] = DB_AC_NAVIGATION." (
ac_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
ac_cat MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
ac_name VARCHAR(50) DEFAULT '' NOT NULL,
ac_url VARCHAR(100) DEFAULT '' NOT NULL,
ac_target TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
ac_access TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL,
ac_order SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
PRIMARY KEY (ac_id)
) TYPE=MyISAM;";

$inf_droptable[1] = DB_AC_NAVIGATION;

$inf_adminpanel[1] = array(
	"title" => $locale['acn_admin'],
	"image" => "wl.gif",
	"panel" => "ac_navigation_admin.php",
	"rights" => "ACN"
);
?>