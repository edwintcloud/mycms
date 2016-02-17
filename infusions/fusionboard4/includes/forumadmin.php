<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: forumadmin.php
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

if(!defined("IN_FUSION")) die("Access Denied");
include LOCALE.LOCALESET."admin/forums.php";

if (!checkrights("F") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

if (isset($_GET['action']) && $_GET['action'] == "prune") { require_once ADMIN."forums_prune.php"; }

if (isset($_GET['action']) && $_GET['action'] == "refresh") {
	$i = 1;
	$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' ORDER BY forum_order");
	while ($data = dbarray($result)) {
		$result2 = dbquery("UPDATE ".DB_FORUMS." SET forum_order='$i' WHERE forum_id='".$data['forum_id']."'");
		$result2 = dbquery("SELECT * FROM ".DB_FORUMS." f
		left join ".DB_PREFIX."fb_forums f2 on f2.forum_id = f.forum_id
		WHERE f2.forum_parent='0' and f.forum_cat='".$data['forum_id']."' ORDER BY f.forum_order");
		echo "<b>".$data['forum_name']."</b> ($i)<br />";
		$k = 1;
		while ($data2 = dbarray($result2)) {
			$result3 = dbquery("UPDATE ".DB_FORUMS." SET forum_order='$k' WHERE forum_id='".$data2['forum_id']."'");
			echo $data2['forum_name']." ($k)<br />";
			echo refreshOrder($data2['forum_id']);
			$k++;
		}
		$i++;
	}
	redirect(FUSION_SELF.$aidlink."&section=forums");
}

function refreshOrder($parent, $level=1){
	$result2 = dbquery("SELECT * FROM ".DB_FORUMS." f
	left join ".DB_PREFIX."fb_forums f2 on f2.forum_id = f.forum_id
	WHERE f2.forum_parent='$parent' ORDER BY f.forum_order");
	$k = 1;
	$list = "";
	while ($data2 = dbarray($result2)) {
		$result3 = dbquery("UPDATE ".DB_FORUMS." SET forum_order='$k' WHERE forum_id='".$data2['forum_id']."'");
		for($i=$level; $i > 0; $i--){
			$list .= "&emsp;";
		}
		$list .= $data2['forum_name']." ($k)<br />";
		$k++;
		$list .= refreshOrder($data2['forum_id'], $level+1);
	}
	return $list;
}

if (isset($_GET['status']) && !isset($message)) {
	if ($_GET['status'] == "savecn") {
		$message = $locale['410'];
	} elseif ($_GET['status'] == "savecu") {
		$message = $locale['411'];
	} elseif ($_GET['status'] == "savefn") {
		$message = $locale['510'];
	} elseif ($_GET['status'] == "savefu") {
		$message = $locale['511'];
	} elseif ($_GET['status'] == "savefm") {
		$message = $locale['515'];
	} elseif ($_GET['status'] == "delcn") {
		$message = $locale['412']."<br />\n<span class='small'>".$locale['413']."</span>";
	} elseif ($_GET['status'] == "delcy") {
		$message = $locale['414'];
	} elseif ($_GET['status'] == "delfn") {
		$message = $locale['512']."<br />\n<span class='small'>".$locale['513']."</span>";
	} elseif ($_GET['status'] == "delfy") {
		$message = $locale['514'];
	}
	if ($message) {	echo "<div class='admin-message'>".$message."</div>\n"; }
}	

if (isset($_POST['save_cat'])) {
	$cat_name = trim(stripinput($_POST['cat_name']));
	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['t']) && $_GET['t'] == "cat")) {
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_name='$cat_name' WHERE forum_id='".$_GET['forum_id']."'");
		$collapsed = isNum($_POST['collapsed']) ? $_POST['collapsed'] : "";
		$result = dbquery("UPDATE ".DB_PREFIX."fb_forums set forum_collapsed='$collapsed' where forum_id='".$_GET['forum_id']."'");
		redirect(FUSION_SELF.$aidlink."&section=forums&status=savecu");
	} else {
		if ($cat_name) {
			$cat_order = isnum($_POST['cat_order']) ? $_POST['cat_order'] : "";
			$collapsed = isnum($_POST['collapsed']) ? $_POST['collapsed'] : "";
			if(!$cat_order) $cat_order=dbresult(dbquery("SELECT MAX(forum_order) FROM ".DB_FORUMS." WHERE forum_cat='0'"),0)+1;
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 WHERE forum_cat='0' AND forum_order>='$cat_order'");	
			$result = dbquery("INSERT INTO ".DB_FORUMS." (forum_cat, forum_name, forum_order, forum_description, forum_moderators, forum_access, forum_post, forum_reply, forum_poll, forum_vote, forum_attach, forum_lastpost, forum_lastuser) VALUES ('0', '$cat_name', '$cat_order', '', '', '0', '0', '0', '0', '0', '0', '0', '0')");
			$result = dbquery("INSERT INTO ".DB_PREFIX."fb_forums (forum_id, forum_icon,forum_parent,forum_collapsed) VALUES('".mysql_insert_id()."', '', '', '$collapsed')");
			redirect(FUSION_SELF.$aidlink."&section=forums&status=savecn");
		}
	}
} elseif (isset($_POST['save_forum'])) {
	$forum_name = trim(stripinput($_POST['forum_name']));
	$forum_description = trim(stripinput($_POST['forum_description']));
	$forum_cat = isnum($_POST['forum_cat']) ? $_POST['forum_cat'] : 0;
	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['t']) && $_GET['t'] == "forum")) {
		$forum_mods = $_POST['forum_mods'];
		$forum_access = isnum($_POST['forum_access']) ? $_POST['forum_access'] : 0;
		$forum_post = isnum($_POST['forum_post']) ? $_POST['forum_post'] : 0;
		$forum_reply = isnum($_POST['forum_reply']) ? $_POST['forum_reply'] : 0;
		$forum_attach = isnum($_POST['forum_attach']) ? $_POST['forum_attach'] : 0;
		$forum_poll = isnum($_POST['forum_poll']) ? $_POST['forum_poll'] : 0;
		$forum_vote = isnum($_POST['forum_vote']) ? $_POST['forum_vote'] : 0;
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_name='$forum_name', forum_cat='$forum_cat', forum_description='$forum_description', forum_moderators='$forum_mods', forum_access='$forum_access', forum_post='$forum_post', forum_reply='$forum_reply', forum_attach='$forum_attach', forum_poll='$forum_poll', forum_vote='$forum_vote' WHERE forum_id='".$_GET['forum_id']."'");
		// start fb4 mod
		
		$forum_icon = addslash(stripinput($_POST['forum_icon']));
		$forum_parent = ((isset($_POST['forum_parent']) && isNum($_POST['forum_parent'])) ? $_POST['forum_parent'] : 0);
		$result = dbquery("UPDATE ".$db_prefix."fb_forums set forum_icon='$forum_icon', forum_parent='$forum_parent' where forum_id='".$_GET['forum_id']."'");
		
		// end fb4 mod
		redirect(FUSION_SELF.$aidlink."&section=forums&status=savefu");
	} else {
		if ($forum_name) {
			$forum_order = isnum($_POST['forum_order']) ? $_POST['forum_order'] : "";
			$forum_parent = ((isset($_POST['forum_parent']) && isNum($_POST['forum_parent'])) ? $_POST['forum_parent'] : 0);
			if(!$forum_order){
				$forum_order=dbresult(dbquery("SELECT MAX(forum_order) FROM ".DB_FORUMS." f
				left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
				WHERE f2.forum_parent='$forum_parent'"),0)+1;
			}
			$result2 = dbquery("select * from ".DB_FORUMS." f
			left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
			WHERE forum_cat='$forum_cat' AND forum_order>='$forum_order'".($forum_parent ? " AND f2.forum_parent='$forum_parent'" : ""));
			while($data2 = dbarray($result2)){
				$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 where forum_id='".$data2['forum_id']."'");
			}
			$result = dbquery("INSERT INTO ".DB_FORUMS." (forum_cat, forum_name, forum_order, forum_description, forum_moderators, forum_access, forum_post, forum_reply, forum_attach, forum_poll, forum_vote, forum_lastpost, forum_lastuser) VALUES ('$forum_cat', '$forum_name', '$forum_order', '$forum_description', '', '0', '101', '101', '101', '0', '0', '0', '0')");
			$result = dbquery("INSERT INTO ".DB_PREFIX."fb_forums (forum_id, forum_icon,forum_parent,forum_collapsed) VALUES('".mysql_insert_id()."', '', '$forum_parent', '0')");
			redirect(FUSION_SELF.$aidlink."&section=forums&status=savefn");
		} else {
			redirect(FUSION_SELF.$aidlink."&section=forums");
		}
	}
} elseif ((isset($_GET['action']) && $_GET['action'] == "mu") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['order']) && isnum($_GET['order']))) {
	if (isset($_GET['t']) && $_GET['t'] == "cat") {
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' AND forum_order='".$_GET['order']."'"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 WHERE forum_id='".$data['forum_id']."'");
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_id='".$_GET['forum_id']."'");
	} elseif ((isset($_GET['t']) && $_GET['t'] == "forum") && (isset($_GET['cat']) && isnum($_GET['cat']))) {
		$parent = (isset($_GET['parent']) && isNum($_GET['parent']) ? " AND f2.forum_parent='".$_GET['parent']."'" : "");
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." f 
		left join ".DB_PREFIX."fb_forums f2 on f.forum_id=f2.forum_id 
		WHERE f.forum_cat='".$_GET['cat']."' AND f.forum_order='".$_GET['order']."'$parent"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 WHERE forum_id='".$data['forum_id']."'");
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_id='".$_GET['forum_id']."'");
	}
	redirect(FUSION_SELF.$aidlink."&section=forums");
} elseif ((isset($_GET['action']) && $_GET['action'] == "md") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['order']) && isnum($_GET['order']))) {
	if (isset($_GET['t']) && $_GET['t'] == "cat") {
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' AND forum_order='".$_GET['order']."'"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_id='".$data['forum_id']."'");
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 WHERE forum_id='".$_GET['forum_id']."'");
	} elseif ((isset($_GET['t']) && $_GET['t'] == "forum") && (isset($_GET['cat']) && isnum($_GET['cat']))) {
		$parent = (isset($_GET['parent']) && isNum($_GET['parent']) ? " AND f2.forum_parent='".$_GET['parent']."'" : "");
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." f 
		left join ".DB_PREFIX."fb_forums f2 on f.forum_id=f2.forum_id 
		WHERE f.forum_cat='".$_GET['cat']."' AND f.forum_order='".$_GET['order']."'$parent"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_id='".$data['forum_id']."'");
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order+1 WHERE forum_id='".$_GET['forum_id']."'");
	}
	redirect(FUSION_SELF.$aidlink."&section=forums");
} elseif ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['t']) && $_GET['t'] == "cat")) {
	if (!dbcount("(forum_id)", DB_FORUMS, "forum_cat='".$_GET['forum_id']."'")) {
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_cat='0' AND forum_order>'".$data['forum_order']."'");
		$result = dbquery("DELETE FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'");
		redirect(FUSION_SELF.$aidlink."&section=forums&status=delcy");
	} else {
		redirect(FUSION_SELF.$aidlink."&section=forums&status=delcn");
	}
} elseif ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['forum_id']) && isnum($_GET['forum_id'])) && (isset($_GET['t']) && $_GET['t'] == "forum")) {
	if (!dbcount("(thread_id)", DB_THREADS, "forum_id='".$_GET['forum_id']."'") && !dbcount("(forum_id)", DB_PREFIX."fb_forums", "forum_parent='".$_GET['forum_id']."'")) {
		$parent = (isset($_GET['parent']) && isNum($_GET['parent']) ? " AND f2.forum_parent='".$_GET['parent']."'" : "");
		$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'"));
		$result = dbquery("UPDATE ".DB_FORUMS." SET forum_order=forum_order-1 WHERE forum_cat='".$data['forum_cat']."' AND forum_order>'".$data['forum_order']."'$parent");
		$result = dbquery("DELETE FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'");
		$result = dbquery("DELETE FROM ".DB_PREFIX."fb_forums where forum_id='".$_GET['forum_id']."'");
		redirect(FUSION_SELF.$aidlink."&section=forums&status=delfy");
	} else {
		redirect(FUSION_SELF.$aidlink."&section=forums&status=delfn");
	}
} else {
	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['forum_id']) && isnum($_GET['forum_id']))) {
		if (isset($_GET['t']) && $_GET['t'] == "cat") {
			$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'");
			if (dbrows($result)) {
				$data = dbarray($result);
				$cat_name = $data['forum_name'];
				$cat_title = $locale['401'];
				$cat_action = FUSION_SELF.$aidlink."&amp;section=forums&amp;action=edit&amp;forum_id=".$data['forum_id']."&amp;t=cat";
				$forum_title = $locale['500'];
				$forum_action = FUSION_SELF.$aidlink."&section=forums";
				$fbResult = dbquery("select * from ".$db_prefix."fb_forums where forum_id='".$_GET['forum_id']."'");
				if(dbrows($fbResult)){
					$fbData = dbarray($fbResult);
					$collapsed = ($fbData['forum_collapsed'] == "1" ? " CHECKED" : "");
				} else {
					$collapsed = "";
				}
			} else {
				redirect(FUSION_SELF.$aidlink."&section=forums");
			}
		} elseif (isset($_GET['t']) && $_GET['t'] == "forum") {
			$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'");
			if (dbrows($result)) {
				$data = dbarray($result);
				$forum_name = $data['forum_name'];
				$forum_description = $data['forum_description'];
				$forum_cat = $data['forum_cat'];
				$forum_access = $data['forum_access'];
				$forum_post = $data['forum_post'];
				$forum_reply = $data['forum_reply'];
				$forum_attach = $data['forum_attach'];
				$forum_poll = $data['forum_poll'];
				$forum_vote = $data['forum_vote'];
				$forum_title = $locale['501'];
				$forum_action = FUSION_SELF.$aidlink."&section=forums&amp;action=edit&amp;forum_id=".$data['forum_id']."&amp;t=forum";
				$cat_title = $locale['400'];
				$cat_action = FUSION_SELF.$aidlink."&section=forums";
				$fbResult = dbquery("select * from ".$db_prefix."fb_forums where forum_id='".$_GET['forum_id']."'");
				if(dbrows($fbResult)){
					$fbData = dbarray($fbResult);
					$forum_icon = stripslash($fbData['forum_icon']);
					$forum_parent = $fbData['forum_parent'];
				} else {
					$fbQuery = dbquery("insert into ".$db_prefix."fb_forums (forum_id, forum_icon) VALUES('".$_GET['forum_id']."', '')");
					$forum_icon = "";
					$forum_parent = "";
				}
			} else {
				redirect(FUSION_SELF.$aidlink."&section=forums");
			}
		}
	} else {
		$cat_name = "";
		$cat_order = "";
		$cat_title = $locale['400'];
		$cat_action = FUSION_SELF.$aidlink."&section=forums";
		$forum_name = "";
		$forum_description = "";
		$forum_cat = 0;
		$forum_order = "";
		$forum_access = 0;
		$forum_post = 0;
		$forum_reply = 0;
		$forum_attach = 0;
		$forum_poll = 0;
		$forum_vote = 0;
		$forum_title = $locale['500'];
		$forum_action = FUSION_SELF.$aidlink."&section=forums";
		$forum_icon = "";
		$forum_parent = "";
		$collapsed = "";
	}
	if (!isset($_GET['t']) || $_GET['t'] != "forum") {
		opentable($cat_title);
		echo "<form name='addcat' method='post' action='$cat_action'>\n";
		echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
		echo "<td class='tbl'>".$locale['420']."<br />\n";
		echo "<input type='text' name='cat_name' value='".$cat_name."' class='textbox' style='width:230px;' /></td>\n";
		echo "<td width='50' class='tbl'>";
		if (!isset($_GET['action']) || $_GET['action'] != "edit") {
			echo $locale['421']."<br />\n<input type='text' name='cat_order' value='".$cat_order."' class='textbox' style='width:45px;' />";
		}
		echo "</td>\n</tr>\n<tr>\n";
		echo "<tr>\n<td class='tbl' colspan='2'><label><input type='checkbox' name='collapsed' value='1'$collapsed />".$locale['fb102']."</label></td>\n</tr>\n<tr>\n";
		echo "<td align='center' colspan='2' class='tbl'>\n";
		echo "<input type='submit' name='save_cat' value='".$locale['422']."' class='button' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
		closetable();
	}
	if (!isset($_GET['t']) || $_GET['t'] != "cat") {
		$cat_opts = ""; $sel = "";
		$result2 = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' ORDER BY forum_order");
		if (dbrows($result2)) {
			while ($data2 = dbarray($result2)) {
				if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['t']) && $_GET['t'] == "forum")) { $sel = ($data2['forum_id'] == $forum_cat ? " selected='selected'" : ""); }
				$cat_opts .= "<option value='".$data2['forum_id']."'".$sel.">".$data2['forum_name']."</option>\n";
			}
	
			function create_options($selected, $hide=array(), $off=false) {
				global $locale; $option_list = ""; $options = getusergroups();
				if ($off) { $option_list = "<option value='0'>".$locale['531']."</option>\n"; }
				while(list($key, $option) = each($options)){
					if (!in_array($option['0'], $hide)) {
						$sel = ($selected == $option['0'] ? " selected='selected'" : "");
						$option_list .= "<option value='".$option['0']."'$sel>".$option['1']."</option>\n";
					}
				}
				return $option_list;
			}
			
			opentable($forum_title);
			echo "<form name='addforum' method='post' action='$forum_action'>\n";
			echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
			echo "<td colspan='2' class='tbl'>".$locale['520']."<br />\n";
			echo "<input type='text' name='forum_name' value='".$forum_name."' class='textbox' style='width:285px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td colspan='2' class='tbl'>".$locale['521']."<br />\n";
			echo "<input type='text' name='forum_description' value='".$forum_description."' class='textbox' style='width:285px;' /></td>\n";
			echo "</tr>\n<tr>\n";
			echo "<td class='tbl'>".$locale['522']."<br />\n";
			echo "<select name='forum_cat' class='textbox' style='width:225px;'>\n".$cat_opts."</select></td>\n";
			echo "<td width='55' class='tbl'>";
			if (!isset($_GET['action']) || $_GET['action'] != "edit") {
				echo $locale['523']."<br />\n<input type='text' name='forum_order' value='".$forum_order."' class='textbox' style='width:45px;' />";
				echo "</td>\n</tr>\n";
			} else {
				echo "</td></tr>\n";
			}
			
			if (isset($_GET['action']) && $_GET['action'] == "edit") {
			
				$forumIconImages = makefileopts(makefilelist(INFUSIONS."fusionboard4/images/forum_icons/", ".|..|index.php"), $forum_icon);
				
				echo "<tr><td colspan='2' class='tbl'>".$locale['fb100']."<br />\n";
				echo "<select name='forum_icon' class='textbox' style='width:285px;' />\n";
				echo "<option value=''>---</option>\n$forumIconImages\n";
				echo "</td>\n";
				echo "</tr>\n<tr>\n";
				
			}
			
			function renderChildren($parent, $level=1){
					global $forum_parent;
					$children = "";
					$p_res = dbquery("select * from ".DB_PREFIX."forums f
					left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
					where f2.forum_parent='$parent'");
					while($p_data = dbarray($p_res)){
						$children .= "<option value='".$p_data['forum_id']."'".($p_data['forum_id']==$forum_parent ? " SELECTED" : "").">";
						for($i = $level; $i--; $i > 0){
							$children .= "--";
						}
						$children .= $p_data['forum_name']."</option>\n";
						$children .= renderChildren($p_data['forum_id'], ($level+1));
					}
					return $children;
				}
				
				$possibleParents = "";
				$c_res = dbquery("select * from ".$db_prefix."forums where forum_cat='0'");
				while($c_data = dbarray($c_res)){
					$possibleParents .= "<optgroup label='".$c_data['forum_name']."'>\n";
					$p_res = dbquery("select * from ".$db_prefix."forums f
					left join ".$db_prefix."fb_forums f2 on f2.forum_id=f.forum_id
					where f2.forum_parent='0' and f.forum_cat='".$c_data['forum_id']."'");
					while($p_data = dbarray($p_res)){
						$possibleParents .= "<option value='".$p_data['forum_id']."'".($p_data['forum_id']==$forum_parent ? " SELECTED" : "").">".$p_data['forum_name']."</option>\n";
						$possibleParents .= renderChildren($p_data['forum_id']);
					}
					$possibleParents .= "</optgroup>\n";
				}
				
				echo "<td colspan='2' class='tbl'>".$locale['fb101']."<br />\n";
				echo "<select name='forum_parent' class='textbox' style='width:285px;' />\n";
				echo "<option value=''>---</option>\n$possibleParents\n";
				echo "</td>\n";
				echo "</tr>\n<tr>\n";
				
			if (!isset($_GET['action']) || $_GET['action'] != "edit") {
				echo "<tr>\n";
				echo "<td align='center' colspan='2' class='tbl'>\n";
				echo "<input type='submit' name='save_forum' value='".$locale['532']."' class='button' />";
				echo "</td>\n</tr>\n";
			}
			
			echo "</table>\n";
			
			if (isset($_GET['action']) && $_GET['action'] == "edit") {
				echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
				echo "<td class='tbl2' colspan='2'><strong>".$locale['524']."</strong></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['525']."</td>\n";
				echo "<td class='tbl'><select name='forum_access' class='textbox' style='width:150px;'>\n".create_options($forum_access, array(), false)."</select></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['526']."</td>\n";
				echo "<td class='tbl'><select name='forum_post' class='textbox' style='width:150px;'>\n".create_options($forum_post, array(0), true)."</select></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['527']."</td>\n";
				echo "<td class='tbl'><select name='forum_reply' class='textbox' style='width:150px;'>\n".create_options($forum_reply, array(0), true)."</select></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['528']."</td>\n";
				echo "<td class='tbl'><select name='forum_attach' class='textbox' style='width:150px;'>\n".create_options($forum_attach, array(0), true)."</select></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['529']."</td>\n";
				echo "<td class='tbl'><select name='forum_poll' class='textbox' style='width:150px;'>\n".create_options($forum_poll, array(0), true)."</select></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td width='1%' class='tbl' style='white-space:nowrap'>".$locale['530']."</td>\n";
				echo "<td class='tbl'><select name='forum_vote' class='textbox' style='width:150px;'>\n".create_options($forum_vote, array(0), true)."</select></td>\n";
				echo "</tr>\n"; //";
				if (!isset($_GET['action']) || $_GET['action'] != "edit") {
					echo "<tr>\n<td align='center' colspan='2' class='tbl'>\n";
					echo "<input type='submit' name='save_forum' value='".$locale['532']."' class='button' /></td>\n";
					echo "</tr>\n</table>\n";
				}
			}
			if (!isset($_GET['action'])) echo "\n</form>";
			if (isset($_GET['action']) && $_GET['action'] == "edit") {
				$mod_groups = getusergroups();
				while(list($key, $mod_group) = each($mod_groups)){
					if ($mod_group['0'] != "0" && $mod_group['0'] != "101" && $mod_group['0'] != "103") {
						if (!preg_match("(^{$mod_group['0']}$|^{$mod_group['0']}\.|\.{$mod_group['0']}\.|\.{$mod_group['0']}$)", $data['forum_moderators'])) {
							$mods1_user_id[] = $mod_group['0'];
							$mods1_user_name[] = $mod_group['1'];
						} else {
							$mods2_user_id[] = $mod_group['0'];
							$mods2_user_name[] = $mod_group['1'];
						}
					}
				}
				echo "<tr>\n<td class='tbl2' colspan='2'><strong>".$locale['533']."</strong></td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td align='center' colspan='2' class='tbl'>\n<select multiple='multiple' size='10' name='modlist1' id='modlist1' class='textbox' style='width:140px' onchange=\"addUser('modlist2','modlist1');\">\n";
				for ($i=0;$i < count($mods1_user_id);$i++) {
					echo "<option value='".$mods1_user_id[$i]."'>".$mods1_user_name[$i]."</option>\n";
				}
				echo "</select>\n";
				echo "<select multiple='multiple' size='10' name='modlist2' id='modlist2' class='textbox' style='width:140px' onchange=\"addUser('modlist1','modlist2');\">\n";
				if (isset($mods2_user_id) && is_array($mods2_user_id)) {
					for ($i=0;$i < count($mods2_user_id);$i++) {
						echo "<option value='".$mods2_user_id[$i]."'>".$mods2_user_name[$i]."</option>\n";
					}
				}
				echo "</select>\n";
				echo "</td>\n";
				echo "</tr>\n<tr>\n";
				echo "<td align='center' colspan='2'><br />\n";
				echo "<input type='hidden' name='forum_mods' />\n";
				echo "<input type='hidden' name='forum_id' value='".$data['forum_id']."' />\n";
				echo "<input type='hidden' name='save_forum' />\n";
				echo "<input type='button' name='save' value='".$locale['532']."' class='button' onclick='saveMods();' /></td>\n";
				echo "</tr>\n</table>\n</form>\n";
				echo "<script type='text/javascript'>\n"."function addUser(toGroup,fromGroup) {\n";
				echo "var listLength = document.getElementById(toGroup).length;\n";
				echo "var selItem = document.getElementById(fromGroup).selectedIndex;\n";
				echo "var selText = document.getElementById(fromGroup).options[selItem].text;\n";
				echo "var selValue = document.getElementById(fromGroup).options[selItem].value;\n";
				echo "var i; var newItem = true;\n";
				echo "for (i = 0; i < listLength; i++) {\n";
				echo "if (document.getElementById(toGroup).options[i].text == selText) {\n";
				echo "newItem = false; break;\n}\n}\n"."if (newItem) {\n";
				echo "document.getElementById(toGroup).options[listLength] = new Option(selText, selValue);\n";
				echo "document.getElementById(fromGroup).options[selItem] = null;\n}\n}\n";
	
				echo "function saveMods() {\n"."var strValues = \"\";\n";
				echo "var boxLength = document.getElementById('modlist2').length;\n";
				echo "var count = 0;\n"."	if (boxLength != 0) {\n"."for (i = 0; i < boxLength; i++) {\n";
				echo "if (count == 0) {\n"."strValues = document.getElementById('modlist2').options[i].value;\n";
				echo "} else {\n"."strValues = strValues + \".\" + document.getElementById('modlist2').options[i].value;\n";
				echo "}\n"."count++;\n}\n}\n";
				echo "if (strValues.length == 0) {\n"."document.forms['addforum'].submit();\n";
				echo "} else {\n"."document.forms['addforum'].forum_mods.value = strValues;\n";
				echo "document.forms['addforum'].submit();\n}\n}\n</script>\n";
			}
			closetable();
	}
	}
	opentable($locale['550']);
	$i = 1; $k = 1;
	echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
	$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_cat='0' ORDER BY forum_order");
	if (dbrows($result) != 0) {
		echo "<tr>\n<td class='tbl2'><strong>".$locale['551']."</strong></td>\n";
		echo "<td align='center' colspan='2' width='1%' class='tbl2' style='white-space:nowrap'><strong>".$locale['552']."</strong></td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><strong>".$locale['553']."</strong></td>\n";
		echo "</tr>\n";
		$i = 1;
		while ($data = dbarray($result)) {
		$forum = $data['forum_id'];
		$boxname = "forum".$forum; $state = "off"; $collapse = true;
			
			echo "<tr>\n<td class='tbl2'><strong>".$data['forum_name']." ".panelbutton($state,$boxname)."</strong></td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$data['forum_order']."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>\n";
			if (dbrows($result) != 1) {
				$up = $data['forum_order'] - 1;	$down = $data['forum_order'] + 1;
				if ($i == 1) {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=md&amp;order=$down&amp;forum_id=".$data['forum_id']."&amp;t=cat'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
				} elseif ($i < dbrows($result)) {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=mu&amp;order=$up&amp;forum_id=".$data['forum_id']."&amp;t=cat'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=md&amp;order=$down&amp;forum_id=".$data['forum_id']."&amp;t=cat'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
				} else {
					echo "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=mu&amp;order=$up&amp;forum_id=".$data['forum_id']."&amp;t=cat'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
				}
			}
			$i++;
			echo "</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=edit&amp;forum_id=".$data['forum_id']."&amp;t=cat'>".$locale['554']."</a> ::\n";
			echo "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=delete&amp;forum_id=".$data['forum_id']."&amp;t=cat' onclick=\"return confirm('".$locale['440']."');\">".$locale['555']."</a></td>\n";
			echo "</tr>\n";
			$result2 = dbquery("SELECT * FROM ".DB_FORUMS." f
			LEFT JOIN ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
			where f.forum_cat='".$data['forum_id']."' and f2.forum_parent='0' ORDER BY f.forum_order");
			echo "</table>".panelstate("off", $boxname)."<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
			if (dbrows($result2)) {
				$k = 1;
				while ($data2 = dbarray($result2)) {
					echo renderForum($data2, $result2, $k);
					echo forumChildren($data2['forum_id']);
					$k++;
				}
			}
			echo "</table></div><table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
		}
		echo "<tr>\n<td align='center' colspan='5' class='tbl2'>[ <a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=refresh'>".$locale['562']."</a> ]</td>\n</tr>\n";
	} else {
		echo "<tr>\n<td align='center' class='tbl1'>".$locale['560']."</td>\n</tr>\n";
	}
	echo "</table>\n";
	closetable();
}

