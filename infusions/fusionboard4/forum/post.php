<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");

if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}
echo "<script src='".INFUSIONS."fusionboard4/includes/js/fb4.js' type='text/javascript'></script>";

add_to_title($locale['global_204']);

require_once INCLUDES."forum_include.php";
require_once INCLUDES."bbcode_include.php";

if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php"); }

if ($settings['forum_edit_lock'] == 1) {
	$lock_edit = true;
} else {
	$lock_edit = false;
}

if(isset($_GET['thread_id'])){
	$announcementCheck = dbquery("select * from ".DB_PREFIX."fb_threads where thread_id='".$_GET['thread_id']."' and thread_announcement='1'");
	$announcementCheck = dbrows($announcementCheck);
} else {
	$announcementCheck = false;
}

$result = dbquery(
	"SELECT f3.*, f.*, f2.forum_name AS forum_cat_name
	FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat=f2.forum_id
	LEFT JOIN ".DB_PREFIX."fb_forums f3 on f3.forum_id=f.forum_id
	WHERE f.forum_id='".$_GET['forum_id']."'"
);

if (dbrows($result)) {
	$fdata = dbarray($result);
	if (!checkgroup($fdata['forum_access']) || !$fdata['forum_cat']) { redirect("index.php"); }
} else {
	redirect("index.php");
}

if (iMEMBER && $fdata['forum_moderators']) {
	$mod_groups = explode(".", $fdata['forum_moderators']);
	foreach ($mod_groups as $mod_group) {
		if (!defined("iMOD") && checkgroup($mod_group)) { define("iMOD", true); }
	}
}
if (!defined("iMOD")) { define("iMOD", false); }

$caption = $fdata['forum_cat_name']." :: ".$fdata['forum_name'];

if ((isset($_GET['action']) && $_GET['action'] == "newthread") && ($fdata['forum_post'] != 0 && checkgroup($fdata['forum_post']))) {
	include "postnewthread.php";
} elseif ((isset($_GET['action']) && $_GET['action'] == "reply") && ($fdata['forum_reply'] != 0 && checkgroup($fdata['forum_reply']))) {
	if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) {
		redirect("index.php");
	}

	$result = dbquery("SELECT * FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."' AND forum_id='".$fdata['forum_id']."'");
	
	if (dbrows($result)) {
		$tdata = dbarray($result);
	} else {
		redirect("index.php");
	}
	
	$caption .= " :: ".$tdata['thread_subject'];
	
	if (!$tdata['thread_locked']) {
		include "postreply.php";
	} else {
		redirect("index.php");
	}
} elseif (isset($_GET['action']) && $_GET['action'] == "edit") {
	if ((!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) || (!isset($_GET['post_id']) || !isnum($_GET['post_id']))) { redirect("index.php"); }

	$result = dbquery("SELECT * FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."' AND forum_id='".$fdata['forum_id']."'");
	
	if (dbrows($result)) {
		$tdata = dbarray($result);
	} else {
		redirect("index.php");
	}

	$result = dbquery("SELECT tp.*, tt.thread_subject, MIN(tp2.post_id) AS first_post FROM ".DB_POSTS." tp
	INNER JOIN ".DB_THREADS." tt on tp.thread_id=tt.thread_id
	INNER JOIN ".DB_POSTS." tp2 on tp.thread_id=tp2.thread_id
	WHERE tp.post_id='".$_GET['post_id']."' AND tp.thread_id='".$tdata['thread_id']."' AND tp.forum_id='".$fdata['forum_id']."' GROUP BY tp2.post_id");
	
	if (dbrows($result)) {
		$pdata = dbarray($result);
		$last_post = dbarray(dbquery("SELECT post_id FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' AND forum_id='".$_GET['forum_id']."' ORDER BY post_datestamp DESC LIMIT 1"));
	} else {
		redirect("index.php");
	}

	if ($userdata['user_id'] != $pdata['post_author'] && !iMOD && !iSUPERADMIN) { redirect("index.php"); }
	
	if (!$tdata['thread_locked'] && (($lock_edit && $last_post['post_id'] == $pdata['post_id'] && $userdata['user_id'] == $pdata['post_author']) || (!$lock_edit && $userdata['user_id'] == $pdata['post_author'])) ) {
		include "postedit.php";
	} else {
		if (iMOD || iSUPERADMIN) { include "postedit.php"; } else { redirect("index.php"); }
	}
} else {
	redirect("index.php");
}

?>