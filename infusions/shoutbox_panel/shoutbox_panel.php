<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2009 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: shoutbox_panel.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| Modified to: DeLuxe ShoutBox Panel v1.0
| Author: Smokeman
| Website: PHPFusion-Tips.dk
| URL: http://www.phpfusion-tips.dk/
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (file_exists(INFUSIONS."shoutbox_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."shoutbox_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."shoutbox_panel/locale/English.php";
}

include LOCALE.LOCALESET."view_profile.php";
include LOCALE.LOCALESET."user_fields.php";
include LOCALE.LOCALESET."user_fields/user_shouts-stat.php";

echo "<script type='text/javascript' language='JavaScript' src='".INCLUDES."jscript.js'></script>";

add_to_head("<style type=\"text/css\">

   /* easyscroll navigation buttons */

   #easyscrollnav, #easyscrollnav li{
      height:15px;
      line-height:0px;
      margin:0;
      padding:0;
      }
   #easyscrollnav{
      margin:0 0;
      }
   #easyscrollnav li{
      list-style:none;
      float:right;
      margin-right:5px;
      padding:0 0;
      cursor:pointer;
      }
   #easyscrollnav li.over{
      text-decoration:none;
      }

#btnUp{
background-image: url(".INFUSIONS."shoutbox_panel/images/up.gif);
background-repeat: no-repeat;
float: right;
height: 15px;
width: 15px;
margin-right: 0px;
}

#btnUp.over {
background-image: url(".INFUSIONS."shoutbox_panel/images/up.gif);
background-repeat: no-repeat;
text-decoration:none;
}


#btnReset{
background-image: url(".INFUSIONS."shoutbox_panel/images/downscroll.gif);
background-repeat: no-repeat;
float: right;
height: 15px;
width: 15px;
margin-right: 0px;

}

#btnReset.over {
background-image: url(".INFUSIONS."shoutbox_panel/images/downscroll.gif);
background-repeat: no-repeat;
text-decoration:none;
}

#btnDown{
background-image: url(".INFUSIONS."shoutbox_panel/images/reset.gif);
background-repeat: no-repeat;
float: right;
height: 15px;
width: 15px;
margin-right: 0px;

}

#btnDown.over {
background-image: url(".INFUSIONS."shoutbox_panel/images/reset.gif);
background-repeat: no-repeat;
text-decoration:none;
}


   /* // easyscroll navigation buttons */

/* // end easyscroll */


</style>");


add_to_head("<script type='text/javascript' src='".INFUSIONS."shoutbox_panel/includes/easyscroll.js'></script>");

$link = FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY : "");
$link = preg_replace("^(&amp;|\?)s_action=(edit|delete)&amp;shout_id=\d*^", "", $link);
$sep = stristr($link, "?") ? "&amp;" : "?";

if (iMEMBER && (isset($_GET['s_action']) && $_GET['s_action'] == "delete") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
	if ((iADMIN && checkrights("S")) || (iMEMBER && dbcount("(shout_id)", DB_SHOUTBOX, "shout_id='".$_GET['shout_id']."' AND shout_name='".$userdata['user_id']."'"))) {
		$result = dbquery("DELETE FROM ".DB_SHOUTBOX." WHERE shout_id='".$_GET['shout_id']."'".(iADMIN ? "" : " AND shout_name='".$userdata['user_id']."'"));
	}
	redirect($link);
}

function sbwrap($text) {

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

		if (!$tags && $chars == 18) {
			$chr .= "<br />";
			$chars = 0;
		}
		$res .= $chr;
	}

	return $res;
}

