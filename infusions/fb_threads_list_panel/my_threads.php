<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: my_threads.php
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

if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

include INFUSIONS."fusionboard4/includes/func.php";

if (!iMEMBER) { redirect("../../index.php"); }

add_to_title($locale['global_200'].$locale['global_041']);

global $lastvisited;

if (!isset($lastvisited) || !isnum($lastvisited)) { $lastvisited = time(); }

$result = dbquery(
	"SELECT COUNT(thread_id) FROM ".DB_THREADS." tt
	INNER JOIN ".DB_FORUMS." tf ON tt.forum_id = tf.forum_id
	INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser = tu.user_id
	WHERE ".groupaccess('tf.forum_access')." AND tt.thread_author='".$userdata['user_id']."' LIMIT 100"
);
$rows = dbrows($result);
if ($rows) {
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	/*$result = dbquery(
		"SELECT tt.forum_id, tt.thread_id, tt.thread_subject, tt.thread_views, tt.thread_lastuser,
		tt.thread_lastpost, tt.thread_postcount, tf.forum_name, tf.forum_access, tu.user_id, tu.user_name
		FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id = tf.forum_id
		INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser = tu.user_id
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_author = '".$userdata['user_id']."'
		ORDER BY tt.thread_lastpost DESC LIMIT ".$_GET['rowstart'].",20"
	);*/
	$result = dbquery(
		"SELECT tu2.user_id as original_id, tu2.user_name as original_name, tt.thread_id, tt.thread_subject, tt.thread_views, tt.thread_lastuser, tt.thread_lastpost,
		tt.thread_poll, tf.forum_id, tf.forum_name, tf.forum_access, tt.thread_lastpostid, tt.thread_postcount, tu.user_id, tu.user_name
		FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser=tu.user_id
		INNER JOIN ".DB_USERS." tu2 ON tt.thread_author=tu2.user_id
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_author = '".$userdata['user_id']."'
		ORDER BY tt.thread_lastpost DESC LIMIT ".$_GET['rowstart'].",20"
	);
	$i=0;
	opentable($locale['global_041']);
	echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n<tr>\n";
	echo "<td class='tbl2' width='1%'>&nbsp;</td>\n";
	echo "<td width='40%' class='tbl2'><strong>".$locale['global_044']."</strong></td>\n";
	echo "<td width='20%' class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_047']."</strong></td>\n";
	echo "<td width='1%' class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_045']."</strong></td>\n";
	echo "<td width='1%' class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_046']."</strong></td>\n";
	echo "</tr>\n";
	while ($data = dbarray($result)) {
		$row_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
		echo "<tr>\n<td class='".$row_color."' style='white-space:nowrap'>";
		if ($fb4['forum_icons']){
			$iconQuery = dbquery("select * from ".$db_prefix."fb_forums where forum_id='".$data['forum_id']."'");
			if(dbrows($iconQuery)){
				$iconData = dbarray($iconQuery);
				$ficon = ($iconData['forum_icon'] !== "" ? $iconData['forum_icon'] : "folder.png");
			} else {
				$ficon = "folder.png";
			}
			echo "<img src='".INFUSIONS."fusionboard4/images/forum_icons/$ficon' alt='".$data['forum_name']."' title='".$data['forum_name']."'>";
		}
		if ($data['thread_lastpost'] > $lastvisited) {
			$thread_match = $data['thread_id']."\|".$data['thread_lastpost']."\|".$data['forum_id'];
			if (iMEMBER && preg_match("(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata['user_threads'])) {
				if(!$fb4['forum_icons']) echo "<img src='".get_image("folder")."' alt='' />";
				$threadbold = "";
			} else {
				if(!$fb4['forum_icons']) echo "<img src='".get_image("foldernew")."' alt='' />";
				$threadbold = " font-weight:bold;";
			}
		} else {
			if(!$fb4['forum_icons']) echo "<img src='".get_image("folder")."' alt='' />";
			$threadbold = "";
		}
		if ($data['thread_poll']) {
			$thread_poll = "<span class='small' style='font-weight:bold'>[".$locale['global_051']."]</span> ";
		} else {
			$thread_poll = "";
		}
		$original_data = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='".$data['thread_id']."' order by post_id asc limit 1"));
		$timepassed = timePassed($original_data['post_datestamp']);
		echo "</td>
		<td width='40%' class='".$row_color."'>";
		$threadPost = dbarray(dbquery("select * from ".DB_THREADS." t
		left join ".DB_POSTS." p on p.thread_id=t.thread_id
		where t.thread_id='".$data['thread_id']."' order by p.post_id asc limit 1"));
		$post_res = dbquery("select * from ".DB_PREFIX."fb_posts where post_id='".$threadPost['post_id']."'");
		if(dbrows($post_res)){
			$post_data = dbarray($post_res);
			if($post_data['post_icon'] && $post_data['post_icon'] !== "page_white.png" && $fb4['post_icons']){
				echo "<div style='float:left;'><br /><img src='".INFUSIONS."fusionboard4/images/post_icons/".$post_data['post_icon']."' alt='' style='vertical-align:middle;'>&nbsp;<br /></div>";
			}
		}
		echo "<a";
		if($fb4['latest_popup']){
			$originalpost = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='".$data['thread_id']."' order by post_id asc limit 1"));
			$post_message = $originalpost['post_smileys'] == 1 ? parsesmileys($originalpost['post_message']) : $originalpost['post_message'];
			$post_message = phpentities(nl2br(parseubb($post_message)));
			echo " title=\"header=[ ".str_replace("]", "]]", str_replace("[", "[[", trimlink($data['thread_subject'], 70)))."] body=[".str_replace("]", "]]", str_replace("[", "[[", trimlink($post_message, 150)))."] delay=[0] fade=[on]\"";
		}
		echo " href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."' title='".$data['thread_subject']."' 
		style='text-decoration:underline; font-size:12px;$threadbold'>".trimlink($data['thread_subject'], 40)."</a> ";
		if($fb4['latest_post']){
			echo "&nbsp;<a";
			if($fb4['latest_popup']){
				$originalpost = dbarray(dbquery("select * from ".DB_POSTS." where post_id='".$data['thread_lastpostid']."' order by post_id asc limit 1"));
				$post = trimlink(nl2br(stripinput(parseubb($originalpost['post_message']))), 200);
				echo " title='header=[".$locale['fb615'].":] body=[".$post."] delay=[0] fade=[on]'";
			}
			echo " href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."&amp;pid=".$data['thread_lastpostid']."#post_".$data['thread_lastpostid']."' title='".$locale['fb615']."' class='small'>(<b>»</b>)</a>";
		}
		echo "<br />\n<a href='".BASEDIR."profile.php?lookup=".$data['original_id']."' style='font-size:11px;'>".showLabel($data['original_id'], false, "panel")."</a><br />
		<span style='font-size:10px;'>".$timepassed."</span>
		</td>\n";
		echo "<td width='20%' class='".$row_color."' style='text-align:right;white-space:nowrap'>
		".timePassed($data['thread_lastpost'], false)."<br />
		by <a href='".BASEDIR."profile.php?lookup=".$data['thread_lastuser']."'>".showLabel($data['thread_lastuser'], false, "panel")."</a>";
		if(!$fb4['latest_post']){
			echo "&nbsp;<a";
			if($fb4['latest_popup']){
				$originalpost = dbarray(dbquery("select * from ".DB_POSTS." where post_id='".$data['thread_lastpostid']."' order by post_id asc limit 1"));
				$post = trimlink(nl2br(stripinput(parseubb($originalpost['post_message']))), 200);
				echo " title='header=[".(($data['thread_postcount']-1) > 0 ? "RE: " : "").$data['thread_subject']."] body=[".$post."] delay=[0] fade=[on]'";
			}
			echo " href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."&amp;pid=".$data['thread_lastpostid']."#post_".$data['thread_lastpostid']."' title='".$locale['fb615']."'><b>»»</b></a>";
		}
		echo "</td>\n";
		echo "<td width='1%' class='".$row_color."' style='text-align:center;white-space:nowrap'>".$data['thread_views']."</td>\n";
		echo "<td width='1%' class='".$row_color."' style='text-align:center;white-space:nowrap'>".($data['thread_postcount']-1)."</td>\n";
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";
	if($fb4['latest_popup']){
		echo "<script src='".INFUSIONS."fusionboard4/includes/js/boxover.js' type='text/javascript'></script>\n";
	}
	closetable();
	if ($rows > 20) echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], 20, $rows, 3)."\n</div>\n";
} else {
	opentable($locale['global_041']);
	echo "<div style='text-align:center'><br />\n".$locale['global_053']."<br /><br />\n</div>\n";
	closetable();
}

require_once THEMES."templates/footer.php";
?>