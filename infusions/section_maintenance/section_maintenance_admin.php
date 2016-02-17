<?php
/*---------------------------------------------------+
| PHP-Fusion 7 Content Management System
+----------------------------------------------------+
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
|----------------------------------------------------+
| Infusion: Section Maintenance Infusion v2.3
| Filename: section_maintenance_admin.php
| Author: HobbyMan
| Web: http://www.php-fusion.hobbysites.net/
+----------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+----------------------------------------------------*/

require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";
require_once INCLUDES."bbcode_include.php";

include INFUSIONS."section_maintenance/infusion_db.php";

// Check if locale file is available matching the current site locale setting.
if (file_exists(INFUSIONS."section_maintenance/locale/".$settings['locale'].".php")) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS."section_maintenance/locale/".$settings['locale'].".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS."section_maintenance/locale/English.php";
}

add_to_title($locale['global_200'].$locale['title'].$locale['sma226'].$locale['sma227']);

if (!checkrights("SMA") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

if (isset($_POST['savesettings'])) {
	$result = dbquery("UPDATE ".DB_SECTION_MAINTENANCE." SET
		sma_all='".(isNum($_POST['sma_all']) ? $_POST['sma_all'] : "0")."',
		sma_reg='".(isNum($_POST['sma_reg']) ? $_POST['sma_reg'] : "0")."',
		sma_cont='".(isNum($_POST['sma_cont']) ? $_POST['sma_cont'] : "0")."',
		sma_photo='".(isNum($_POST['sma_photo']) ? $_POST['sma_photo'] : "0")."',
		sma_articles='".(isNum($_POST['sma_articles']) ? $_POST['sma_articles'] : "0")."',
		sma_news='".(isNum($_POST['sma_news']) ? $_POST['sma_news'] : "0")."',
		sma_forum='".(isNum($_POST['sma_forum']) ? $_POST['sma_forum'] : "0")."',
		sma_members='".(isNum($_POST['sma_members']) ? $_POST['sma_members'] : "0")."',
		sma_down='".(isNum($_POST['sma_down']) ? $_POST['sma_down'] : "0")."',
		sma_prof='".(isNum($_POST['sma_prof']) ? $_POST['sma_prof'] : "0")."',
		sma_pm='".(isNum($_POST['sma_pm']) ? $_POST['sma_pm'] : "0")."',
		sma_weblinks='".(isNum($_POST['sma_weblinks']) ? $_POST['sma_weblinks'] : "0")."',
		sma_submissions='".(isNum($_POST['sma_submissions']) ? $_POST['sma_submissions'] : "0")."',
		sma_faq='".(isNum($_POST['sma_faq']) ? $_POST['sma_faq'] : "0")."',
		sma_cust='".(isNum($_POST['sma_cust']) ? $_POST['sma_cust'] : "0")."',
		sma_inf='".(isNum($_POST['sma_cust']) ? $_POST['sma_inf'] : "0")."',
	    sma_temp='".(isNum($_POST['sma_temp']) ? $_POST['sma_temp'] : "0")."',
		sma_time='".(isNum($_POST['sma_time']) ? $_POST['sma_time'] : "0")."',
		sma_period='".(isNum($_POST['sma_period']) ? $_POST['sma_period'] : "0")."',
		sma_datestamp='".(isNum($_POST['sma_datestamp']) ? $_POST['sma_datestamp'] : "0")."',
		sma_sign='".addslash(descript($_POST['sma_sign']))."',
		sma_show_sig='".(isNum($_POST['sma_show_sig']) ? $_POST['sma_show_sig'] : "0")."',
		sma_show_image='".(isNum($_POST['sma_show_image']) ? $_POST['sma_show_image'] : "0")."',
		sma_show_admmsg='".(isNum($_POST['sma_show_admmsg']) ? $_POST['sma_show_admmsg'] : "0")."',
		sma_message='".addslash(descript($_POST['sma_message']))."'
	");

	if (!$result) { $error = 1; }
	redirect(FUSION_SELF.$aidlink."&error=".$error);
}

$data_sm = dbarray(dbquery("SELECT * FROM ".DB_SECTION_MAINTENANCE));

$version = $locale['sma225'].$data_sm['sma_version'];

$showgreen = "".$locale['sma204']." <img src='".INFUSIONS."section_maintenance/images/green.gif' border='0' alt='' />";
$showred = "".$locale['sma205']." <img src='".INFUSIONS."section_maintenance/images/red.gif' border='0' alt='' />";
$showgrey = "".$locale['sma205']." <img src='".INFUSIONS."section_maintenance/images/grey.gif' border='0' alt='' />";

if ($data_sm['sma_all'] == 1)
{
  $showgreen = "".$locale['sma205']." <img src='".INFUSIONS."section_maintenance/images/grey.gif' border='0' alt='' />";
  $disable = "onchange='submit();' disabled";
} else {
  $showgreen = "".$locale['sma204']." <img src='".INFUSIONS."section_maintenance/images/green.gif' border='0' alt='' />";
  $disable = "";
}

if ($data_sm['sma_all'] == 0)
{
  $showred = "".$locale['sma205']." <img src='".INFUSIONS."section_maintenance/images/red.gif' border='0' alt='' />";
} else {
  $showgreen = "".$locale['sma205']." <img src='".INFUSIONS."section_maintenance/images/grey.gif' border='0' alt='' />";
}

opentable($locale['title'].$locale['sma226'].$locale['sma227'].$locale['sma226'].$version);

//************************************** Begin Form ***************************************

    echo "<form name='sectionmaint' method='post' action='".FUSION_SELF.$aidlink."'>\n";
    echo "<br /><table align='center' cellpadding='0' cellspacing='0' width='600'><tr>\n";
    echo "<td colspan='3'><b>".$locale['sma101']."</b><br /><br /></td>\n";
    echo "</tr>\n";

//**************************************** Headings ***************************************
  echo "<tr>\n";
  echo "<td class='tbl'align='left'><b>".$locale['sma206']."</b></td>";
  echo "<td class='tbl' align='center'><b>".$locale['sma207']."</b></td>";
  echo "<td class='tbl' align='left'><b>".$locale['sma208']."</b></td>";
  echo "</tr>\n";
  
  //************************************* Registration *************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma116']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_reg' class='textbox' $disable>";
  echo "<option value='0'".($data_sm['sma_reg'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo "<option value='1'".($data_sm['sma_reg'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo "</select></td>\n";
  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_reg'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_reg'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}
  echo "</td>\n</tr>\n";

//************************************* Contact *************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma113']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_cont' class='textbox' $disable>";
  echo "<option value='0'".($data_sm['sma_cont'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo "<option value='1'".($data_sm['sma_cont'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo "</select></td>\n";
  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_cont'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_cont'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}
  echo "</td>\n</tr>\n";

//************************************* Photogallery *************************************
  echo "<tr>\n<td width='40%' class='tbl2'>".$locale['sma102']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_photo' class='textbox' $disable>";
  echo "<option value='0'".($data_sm['sma_photo'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo "<option value='1'".($data_sm['sma_photo'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo "</select></td>\n";
  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_photo'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_photo'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}
  echo "</td>\n</tr>\n";

//************************************* Articles *******************************************
  echo "<tr>\n<td width='40%' class='tbl'>".$locale['sma103']."</td><td width='40%' class='tbl' align='center'>\n";
  echo "<select name='sma_articles' class='textbox' $disable>";
  echo "<option value='0'".($data_sm['sma_articles'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo "<option value='1'".($data_sm['sma_articles'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>";
  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_articles'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_articles'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}
  echo "</td>\n</tr>\n";

//********************************** News and News Cats **********************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma104']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_news' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_news'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_news'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_news'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_news'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Forums ****************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma105']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_forum' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_forum'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_forum'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_forum'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_forum'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Members List ****************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma106']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_members' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_members'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_members'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_members'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_members'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Downloads ****************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma107']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_down' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_down'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_down'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_down'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_down'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Profiles ****************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma117']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_prof' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_prof'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_prof'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_prof'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_prof'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Private Messaging ****************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma108']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_pm' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_pm'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_pm'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_pm'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_pm'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Weblinks ****************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma109']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_weblinks' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_weblinks'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_weblinks'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_weblinks'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_weblinks'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Submissions ****************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma110']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_submissions' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_submissions'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_submissions'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_submissions'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_submissions'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** FAQ ****************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma111']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_faq' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_faq'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_faq'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_faq'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_faq'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

  echo "<tr><td colspan='3'><hr></td></tr>\n";

//*************************************** Custom Pages ****************************************
  echo "<tr><td width='40%' class='tbl'>".$locale['sma114']."</td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_cust' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_cust'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_cust'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_cust'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_cust'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** All Infusions ****************************************
  echo "<tr><td width='40%' class='tbl2'>".$locale['sma115']."</td><td width='40%' class='tbl2' align='center'>\n";

  echo "<select name='sma_inf' class='textbox' $disable>";
  echo"<option value='0'".($data_sm['sma_inf'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo"<option value='1'".($data_sm['sma_inf'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl2' align='center'>\n";

if($data_sm['sma_inf'] == 0 && $data_sm['sma_all'] == 0) {
    echo "$showgreen";
} elseif ($data_sm['sma_inf'] == 1 && $data_sm['sma_all'] == 0) {
    echo "$showred";
} elseif ($data_sm['sma_all'] == 1) {
    echo "$showgrey";
}

  echo "</td></tr>\n";

//*************************************** Select All ****************************************
  echo "<tr><td width='40%' class='tbl'><b>".$locale['sma112']."</b></td><td width='40%' class='tbl' align='center'>\n";

  echo "<select name='sma_all' class='textbox'>";
  echo "<option value='0'".($data_sm['sma_all'] == "0" ? " selected" : "").">".$locale['sma201']."</option>";
  echo "<option value='1'".($data_sm['sma_all'] == "1" ? " selected" : "").">".$locale['sma200']."</option>";
  echo"</select></td>\n";

  echo "<td class='tbl' align='center'>\n";

if($data_sm['sma_all'] == 0)
{
    echo "$showgreen";
} else {
    echo "$showred";
}
  echo "</td>\n</tr>\n";

//************************************ Maintenance Message *******************************************
  echo "<tr><td colspan='3'><hr></td></tr>\n";
  echo "<tr><td width='40%'valign='top' class='tbl2'><b>".$locale['sma209']."</b></td><td colspan='2' class='tbl2' align='right'>";
  echo "<textarea name='sma_message' rows='4' cols='44' class='textbox' style='width:300px;' />".stripslashes($data_sm['sma_message'])."</textarea><br />";
  echo display_bbcodes("340px", "sma_message", "sectionmaint", "smiley|b|i|u|left|center|right|small|big|color")."\n";
  echo "</td></tr><tr>\n";

//************************************ Optionals *******************************************

  echo "<tr><td colspan='3'><hr></td></tr>\n";
  echo "<tr><td width='40%' colspan='3'class='tbl'><b>".$locale['sma210']."</b><br /></td>";
  echo "</tr\n><tr>\n";
  echo "<td class='tbl2'colspan='3' align='left'>\n";
  echo "<b>".$locale['sma233']."</b>&nbsp;";
  echo "&nbsp;<label><input type='radio' name='sma_temp' value='0'".($data_sm['sma_temp'] == "0" ? " checked='checked'" : "")." />&nbsp;".$locale['sma216']."</label>";
  echo "&nbsp;<label><input type='radio' name='sma_temp' value='1'".($data_sm['sma_temp'] == "1" ? " checked='checked'" : "")." />&nbsp;".$locale['sma217']."</label>";
  echo "</td>\n";
  
  echo "</tr><tr>\n";
  echo "<td class='tbl2'colspan='3' align='left'>\n";
  echo "<b>".$locale['sma303']." ".$locale['sma304']."</b>&nbsp;&nbsp;<input type='text' name='sma_time' size='2' value='".$data_sm['sma_time']."' class='textbox' style='width:20px;' />\n";

  echo "<select name='sma_period' class='textbox'>";
  echo "<option value='0'".($data_sm['sma_period'] == "0" ? " selected" : "").">".$locale['sma220']."</option>";
  echo "<option value='1'".($data_sm['sma_period'] == "1" ? " selected" : "").">".$locale['sma221']."</option>";
  echo "<option value='2'".($data_sm['sma_period'] == "2" ? " selected" : "").">".$locale['sma222']."</option>";
  echo "<option value='3'".($data_sm['sma_period'] == "3" ? " selected" : "").">".$locale['sma223']."</option>";
  echo "<option value='4'".($data_sm['sma_period'] == "4" ? " selected" : "").">".$locale['sma224']."</option>";
  echo "</select>\n";
  echo "</b>&nbsp;".$locale['sma213']."</td></tr>\n";

  echo "<tr><td class='tbl2'colspan='3' align='left'>\n";
  echo "".$locale['sma230']."";
  echo "&nbsp;<label><input type='radio' name='sma_show_admmsg' value='0'".($data_sm['sma_show_admmsg'] == "0" ? " checked='checked'" : "")." />&nbsp;".$locale['sma216']."</label>";
  echo "&nbsp;<label><input type='radio' name='sma_show_admmsg' value='1'".($data_sm['sma_show_admmsg'] == "1" ? " checked='checked'" : "")." />&nbsp;".$locale['sma217']."</label>";
  echo "</b></td>\n</tr>\n";

  echo "<tr><td class='tbl2'>".$locale['sma214']." <label><input type='radio' name='sma_show_sig' value='1'".($data_sm['sma_show_sig'] == "1" ? " checked='checked'" : "")." />&nbsp;".$locale['sma216']."</label>";
  echo "&nbsp;<label><input type='radio' name='sma_show_sig' value='0'".($data_sm['sma_show_sig'] == "0" ? " checked='checked'" : "")." />&nbsp;".$locale['sma217']."</label></td>\n";
  echo "<td width='40%' class='tbl2' colspan='2' align='center' valign='middle' />\n";
  echo "".$locale['sma305']." ".$userdata['user_name']."<input type='hidden' name='sma_sign' value='".$userdata['user_name']."' class='textbox' />&nbsp;".$locale['sma218']."&nbsp;<input type='hidden' name='sma_datestamp' value='".time()."' class='textbox' />".strftime('%d/%m/%Y %H:%M', $data_sm['sma_datestamp']+($settings['timeoffset']*3600))."</td></tr>\n";

  echo "<tr><td class='tbl2'>".$locale['sma215']." <label><input type='radio' name='sma_show_image' value='1'".($data_sm['sma_show_image'] == "1" ? " checked='checked'" : "")." />&nbsp;".$locale['sma216']."</label>";
  echo "&nbsp;<label><input type='radio' name='sma_show_image' value='0'".($data_sm['sma_show_image'] == "0" ? " checked='checked'" : "")." />&nbsp;".$locale['sma217']."</label></td>\n";
  echo "<td width='40%' class='tbl2'colspan='2' align='right' valign='top' />\n";
  echo "<img src='".INFUSIONS."section_maintenance/images/maintenance_sm.jpg' alt='".$locale['sma205']."' width='80' border='1' /></td></tr>\n";

//************************************ End Form *******************************************
  echo "<tr><td colspan='3' class='tbl'><br /><div align='center'>\n";
  echo "<input type='submit' name='savesettings' value='".$locale['sma202']."' class='button' />
</td>\n";
  echo "</tr>\n</table>\n</form>\n";

  echo "<br /><div align='center'><a target='_blank' href='".INFUSIONS."section_maintenance/section_closed.php'>".$locale['sma306']."</a></div>\n";
	
closetable();

// Please do not remove Copyright Info
   $title = $locale['title'];
   $data_v = dbarray(dbquery("SELECT inf_title, inf_version FROM ".DB_INFUSIONS." WHERE inf_title='$title'"));	
   $version = $data_v['inf_version'];
    echo "<br /><div class='small' align='center'>".$title." [v".$version."] by <a target='_blank' title='".$locale['title']." Infusion' href='http://www.php-fusion.hobbysites.net'>HobbyMan</a> ".date("Y")."<br /></div>\n";
// End copyright info

$v = 2.3; // Alter $version to whatever variable your version is stored in!
echo "<!-- Version Checker 2.0.0 @ http://version.starefossen.com - Copyright Starefossen 2007-2009 -->";
echo "<script type='text/javascript' src='http://www.starefossen.com/version/infusions/version_updater/checker/js.php?ps=sectmaint&amp;v=".$v."'></script>";
echo "<noscript><a href='http://version.starefossen.com/' target='_blank'><strong>JavaScript disabled:</strong> Check version manually!</a></noscript>";

require_once THEMES."templates/footer.php";
?>