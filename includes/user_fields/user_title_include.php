<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if (!defined("IN_FUSION")) { die("Access Denied"); }

$fb4 = dbarray(dbquery("select * from ".$db_prefix."fb_settings"));

// Check if locale file is available matching the current site locale setting.
if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS."fusionboard4/locale/English.php";
}

if ($profile_method == "input" && isset($user_data)) {
	$titleOpts = "";
	$titleQuery = dbquery("select * from ".$db_prefix."fb_titles where title_status='1' and (".groupaccess("title_access").")");
	while($titleData = dbarray($titleQuery)){
		$titleOpts .= "<option value='".$titleData['title_id']."'".($titleData['title_id'] == $user_data['user_title'] ? " SELECTED" : "").">".stripslash($titleData['title_title'])."</option>\n";
	}
	if($fb4['user_titles']){
		echo "<tr>\n";
		echo "<td class='tbl'><script type='text/javascript' src='".INFUSIONS."fusionboard4/includes/js/fb4.js'></script>
		".$locale['uf_title_profile']."&nbsp;
		(<a href=\"javascript:;\" id=\"preset-toggle\" onclick=\"sw('title', 'custom', 'preset');\"><b>preset</b></a> | <a href=\"javascript:;\" onclick=\"sw('title', 'preset', 'custom');\" id=\"custom-toggle\">custom</a>)</td>\n";
		echo "<td class='tbl'>
		<div id=\"title-preset\" style=\"display:block;\">
		<select name='user_title' class='textbox' style='width:200px;' />";
		if($fb4['user_titles_custom'] && checkgroup($fb4['user_titles_custom_access'])){
			echo "<option value='---'>".$locale['uf_custom']."</option>";
		}
		echo "$titleOpts
		</select>
		</div>
		<div id=\"title-custom\" style=\"display:none;\">";
		if($fb4['user_titles_custom'] && checkgroup($fb4['user_titles_custom_access'])){
			$title = (!isnum($user_data['user_title']) ? stripslash($user_data['user_title']) : "");
			echo "<input type='text' name='user_title_custom' value='$title' class='textbox' style='width:200px;'>\n";
		} else {
			echo $locale['uf_custom_disabled'];
		}
		echo "</div>
		</td>\n";
		echo "</tr>\n";
	}
} elseif ($profile_method == "display") {
	if ($fb4['user_titles_profile'] && $fb4['user_titles']) {
		function useraccess($field) {
			global $user_data;
			if ($user_data['user_level'] == 0) { return "$field = '0'";
			} elseif ($user_data['user_level'] == 103) { return "1 = 1";
			} elseif ($user_data['user_level'] >= 102) { $res = "($field='0' OR $field='101' OR $field='102'";
			} elseif ($user_data['user_level'] >= 101) { $res = "($field='0' OR $field='101'";
			}
			if (substr($user_data['user_groups'], 1) != "" && $user_data['user_level'] !== 103) { $res .= " OR $field='".str_replace(".", "' OR $field='", substr($user_data['user_groups'], 1))."'"; }
			$res .= ")";
			return $res;
		}
		$titleLookup = dbquery("select * from ".$db_prefix."fb_titles where title_id='".$user_data['user_title']."' and (".useraccess("title_access").")");
		if(dbrows($titleLookup)){
			$titleData = dbarray($titleLookup);
			$title = stripslash($titleData['title_title']);
		} else {
			$title = stripslash($user_data['user_title']);
		}
		echo "<tr>\n";
		echo "<td width='1%' class='tbl1' style='white-space:nowrap'>".$locale['uf_title_profile']."</td>\n";
		echo "<td align='right' class='tbl1'>$title</td>\n";
		echo "</tr>\n";
	}
} elseif ($profile_method == "validate_insert") {
	$db_fields .= ", user_title";
	$db_values .= ", '".$fb4['title_default']."'";
} elseif ($profile_method == "validate_update") {
	if($fb4['user_titles_custom'] && checkgroup($fb4['user_titles_custom_access'])){
		if($_POST['user_title'] == "---"){
			echo "test";
			$newTitle = addslash(stripinput($_POST['user_title_custom']));
		} else {
			echo "test2";
			$newTitle = $_POST['user_title'];
		}
	} else {
		echo "test3";
		$newTitle = $_POST['user_title'];
	}
	$db_values .= ", user_title='".$newTitle."'";
}
?>
