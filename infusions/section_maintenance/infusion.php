<?php
/*---------------------------------------------------+
| PHP-Fusion 7 Content Management System
+----------------------------------------------------+
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
|----------------------------------------------------+
| Infusion: Section Maintenance Infusion v2.3
| Filename: infusion.php
| Author: HobbyMan
| Web: http://www.php-fusion.hobbysites.net/
+----------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+----------------------------------------------------*/

if (!defined("IN_FUSION")) { die("Access Denied"); }

include INFUSIONS."section_maintenance/infusion_db.php";

if (file_exists(INFUSIONS."section_maintenance/locale/".$settings['locale'].".php")) {
	include INFUSIONS."section_maintenance/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."section_maintenance/locale/English.php";
}

$inf_title = $locale['title'];
$inf_description = $locale['sma101'];
$inf_version = "2.3";
$inf_developer = "HobbyMan";
$inf_email = "hobbyman@hobbysites.net";
$inf_weburl = "http://php-fusion.hobbysites.net/";

$inf_folder = "section_maintenance";

$inf_newtable[1] = DB_SECTION_MAINTENANCE." (
sma_all TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_cont TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_reg TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_photo TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_articles TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_news TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_forum TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_members TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_down TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_prof TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_pm TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_weblinks TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_submissions TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_faq TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_cust TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_inf TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_temp TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_time TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL,
sma_period TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
sma_datestamp INT(10) UNSIGNED DEFAULT '0' NOT NULL,
sma_sign TEXT NOT NULL,
sma_show_sig TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_show_image TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_show_admmsg TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
sma_message TEXT NOT NULL,
sma_version VARCHAR(10) DEFAULT '' NOT NULL,
PRIMARY KEY (sma_all)
) TYPE=MyISAM;";

$inf_insertdbrow[1] = DB_SECTION_MAINTENANCE." (sma_all, sma_datestamp, sma_version) VALUES('0', '".time()."', '$inf_version')";
$inf_insertdbrow[2] = DB_PANELS." SET panel_name='".$locale['title']."', panel_filename='".$inf_folder."', panel_content='\include INFUSIONS.\"section_maintenance/functions.php\";', panel_side=2, panel_order='1', panel_type='php', panel_access='0', panel_display='1', panel_status='1' ";

$inf_altertable[1] = DB_SECTION_MAINTENANCE." ADD sma_inf TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL";
$inf_altertable[2] = DB_SECTION_MAINTENANCE." ADD sma_show_admmsg TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL";
$inf_altertable[3] = DB_SECTION_MAINTENANCE." ADD sma_inf TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL";

$inf_droptable[1] = DB_SECTION_MAINTENANCE;

$inf_deldbrow[1] = DB_PANELS." WHERE panel_filename='$inf_folder'";

$inf_adminpanel[1] = array(
	"title" => $locale['title'],
	"image" => "section_maint.gif",
	"panel" => "section_maintenance_admin.php",
	"rights" => "SMA"
);

?>