function renderForum($data2, $result2, $k, $indent=""){
	
	global $aidlink, $locale;
	
	$forumR = "";
	$forumR .= "<tr>\n";
	$forumR .= "<td class='tbl1'>$indent<span class='alt'>".$data2['forum_name'];
	if(dbcount("(forum_id)", DB_PREFIX."fb_forums", "forum_parent='".$data2['forum_id']."'")){
		$forumR .= " ".panelbutton("off","forum".$data2['forum_id']);
	}
	$forumR .= "</span>\n";
	$forumR .= "[<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=prune&amp;forum_id=".$data2['forum_id']."'>".$locale['563']."</a>]<br />\n";
	$forumR .= ($data2['forum_description'] ? "$indent<span class='small'>".$data2['forum_description']."</span>" : "")."</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$data2['forum_order']."</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>\n";
	
	$parent = ($data2['forum_parent'] > 0 ? "&amp;parent=".$data2['forum_parent'] : "");
	
	if (dbrows($result2) != 1) {
		$up = $data2['forum_order'] - 1; $down = $data2['forum_order'] + 1;
		if ($k == 1) {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=md&amp;order=$down&amp;forum_id=".$data2['forum_id']."&amp;t=forum&amp;cat=".$data2['forum_cat']."$parent'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
		} elseif ($k < dbrows($result2)) {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=mu&amp;order=$up&amp;forum_id=".$data2['forum_id']."&amp;t=forum&amp;cat=".$data2['forum_cat']."$parent'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=md&amp;order=$down&amp;forum_id=".$data2['forum_id']."&amp;t=forum&amp;cat=".$data2['forum_cat']."$parent'><img src='".get_image("down")."' alt='".$locale['557']."' title='".$locale['557']."' style='border:0px;' /></a>\n";
		} else {
			$forumR .= "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=mu&amp;order=$up&amp;forum_id=".$data2['forum_id']."&amp;t=forum&amp;cat=".$data2['forum_cat']."$parent'><img src='".get_image("up")."' alt='".$locale['556']."' title='".$locale['558']."' style='border:0px;' /></a>\n";
		}
	}
	$forumR .= "</td>\n";
	$forumR .= "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=edit&amp;forum_id=".$data2['forum_id']."&amp;t=forum'>".$locale['554']."</a> ::\n";
	$forumR .= "<a href='".FUSION_SELF.$aidlink."&amp;section=forums&amp;action=delete&amp;forum_id=".$data2['forum_id']."&amp;t=forum$parent' onclick=\"return confirm('".$locale['570']."');\">".$locale['555']."</a></td>\n";
	$forumR .= "</tr>\n";
	
	return $forumR;
}

function forumChildren($parent, $level=1){

	$result = dbquery("select * from ".DB_PREFIX."forums f
	left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
	where f2.forum_parent='$parent' order by f.forum_order asc");
	$forumR = "";
	
	if (dbrows($result)) {
		$forumR .= "</table>".panelstate("off", "forum".$parent)."<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
		$k = 1;
		$children = "";
		for($i = $level; $i--; $i > 0){
			$children .= "&emsp;";
		}
		while ($data = dbarray($result)) {
			
			$forumR .= renderForum($data, $result, $k, $children);
			$k++;
			$forumR .= forumChildren($data['forum_id'], ($level+1));
			
		}
		$forumR .= "</table></div><table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
	}
	return $forumR;
}
?>