<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: new_infusion_admin.php
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
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";

include INFUSIONS."custom-panels_control/infusion_db.php";

if (!checkrights("CPAC") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

// Check if locale file is available matching the current site locale setting.
if (file_exists(INFUSIONS."custom-panels_control/locale/".$settings['locale'].".php")) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS."custom-panels_control/locale/".$settings['locale'].".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS."custom-panels_control/locale/English.php";
}

opentable($locale['cpac_title']);
echo "<div class='admin-message'>".$locale['cpac_desc']."</div>\n";
echo "<center><a href='../../administration/panel_editor.php".$aidlink."'>".$locale['cpac_020']."</a></center>\n";
closetable();

require_once THEMES."templates/footer.php";
?>