openside($locale['global_150']);
if (iMEMBER || $settings['guestposts'] == "1") {
	include_once INCLUDES."bbcode_include.php";
	if (isset($_POST['post_shout'])) {
		$flood = false;
		if (iMEMBER) {
			$shout_name = $userdata['user_id'];
		} elseif ($settings['guestposts'] == "1") {
			$shout_name = trim(stripinput($_POST['shout_name']));
			$shout_name = preg_replace("(^[0-9]*)", "", $shout_name);
			if (isnum($shout_name)) { $shout_name = ""; }
			include_once INCLUDES."securimage/securimage.php";
			$securimage = new Securimage();
			if (!isset($_POST['sb_captcha_code']) || $securimage->check($_POST['sb_captcha_code']) == false) { redirect($link); }
		}
		$shout_message = str_replace("\n", " ", $_POST['shout_message']);
		$shout_message = preg_replace("/^(.{255}).*$/", "$1", $shout_message);
		$shout_message = trim(stripinput(censorwords($shout_message)));
		if (iMEMBER && (isset($_GET['s_action']) && $_GET['s_action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
			$comment_updated = false;
			if ((iADMIN && checkrights("S")) || (iMEMBER && dbcount("(shout_id)", DB_SHOUTBOX, "shout_id='".$_GET['shout_id']."' AND shout_name='".$userdata['user_id']."'"))) {
				if ($shout_message) {
					$result = dbquery("UPDATE ".DB_SHOUTBOX." SET shout_message='$shout_message' WHERE shout_id='".$_GET['shout_id']."'".(iADMIN ? "" : " AND shout_name='".$userdata['user_id']."'"));
				}
			}
			redirect($link);
		} elseif ($shout_name && $shout_message) {
			require_once INCLUDES."flood_include.php";
			if (!flood_control("shout_datestamp", DB_SHOUTBOX, "shout_ip='".USER_IP."'")) {
				$result = dbquery("INSERT INTO ".DB_SHOUTBOX." (shout_name, shout_message, shout_datestamp, shout_ip) VALUES ('$shout_name', '$shout_message', '".time()."', '".USER_IP."')");
			}
		}
		redirect($link);
	}
	if (iMEMBER && (isset($_GET['s_action']) && $_GET['s_action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
		$esresult = dbquery(
			"SELECT ts.*, tu.user_id, tu.user_name FROM ".DB_SHOUTBOX." ts
			LEFT JOIN ".DB_USERS." tu ON ts.shout_name=tu.user_id
			WHERE ts.shout_id='".$_GET['shout_id']."'"
		);
		if (dbrows($esresult)) {
			$esdata = dbarray($esresult);
			if ((iADMIN && checkrights("S")) || (iMEMBER && $esdata['shout_name'] == $userdata['user_id'] && isset($esdata['user_name']))) {
				if ((isset($_GET['s_action']) && $_GET['s_action'] == "edit") && (isset($_GET['shout_id']) && isnum($_GET['shout_id']))) {
					$edit_url = $sep."s_action=edit&amp;shout_id=".$esdata['shout_id'];
				} else {
					$edit_url = "";
				}
				$shout_link = $link.$edit_url;
				$shout_message = $esdata['shout_message'];
			}
		} else {
			$shout_link = $link;
			$shout_message = "";
		}
	} else {
		$shout_link = $link;
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

	echo "<form name='shout_form' method='post' action='".$shout_link."'>\n";
	if (iGUEST) {
		echo $locale['global_151']."<br />\n";
		echo "<input type='text' name='shout_name' value='' class='textbox' maxlength='30' style='width:140px' /><br />\n";
		echo $locale['global_152']."<br />\n";
	}
	echo "<div align='center' valign='middle'>".$locale['dsp007']."<span id='count_display' style='padding : 1px 3px 1px 3px; border:1px solid;'><strong>200</strong></span><br /><br /><textarea class='textbox' name='shout_message' rows='4' cols='20' style=\"width:140px;\" onfocus=\"if(this.value=='')this.value='';\" onblur=\"if(this.value=='')this.value=='';\" onKeyDown=\"textCounter(this,'count_display',200);\" onKeyUp=\"textCounter(this,'count_display',200);\">".$shout_message.(empty($shout_message)?"":"")."</textarea>\n</div>\n";
	echo display_bbcodes("140px;", "shout_message", "shout_form", "smiley|b|i|u|url|color")."\n";
	if (iGUEST) {
		echo $locale['global_158']."<br />\n";
		echo "<img id='sb_captcha' src='".INCLUDES."securimage/securimage_show.php' alt='' /><br />\n";
    echo "<a href='".INCLUDES."securimage/securimage_play.php'><img src='".INCLUDES."securimage/images/audio_icon.gif' alt='' class='tbl-border' style='margin-bottom:1px' /></a>\n";
    echo "<a href='#' onclick=\"document.getElementById('sb_captcha').src = '".INCLUDES."securimage/securimage_show.php?sid=' + Math.random(); return false\"><img src='".INCLUDES."securimage/images/refresh.gif' alt='' class='tbl-border' /></a><br />\n";
		echo $locale['global_159']."<br />\n<input type='text' name='sb_captcha_code' class='textbox' style='width:100px' /><br />\n";
	}
	echo "<input type='submit' name='post_shout' value='".$locale['global_153']."' class='button' />\n";
	echo "</form>\n";
} else {
	echo "<div style='text-align:center'>".$locale['global_154']."</div><br />\n";
}
$numrows = dbcount("(shout_id)", DB_SHOUTBOX);
$result = dbquery(
	"SELECT ts.*, tu.*, tu.user_id, tu.user_name FROM ".DB_SHOUTBOX." ts
	LEFT JOIN ".DB_USERS." tu ON ts.shout_name=tu.user_id
	ORDER BY ts.shout_datestamp DESC LIMIT 0,".$settings['numofshouts']
);

if (dbrows($result)) {
	$i = 0;

echo "<div id='myContent'>";

	while ($data = dbarray($result)) {
$shoutcount = dbcount("(shout_id)", DB_SHOUTBOX, "shout_name='".$data['user_id']."'");
$lseen = time() - $data['user_lastvisit'];

		echo "<div class='shoutboxname'>";

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

			<a href='".BASEDIR."profile.php?lookup=".$data['shout_name']."' class='shoutboxname'><b>".$data['user_name']."</b></a></span> <img src='".INFUSIONS."shoutbox_panel/images/online.png' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$data['user_name']." ".$locale['dsp001']."")."] delay=[0] fade=[off]\" alt='' />\n"; if (iMEMBER) { echo " <a href='".BASEDIR."messages.php?msg_send=".$data['user_id']."'> <img src='".INFUSIONS."shoutbox_panel/images/pm.gif' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$locale['dsp003']." ".$data['user_name']."")."] delay=[0] fade=[off]\" alt='' border='0' /></a></span>\n";}
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

			<a href='".BASEDIR."profile.php?lookup=".$data['shout_name']."' class='shoutboxname'><b>".$data['user_name']."</b></a></span> <img src='".INFUSIONS."shoutbox_panel/images/offline.png' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$data['user_name']." ".$locale['dsp002']."")."] delay=[0] fade=[off]\" alt='' />\n"; if (iMEMBER) { echo " <a href='".BASEDIR."messages.php?msg_send=".$data['user_id']."'> <img src='".INFUSIONS."shoutbox_panel/images/pm.gif' title=\"header=[".$data['user_name']."] body=[".rawurlencode("".$locale['dsp003']." ".$data['user_name']."")."] delay=[0] fade=[off]\" alt='' border='0' /></a></span>\n";}
		} else {
			echo $data['shout_name']."\n";
		}
	}

		echo "</div>\n";

		echo "<div class='shoutboxdate'>".showdate("shortdate", $data['shout_datestamp'])."</div>";
		echo "<div class='shoutbox'>".sbwrap(parseubb(parsesmileys($data['shout_message']), "b|i|u|url|color"))."</div>\n";
		if ((iADMIN && checkrights("S")) || (iMEMBER && $data['shout_name'] == $userdata['user_id'] && isset($data['user_name']))) {
			echo "[<a href='".$link.$sep."s_action=edit&amp;shout_id=".$data['shout_id']."#edit_shout"."' class='shoutboxedit' title=\"header=[".$locale['global_076']."] body=[".rawurlencode("".$locale['dsp005']."")."] delay=[0] fade=[off]\">".$locale['global_076']."</a>]\n";
			echo "[<a href='".$link.$sep."s_action=delete&amp;shout_id=".$data['shout_id']."' class='shoutboxdel' title=\"header=[".$locale['global_157']."] body=[".rawurlencode("".$locale['dsp006']."")."] delay=[0] fade=[off]\">".$locale['global_157']."</a>]<br /><br />\n";
		}
		$i++;
		if ($i != $numrows) { echo "\n"; }
	}


echo "</div>";

	if ($numrows > $settings['numofshouts']) {
		echo "<div style='text-align:center'>\n<a href='".INFUSIONS."shoutbox_panel/shoutbox_archive.php' class='side' title=\"header=[".$locale['global_155']."] body=[".rawurlencode("".$locale['dsp004']."")."] delay=[0] fade=[off]\">".$locale['global_155']."</a>\n</div>\n";
	}
} else {
	echo "<div>".$locale['global_156']."</div>\n";
}

closeside();
?>
