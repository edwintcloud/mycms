<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2009 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ac_navigation_panel.php
| Author: © 2009 ptown67
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

add_to_head("
<script src='".INFUSIONS."ac_navigation_panel/jscript/jquery.cookie.js' type='text/javascript'></script>
<script src='".INFUSIONS."ac_navigation_panel/jscript/menu.js' type='text/javascript'></script>
<link rel='stylesheet' type='text/css' href='".INFUSIONS."ac_navigation_panel/style.css' />
");

openside($locale['acn_side']);

echo "<div align='center'>\n";
echo "<ul id='menu2' class='menu expandfirst'>\n";
$i = 1;
$result = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' ORDER BY ac_order");
if (dbrows($result) != 0) {
while ($data = dbarray($result)) {
if (checkgroup($data['ac_access'])) {
echo "<li>\n";
echo "<a class='m".$i++."' href='#'>".$data['ac_name']."</a>\n";
echo "<ul>\n";
$result2 = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='".$data['ac_id']."' ORDER BY ac_order");
if (dbrows($result2)) {
while ($data2 = dbarray($result2)) {
if (checkgroup($data2['ac_access'])) {
if ($data2['ac_target'] == "1") { $target = " target='_blank'"; } else { $target = ""; }
echo "<a href='".BASEDIR.$data2['ac_url']."'$target>".THEME_BULLET." ".$data2['ac_name']."</a>\n";
}
}
}
echo "</ul>\n";
echo "</li>\n";
}
}
}
echo "</ul>\n";
echo "</div>\n";

closeside();
?>