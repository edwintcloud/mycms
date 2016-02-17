<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: options.php
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
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."forum/options.php";
include INFUSIONS."fusionboard4/includes/func.php";

if($fb4['fboard_on']){
	include INFUSIONS."fusionboard4/forum/options.php";
	require_once THEMES."templates/footer.php";
	die();
}

if (iMEMBER) {
	if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php"); }
	$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'"));
	if (!checkgroup($data['forum_access'])) { redirect("index.php"); }
	if (iSUPERADMIN) { define("iMOD", true); }
	if (!defined("iMOD") && iMEMBER && $data['forum_moderators']) {
		$mod_groups = explode(".", $data['forum_moderators']);
		foreach ($mod_groups as $mod_group) {
			if (!defined("iMOD") && checkgroup($mod_group)) { define("iMOD", true); }
		}
	}
	if (!defined("iMOD")) { define("iMOD", false); }
} else {
	define("iMOD", false);
}

if (isset($_POST['step']) && $_POST['step'] != "") { $_GET['step'] = $_POST['step']; }

if ((!iMOD && !iADMIN) || !checkgroup($data['forum_post'])) { redirect("index.php"); }

if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }

if (isset($_POST['canceldelete'])) { redirect("viewthread.php?forum_id=".$_GET['forum_id']."&thread_id=".$_GET['thread_id']); }

