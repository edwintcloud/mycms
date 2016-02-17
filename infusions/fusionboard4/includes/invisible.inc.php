<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");

if(isset($_GET['invisible']) && $_GET['invisible'] == "off" && iADMIN){
	$result = dbquery("update ".DB_PREFIX."fb_users set user_invisible='0' where user_id='".$userdata['user_id']."'");
}

if(isset($_GET['invisible']) && $_GET['invisible'] == "on" && iADMIN){
	$result = dbquery("SELECT * FROM ".DB_PREFIX."fb_users where user_id='".$userdata['user_id']."'");
	if(dbrows($result)){
		$result = dbquery("update ".DB_PREFIX."fb_users set user_invisible='1' where user_id='".$userdata['user_id']."'");
	} else {
		$result = dbquery("INSERT INTO ".DB_PREFIX."fb_users (user_id, user_layout, user_notes, user_warning, user_invisible, user_lv, user_banned)
		VALUES('".$userdata['user_id']."', '0', '', '0', '1', '".time()."', '')");
	}
}

/* Invisible mode mod */
if(iMEMBER){
	$result = dbquery("SELECT * FROM ".DB_PREFIX."fb_users where user_id='".$userdata['user_id']."'");
	if(dbrows($result)){
		$data = dbarray($result);
		if($data['user_invisible'] && iADMIN){
			$result = dbquery("update ".DB_USERS." set user_lastvisit='".$data['user_lv']."' where user_id='".$userdata['user_id']."'");
			$result = dbquery("DELETE FROM ".DB_ONLINE." WHERE online_user='".$userdata['user_id']."'");
			if(!defined("INVISIBLEMODE")) define("INVISIBLEMODE", true);
		} else {
			$result = dbquery("SELECT * FROM ".DB_ONLINE." where online_user='".$userdata['user_id']."'");
			if (dbrows($result)) {
				$result = dbquery("UPDATE ".DB_ONLINE." SET online_lastactive='".time()."' WHERE online_user='".$userdata['user_id']."'");
			} else {
				$result = dbquery("INSERT INTO ".DB_ONLINE." (online_user, online_ip, online_lastactive) VALUES ('".$userdata['user_id']."', '".USER_IP."', '".time()."')");
			}
			$result = dbquery("update ".DB_PREFIX."fb_users set user_lv='".time()."' where user_id='".$userdata['user_id']."'");
		}
	} else {
		$result = dbquery("INSERT INTO ".DB_PREFIX."fb_users (user_id, user_layout, user_notes, user_warning, user_invisible, user_lv, user_banned)
		VALUES('".$userdata['user_id']."', '0', '', '0', '0', '".time()."', '')");
		$result = dbquery("SELECT * FROM ".DB_ONLINE." where online_user='".$userdata['user_id']."'");
		if (dbrows($result)) {
			$result = dbquery("UPDATE ".DB_ONLINE." SET online_lastactive='".time()."' WHERE online_user='".$userdata['user_id']."'");
		} else {
			$result = dbquery("INSERT INTO ".DB_ONLINE." (online_user, online_ip, online_lastactive) VALUES ('".$userdata['user_id']."', '".USER_IP."', '".time()."')");
		}
	}
} else {
	$result = dbquery("SELECT * FROM ".DB_ONLINE." WHERE online_user='0' AND online_ip='".USER_IP."'");
	if (dbrows($result)) {
		$result = dbquery("UPDATE ".DB_ONLINE." SET online_lastactive='".time()."' WHERE online_user='0' AND online_ip='".USER_IP."'");
	} else {
		$result = dbquery("INSERT INTO ".DB_ONLINE." (online_user, online_ip, online_lastactive) VALUES ('0', '".USER_IP."', '".time()."')");
	}
}
if(!defined("INVISIBLEMODE")) define("INVISIBLEMODE", false);

/* Redundant online users panel code if the site doesnt have this panel enabled */
$result = dbquery("SELECT * FROM ".DB_ONLINE." WHERE online_user=".($userdata['user_level'] != 0 ? "'".$userdata['user_id']."'" : "'0' AND online_ip='".USER_IP."'"));
if (dbrows($result)) {
	$result = dbquery("UPDATE ".DB_ONLINE." SET online_lastactive='".time()."' WHERE online_user=".($userdata['user_level'] != 0 ? "'".$userdata['user_id']."'" : "'0' AND online_ip='".USER_IP."'")."");
} else {
	$result = dbquery("INSERT INTO ".DB_ONLINE." (online_user, online_ip, online_lastactive) VALUES ('".($userdata['user_level'] != 0 ? $userdata['user_id'] : "0")."', '".USER_IP."', '".time()."')");
}
$result = dbquery("DELETE FROM ".DB_ONLINE." WHERE online_lastactive<".(time()-60)."");

$fb4 = dbarray(dbquery("select * from ".DB_PREFIX."fb_settings"));
$guests = 0; $members = array();
while ($data = dbarray($result)) {
	if ($data['online_user'] == "0") {
		$guests++;
	} else {
		array_push($members, array($data['user_id'], $data['user_name']));
	}
}
if (count($members)) {
	$i = 1;
	while (list($key, $member) = each($members)) {
		echo "<a href='".BASEDIR."profile.php?lookup=".$member[0]."' class='side'>".$member[1]."</a>";
		if ($i != count($members)) { echo ",\n"; } else { echo "<br />\n"; }
		$i++;
	}
}
/* fusionBoard 4 statistics */
$todayOnline = explode(".", $fb4['stat_today_users']);
if(iMEMBER){
	if(strftime("%d") !== strftime("%d", $fb4['stat_today_date'])){
		$query = dbquery("update ".DB_PREFIX."fb_settings set stat_today_users='', stat_today_date='".time()."'");
	}
	if(!in_array($userdata['user_id'], $todayOnline)){
		$query = dbquery("update ".DB_PREFIX."fb_settings set stat_today_users='".($fb4['stat_today_users'].".".$userdata['user_id'])."'");
	}
}
if(($i+$guests) > $fb4['stat_moau']){
	$query = dbquery("update ".DB_PREFIX."fb_settings set stat_moau='".($i+$guests)."', stat_moau_date='".time()."'");
}
/* end statistics */
?>