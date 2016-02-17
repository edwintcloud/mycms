<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: upgrade.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../maincore.php";
require_once THEMES."templates/admin_header.php";

if (file_exists(LOCALE.LOCALESET."admin/upgrade.php")) {
	include LOCALE.LOCALESET."admin/upgrade.php";
} else {
	include LOCALE."English/admin/upgrade.php";
}

if (!checkrights("U") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

opentable($locale['400']);
echo "<div style='text-align:center'><br />\n";
echo "<form name='upgradeform' method='post' action='".FUSION_SELF.$aidlink."'>\n";

if (str_replace(".", "", $settings['version']) < "70007") {
	if (!isset($_POST['stage'])) {
		echo "A minor database upgrade is available for this installation of PHP-Fusion.<br />\n";
		echo "Simply click Upgrade to update your system.<br /><br />\n";
		echo "<input type='hidden' name='stage' value='2'>\n";
		echo "<input type='submit' name='upgrade' value='Upgrade' class='button'><br /><br />\n";
	} elseif (isset($_POST['upgrade']) && isset($_POST['stage']) && $_POST['stage'] == 2) {
		$result = dbquery("UPDATE ".DB_SETTINGS." SET version='7.00.07'");
		echo "Database upgrade complete.<br /><br />\n";
	}
} else {
	echo $locale['401']."<br /><br />\n";
}
	
echo "</form>\n</div>\n";
closetable();

require_once THEMES."templates/footer.php";
?>
