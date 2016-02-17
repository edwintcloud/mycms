<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if((isset($_GET['mode']) && $_GET['mode'] == "ajax") && (isset($_GET['level']) && is_numeric($_GET['level'])) && iADMIN){

	include "../../../maincore.php";
	if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
		include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
	} else {
		include INFUSIONS."fusionboard4/locale/English.php";
	}
	$result = dbquery("select * from ".DB_PREFIX."fb_warn_rules where rule_level='".$_GET['level']."'");
	if(!dbrows($result)) die("Failed.");
	$data = dbarray($result);
	echo "<form action='admin.php".$aidlink."&section=warnings&level=".$_GET['level']."' method='post' name='ruleform'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='5' border='0' style='text-align:center;'>\n";
	echo "<tr>\n<td style='font-weight:bold;' width='16.6%' valign='top'>".$data['rule_level']."</td>\n";
	echo "<td valign='top' width='16.6%'><textarea name='rule_pm' cols='10' rows='6' class='textbox'>".stripslash($data['rule_pm'])."</textarea></td>\n";
	echo "<td valign='top' width='16.6%'><textarea name='rule_email' cols='10' rows='6' class='textbox'>".stripslash($data['rule_email'])."</textarea></td>\n";
	echo "<td valign='top' width='16.6%'><input type='text' name='rule_bantime' class='textbox' style='width:50px;' value='".$data['rule_bantime']."'><br />\n";
	echo "<span class='small'>(".$locale['fbw113'].")</span></td>\n";
	echo "<td valign='top' width='16.6%'><select name='rule_perma' class='textbox'>\n";
	echo "<option value='1'".($data['rule_perma'] ? " SELECTED" : "").">".$locale['fb4001']."</option>\n";
	echo "<option value='0'".(!$data['rule_perma'] ? " SELECTED" : "").">".$locale['fb4002']."</option>\n";
	echo "</select></td>\n";
	echo "<td width='16.6%' valign='top'><input type='submit' name='goRule' value='".$locale['fbw112']."' class='button'></td>\n</tr>\n";
	echo "</table>\n</form>\n";
	die();
}

if(!defined("IN_FUSION")) die("Access Denied");
	
function showWarning($user, $pre="<br />\n<br />\n"){

	global $locale, $userdata, $fb4, $fdata;
	
	$user_data = dbarray(dbquery("select * from ".DB_USERS." where user_id='$user'"));
	
	if(!isset($userdata['user_id'])) $userdata['user_id'] = "0";
	
	$can_warn = false; 
	$can_see = false; 
	$warnings_on = $fb4['w_enabled'];
	
	$w_can_see = explode("|", $fb4['w_can_see']); $w_can_give = explode("|", $fb4['w_can_give']); $w_protected = explode("|", $fb4['w_protected']);
	
	if($userdata['user_groups']){ $checks = explode(".", $userdata['user_groups']); } else { $checks = array(); }
	array_push($checks, $userdata['user_level']);
	if(isset($fdata)){
		$mod_groups = explode(".", $fdata['forum_moderators']);
		$user_groups = explode(".", $userdata['user_groups']);
		foreach($mod_groups as $mod_group){
			if(in_array($mod_group, $user_groups)){
				array_push($checks, "mod");
			}
		}
	}
	
	foreach($checks as $check){ if(in_array($check, $w_can_see)){ $can_see = true; } }
	
	if($user_data['user_groups']){ $checks = explode(".", $user_data['user_groups']); } else { $checks = array(); }
	array_push($checks, $user_data['user_level']);
	if(isset($fdata)){
		$mod_groups = explode(".", $fdata['forum_moderators']);
		$user_groups = explode(".", $user_data['user_groups']);
		foreach($mod_groups as $mod_group){
			if(in_array($mod_group, $user_groups)){
				array_push($checks, "mod");
			}
		}
	}
	foreach($checks as $check){
		if(in_array($check, $w_can_give)) $can_warn = true;
		if(in_array($check, $w_protected)){ $can_warn = false; $can_see = false; break; }
	}
	if(!$warnings_on){ $can_see = false; $can_warn = false; }
	if($userdata['user_id'] == $user && !$fb4['w_see_own']){ $can_see = false; $can_warn = false; }
	
	$checks = "";
	if($userdata['user_groups']){ $checks = explode(".", $userdata['user_groups']); } else { $checks = array(); }
	array_push($checks, $userdata['user_level']);
	foreach($checks as $check){
		if(in_array($check, $w_can_give)) $can_warn = true;
	}

	$result = dbquery("select * from ".DB_PREFIX."fb_users where user_id='$user'");
	if(dbrows($result)){
		$data = dbarray($result);
		$warning = $data['user_warning'];
	} else {
		@$result = dbquery("INSERT INTO ".DB_PREFIX."fb_users (user_id, user_layout, user_notes, user_warning, user_invisible, user_lv, user_banned) VALUES('".$user."', '0', '', '0', '0', '".time()."', '')");
		$warning = 0;
	}
	
	if($can_see && $can_warn && $warnings_on){
		echo $pre;
		if($warning > 0){ echo "<a href='".FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY."&amp;warn=$user&amp;set=".($warning-1)."" : 
		"?warn=$user&amp;set=".($warning-1))."' onclick=\"return confirm('".$locale['fbw102']."');\">";
		echo "<img src='".INFUSIONS."fusionboard4/images/warning/warn_minus.gif' alt='-' title='-' style='border:0px;'></a> "; }
		echo "<img src='".INFUSIONS."fusionboard4/images/warning/warn".$warning.".gif' ";
		echo "alt='".$locale['fbw100'].$warning."' title='".$locale['fbw100'].$warning."' />";
		if($warning < 5){ echo " <a href='".FUSION_SELF.(FUSION_QUERY ? "?".FUSION_QUERY."&amp;warn=$user&amp;set=".($warning+1)."" : 
		"?warn=$user&amp;set=".($warning+1))."' onclick=\"return confirm('".$locale['fbw101']."');\">";
		echo "<img src='".INFUSIONS."fusionboard4/images/warning/warn_add.gif' alt='+' title='+' style='border:0px;'></a>"; }
	} elseif($can_see && $warnings_on){
		echo $pre;
		echo "<img src='".INFUSIONS."fusionboard4/images/warning/warn".$warning.".gif' ";
		echo "alt='".$locale['fbw100'].$warning."' title='".$locale['fbw100'].$warning."' />";
	}
}

