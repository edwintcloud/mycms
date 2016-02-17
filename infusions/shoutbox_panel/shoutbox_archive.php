<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: shoutbox_archive.php
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

include_once INCLUDES."bbcode_include.php";

if (iMEMBER && (isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
	if ((iADMIN && checkrights("S")) || (iMEMBER && dbcount("(shout_id)", DB_SHOUTBOX, "shout_id='".$_GET['shout_id']."' AND shout_name='".$userdata['user_id']."'"))) {
		$result = dbquery("DELETE FROM ".DB_SHOUTBOX." WHERE shout_id='".$_GET['shout_id']."'".(iADMIN ? "" : " AND shout_name='".$userdata['user_id']."'"));
	}
	redirect(FUSION_SELF);
}

function sbawrap($text) {
	
	$i = 0; $tags = 0; $chars = 0; $res = "";
	
	$str_len = strlen($text);
	
	for ($i = 0; $i < $str_len; $i++) {
		$chr = substr($text, $i, 1);
		if ($chr == "<") {
			if (substr($text, ($i + 1), 6) == "a href" || substr($text, ($i + 1), 3) == "img") {
				$chr = " ".$chr;
				$chars = 0;
			}
			$tags++;
		} elseif ($chr == "&") {
			if (substr($text, ($i + 1), 5) == "quot;") {
				$chars = $chars - 5;
			} elseif (substr($text, ($i + 1), 4) == "amp;" || substr($text, ($i + 1), 4) == "#39;" || substr($text, ($i + 1), 4) == "#92;") {
				$chars = $chars - 4;
			} elseif (substr($text, ($i + 1), 3) == "lt;" || substr($text, ($i + 1), 3) == "gt;") {
				$chars = $chars - 3;
			}
		} elseif ($chr == ">") {
			$tags--;
		} elseif ($chr == " ") {
			$chars = 0;
		} elseif (!$tags) {
			$chars++;
		}
		
		if (!$tags && $chars == 40) {
			$chr .= " ";
			$chars = 0;
		}
		$res .= $chr;
	}
	
	return $res;
}

add_to_title($locale['global_200'].$locale['global_155']);

opentable($locale['global_155']);
if (iMEMBER || $settings['guestposts'] == "1") {
	if (isset($_POST['post_ashout'])) {
		$flood = false;
		if (iMEMBER) {
			$shout_name = $userdata['user_id'];
		} elseif ($settings['guestposts'] == "1") {
			$shout_name = trim(stripinput($_POST['shout_name']));
			$shout_name = preg_replace("(^[0-9]*)", "", $shout_name);
			if (isnum($shout_name)) { $shout_name = ""; }
		}
		$shout_message = str_replace("\n", " ", $_POST['shout_message']);
		$shout_message = preg_replace("/^(.{255}).*$/", "$1", $shout_message);
		$shout_message = trim(stripinput(censorwords($shout_message)));
		if (iMEMBER && (isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
			$comment_updated = false;
			if ((iADMIN && checkrights("S")) || (iMEMBER && dbcount("(shout_id)", DB_SHOUTBOX, "shout_id='".$_GET['shout_id']."' AND shout_name='".$userdata['user_id']."'"))) {
				if ($shout_message) {
					$result = dbquery("UPDATE ".DB_SHOUTBOX." SET shout_message='$shout_message' WHERE shout_id='".$_GET['shout_id']."'".(iADMIN ? "" : " AND shout_name='".$userdata['user_id']."'"));
				}
			}
			redirect(FUSION_SELF);
		} elseif ($shout_name && $shout_message) {
			require_once INCLUDES."flood_include.php";
			if (!flood_control("shout_datestamp", DB_SHOUTBOX, "shout_ip='".USER_IP."'")) {
				$result = dbquery("INSERT INTO ".DB_SHOUTBOX." (shout_name, shout_message, shout_datestamp, shout_ip) VALUES ('$shout_name', '$shout_message', '".time()."', '".USER_IP."')");
			}
			redirect(FUSION_SELF);
		}
	}
	if (iMEMBER && (isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
		$esresult = dbquery(
			"SELECT ts.*, tu.user_id, tu.user_name FROM ".DB_SHOUTBOX." ts
			LEFT JOIN ".DB_USERS." tu ON ts.shout_name=tu.user_id
			WHERE ts.shout_id='".$_GET['shout_id']."'"
		);
		if (dbrows($esresult)) {
			$esdata = dbarray($esresult);
			if ((iADMIN && checkrights("S")) || (iMEMBER && $esdata['shout_name'] == $userdata['user_id'] && isset($esdata['user_name']))) {
				if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
					$edit_url = "?action=edit&amp;shout_id=".$esdata['shout_id'];
				} else {
					$edit_url = "";
				}
				$shout_link = FUSION_SELF.$edit_url;
				$shout_message = $esdata['shout_message'];
			}
		} else {
			$shout_link = FUSION_SELF;
			$shout_message = "";
		}
	} else {
		$shout_link = FUSION_SELF;
		$shout_message = "";
	}


	?>
	<script type="text/javascript">
function textCounter(textarea, counterID, maxLen) {
cnt = document.getElementById(counterID);
if (textarea.value.length > maxLen)
{
textarea.value = textarea.value.substring(0,maxLen);
}
cnt.innerHTML = maxLen - textarea.value.length;
}
</script>
	<?php


	echo "<a id='edit_shout' name='edit_shout'></a>\n";
	echo "<form name='chatform2' method='post' action='".$shout_link."'>\n";
	echo "<div style='text-align:center'>\n";
	if (iGUEST) {
		echo $locale['global_151']."<br />\n";
		echo "<input type='text' name='shout_name' value='' class='textbox' maxlength='30' style='width:140px;' /><br />\n";
		echo $locale['global_152']."<br />\n";
	}
	echo "<div align='center' valign='middle'>".$locale['dsp007']."<span id='count_display' style='padding : 1px 3px 1px 3px; border:1px solid;'><strong>200</strong></span><br /><br /><textarea class='textbox' name='shout_message' rows='4' cols='20' style=\"width:165px;\" onfocus=\"if(this.value=='')this.value='';\" onblur=\"if(this.value=='')this.value=='';\" onKeyDown=\"textCounter(this,'count_display',200);\" onKeyUp=\"textCounter(this,'count_display',200);\">".$shout_message.(empty($shout_message)?"":"")."</textarea>\n</div>\n";
	echo "<div style='text-align:center'>".display_bbcodes("100%", "shout_message", "chatform2", "smiley|b|i|u|url|color")."</div>\n";
	echo "<input type='submit' name='post_ashout' value='".$locale['global_153']."' class='button' />\n";
	echo "</div>\n</form>\n<br />\n";
} else {
	echo "<div style='text-align:center'>".$locale['global_154']."</div>\n";
}
$rows = dbcount("(shout_id)", DB_SHOUTBOX);
if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
if ($rows != 0) {
	$result = dbquery(
		"SELECT * FROM ".DB_SHOUTBOX." LEFT JOIN ".DB_USERS."
		ON ".DB_SHOUTBOX.".shout_name=".DB_USERS.".user_id
		ORDER BY shout_datestamp DESC LIMIT ".$_GET['rowstart'].",20"
	);
	while ($data = dbarray($result)) {
		echo "<div class='tbl2'>\n";
		if ((iADMIN && checkrights("S")) || (iMEMBER && $data['shout_name'] == $userdata['user_id'] && isset($data['user_name']))) {
			echo "<div style='float:right'>\n";
      echo "<a href='".FUSION_SELF."?action=edit&amp;shout_id=".$data['shout_id']."' class='shoutboxedit' title=\"header=[".$locale['global_076']."] body=[".rawurlencode("".$locale['dsp005']."")."] delay=[0] fade=[off]\">".$locale['global_076']."</a> | \n";
			echo "<a href='".FUSION_SELF."?action=delete&amp;shout_id=".$data['shout_id']."' class='shoutboxdel' title=\"header=[".$locale['global_157']."] body=[".rawurlencode("".$locale['dsp006']."")."] delay=[0] fade=[off]\">".$locale['global_157']."</a>\n</div>\n";
		}
		
		
		
$shoutcount = dbcount("(shout_id)", DB_SHOUTBOX, "shout_name='".$data['user_id']."'");
$lseen = time() - $data['user_lastvisit'];
		
	if($lseen < 60) {
		if ($data['user_name']) {

				if ($data['user_avatar'] != "") { $avatar = "<img src='".IMAGES."avatars/".$data['user_avatar']."' border='0' alt='' />"; }
				else { $avatar = "<img src='".INFUSIONS."shoutbox_panel/images/noav.gif' border='0' alt='' />"; }
				echo "<span class='small' title=\"header=[".$data['user_name']."] body=[".rawurlencode("<center>$avatar</center></span><hr />
<span class='small'><b><font color='#0066cc'>".$locale['global_101'].":</font> <font color='#ff6600'>".$data['user_name']."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['424']."</font> <font color='#ff6600'>".getuserlevel($data['user_level'])."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['u040']."</font> <font color='#ff6600'>".showdate("shortdate", $data['user_joined'])."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['u041']."</font> <font color='#ff6600'>".showdate("shortdate", $data['user_lastvisit'])."</font></b> <br /><br />
<b><font color='#0066cc'>".$locale['uf_shouts-stat']."</font> <font color='#ff6600'>$shoutcount</font></b> ")."] delay=[0] fade=[off]\">




			<a href='".BASEDIR."profile.php?lookup=".$data['shout_name']."' class='comment-name'><b>".$data['user_name']."</b></a></span> <img src='".INFUSIONS."shoutbox_panel/images/online.png' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$data['user_name']." ".$locale['dsp001']."")."] delay=[0] fade=[off]\" alt='' />\n"; if (iMEMBER) { echo " <a href='".BASEDIR."messages.php?msg_send=".$data['user_id']."'> <img src='".INFUSIONS."shoutbox_panel/images/pm.gif' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$locale['dsp003']." ".$data['user_name']."")."] delay=[0] fade=[off]\" alt='' border='0' /></a></span>\n";}
		} else {
			echo $data['shout_name']."\n";
		}
	}

	if($lseen > 60) {
		if ($data['user_name']) {

				if ($data['user_avatar'] != "") { $avatar = "<img src='".IMAGES."avatars/".$data['user_avatar']."' border='0' alt='' />"; }
				else { $avatar = "<img src='".INFUSIONS."shoutbox_panel/images/noav.gif' border='0' alt='' />"; }
				echo "<span class='small' title=\"header=[".$data['user_name']."] body=[".rawurlencode("<center>$avatar</center></span><hr />
<span class='small'><b><font color='#0066cc'>".$locale['global_101'].":</font> <font color='#ff6600'>".$data['user_name']."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['424']."</font> <font color='#ff6600'>".getuserlevel($data['user_level'])."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['u040']."</font> <font color='#ff6600'>".showdate("shortdate", $data['user_joined'])."</font></b><br /><br />
<b><font color='#0066cc'>".$locale['u041']."</font> <font color='#ff6600'>".showdate("shortdate", $data['user_lastvisit'])."</font></b> <br /><br />
<b><font color='#0066cc'>".$locale['uf_shouts-stat']."</font> <font color='#ff6600'>$shoutcount</font></b> ")."] delay=[0] fade=[off]\">




			<a href='".BASEDIR."profile.php?lookup=".$data['shout_name']."' class='comment-name'><b>".$data['user_name']."</b></a></span> <img src='".INFUSIONS."shoutbox_panel/images/offline.png' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$data['user_name']." ".$locale['dsp002']."")."] delay=[0] fade=[off]\" alt='' />\n"; if (iMEMBER) { echo " <a href='".BASEDIR."messages.php?msg_send=".$data['user_id']."'> <img src='".INFUSIONS."shoutbox_panel/images/pm.gif' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$locale['dsp003']." ".$data['user_name']."")."] delay=[0] fade=[off]\" alt='' border='0' /></a></span>\n";}
		} else {
			echo $data['shout_name']."\n";
		}
	}
		
		
		
		
		
		echo "<span class='small'>".showdate("longdate", $data['shout_datestamp'])."</span>";
		echo "</div>\n<div class='tbl1'>\n".sbawrap(parseubb(parsesmileys($data['shout_message']), "b|i|u|url|color"))."</div>\n";
	}
} else {
	echo "<div style='text-align:center'><br />\n".$locale['global_156']."<br /><br />\n</div>\n";
}
closetable();

echo "<div align='center' style='margin-top:5px;'>\n".makepagenav($_GET['rowstart'], 20, $rows, 3, FUSION_SELF."?")."\n</div>\n";

require_once THEMES."templates/footer.php";
?>
