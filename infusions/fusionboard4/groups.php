<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if (! defined ( "USER_CP" )) {
	require_once "../../maincore.php";
	require_once THEMES . "templates/header.php";
	include LOCALE . LOCALESET . "forum/main.php";
	include INFUSIONS . "fusionboard4/includes/func.php";
	
	if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
		include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
	} else {
		include INFUSIONS . "fusionboard4/locale/English.php";
	}
	if (! $fb4 ['group_enable'])
		redirect ( FORUM . "index.php" );
	
	opentable ( $locale ['uc250'] );
	
	renderNav ( false, false, array (INFUSIONS . "fusionboard4/groups.php", $locale ['uc250'] ) );
	add_to_title ( " :: " . $locale ['uc250'] );
	echo "<table width='100%' cellspacing='1' cellpadding='0' border='0' class='tbl-border'>\n";
}
echo "<style type='text/css'>
.grouptext { font-size:14px;font-family:Tahoma;width:300px;margin-top:3px;margin-left:7px; }
</style>\n";
if (isset ( $_GET ['action'] ) && $_GET ['action'] == "create" && checkgroup ( $fb4 ['group_create'] )) {
	if (isset ( $_POST ['addGroup'] )) {
		$group_name = (isset ( $_POST ['group_name'] ) ? addslash ( stripinput ( $_POST ['group_name'] ) ) : "");
		$group_desc = (isset ( $_POST ['group_desc'] ) ? addslash ( stripinput ( $_POST ['group_desc'] ) ) : "");
		$group_type = (isset ( $_POST ['group_type'] ) && isNum ( $_POST ['group_type'] ) ? $_POST ['group_type'] : 1);
		$group_wall = (isset ( $_POST ['group_wall'] ) && isNum ( $_POST ['group_wall'] ) ? $_POST ['group_wall'] : 0);
		$group_visibility = (isset ( $_POST ['group_visibility'] ) && isNum ( $_POST ['group_visibility'] ) ? $_POST ['group_visibility'] : 0);
		$group_moderate = (isset ( $_POST ['group_moderate'] ) && isNum ( $_POST ['group_moderate'] ) ? $_POST ['group_moderate'] : 0);
		$result = dbquery ( "insert into " . DB_USER_GROUPS . " (group_name, group_description) VALUES('$group_name', '$group_desc')" );
		$group_id = mysql_insert_id ();
		$result = dbquery ( "insert into " . DB_PREFIX . "fb_groups (group_id, group_leader, group_officers, group_access, group_visibility, group_wall, group_description, group_recentnews, group_created, group_image, group_moderate) VALUES('$group_id', '" . $userdata ['user_id'] . "', '', '$group_type', '$group_visibility', '$group_wall', '$group_desc', '', '" . time () . "', '', '$group_moderate')" );
		$result = dbquery ( "update " . DB_USERS . " set user_groups='" . ($userdata ['user_groups'] == "" ? $group_id : $userdata ['user_groups'] . ".$group_id") . "' where user_id='" . $userdata ['user_id'] . "'" );
		redirect ( FUSION_SELF . "?section=groups&view=$group_id" );
	} else {
		echo "<form action='" . FUSION_SELF . "?section=groups&amp;action=create' method='post' name='addform'>\n";
		echo "<tr>\n<td class='tbl1 navtitle'>" . $locale ['uc265'] . "</td>\n</tr>\n";
		echo "<tr>\n<td class='tbl2' align='center'><div style='width:300px;padding:10px;'><div align='left'>\n";
		echo $locale ['uc266'] . "<br />\n<input type='text' name='group_name' class='textbox grouptext'><br />\n<br />\n";
		echo $locale ['uc267'] . "<br />\n<textarea name='group_desc' class='textbox grouptext' rows='4'></textarea><br />\n<br />\n";
		echo $locale ['uc268'] . "<br />\n<select name='group_type' class='textbox grouptext'>\n";
		echo "<option value='1'>" . $locale ['uc269'] . "</option>\n";
		echo "<option value='2'>" . $locale ['uc270'] . "</option>\n";
		echo "<option value='3'>" . $locale ['uc291'] . "</option>\n";
		echo "</select><br />\n<br />\n";
		echo "<fieldset class='fields' style='width:300px;'><legend><a name=''>" . $locale ['uc271'] . "</a></legend>\n";
		echo "<label><input type='checkbox' name='group_wall' value='1' />" . $locale ['uc272'] . "</label><br />\n";
		//echo "<label><input type='checkbox' name='group_moderate' value='1' />".$locale['uc369']."</label><br />\n";
		echo "<label><input type='checkbox' name='group_visibility' value='1' />" . $locale ['uc274'] . "</label><br />\n";
		echo "</fieldset>\n<br /><div align='center'><input type='submit' name='addGroup' value='" . $locale ['uc275'] . "' class='button'></div>\n";
		echo "</div>\n</div>\n</td>\n</tr>\n</form>\n";
	}
} elseif (isset ( $_GET ['join'] ) && isNum ( $_GET ['join'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['join'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	$in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and user_id='" . $userdata ['user_id'] . "'" ) );
	
	if ($data ['group_access'] == "1" && ! $in_group && iMEMBER) {
		$result = dbquery ( "update " . DB_USERS . " set user_groups='" . ($userdata ['user_groups'] == "" ? $data ['group_id'] : $userdata ['user_groups'] . "." . $data ['group_id']) . "' where user_id='" . $userdata ['user_id'] . "'" );
		redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['join'] );
	} else {
		redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['join'] );
	}

} elseif (isset ( $_GET ['leave'] ) && isNum ( $_GET ['leave'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['leave'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	$in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and user_id='" . $userdata ['user_id'] . "'" ) );
	
	if ($in_group && iMEMBER && $data ['group_leader'] !== $userdata ['user_id']) {
		$groups = explode ( ".", $userdata ['user_groups'] );
		$newlist = "";
		$i = 0;
		foreach ( $groups as $group ) {
			if ($group !== $data ['group_id']) {
				if ($i > 0) {
					$newlist .= ".";
				}
				$newlist .= $group;
				$i ++;
			}
		}
		$result = dbquery ( "update " . DB_USERS . " set user_groups='$newlist' where user_id='" . $userdata ['user_id'] . "'" );
		redirect ( FUSION_SELF . "?section=groups" );
	} else {
		redirect ( FUSION_SELF . "?section=groups" );
	}
} elseif (isset ( $_GET ['acceptinvite'] ) && isNum ( $_GET ['acceptinvite'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['acceptinvite'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	
	$invite = dbquery ( "select * from " . DB_PREFIX . "fb_invites where invite_to='" . $userdata ['user_id'] . "' and invite_group='" . $data ['group_id'] . "'" );
	if (! dbrows ( $invite ))
		redirect ( FUSION_SELF . "?section=groups" );
	
	$query = dbquery ( "update " . DB_USERS . " set user_groups='" . ($userdata ['user_groups'] == "" ? $data ['group_id'] : $userdata ['user_groups'] . "." . $data ['group_id']) . "' where user_id='" . $userdata ['user_id'] . "'" );
	$result = dbquery ( "delete from " . DB_PREFIX . "fb_invites where invite_to='" . $userdata ['user_id'] . "' and invite_group='" . $_GET ['acceptinvite'] . "'" );
	
	redirect ( FUSION_SELF . "?section=groups&view=" . $data ['group_id'] );

} elseif (isset ( $_GET ['apply'] ) && isNum ( $_GET ['apply'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['apply'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	$in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and user_id='" . $userdata ['user_id'] . "'" ) );
	$rows = dbcount ( "(apply_user)", DB_PREFIX . "fb_apply", "apply_group='" . $data ['group_id'] . "' and apply_user='" . $userdata ['user_id'] . "'" );
	if (! $in_group && iMEMBER && ! $rows) {
		if (isset ( $_POST ['goApply'] )) {
			echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc293'] . "<span style='font-weight:normal;'>" . stripslash ( $data ['group_name'] ) . "</span></td>\n</tr>\n";
			$apply_reason = (isset ( $_POST ['apply_reason'] ) ? addslash ( stripinput ( $_POST ['apply_reason'] ) ) : "");
			$apply_user = $userdata ['user_id'];
			$apply_group = $data ['group_id'];
			$query = dbquery ( "insert into " . DB_PREFIX . "fb_apply (apply_user, apply_group, apply_reason) VALUES('$apply_user', '$apply_group', '$apply_reason')" );
			$message = str_replace ( array ("{1}" ), array ($settings ['siteurl'] . "infusions/fusionboard4/groups.php?view=" . $data ['group_id'] ), $locale ['uc303'] );
			sendMessage ( $data ['group_leader'], $userdata ['user_id'], $userdata ['user_name'] . $locale ['uc302'] . "\"" . stripslash ( $data ['group_name'] ) . "\"", $message );
			if ($query)
				redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['apply'] );
		} else {
			echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc293'] . "<span style='font-weight:normal;'>" . stripslash ( $data ['group_name'] ) . "</span></td>\n</tr>\n";
			echo "<tr>\n<td class='tbl1' align='center'><table width='300' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
			echo "<tr>\n<td class='tbl2'>" . $locale ['uc294'] . "</td>\n<td class='tbl1'>" . $userdata ['user_name'] . "</td>\n</tr>\n";
			echo "<tr>\n<td class='tbl2'>" . $locale ['uc295'] . "</td>\n<td class='tbl1'>" . stripslash ( $data ['group_name'] ) . "</td>\n</tr>\n";
			echo "<tr>\n<td class='tbl2'>" . $locale ['uc296'] . "</td>\n<td class='tbl1'>\n";
			echo "<form action='" . FUSION_SELF . "?section=groups&apply=" . $_GET ['apply'] . "' method='post' name='applyform'>\n";
			echo "<textarea name='apply_reason' class='textbox'></textarea>\n";
			echo "</td>\n</tr>\n";
			echo "<tr>\n<td class='tbl2' colspan='2' align='center'><input type='submit' name='goApply' class='button' value='" . $locale ['uc297'] . "'></td>\n</tr>\n";
			echo "</form></table>\n</td>\n</tr>\n";
		}
	} else {
		redirect ( FUSION_SELF . "?section=groups" );
	}
} elseif (isset ( $_GET ['members'] ) && isNum ( $_GET ['members'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['members'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	
	$in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and user_id='" . $userdata ['user_id'] . "'" ) );
	if (isset ( $_POST ['goInvite'] ) && $in_group) {
		foreach ( $_POST as $user => $invite ) {
			if (! ereg ( "user_", $user ))
				continue;
			if (! $invite)
				continue;
			$to = str_replace ( "user_", "", $user );
			$from = $userdata ['user_id'];
			$group = $data ['group_id'];
			$already_in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and  user_id='$to'" ) );
			$already_invited = dbcount ( "(invite_to)", DB_PREFIX . "fb_invites", "invite_to='$to' and invite_group='$group'" );
			if (! $already_in_group && ! $already_invited && ! checkIgnore ( $to )) {
				$result = dbquery ( "insert into " . DB_PREFIX . "fb_invites (invite_to,invite_from,invite_group) VALUES('$to', '$from', '$group')" );
				$subject = $locale ['uc312'] . "\"" . stripslash ( $data ['group_name'] ) . "\"";
				$message = str_replace ( "{1}", $settings ['siteurl'] . "infusions/fusionboard4/usercp.php?section=requests", $locale ['uc313'] );
				sendMessage ( $to, $from, $subject, $message );
			}
		}
		redirect ( FUSION_SELF . "?section=groups&members=" . $_GET ['members'] . "&status=sent" );
	}
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "sent") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;' colspan='2'>" . $locale ['uc314'] . "</td>\n</tr>\n";
	}
	echo "<tr>\n<td class='forum-caption' style='padding-left:7px;padding-right:7px;'" . (iMEMBER ? " colspan='2'" : "") . "'>\n";
	echo "<div style='float:right;font-weight:normal;'><a href='" . FUSION_SELF . "?section=groups&amp;view=" . $data ['group_id'] . "'>" . $locale ['uc309'] . "</a></div>\n";
	echo stripslash ( $data ['group_name'] ) . "</td>\n</tr>\n";
	$total = dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' ORDER BY user_level DESC, user_name limit 10" );
	echo "<tr>\n<td class='tbl1' style='vertical-align:top;'>\n<b>";
	echo str_replace ( array ("{1}", "{2}" ), array (dbrows ( $total ), dbrows ( $total ) ), $locale ['uc279'] ) . "</b><br /><br />\n";
	$i = 0;
	while ( $user = dbarray ( $total ) ) {
		if ($i > 0)
			echo "<br />\n";
		echo "&raquo; <a href='" . BASEDIR . "profile.php?lookup=" . $user ['user_id'] . "'>" . showLabel ( $user ['user_id'] ) . "</a>";
		$i ++;
	}
	echo "</td>\n";
	if (iMEMBER && $in_group) {
		echo "<td class='tbl2' style='width:220px;vertical-align:top;'>";
		echo "<b>" . $locale ['uc310'] . "</b><br /><form action='" . FUSION_SELF . "?section=groups&amp;members=" . $_GET ['members'] . "' method='post' name='inviteform'>\n";
		echo "<div class='tbl1' style='width:180px; padding:10px;height:200px;border:1px solid #656565;overflow:scroll;' id='invitelist'>";
		$buddies = dbquery ( "select * from " . DB_PREFIX . "fb_buddies where 
		(buddy_user='" . $userdata ['user_id'] . "' or buddy_buddy='" . $userdata ['user_id'] . "') and buddy_approved='1'" );
		$i = 0;
		while ( $buddy = dbarray ( $buddies ) ) {
			$buddy_id = ($userdata ['user_id'] == $buddy ['buddy_user'] ? $buddy ['buddy_buddy'] : $buddy ['buddy_user']);
			$already_in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and  user_id='$buddy_id'" ) );
			$already_invited = dbcount ( "(invite_to)", DB_PREFIX . "fb_invites", "invite_to='$buddy_id' and invite_group='" . $data ['group_id'] . "'" );
			if (! $already_in_group && ! $already_invited) {
				$buddyData = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_id='$buddy_id'" ) );
				if ($i > 0)
					echo "<br />\n";
				echo "<label><input type='checkbox' name='user_" . $buddyData ['user_id'] . "' value='1' /> " . showLabel ( $buddyData ['user_id'] ) . "</label>";
				$i ++;
			}
		}
		echo "</div>\n<br /><input type='submit' name='goInvite' class='button' value='" . $locale ['uc311'] . "'></form>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";

} elseif (isset ( $_GET ['view'] ) && isNum ( $_GET ['view'] )) {
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups f
	left join " . DB_USER_GROUPS . " ug on ug.group_id=f.group_id
	left join " . DB_USERS . " u on u.user_id=f.group_leader
	where f.group_id='" . $_GET ['view'] . "'" );
	if (! dbrows ( $result ))
		redirect ( FUSION_SELF . "?section=groups" );
	$data = dbarray ( $result );
	$in_group = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' and  user_id='" . $userdata ['user_id'] . "'" ) );
	if (isset ( $_GET ['accept'] ) && isNum ( $_GET ['accept'] ) && $userdata ['user_id'] == $data ['group_leader']) {
		$apply = dbcount ( "(apply_user)", DB_PREFIX . "fb_apply", "apply_group='" . $data ['group_id'] . "' and apply_user='" . $_GET ['accept'] . "'" );
		if ($apply) {
			$query = dbquery ( "delete from " . DB_PREFIX . "fb_apply where apply_group='" . $data ['group_id'] . "' and apply_user='" . $_GET ['accept'] . "'" );
			$applyUser = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_id='" . $_GET ['accept'] . "'" ) );
			$query = dbquery ( "update " . DB_USERS . " set user_groups='" . ($applyUser ['user_groups'] == "" ? $data ['group_id'] : $applyUser ['user_groups'] . "." . $data ['group_id']) . "' where user_id='" . $applyUser ['user_id'] . "'" );
			$message = str_replace ( array ("{1}" ), array ($settings ['siteurl'] . "infusions/fusionboard4/groups.php?view=" . $data ['group_id'] ), $locale ['uc306'] );
			sendMessage ( $applyUser ['user_id'], $data ['group_leader'], $locale ['uc304'], $message );
			redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['view'] );
		} else {
			redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['view'] );
		}
	}
	if (isset ( $_GET ['deny'] ) && isNum ( $_GET ['deny'] ) && $userdata ['user_id'] == $data ['group_leader']) {
		$apply = dbcount ( "(apply_user)", DB_PREFIX . "fb_apply", "apply_group='" . $data ['group_id'] . "' and apply_user='" . $_GET ['deny'] . "'" );
		if ($apply) {
			$query = dbquery ( "delete from " . DB_PREFIX . "fb_apply where apply_group='" . $data ['group_id'] . "' and apply_user='" . $_GET ['deny'] . "'" );
			$message = str_replace ( array ("{1}" ), array ($settings ['siteurl'] . "infusions/fusionboard4/groups.php?view=" . $data ['group_id'] ), $locale ['uc307'] );
			sendMessage ( $applyUser ['user_id'], $data ['group_leader'], $locale ['uc305'], $message );
			redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['view'] );
		} else {
			redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['view'] );
		}
	}
	if (isset ( $_GET ['action'] ) && $_GET ['action'] == "edit" && $userdata ['user_id'] == $data ['group_leader']) {
		if (isset ( $_POST ['goGroup'] )) {
			$group_name = (isset ( $_POST ['group_name'] ) ? addslash ( stripinput ( $_POST ['group_name'] ) ) : "");
			$group_desc = (isset ( $_POST ['group_desc'] ) ? addslash ( stripinput ( $_POST ['group_desc'] ) ) : "");
			$group_type = (isset ( $_POST ['group_type'] ) && isNum ( $_POST ['group_type'] ) ? $_POST ['group_type'] : 1);
			$group_wall = (isset ( $_POST ['group_wall'] ) && isNum ( $_POST ['group_wall'] ) ? $_POST ['group_wall'] : 0);
			$group_visibility = (isset ( $_POST ['group_visibility'] ) && isNum ( $_POST ['group_visibility'] ) ? $_POST ['group_visibility'] : 0);
			$group_moderate = (isset ( $_POST ['group_moderate'] ) && isNum ( $_POST ['group_moderate'] ) ? $_POST ['group_moderate'] : 0);
			$result = dbquery ( "update " . DB_PREFIX . "fb_groups set group_description='$group_desc', group_access='$group_type', 
			group_wall='$group_wall', group_visibility='$group_visibility', group_moderate='$group_moderate' 
			where group_id='" . $_GET ['view'] . "'" );
			$result = dbquery ( "update " . DB_USER_GROUPS . " set group_name='$group_name', group_description='$group_desc' where group_id='" . $_GET ['view'] . "'" );
			redirect ( FUSION_SELF . "?section=groups&view=" . $_GET ['view'] );
		}
		echo "<tr>\n<td class='forum-caption' style='padding-left:5px;'>" . $locale ['uc367'] . "\"" . stripslash ( $data ['group_name'] ) . "\"</td>\n</tr>\n";
		echo "<tr>\n<td class='tbl1' style='padding:10px;'><form action='" . FUSION_SELF . "?section=groups&amp;view=" . $_GET ['view'] . "&amp;action=edit' method='post' name='groupeditform'>\n";
		echo $locale ['uc266'] . "<br /><input name='group_name' class='textbox' style='font-size:14px;margin:3px;margin-left:6px;width:320px;' value='" . stripslash ( $data ['group_name'] ) . "'><br />\n";
		echo $locale ['uc267'] . "<br /><textarea name='group_desc' class='textbox' style='font-size:14px;margin:3px;margin-left:6px;width:320px;'>" . stripslash ( $data ['group_description'] ) . "</textarea><br />\n";
		echo $locale ['uc268'] . "<br />\n<select name='group_type' class='textbox grouptext'>\n";
		echo "<option value='1'" . ($data ['group_access'] == "1" ? " SELECTED" : "") . ">" . $locale ['uc269'] . "</option>\n";
		echo "<option value='2'" . ($data ['group_access'] == "2" ? " SELECTED" : "") . ">" . $locale ['uc270'] . "</option>\n";
		echo "<option value='3'" . ($data ['group_access'] == "3" ? " SELECTED" : "") . ">" . $locale ['uc291'] . "</option>\n";
		echo "</select><br />\n<br />\n";
		echo "<fieldset class='fields' style='width:300px;'><legend><a name=''>" . $locale ['uc271'] . "</a></legend>\n";
		echo "<label><input type='checkbox' name='group_wall' value='1' " . ($data ['group_wall'] ? " CHECKED " : "") . "/>" . $locale ['uc272'] . "</label><br />\n";
		//echo "<label><input type='checkbox' name='group_moderate' value='1' ".($data['group_moderate'] ? " CHECKED " : "")."/>".$locale['uc369']."</label><br />\n";
		echo "<label><input type='checkbox' name='group_visibility' value='1' " . ($data ['group_visibility'] ? " CHECKED " : "") . "/>" . $locale ['uc274'] . "</label><br />\n";
		echo "</fieldset>\n<br /><input type='submit' name='goGroup' value='" . $locale ['uc368'] . "' class='button'>\n";
		echo "</form>\n</td>\n</tr>\n";
	} elseif ($data ['group_visibility'] == "0" || $in_group) {
		echo "<tr>\n<td class='forum-caption' style='padding-left:5px;'>";
		if ($userdata ['user_id'] == $data ['group_leader']) {
			echo "<div style='float:right;padding-right:5px;'><a href='" . FUSION_SELF . "?section=groups&amp;view=" . $_GET ['view'] . "&amp;action=edit'>" . $locale ['uc366'] . "</a></div>\n";
		}
		echo "\n" . stripslash ( $data ['group_name'] ) . "</td>\n</tr>\n";
		echo "<tr>\n<td class='tbl1'><div style='float:right;'>" . $locale ['uc278'] . "<b>" . ($data ['group_access'] == "1" ? $locale ['uc269'] : ($data ['group_access'] == "2" ? $locale ['uc270'] : $locale ['uc291'])) . "</b></div>\n";
		echo "<span style='font-size:18px;'>" . stripslash ( $data ['group_name'] ) . "</span><br />\n";
		echo $locale ['uc276'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['user_id'] . "'>" . $data ['user_name'] . "</a><br />\n";
		echo "<br />" . nl2br ( parseubb ( stripslash ( $data ['group_description'] ) ) ) . "\n";
		echo "</td>\n</tr>\n";
		$count = dbrows ( dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "'" ) );
		$total = dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' ORDER BY user_level DESC, user_name limit 7" );
		echo "<tr>\n<td class='tbl2 navsection'><div style='float:right;font-weight:normal;'><a href='" . FUSION_SELF . "?section=groups&amp;members=" . $data ['group_id'] . "'>" . $locale ['uc308'] . "</a></div>\n";
		echo str_replace ( array ("{1}", "{2}" ), array (($count > 7 ? 7 : $count), $count ), $locale ['uc279'] ) . "</td>\n</tr>\n<tr>\n<td class='tbl1'>\n";
		echo "<table border='0' cellspacing='3' cellpadding='0'>\n<tr>\n";
		while ( $tdata = dbarray ( $total ) ) {
			echo "<td align='center' style='width:1px;vertical-align:bottom;'>";
			if ($tdata ['user_avatar']) {
				list ( $width, $height ) = getimagesize ( IMAGES . "avatars/" . $tdata ['user_avatar'] );
				$new_width = 70;
				$new_height = ($height * ($new_width / $height));
				echo "<img src='" . IMAGES . "avatars/" . $tdata ['user_avatar'] . "' alt='' style='width:" . $new_width . "px;height:" . $new_height . "px'>\n";
			} else {
				echo "<img src='" . IMAGES . "noav.gif' alt='' style='width:70px;height:70px'>\n";
			}
			echo "<br /><a href='" . BASEDIR . "profile.php?lookup=" . $tdata ['user_id'] . "'>" . showLabel ( $tdata ['user_id'] ) . "</a></td>\n";
		}
		echo "</tr>\n</table>\n";
		echo "</td>\n</tr>\n";
		$apply = dbquery ( "select * from " . DB_PREFIX . "fb_apply a
		left join " . DB_USERS . " u on u.user_id=a.apply_user
		where a.apply_group='" . $data ['group_id'] . "'" );
		if (dbrows ( $apply ) && ($userdata ['user_id'] == $data ['group_leader'])) {
			echo "<tr>\n<td class='tbl2 navsection'>" . str_replace ( array ("{1}", "{2}" ), array ((dbrows ( $apply ) > 9 ? "10" : dbrows ( $apply )), dbrows ( $apply ) ), $locale ['uc299'] ) . "</td>\n</tr>\n<tr>\n<td class='tbl1'>\n";
			echo "<script src='" . INFUSIONS . "fusionboard4/includes/js/boxover.js' type='text/javascript'></script>\n";
			echo "<table border='0' cellspacing='3' cellpadding='0'>\n<tr>\n";
			while ( $adata = dbarray ( $apply ) ) {
				$body = $locale ['uc296'] . stripslash ( $adata ['apply_reason'] );
				echo "<td align='center' style='width:1px;vertical-align:bottom;' title='header=[" . $adata ['user_name'] . "] body=[$body] singleclickstop=[on]'>";
				if ($adata ['user_avatar']) {
					list ( $width, $height ) = getimagesize ( IMAGES . "avatars/" . $adata ['user_avatar'] );
					$new_width = 70;
					$new_height = ($height * ($new_width / $height));
					echo "<img src='" . IMAGES . "avatars/" . $adata ['user_avatar'] . "' alt='' style='width:" . $new_width . "px;height:" . $new_height . "px'>\n";
				} else {
					echo "<img src='" . IMAGES . "noav.gif' alt='' style='width:70px;height:70px'>\n";
				}
				echo "<br /><span class='small'><a href='" . FUSION_SELF . "?section=groups&amp;view=" . $data ['group_id'] . "&amp;accept=" . $adata ['user_id'] . "'>" . $locale ['uc300'] . "</a> :: ";
				echo "<a href='" . FUSION_SELF . "?section=groups&amp;view=" . $data ['group_id'] . "&amp;deny=" . $adata ['user_id'] . "'>" . $locale ['uc301'] . "</a></span></td>\n";
			}
			echo "</tr>\n</table>\n";
			echo "</td>\n</tr>\n";
		}
		/* forums not implemented 
		if($data['group_moderate']){
			echo "<tr>\n<td class='tbl2 navsection'>".$locale['uc370']."</td>\n</tr>\n<tr>\n<td class='tbl1' style='padding:6px;'>\n";
			
			echo "</td>\n</tr>\n";
		}
		*/
		if ($data ['group_wall']) {
			include INFUSIONS . "fusionboard4/includes/comments_enhanced.php";
			echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc280'] . "</td>\n</tr>\n<tr>\n<td class='tbl2' style='padding:6px;'>\n";
			showcomments ( "G", DB_USER_GROUPS, "group_id", $_GET ['view'], FUSION_SELF . "?section=groups&amp;view=" . $_GET ['view'], $in_group );
			echo "</td>\n</tr>\n";
		}
	} else {
		echo "<tr>\n<td class='tbl2 navtitle'>" . stripslash ( $data ['group_name'] ) . "</td>\n</tr>\n";
		echo "<tr>\n<td class='tbl1'><div style='float:right;'>" . $locale ['uc278'] . "<b>" . ($data ['group_access'] == "1" ? $locale ['uc269'] : ($data ['group_access'] == "2" ? $locale ['uc270'] : $locale ['uc291'])) . "</b></div>\n";
		echo "<span style='font-size:18px;'>" . stripslash ( $data ['group_name'] ) . "</span><br />\n";
		echo $locale ['uc276'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['user_id'] . "'>" . $data ['user_name'] . "</a><br /><br />\n";
		echo $locale ['uc290'] . "</td>\n</tr>\n";
	}
	echo "<tr>\n<td class='tbl2 small' align='center'>";
	$invited = dbcount ( "(invite_to)", DB_PREFIX . "fb_invites", "invite_to='" . $userdata ['user_id'] . "' and invite_group='" . $data ['group_id'] . "'" );
	if ($in_group) {
		if ($userdata ['user_id'] !== $data ['group_leader']) {
			echo "<a href='" . FUSION_SELF . "?section=groups&amp;leave=" . $data ['group_id'] . "'>" . $locale ['uc287'] . "</a> :: \n";
		}
		echo "<a href='" . FUSION_SELF . "?section=groups&amp;members=" . $data ['group_id'] . "'>" . $locale ['uc288'] . "</a>\n";
	} elseif ($invited) {
		echo "<a href='" . FUSION_SELF . "?section=groups&amp;acceptinvite=" . $data ['group_id'] . "'>" . $locale ['uc330'] . "</a>\n";
	} elseif ($data ['group_access'] == "1") {
		echo "<a href='" . FUSION_SELF . "?section=groups&amp;join=" . $data ['group_id'] . "'>" . $locale ['uc286'] . "</a>\n"; // join group
	} elseif ($data ['group_access'] == "2") {
		echo $locale ['uc292'];
	} elseif ($data ['group_access'] == "3") {
		$rows = dbcount ( "(apply_user)", DB_PREFIX . "fb_apply", "apply_group='" . $data ['group_id'] . "' and apply_user='" . $userdata ['user_id'] . "'" );
		if ($rows) {
			echo $locale ['uc298'];
		} else {
			echo "<a href='" . FUSION_SELF . "?section=groups&amp;apply=" . $data ['group_id'] . "'>" . $locale ['uc291'] . "</a>\n";
		}
	}
	echo "</td>\n</tr>\n";
} else {
	$rows = dbcount ( "(group_id)", DB_PREFIX . "fb_groups" );
	echo "<tr>\n<td class='tbl1 navtitle'" . ($rows ? " colspan='5'" : "") . ">" . $locale ['uc250'] . "</td>\n</tr>\n";
	if ($rows) {
		echo "<tr>\n<td class='tbl2' style='font-weight:bold;'>" . $locale ['uc251'] . "</td>\n";
		echo "<td class='tbl2' style='font-weight:bold;' width='1%'>" . $locale ['uc252'] . "</td>\n";
		echo "<td class='tbl2' style='font-weight:bold;' width='1%'>" . $locale ['uc253'] . "</td>\n";
		echo "<td class='tbl2' style='font-weight:bold;white-space:nowrap' width='1%'>" . $locale ['uc254'] . "</td>\n";
		echo "<td class='tbl2' style='font-weight:bold; width:150px;'>" . $locale ['uc255'] . "</td>\n</tr>\n";
		$result = dbquery ( "select * from " . DB_PREFIX . "fb_groups fg
		left join " . DB_USER_GROUPS . " g on fg.group_id=g.group_id
		order by fg.group_created desc" );
		while ( $data = dbarray ( $result ) ) {
			$members = dbquery ( "SELECT * FROM " . DB_USERS . " WHERE user_groups REGEXP('^\\\.{$data['group_id']}$|\\\.{$data['group_id']}\\\.|\\\.{$data['group_id']}$') or user_groups='" . $data ['group_id'] . "' ORDER BY user_level DESC, user_name" );
			$members = dbrows ( $members );
			$wallposts = dbcount ( "(comment_id)", DB_COMMENTS, "comment_type='g' and comment_item_id='" . $data ['group_id'] . "'" );
			echo "<tr>\n<td class='tbl2' style='padding:7px;'><span style='font-size:13px;'><a href='" . FUSION_SELF . "?section=groups&amp;view=" . $data ['group_id'] . "' style='text-decoration:underline;'>" . $data ['group_name'] . "</a></span>\n";
			if ($data ['group_description']) {
				echo "<br />\n<span class='small'>" . trimlink ( $data ['group_description'], 30 ) . "</span>\n";
			}
			echo "</td>\n";
			echo "<td class='tbl1' style='white-space:nowrap;padding:7px;'>" . timePassed ( $data ['group_created'], false ) . "</td>\n";
			echo "<td class='tbl2' style='text-align:center;padding:7px;'>$members</td>\n";
			echo "<td class='tbl1' style='text-align:center;padding:7px;'>$wallposts</td>\n";
			echo "<td class='tbl2' style='padding:7px;'>";
			if ($wallposts) {
				$latest = dbarray ( dbquery ( "select c.*, u.* from " . DB_COMMENTS . " c
				left join " . DB_USERS . " u on u.user_id=c.comment_name
				where c.comment_type='g' and c.comment_item_id='" . $data ['group_id'] . "' order by c.comment_datestamp desc limit 1" ) );
				echo timepassed ( $latest ['comment_datestamp'] ) . "<br />\n";
				echo $locale ['uc282'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $latest ['user_id'] . "'>" . showLabel ( $latest ['user_id'] ) . "</a>\n";
			} else {
				echo $locale ['uc281'];
			}
			echo "</td>\n</tr>\n";
		}
	} else {
		echo "<tr>\n<td class='tbl1' style='text-align:center;'>" . $locale ['uc258'] . "</td>\n</tr>\n";
	}
	echo "<tr>\n<td class='tbl1' style='text-align:center;'" . ($rows ? " colspan='5'" : "") . ">" . (checkgroup ( $fb4 ['group_create'] ) ? "<a href='" . FUSION_SELF . "?section=groups&amp;action=create'>" . $locale ['uc256'] . "</a> :: " : "") . "<a href='" . FUSION_SELF . "?section=groups&amp;action=search'>" . $locale ['uc257'] . "</a></td>\n</tr>\n";
}
if (! defined ( "USER_CP" )) {
	echo "</table>\n</td>\n</tr>\n</table>\n";
	closetable ();
	require_once THEMES . "templates/footer.php";
}
?>