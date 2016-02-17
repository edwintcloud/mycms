<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
echo "<div class='mainforum'>\n";

renderNav(false, $announcementCheck);

if ($rows != 0) {
	if ($_GET['rowstart'] == 0 && $fdata['thread_poll'] == "1") {
		if (iMEMBER) {
			$presult = dbquery(
				"SELECT tfp.*, tfv.forum_vote_user_id FROM ".DB_FORUM_POLLS." tfp 
				LEFT JOIN ".DB_FORUM_POLL_VOTERS." tfv
				ON tfp.thread_id=tfv.thread_id AND forum_vote_user_id='".$userdata['user_id']."'
				WHERE tfp.thread_id='".$_GET['thread_id']."'"
			);
		} else {
			$presult = dbquery(
				"SELECT tfp.* FROM ".DB_FORUM_POLLS." tfp
				WHERE tfp.thread_id='".$_GET['thread_id']."'"
			);
		}
		if (dbrows($presult)) {
			$pdata = dbarray($presult); $i = 1;
			if (iMEMBER) { echo "<form name='voteform' method='post' action='".FUSION_SELF."?forum_id=".$fdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n"; }
			echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border' style='margin-bottom:5px'>\n<tr>\n";
			echo "<td align='center' class='tbl2'><strong>".$pdata['forum_poll_title']."</strong></td>\n</tr>\n<tr>\n<td class='tbl1'>\n";
			echo "<table align='center' cellpadding='0' cellspacing='0'>\n";
			$presult = dbquery("SELECT * FROM ".DB_FORUM_POLL_OPTIONS." WHERE thread_id='".$_GET['thread_id']."' ORDER BY forum_poll_option_id ASC");
			$poll_options = dbrows($presult);
			while ($pvdata = dbarray($presult)) {
				if ((iMEMBER && isset($pdata['forum_vote_user_id']) || (!$fdata['forum_vote'] || !checkgroup($fdata['forum_vote'])))) {
					$option_votes = ($pdata['forum_poll_votes'] ? number_format(100 / $pdata['forum_poll_votes'] * $pvdata['forum_poll_option_votes']) : 0);
					echo "<tr>\n<td class='tbl1'>".$pvdata['forum_poll_option_text']."</td>\n";
					echo "<td class='tbl1'><img src='".get_image("pollbar")."' alt='".$pvdata['forum_poll_option_text']."' height='12' width='".(200 / 100 * $option_votes)."' class='poll' /></td>\n";
					echo "<td class='tbl1'>".$option_votes."%</td><td class='tbl1'>[".$pvdata['forum_poll_option_votes']." ".($pvdata['forum_poll_option_votes'] == 1 ? $locale['global_133'] : $locale['global_134'])."]</td>\n</tr>\n";
				} else {
					echo "<tr>\n<td class='tbl1'><label><input type='radio' name='poll_option' value='".$i."' style='vertical-align:middle' /> ".$pvdata['forum_poll_option_text']."</label></td>\n</tr>\n";
					$i++;
				}
			}
			if ((iMEMBER && isset($pdata['forum_vote_user_id']) || (!$fdata['forum_vote'] || !checkgroup($fdata['forum_vote'])))) {
				echo "<tr>\n<td align='center' colspan='4' class='tbl1'>".$locale['480']." : ".$pdata['forum_poll_votes']."</td>\n</tr>\n";
			} else {
				echo "<tr>\n<td class='tbl1'><input type='submit' name='cast_vote' value='".$locale['481']."' class='button' /></td>\n</tr>\n";
			}
			echo "</table>\n</td>\n</tr>\n</table>\n";
			if (iMEMBER) { echo "</form>\n"; }
		}
	}
}

echo "<table cellpadding='0' cellspacing='0' width='100%'>\n";
echo "<tr>";
if (($rows > $posts_per_page) || ($can_post || $can_reply)) {
	echo "<td align='left' style='padding:0px 0px 4px 5px;white-space:nowrap;' width='1%'>\n";
	if (iMEMBER && ($can_post || $can_reply)) {
		if (!$fdata['thread_locked'] && $can_reply) {
			echo "<a href='post.php?action=reply&amp;forum_id=".$fdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'><img src='".get_image("reply")."' alt='".$locale['565']."' style='border:0px' /></a>&nbsp;\n";
		}
		if ($can_post) {
			echo "<nobr><a href='post.php?action=newthread&amp;forum_id=".$fdata['forum_id']."'><img src='".get_image("newthread")."' alt='".$locale['566']."' style='border:0px' /></a>&nbsp;\n";
		}
		echo "</td><td>";
	}
	if ($rows > $posts_per_page) { 
		echo "<nobr>".makePageNav($_GET['rowstart'],$posts_per_page,$rows,3,FUSION_SELF."?thread_id=".$_GET['thread_id']."&amp;").""; 
	}
	echo "</td>";
}
echo "<td style='text-align:right;'>";
	renderTools();
echo "</td>
</tr></table><br />";

$result = dbquery(
	"SELECT p.*, u.*, u2.user_name AS edit_name
	FROM ".DB_POSTS." p
	LEFT JOIN ".DB_USERS." u ON p.post_author = u.user_id
	LEFT JOIN ".DB_USERS." u2 ON p.post_edituser = u2.user_id AND post_edituser > '0'
	WHERE p.thread_id='".$_GET['thread_id']."' ORDER BY post_datestamp LIMIT ".$_GET['rowstart'].",$posts_per_page"
);
if (iMOD) { echo "<form name='mod_form' method='post' action='".FUSION_SELF."?thread_id=".$_GET['thread_id']."&amp;rowstart=".$_GET['rowstart']."'>\n"; }
$numrows = dbrows($result);
$current_row = 1;
while ($data = dbarray($result)) {
	$message = $data['post_message'];
	if ($data['post_smileys']) { $message = parsesmileys($message); }
	
	echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
	/* --- post header --- */
	echo "<tr><td class='tbl2' style='padding:6px;'><div style='float:right' class='small'><a href='#post_".$data['post_id']."' name='post_".$data['post_id']."' id='post_".$data['post_id']."'>#".($current_row+$_GET['rowstart'])."</a></div>
	".timePassed($data['post_datestamp'], false);
	if ($data['post_edittime'] != "0") {
		$r = dbquery("select * from ".$db_prefix."fb_posts where post_id='".$data['post_id']."'");
		if(dbrows($r)){ $d = dbarray($r); $edit_reason = $d['post_editreason'];
		} else { $edit_reason = ""; }
		echo "<span class='small'";
		if(isset($edit_reason) && $edit_reason !== ""){ echo " title='header=[".$locale['fb502']."] body=[$edit_reason]'"; } 
		echo ">&nbsp;|&nbsp;".$locale['508']."
		<a href='../profile.php?lookup=".$data['post_edituser']."'>".$data['edit_name']."</a> ".timePassed($data['post_edittime'], false);
		echo "</span>\n";
	}
	echo "</td></tr>\n";
	
	/* --- user box --- */
	echo "<tr><td class='tbl2' style='padding:7px;'><div style='float:right; padding:10px;'>";
	echo "<!--forum_thread_user_info--><strong>".$locale['502']."</strong> ".$data['user_posts']."<br />\n";
	echo "<strong>".$locale['504']."</strong> ".showdate("%d.%m.%y", $data['user_joined'])."\n";
	if($data['user_location']){ echo "<br /><strong>".$locale['fb500'].":</strong> ".stripslash($data['user_location'])."\n"; }
	if($data['user_birthdate'] !== "0000-00-00"){
		$birthday = explode("-", $data['user_birthdate']);
		$age = (strftime("%Y") - $birthday[0]);
		if(strftime("%m") < $birthday[1]){
			$age--;
		} elseif(strftime("%m") == $birthday[1]) {
			if(strftime("%d") < $birthday[2]){
				$age--;
			}
		}
		echo "<br /><strong>".$locale['fb512']."</strong> $age\n";
	}
	echo showWarning($data['user_id']);
	echo "</div><div><table width='75%' cellspacing='0' cellpadding='0' border='0'>
	<tr>";
	if ($data['user_avatar'] && file_exists(IMAGES."avatars/".$data['user_avatar'])) {
		echo "<td style='width:100px;padding:2px;'><img src='".IMAGES."avatars/".$data['user_avatar']."' alt='".$locale['567']."' /></td>\n";
	} else {
		if($fb4['no_avatar']){
			echo "<td style='width:100px;padding:2px;'><img src='".IMAGES."noav.gif' alt='".$locale['567']."' /></td>\n";
		}
	}
	echo "<td style='padding:8px;'>
	<span style='font-size:18px; font-weight:bold;'><a href='".BASEDIR."profile.php?lookup=".$data['user_id']."'>".showLabel($data['user_id'], false, "post")."</a>";
	renderAwards($data['user_id']);
	echo "</span>";
	if ($fb4['user_titles_posts'] && $fb4['user_titles']) {
		$titleLookup = dbquery("select * from ".$db_prefix."fb_titles where title_id='".$data['user_title']."' and (".useraccess("title_access").")");
		if(dbrows($titleLookup)){
			$titleData = dbarray($titleLookup);
			$title = stripslash($titleData['title_title']);
		} else {
			$title = stripslash($data['user_title']);
		}
		$title = "<br />
	<span style='font-size:12px;'><b>$title</b><br />";
	} else {
		$title = "";
	}
	
	echo $title;
	renderMods();
	if($title) echo "</span>\n";
	echo "</td></tr></table></div></td></tr>\n";
	echo "<tr>\n<td valign='top' class='tbl1' style='padding:10px;'>\n";
	
	if ($current_row == 1) {
		add_to_title($locale['global_201'].$fdata['thread_subject']);
		$post_res = dbquery("select * from ".DB_PREFIX."fb_posts where post_id='".$data['post_id']."'");
		if(dbrows($post_res)){
			$post_data = dbarray($post_res);
			if($post_data['post_icon']){
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/".$post_data['post_icon']."' alt='' style='vertical-align:middle;'>&nbsp;";
			} else {
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png' alt='' style='vertical-align:middle;'>&nbsp;";
			}
		} else {
			$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png' alt='' style='vertical-align:middle;'>&nbsp;";
		}
		if(!$fb4['post_icons']) $ficon="";
		echo "<div style='padding-bottom:2px;'>$ficon".($announcementCheck ? $locale['fb902'] : "")."<strong>
		<!--forum_thread_title-->".$fdata['thread_subject']."</strong></div><hr>";
	}
	if (iMOD) { echo "<div style='float:right; padding-top:1px; border:0px;'><input type='checkbox' name='delete_post[]' value='".$data['post_id']."' /></div>\n"; }
	
	if (isset($_GET['highlight'])) {
		$words = explode(" ", urldecode($_GET['highlight']));
		$message = parseubb(highlight_words($words, $message));
	} else {
		$message = parseubb($message);
	}
	echo nl2br($message);	
	$a_result = dbquery("select * from ".$db_prefix."forum_attachments where post_id='".$data['post_id']."'");
	$a_files = ""; $a_images = ""; $i_files = 0; $i_images = 0;
	if(dbrows($a_result)){
		while($a_data = dbarray($a_result)){
			if (in_array($a_data['attach_ext'], $imagetypes) && @getimagesize(FORUM."attachments/".$a_data['attach_name'])) {
				$a_images .= display_image_fb($a_data['attach_name'])."\n";
				$i_images++;
			} else {
				if($fb4['attach_count']){
					$fb_res = dbquery("select * from ".DB_PREFIX."fb_attachments where attach_id='".$a_data['attach_id']."'");
					if(dbrows($fb_res)){
						$fb_data = dbarray($fb_res);
						$count = $fb_data['attach_count'].($fb_data['attach_count'] == "1" ? $locale['fb510'] : $locale['fb509']);
					} else {
						$count = "0".$locale['fb509'];
					}
				} else {
					$count = "";
				}
				if($i_files > 0) $a_files .= "<br />";
				$a_files .= "<a href='".FUSION_SELF."?thread_id=".$_GET['thread_id']."&amp;getfile=".$a_data['attach_id']."'>".$a_data['attach_name']."</a> [<span class='small'>".parsebytesize(filesize(FORUM."attachments/".$a_data['attach_name']))." / ".$count."</span>]\n";
				$i_files++;
			}
		}
		if($a_files){
			echo "<br /><br /><fieldset style='border:1px solid #ccc;width:320px;'>
				<legend>".$locale['fb568']."</legend>
				<div style='padding:3px;width:320px;'>$a_files</div>
			</fieldset><br />\n";
		}
		if($a_images){
			if(!$a_files) echo "<br /><br />\n";
			echo "<fieldset style='border:1px solid #ccc;width:320px;'>
				<legend>".$locale['fb567']."</legend>
				<div style='padding:3px;width:320px;'>$a_images</div>
			</fieldset><br />\n";
		}
	}
	if ($data['post_showsig'] && array_key_exists("user_sig", $data) && $data['user_sig']) {
		echo "\n<br /><hr />".nl2br(parseubb(parsesmileys($data['user_sig']), "b|i|u||center|small|url|mail|img|color"));
	}
	if(((dbrows(dbquery("select * from ".DB_PREFIX."fb_rate where rate_post='".$data['post_id']."'")) || (iMEMBER && $userdata['user_id'] !== $data['user_id']))  && $fb4['show_ratings']) || (iMEMBER & ($can_post || $can_reply))){
		echo "<br /><br /><br />\n";
		
		if($fb4['show_ratings']){
			postRatings($data['post_id']);
			
			echo "<div style='float:right;'>\n";
			if(iMEMBER && $userdata['user_id'] !== $data['user_id']){
				echo "<span id='rb_".$data['post_id']."' style='vertical-align:middle;'>";
				showRatings($data['post_id'], $userdata['user_id'], $data['post_author'], false);
				echo "</span>&nbsp;";
			}
		} else {
			echo "<div style='float:right;'>\n";
		}
		if (iMEMBER && ($can_post || $can_reply)) {
			if (!$fdata['thread_locked']) {
				echo "<a href='post.php?action=reply&amp;forum_id=".$data['forum_id']."&amp;thread_id=".$data['thread_id']."&amp;post_id=".$data['post_id']."&amp;quote=".$data['post_id']."'><img src='".get_image("quote")."' alt='".$locale['569']."' style='border:0px;vertical-align:middle' /></a>\n";
				if (iMOD || ($lock_edit && $last_post['post_id'] == $data['post_id'] && $userdata['user_id'] == $data['post_author']) || (!$lock_edit && $userdata['user_id'] == $data['post_author'])) {
					echo "<a href='post.php?action=edit&amp;forum_id=".$data['forum_id']."&amp;thread_id=".$data['thread_id']."&amp;post_id=".$data['post_id']."'><img src='".get_image("forum_edit")."' alt='".$locale['568']."' style='border:0px;vertical-align:middle' /></a>\n";
				}
			} else {
				if (iMOD) {
					echo "<a href='post.php?action=edit&amp;forum_id=".$data['forum_id']."&amp;thread_id=".$data['thread_id']."&amp;post_id=".$data['post_id']."'><img src='".get_image("forum_edit")."' alt='".$locale['568']."' style='border:0px;vertical-align:middle' /></a>\n";
				}
			}
		}
		if(!$fb4['buttons']) { echo (iMEMBER && ($can_post || $can_reply) ? " :: " : "").showStatus($data['user_id'], false); } 
		else { echo (iMEMBER && ($can_post || $can_reply) ? " " : "").showStatus($data['user_id']); }
		echo "</div>\n";
	}
	echo "<!--sub_forum_post--></td>\n</tr>\n";
	$current_row++;
	echo "</table><br />";
}
echo "</div>";
opentable($locale['fb501']);
?>