if((isset($_GET['warn']) && isNum($_GET['warn'])) && (isset($_GET['set']) && isNum($_GET['set'])) && iMEMBER){

	$user_data = dbarray(dbquery("select * from ".DB_USERS." where user_id='".$_GET['warn']."'"));
	
	$can_warn = false; 
	$can_see = false; 
	$warnings_on = $fb4['w_enabled'];
	
	$w_can_see = explode("|", $fb4['w_can_see']); $w_can_give = explode("|", $fb4['w_can_give']); $w_protected = explode("|", $fb4['w_protected']);
	
	if($user_data['user_groups']){ $checks = explode(".", $user_data['user_groups']); } else { $checks = array(); }
	array_push($checks, $user_data['user_level']);
	if(isset($fdata)){
		$mod_groups = explode(".", $fdata['forum_moderators']);
		$user_groups = explode(".", $user_data['user_groups']);
		foreach($mod_groups as $mod_group){
			if(in_array($mod_group, $user_groups)){
				array_push($checks, "mod");
			}
		}
	}
	foreach($checks as $check){
		if(in_array($check, $w_can_see)) $can_see = true;
		if(in_array($check, $w_can_give)) $can_warn = true;
		if(in_array($check, $w_protected)){ $can_warn = false; $can_see = false; break; }
	}
	
	if($userdata['user_groups']){ $checks = explode(".", $userdata['user_groups']); } else { $checks = array(); }
	array_push($checks, $userdata['user_level']);
	foreach($checks as $check){
		if(in_array($check, $w_can_give)) $can_warn = true;
	}
	if(!$warnings_on){ $can_see = false; $can_warn = false; }
	if($userdata['user_id'] == $_GET['warn'] && !$fb4['w_see_own']){ $can_see = false; $can_warn = false; }
	
	if($can_warn && $warnings_on && ($_GET['set'] >= 0 && $_GET['set'] <= 5)){
		$result = dbquery("update ".DB_PREFIX."fb_users set user_warning='".$_GET['set']."' where user_id='".$_GET['warn']."'");
		$user = dbarray(dbquery("select * from ".DB_USERS." where user_id='".$_GET['warn']."'"));
		if(!$user['user_status']){
			if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
				include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
			} else {
				include INFUSIONS."fusionboard4/locale/English.php";
			}
			$rules = dbarray(dbquery("select * from ".DB_PREFIX."fb_warn_rules where rule_level='".$_GET['set']."'"));
			if($rules['rule_pm'] !== ""){
				$message = stripslash($rules['rule_pm']);
				sendMessage($_GET['warn'], $userdata['user_id'], $locale['fbw114'], $message);
			}
			if($rules['rule_email'] !== ""){
				require_once INCLUDES."sendmail_include.php";
				$message_content = str_replace("[WARNING]", "\"<em>".stripslash($rules['rule_email'])."</em>\"", $locale['fbw116']);
				sendemail($user['user_name'], $user['user_email'], $settings['siteusername'], $settings['siteemail'], $locale['625'], $data['user_name'].$message_content, "html");
			}
			if($rules['rule_bantime']){
				$query = dbquery("update ".DB_USERS." set user_status='1' where user_id='".$user['user_id']."'");
				$query = dbquery("update ".DB_PREFIX."fb_users set user_banned='".(time()+(3600*$rules['rule_bantime']))."' where user_id='".$user['user_id']."'");
			}
			if($rules['rule_permaban']){
				$query = dbquery("update ".DB_USERS." set user_status='1' where user_id='".$user['user_id']."'");
				$query = dbquery("update ".DB_PREFIX."fb_users set user_banned='0' where user_id='".$user['user_id']."'");
			}
		}
		$path = "";
		foreach($_GET as $key=>$value){
			if($key !== "set" && $key !== "warn"){
				if(!$path){ $path .= "?"; } else { $path .= "&"; }
				$path .= $key."=".$value;
			}
		}
		redirect(FUSION_SELF.$path);
	}
}

/* unban timed ban users */
$result = dbquery("select * from ".DB_PREFIX."fb_users where user_banned > 0 and user_banned < ".time()."");
while($data = dbarray($result)){
	$query = dbquery("update ".DB_USERS." set user_status='0' where user_id='".$data['user_id']."'");
	$query = dbquery("update ".DB_PREFIX."fb_users set user_banned='0' where user_id='".$data['user_id']."'");
}
?>