if (isset($_GET['step']) && $_GET['step'] == "renew") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpost='".time()."' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['458']);
	echo "<div style='text-align:center'><br />\n".$locale['459']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "delete") {
	opentable($locale['400']);
	echo "<div style='text-align:center'><br />\n";
	if (!isset($_POST['deletethread'])) {
		echo "<form name='delform' method='post' action='".FUSION_SELF."?step=delete&amp;forum_id=".$_GET['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n";
		echo $locale['404']."<br /><br />\n";
		echo "<input type='submit' name='deletethread' value='".$locale['405']."' class='button' style='width:75px'>\n";
		echo "<input type='submit' name='canceldelete' value='".$locale['406']."' class='button' style='width:75px'><br /><br />\n";
		echo "</form>\n";
	} else {
		$result = dbquery("SELECT post_author, COUNT(post_id) as num_posts FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' GROUP BY post_author");
		if (dbrows($result)) {
			while ($pdata = dbarray($result)) {
				$result2 = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts-".$pdata['num_posts']." WHERE user_id='".$pdata['post_author']."'");
			}
		}
		
		$tdata = dbarray(dbquery("SELECT thread_id,thread_lastpost,thread_lastuser FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'"));

		$threads_count = dbcount("(forum_id)", DB_THREADS, "forum_id='".$_GET['forum_id']."'") - 1;
		$result = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."'");
		$del_posts = mysql_affected_rows();
		$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("SELECT * FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$_GET['thread_id']."'");
		if (dbrows($result) != 0) {
			while ($attach = dbarray($result)) {
				unlink(FORUM."attachments/".$attach['attach_name']);
			}
		}
		$result = dbquery("DELETE FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$_GET['thread_id']."'");
		
		if ($threads_count > 0) {
			$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."' AND forum_lastpost='".$tdata['thread_lastpost']."' AND forum_lastuser='".$tdata['thread_lastuser']."'");
			if (dbrows($result)) {
				$result = dbquery("SELECT forum_id,post_author,post_datestamp FROM ".DB_POSTS." WHERE forum_id='".$_GET['forum_id']."' ORDER BY post_datestamp DESC LIMIT 1");
				$pdata = dbarray($result);
				$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata['post_datestamp']."', forum_postcount=forum_postcount-".$del_posts.", forum_threadcount=forum_threadcount-1, forum_lastuser='".$pdata['post_author']."' WHERE forum_id='".$_GET['forum_id']."'");
			}
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=0, forum_threadcount=0, forum_lastuser='0' WHERE forum_id='".$_GET['forum_id']."'");
		}
		echo $locale['401']."<br /><br />\n";
		echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n";
	}
	echo "</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "lock") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_locked='1' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['410']);
	echo "<div style='text-align:center'><br />\n".$locale['411']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "unlock") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_locked='0' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['420']);
	echo "<div style='text-align:center'><br />\n".$locale['421']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "sticky") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_sticky='1' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['430']);
	echo "<div style='text-align:center'><br />\n".$locale['431']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "nonsticky") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_sticky='0' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['440']);
	echo "<div style='text-align:center'><br />".$locale['441']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "move") {
	opentable($locale['450']);
	if (isset($_POST['move_thread'])) {
		if (!isset($_POST['new_forum_id']) || !isnum($_POST['new_forum_id'])) { redirect("index.php"); }
		
		if (!dbcount("(forum_id)", DB_FORUMS, "forum_id='".$_POST['new_forum_id']."'")) { redirect("../index.php"); }
		
		$result = dbquery("UPDATE ".DB_THREADS." SET forum_id='".$_POST['new_forum_id']."' WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("UPDATE ".DB_POSTS." SET forum_id='".$_POST['new_forum_id']."' WHERE thread_id='".$_GET['thread_id']."'");
		
		$post_count = dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'");
		
		$result = dbquery("SELECT thread_lastpost, thread_lastuser FROM ".DB_THREADS." WHERE forum_id='".$_GET['forum_id']."' ORDER BY thread_lastpost DESC LIMIT 0,1");
		if (dbrows($result)) {
			$pdata2 = dbarray($result);
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata2['thread_lastpost']."', forum_postcount=forum_postcount-".$post_count.", forum_threadcount=forum_threadcount-1, forum_lastuser='".$pdata2['thread_lastuser']."' WHERE forum_id='".$_GET['forum_id']."'");
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=forum_postcount-".$post_count.", forum_threadcount=forum_threadcount-1, forum_lastuser='0' WHERE forum_id='".$_GET['forum_id']."'");
		}

		$result = dbquery("SELECT thread_lastpost, thread_lastuser FROM ".DB_THREADS." WHERE forum_id='".$_POST['new_forum_id']."' ORDER BY thread_lastpost DESC LIMIT 0,1");
		if (dbrows($result)) {
			$pdata2 = dbarray($result);
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata2['thread_lastpost']."', forum_postcount=forum_postcount+".$post_count.", forum_threadcount=forum_threadcount+1, forum_lastuser='".$pdata2['thread_lastuser']."' WHERE forum_id='".$_POST['new_forum_id']."'");
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=forum_postcount+1, forum_threadcount=forum_threadcount+".$post_count.", forum_lastuser='0' WHERE forum_id='".$_POST['new_forum_id']."'");
		}
		
		echo "<div style='text-align:center'><br />\n".$locale['452']."<br /><br />\n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	} else {
		$move_list = ""; $sel = "";
		$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' ORDER BY forum_order");
		if (dbrows($result) != 0) {
			while ($data = dbarray($result)) {
				$result2 = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='".$data['forum_id']."' ORDER BY forum_order");
				if (dbrows($result2) != 0) {
					$move_list .= "<optgroup label='".$data['forum_name']."'>\n";
					while ($data2 = dbarray($result2)) {
						if ($_GET['forum_id'] == $data2['forum_id']) { $sel = " selected"; } else { $sel = ""; }
						$move_list .= "<option value='".$data2['forum_id']."'$sel>".$data2['forum_name']."</option>\n";
					}
					$move_list .= "</optgroup>\n";
				}
			}
		}
		echo "<form name='moveform' method='post' action='".FUSION_SELF."?step=move&forum_id=".$_GET['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border'>\n<tr>\n";
		echo "<td class='tbl2' width='150'>".$locale['451']."</td>\n";
		echo "<td class='tbl1'><select name='new_forum_id' class='textbox' style='width:250px;'>\n".$move_list."</select></td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td colspan='2' class='tbl2' style='text-align:center;'><input type='submit' name='move_thread' value='".$locale['450']."' class='button' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
	}
	closetable();
} else {
	redirect("index.php");
}
require_once THEMES."templates/footer.php";
?>