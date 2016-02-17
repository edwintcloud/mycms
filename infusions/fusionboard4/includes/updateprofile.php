<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/

if (!defined("IN_FUSION")) { die("Access Denied"); }

if (!iMEMBER) { redirect("index.php"); }

$result = dbquery("SELECT * FROM ".DB_USER_FIELDS." ORDER BY field_order");
if (dbrows($result)) {
	$profile_method = "validate_update"; 
	while($data = dbarray($result)) {
		if (file_exists(LOCALE.LOCALESET."user_fields/".$data['field_name'].".php")) {
			include LOCALE.LOCALESET."user_fields/".$data['field_name'].".php";
		}
		if (file_exists(INCLUDES."user_fields/".$data['field_name']."_include.php")) {
			include INCLUDES."user_fields/".$data['field_name']."_include.php";
		}
	}
}

$result = dbquery("UPDATE ".DB_USERS." SET user_id=".$userdata['user_id'].$db_values." WHERE user_id='".$user_data['user_id']."'");
redirect(INFUSIONS."fusionboard4/usercp.php?section=details&status=updated");
?>
