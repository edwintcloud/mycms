<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: contact.php
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
require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."contact.php";

add_to_title($locale['global_200']."Scripts");

	opentable($locale['400']);
	echo $locale['401']."<br /><br />\n";
	echo "<form name='userform' method='post' action='".FUSION_SELF."'>\n";
	echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
	echo "<td width='100' class='tbl'>".$locale['402']."</td>\n";
	echo "<td class='tbl'><input type='text' name='mailname' maxlength='50' class='textbox' style='width: 200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td width='100' class='tbl'>".$locale['403']."</td>\n";
	echo "<td class='tbl'><input type='text' name='email' maxlength='100' class='textbox' style='width: 200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td width='100' class='tbl'>".$locale['404']."</td>\n";
	echo "<td class='tbl'><input type='text' name='subject' maxlength='50' class='textbox' style='width: 200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td width='100' class='tbl'>".$locale['405']."</td>\n";
	echo "<td class='tbl'><textarea name='message' rows='10' class='textbox' cols='50'></textarea></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td width='100' class='tbl'>".$locale['407']."</td>\n";
	echo "<td class='tbl'>";
	echo "<img id='captcha' src='".INCLUDES."securimage/securimage_show.php' alt='' align='left' />\n";
  echo "<a href='".INCLUDES."securimage/securimage_play.php'><img src='".INCLUDES."securimage/images/audio_icon.gif' alt='' align='top' class='tbl-border' style='margin-bottom:1px' /></a><br />\n";
  echo "<a href='#' onclick=\"document.getElementById('captcha').src = '".INCLUDES."securimage/securimage_show.php?sid=' + Math.random(); return false\"><img src='".INCLUDES."securimage/images/refresh.gif' alt='' align='bottom' class='tbl-border' /></a>\n";
	echo "</td>\n</tr>\n<tr>";
	echo "<td class='tbl'>".$locale['408']."</td>\n";
	echo "<td class='tbl'><input type='text' name='captcha_code' class='textbox' style='width:100px' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td align='center' colspan='2' class='tbl'>\n";
	echo "<input type='submit' name='sendmessage' value='".$locale['406']."' class='button' /></td>\n";
	echo "</tr>\n</table>\n</form>\n";
	closetable();

require_once THEMES."templates/footer.php";
?>
