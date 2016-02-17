<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: comments_enhanced.php
| Author: Nick Jones / Ian Unruh
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

include LOCALE.LOCALESET."comments.php";
if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

function showcomments($ctype, $cdb, $ccol, $cid, $clink, $ingroup) {

	global $settings, $locale, $userdata, $aidlink;
	
	if (iMEMBER && (isset($_GET['c_action']) && $_GET['c_action'] == "edit") && (isset($_GET['comment_id']) && isnum($_GET['comment_id']))) {
		$eresult = dbquery(
			"SELECT tcm.*,user_name FROM ".DB_COMMENTS." tcm
			LEFT JOIN ".DB_USERS." tcu ON tcm.comment_name=tcu.user_id
			WHERE comment_id='".$_GET['comment_id']."' AND comment_item_id='".$cid."' AND comment_type='".$ctype."'"
		);
		if (dbrows($eresult)) {
			$edata = dbarray($eresult);
			if ((iADMIN && checkrights("C")) || (iMEMBER && $edata['comment_name'] == $userdata['user_id'] && isset($edata['user_name']))) {
				$clink .= "&amp;c_action=edit&amp;comment_id=".$edata['comment_id'];
				$comment_message = $edata['comment_message'];
			}
		} else {
			$comment_message = "";
		}
	} else {
		$comment_message = "";
	}
	if (iMEMBER && $ingroup) {
		add_to_head("<script type='text/javascript'>window.onload=setTimeout(\"hideall()\", 250);
		function hideall(){
			document.getElementById('bbcode').style.display='none';
		}
		function showhide(msg_id) {
		   document.getElementById(msg_id).style.display = document.getElementById(msg_id).style.display == 'none' ? 'block' : 'none';
		}</script>\n");
		require_once INCLUDES."bbcode_include.php";
		echo "<a id='edit_comment' name='edit_comment'></a>\n";
		echo "<form name='inputform' method='post' action='".$clink."'>\n";
		echo "<div align='center'>\n";
		echo "<textarea name='comment_message' rows='2' class='textbox' style='width:90%'>".$comment_message."</textarea><br />\n";
		echo "<input type='submit' name='post_comment' value='".$locale['uc283']."' class='button' /> :: <a onClick='showhide(\"bbcode\")'>".$locale['uc285']."</a>\n";
		echo "<div id='bbcode'><br />".display_bbcodes("360px", "comment_message")."</div>\n";
		echo "</div>\n</form>\n";
	} else {
		echo "<div align='center'>".$locale['uc289']."</div>\n";
	}
	
	echo "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' style='padding:6px;'>\n";
	
	if (iMEMBER && (isset($_GET['c_action']) && $_GET['c_action'] == "delete") && (isset($_GET['comment_id']) && isnum($_GET['comment_id']))) {
		if ((iADMIN && checkrights("C")) || (iMEMBER && dbcount("(comment_id)", DB_COMMENTS, "comment_id='".$_GET['comment_id']."' AND comment_name='".$userdata['user_id']."'"))) {
			$result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_id='".$_GET['comment_id']."'".(iADMIN ? "" : " AND comment_name='".$userdata['user_id']."'"));
		}
		redirect($clink);
	}

	if ((iMEMBER || $settings['guestposts'] == "1") && isset($_POST['post_comment'])) {
			
		if (iMEMBER) {
			$comment_name = $userdata['user_id'];
		} elseif ($settings['guestposts'] == "1") {
			$comment_name = trim(stripinput($_POST['comment_name']));
			$comment_name = preg_replace("(^[0-9]*)", "", $comment_name);
			if (isnum($comment_name)) { $comment_name = ""; }
		}
		
		$comment_message = trim(stripinput(censorwords($_POST['comment_message'])));
		
		if (iMEMBER && (isset($_GET['c_action']) && $_GET['c_action'] == "edit") && (isset($_GET['comment_id']) && isnum($_GET['comment_id']))) {
			$comment_updated = false;
			if ((iADMIN && checkrights("C")) || (iMEMBER && dbcount("(comment_id)", DB_COMMENTS, "comment_id='".$_GET['comment_id']."' AND comment_name='".$userdata['user_id']."'"))) {
				if ($comment_message) {
					$result = dbquery("UPDATE ".DB_COMMENTS." SET comment_message='$comment_message' WHERE comment_id='".$_GET['comment_id']."'".(iADMIN ? "" : " AND comment_name='".$userdata['user_id']."'"));
					$comment_updated = true;
				}
			}
			if ($comment_updated) {
				$c_start = (ceil(dbcount("(comment_id)", DB_COMMENTS, "comment_id<='".$_GET['comment_id']."' AND comment_item_id='".$cid."' AND comment_type='".$ctype."'") / 10) - 1) * 10;
			}
			redirect($clink."&amp;rstart=".(isset($c_start) && isnum($c_start) ? $c_start : ""));
		} else {
			if (!dbcount("(".$ccol.")", $cdb, $ccol."='".$cid."'")) { redirect(BASEDIR."index.php"); }
			if ($comment_name && $comment_message) {
				require_once INCLUDES."flood_include.php";
				if (!flood_control("comment_datestamp", DB_COMMENTS, "comment_ip='".USER_IP."'")) {
					$result = dbquery("INSERT INTO ".DB_COMMENTS." (comment_item_id, comment_type, comment_name, comment_message, comment_datestamp, comment_ip) VALUES ('$cid', '$ctype', '$comment_name', '$comment_message', '".time()."', '".USER_IP."')");
				}
			}
			$c_start = (ceil(dbcount("(comment_id)", DB_COMMENTS, "comment_item_id='".$cid."' AND comment_type='".$ctype."'") / 10) - 1) * 10;
			redirect($clink."&amp;rstart=".$c_start);
		}
	}
	echo "<a id='comments' name='comments'></a>";
	$c_rows = dbcount("(comment_id)", DB_COMMENTS, "comment_item_id='$cid' AND comment_type='$ctype'");
	if (!isset($_GET['c_start']) || !isnum($_GET['c_start'])) { $_GET['c_start'] = 0; }
	$result = dbquery(
		"SELECT tcm.*,tcu.* FROM ".DB_COMMENTS." tcm
		LEFT JOIN ".DB_USERS." tcu ON tcm.comment_name=tcu.user_id
		WHERE comment_item_id='$cid' AND comment_type='$ctype'
		ORDER BY comment_datestamp DESC LIMIT ".$_GET['c_start'].",10"
	);
	if (dbrows($result)) {
		$i = $_GET['c_start']+1;
		if ($c_rows > 10) {
			echo "<div style='text-align:center;margin-bottom:5px;'>".makecommentnav($_GET['c_start'], 10, $c_rows, 3, $clink."&amp;")."</div>\n";
		}
		echo "<table width='100%' cellspacing='1' cellpadding='0'>\n";
		while ($data = dbarray($result)) {
			echo "<tr><td class='tbl2' rowspan='2' width='1'>\n";
			if($data['user_avatar']){
				list($width, $height) = getimagesize(IMAGES."avatars/".$data['user_avatar']);
				$new_width = 70;
				$new_height = ($height * ($new_width/$height));
				echo "<img src='".IMAGES."avatars/".$data['user_avatar']."' alt='' style='width:".$new_width."px;height:".$new_height."px'>\n";
			} else {
				echo "<img src='".IMAGES."noav.gif' alt='' style='width:70px;height:70px'>\n";
			}
			echo "</td>\n<td class='tbl2' style='height:30px;'>";
			if ((iADMIN && checkrights("C")) || (iMEMBER && $data['comment_name'] == $userdata['user_id'] && isset($data['user_name']))) {
				echo "<div style='float:right'>\n<a href='".FUSION_REQUEST."&amp;c_action=edit&amp;comment_id=".$data['comment_id']."#edit_comment'>".$locale['c108']."</a> |\n";
				echo "<a href='".FUSION_REQUEST."&amp;c_action=delete&amp;comment_id=".$data['comment_id']."'>".$locale['c109']."</a>\n</div>\n";
			}
			echo "<a href='".FUSION_REQUEST."#c".$data['comment_id']."' id='c".$data['comment_id']."' name='c".$data['comment_id']."'>#".$i."</a> | ";
			echo "<a href='".BASEDIR."profile.php?lookup=".$data['comment_name']."'>".showLabel($data['comment_name'])."</a>\n";
			echo "<span class='small'>".timepassed($data['comment_datestamp'])."</span></td></tr>\n<tr><td class='tbl1' style='vertical-align:top;'>\n";
			echo nl2br(parseubb(parsesmileys($data['comment_message'])))."</td></tr>\n";
			$i++;
		}
		
		echo "</table>";
		if (iADMIN && checkrights("C")) {
			echo "<div align='right' class='tbl2'><a href='".ADMIN."comments.php".$aidlink."&amp;ctype=$ctype&amp;cid=$cid'>".$locale['c106']."</a></div>\n";
		}
		if ($c_rows > 10) {
			echo "<div style='text-align:center;margin-top:5px;'>".makecommentnav($_GET['c_start'], 10, $c_rows, 3, $clink."&amp;")."</div>\n";
		}
	} else {
		echo $locale['uc284']."\n";
	}
}

function makecommentnav($start, $count, $total, $range = 0, $link) {

	global $locale;

	$pg_cnt = ceil($total / $count);
	if ($pg_cnt <= 1) { return ""; }

	$idx_back = $start - $count;
	$idx_next = $start + $count;
	$cur_page = ceil(($start + 1) / $count);

	$res = $locale['global_092']." ".$cur_page.$locale['global_093'].$pg_cnt.": ";
	if ($idx_back >= 0) {
		if ($cur_page > ($range + 1)) {
			$res .= "<a href='".$link."ce4dstart=0'>1</a>...";
		}
	}
	$idx_fst = max($cur_page - $range, 1);
	$idx_lst = min($cur_page + $range, $pg_cnt);
	if ($range == 0) {
		$idx_fst = 1;
		$idx_lst = $pg_cnt;
	}
	for ($i = $idx_fst; $i <= $idx_lst; $i++) {
		$offset_page = ($i - 1) * $count;
		if ($i == $cur_page) {
			$res .= "<span><strong>".$i."</strong></span>";
		} else {
			$res .= "<a href='".$link."c_start=".$offset_page."'>".$i."</a>";
		}
	}
	if ($idx_next < $total) {
		if ($cur_page < ($pg_cnt - $range)) {
			$res .= "...<a href='".$link."c_start=".($pg_cnt - 1) * $count."'>".$pg_cnt."</a>\n";
		}
	}
	
	return "<div class='pagenav'>\n".$res."</div>\n";
}
?>