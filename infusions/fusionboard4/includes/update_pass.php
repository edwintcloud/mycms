<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/
if(!defined("IN_FUSION")) die("Access Denied");
include LOCALE.LOCALESET."edit_profile.php";
include LOCALE.LOCALESET."user_fields.php";

$error = ""; $db_values = ""; $set_avatar = "";

$user_name = $userdata['user_name'];
$user_email = trim(stripinput($_POST['user_email']));
$user_new_password = trim(stripinput($_POST['user_new_password']));
$user_new_password2 = trim(stripinput($_POST['user_new_password2']));

if (iADMIN) {
	$user_new_admin_password = trim(stripinput($_POST['user_new_admin_password']));
	$user_new_admin_password2 = trim(stripinput($_POST['user_new_admin_password2']));
} else {
	$user_new_admin_password = "";
}

if ($user_name == "" || $user_email == "") {
	$error .= $locale['430']."<br />\n";
} else {
	if (preg_check("/^[-0-9A-Z_@\s]+$/i", $user_name)) {
		if ($user_name != $userdata['user_name']) {
			$result = dbquery("SELECT user_name FROM ".DB_USERS." WHERE user_name='".$user_name."' AND user_id<>'".$userdata['user_id']."'");
			if (dbrows($result)) {
				$error .= $locale['432']."<br />\n";
			}
		}
	} else {
		$error .= $locale['431']."<br />\n";
	}
	
	if (preg_check("/^[-0-9A-Z_\.]{1,50}@([-0-9A-Z_\.]+\.){1,50}([0-9A-Z]){2,4}$/i", $user_email)) {
		if ($user_email != $userdata['user_email']) {
			if ((isset($_POST['user_password'])) && md5(md5($_POST['user_password'])) == $userdata['user_password']) {
				$result = dbquery("SELECT user_email FROM ".DB_USERS." WHERE user_email='".$user_email."'");
				if (dbrows($result)) {
					$error .= $locale['434']."<br />\n";
				}
			} else {
				$error .= $locale['437']."<br />\n";
			}
		}
	} else {
		$error .= $locale['433']."<br />\n";
	}
}

if ($user_new_password) {
	if ((isset($_POST['user_password'])) && md5(md5($_POST['user_password'])) == $userdata['user_password']) {
		if ($user_new_password2 != $user_new_password) {
			$error .= $locale['435']."<br />";
		} else {
			if (!preg_match("/^[0-9A-Z@]{6,20}$/i", $user_new_password)) {
				$error .= $locale['436']."<br />\n";
			}
			if ((md5(md5($user_new_password)) == md5(md5($user_new_admin_password))) || (md5(md5($user_new_password)) == $userdata['user_admin_password'])) {
				$error .= $locale['439']."<br><br>\n";
			}
		}
	} else {
		$error .= $locale['437']."<br />\n";
	}
}

if (iADMIN && $user_new_admin_password) {
	if ($userdata['user_admin_password']) {
		if ((!isset($_POST['user_admin_password'])) || md5(md5($_POST['user_admin_password'])) != $userdata['user_admin_password']) {
			$error .= $locale['441']."<br />\n";
		}
	}
	if (!$error) {
		if ($user_new_admin_password2 != $user_new_admin_password) {
			$error .= $locale['438']."<br />";
		} else {
			if (!preg_match("/^[0-9A-Z@]{6,20}$/i", $user_new_admin_password)) {
				$error .= $locale['440']."<br />\n";
			}
			if ((md5(md5($user_new_admin_password)) == md5(md5($user_new_password))) || (md5(md5($user_new_admin_password)) == $userdata['user_password'])) {
				$error .= $locale['439']."<br><br>\n";
			}
		}
	}
}
if(!$error){
	if ($user_new_password) { $new_pass = " user_password='".md5(md5($user_new_password))."', "; } else { $new_pass = " "; }
	if (iADMIN && $user_new_admin_password) { $new_admin_pass = " user_admin_password='".md5(md5($user_new_admin_password))."', "; } else { $new_admin_pass = " "; }

	$result = dbquery("UPDATE ".DB_USERS." SET user_name='$user_name',".$new_pass.$new_admin_pass."user_email='$user_email' WHERE user_id='".$userdata['user_id']."'");
	redirect(FUSION_SELF."?section=email&status=updated");
}
?>