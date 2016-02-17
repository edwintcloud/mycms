<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");
if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

include INFUSIONS."fusionboard4/includes/rating.inc.php";

add_to_head("<script type='text/javascript'><!--
function show(id) {
var d = document.getElementById(id);
if (d.style.display=='none') { d.style.display='block'; } else { d.style.display='none'; }
}
//--></script>
<script src='".INFUSIONS."fusionboard4/includes/js/boxover.js' type='text/javascript'></script>");

$views = array(1, 2, 3, 4);
if((isset($_GET['view']) && isNum($_GET['view'])) && iMEMBER && $fb4['layout_change'] && in_array($_GET['view'], $views)){
	$result = dbquery("SELECT * FROM ".DB_PREFIX."fb_users where user_id='".$userdata['user_id']."'");
	if(dbrows($result)){
		$result = dbquery("update ".DB_PREFIX."fb_users set user_layout='".$_GET['view']."' where user_id='".$userdata['user_id']."'");
	} else {
		$result = dbquery("INSERT INTO ".DB_PREFIX."fb_users (user_id, user_layout, user_notes, user_warning, user_invisible, user_lv)
		VALUES('".$userdata['user_id']."', '".$_GET['view']."', '', '0', '0', '".time()."')");
	}
}

$posts_per_page = $fb4['posts_per_page'];

add_to_title($locale['global_200'].$locale['400']);

if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }

if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

$result = dbquery(
	"SELECT t.*, f.*, f3.*, f2.forum_name AS forum_cat_name
	FROM ".DB_THREADS." t
	LEFT JOIN ".DB_FORUMS." f ON t.forum_id=f.forum_id
	LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat=f2.forum_id
	LEFT JOIN ".DB_PREFIX."fb_forums f3 on f.forum_id=f3.forum_id
	WHERE t.thread_id='".$_GET['thread_id']."'"
);

$announcementCheck = dbquery("select * from ".DB_PREFIX."fb_threads where thread_id='".$_GET['thread_id']."' and thread_announcement='1'");
$announcementCheck = dbrows($announcementCheck);

if (dbrows($result)) {
	$fdata = dbarray($result);
	if (!checkgroup($fdata['forum_access']) || !$fdata['forum_cat']) { redirect("index.php"); }
} else {
	redirect("index.php");
}

$threadID = $_GET['thread_id'];

if ($fdata['forum_post'] != 0 && checkgroup($fdata['forum_post'])) {
	$can_post = true;
} else {
	$can_post = false;
}

if ($fdata['forum_reply'] != 0 && checkgroup($fdata['forum_reply'])) {
	if($announcementCheck && !checkgroup($fb4['announce_reply'])){
		$can_reply = false;
	} else {
		$can_reply = true;
	}
} else {
	$can_reply = false;
}

if ($settings['forum_edit_lock'] == 1) {
	$lock_edit = true;
} else {
	$lock_edit = false;
}

$mod_groups = explode(".", $fdata['forum_moderators']);

if (iSUPERADMIN) { define("iMOD", true); }

if (!defined("iMOD") && iMEMBER && $fdata['forum_moderators']) {
	foreach ($mod_groups as $mod_group) {
		if (!defined("iMOD") && checkgroup($mod_group)) { define("iMOD", true); }
	}
}

if (!defined("iMOD")) { define("iMOD", false); }

if (iMEMBER) {
	$thread_match = $fdata['thread_id']."\|".$fdata['thread_lastpost']."\|".$fdata['forum_id'];
	if (($fdata['thread_lastpost'] > $lastvisited) && !preg_match("(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata['user_threads'])) {
		$result = dbquery("UPDATE ".DB_USERS." SET user_threads='".$userdata['user_threads'].".".stripslashes($thread_match)."' WHERE user_id='".$userdata['user_id']."'");
	}
}

$result = dbquery("UPDATE ".DB_THREADS." SET thread_views=thread_views+1 WHERE thread_id='".$_GET['thread_id']."'");

if ((iMOD || iSUPERADMIN) && isset($_POST['delete_posts']) && (isset($_POST['delete_post'])) && is_array($_POST['delete_post']) && count($_POST['delete_post'])) {
	$del_posts = ""; $i = 0; $post_count = 0;
	foreach ($_POST['delete_post'] as $del_post_id) {
		if (isnum($del_post_id)) { $del_posts .= ($del_posts ? "," : "").$del_post_id; $i++; }
	}
	if ($del_posts) {
		$result = dbquery("SELECT post_author, COUNT(post_id) as num_posts FROM ".DB_POSTS." WHERE post_id IN (".$del_posts.") GROUP BY post_author");
		if (dbrows($result)) {
			while ($pdata = dbarray($result)) {
				$result2 = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts-".$pdata['num_posts']." WHERE user_id='".$pdata['post_author']."'");
				$post_count = $post_count + $pdata['num_posts'];
			}
		}
		$result = dbquery("SELECT attach_name FROM ".DB_FORUM_ATTACHMENTS." WHERE post_id IN (".$del_posts.")");
		if (dbrows($result)) {
			while ($data = dbarray($result)) {
				unlink(FORUM."attachments/".$data['attach_name']);
			}
		}
		$result = dbquery("DELETE FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$_GET['thread_id']."' AND post_id IN(".$del_posts.")");
		$result = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' AND post_id IN(".$del_posts.")");
	}
	if (!dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'")) {
		$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLL_VOTERS." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLL_OPTIONS." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLLS." WHERE thread_id='".$_GET['thread_id']."'");
		$thread_count = false;
	} else {
		$result = dbquery("SELECT post_datestamp, post_author, post_id FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' ORDER BY post_datestamp DESC LIMIT 1");
		$ldata = dbarray($result);
		$result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpost='".$ldata['post_datestamp']."', thread_lastpostid='".$ldata['post_id']."', thread_postcount=thread_postcount-1, thread_lastuser='".$ldata['post_author']."' WHERE thread_id='".$_GET['thread_id']."'");
		$thread_count = true; unset($ldata);
	}
	$result = dbquery("SELECT post_datestamp, post_author FROM ".DB_POSTS." WHERE forum_id='".$fdata['forum_id']."' ORDER BY post_datestamp DESC LIMIT 1");
	if (dbrows($result)) {
		$ldata = dbarray($result);
		$forum_lastpost = "forum_lastpost='".$ldata['post_datestamp']."', forum_lastuser='".$ldata['post_author']."'";
	} else {
		$forum_lastpost = "forum_lastpost='0', forum_lastuser='0'";
	}
	$result = dbquery("UPDATE ".DB_FORUMS." SET ".$forum_lastpost.(!$thread_count ? "forum_threadcount=forum_threadcount-1," : ",")." forum_postcount=forum_postcount-".$post_count." WHERE forum_id = '".$fdata['forum_id']."'");
	if (!$thread_count) { redirect("viewforum.php?forum_id=".$fdata['forum_id']); }
}

if (isset($_GET['pid']) && isnum($_GET['pid'])) {
	$reply_count = dbcount("(post_id)", DB_POSTS, "thread_id='".$fdata['thread_id']."' AND post_id<='".$_GET['pid']."'");
	if ($reply_count > $posts_per_page) { $_GET['rowstart'] = ((ceil($reply_count / $posts_per_page)-1) * $posts_per_page); }
}

if (iMEMBER && isset($_POST['cast_vote']) && (isset($_POST['poll_option']) && isnum($_POST['poll_option']))) {
	$result = dbquery("SELECT * FROM ".DB_FORUM_POLL_VOTERS." WHERE forum_vote_user_id='".$userdata['user_id']."' AND thread_id='".$_GET['thread_id']."'");
	if (!dbrows($result)) {
		$result = dbquery("UPDATE ".DB_FORUM_POLL_OPTIONS." SET forum_poll_option_votes=forum_poll_option_votes+1 WHERE thread_id='".$_GET['thread_id']."' AND forum_poll_option_id='".$_POST['poll_option']."'");
		$result = dbquery("UPDATE ".DB_FORUM_POLLS." SET forum_poll_votes=forum_poll_votes+1 WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("INSERT INTO ".DB_FORUM_POLL_VOTERS." (thread_id, forum_vote_user_id, forum_vote_user_ip) VALUES ('".$_GET['thread_id']."', '".$userdata['user_id']."', '".USER_IP."')");
	}
	redirect(FUSION_SELF."?thread_id=".$_GET['thread_id']);
}

if (iMEMBER && $can_reply && !$fdata['thread_locked'] && isset($_POST['postquickreply'])) {
	$message = stripinput(censorwords($_POST['message']));
	if ($message != "") {
		require_once INCLUDES."flood_include.php";
		if (!flood_control("post_datestamp", DB_POSTS, "post_author='".$userdata['user_id']."'")) {
			$sig = ($userdata['user_sig'] ? '1' :'0');
			$smileys = isset($_POST['disable_smileys']) || preg_match("#\[code\](.*?)\[/code\]#si", $message) ? "0" : "1";
			$result = dbquery("INSERT INTO ".DB_POSTS." (forum_id, thread_id, post_message, post_showsig, post_smileys, post_author, post_datestamp, post_ip, post_edituser, post_edittime) VALUES ('".$fdata['forum_id']."', '".$_GET['thread_id']."', '$message', '$sig', '$smileys', '".$userdata['user_id']."', '".time()."', '".USER_IP."', '0', '0')");
			$newpost_id = mysql_insert_id();
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".time()."', forum_postcount=forum_postcount+1, forum_lastuser='".$userdata['user_id']."' WHERE forum_id='".$fdata['forum_id']."'");
			$result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpost='".time()."', thread_lastpostid='".$newpost_id."', thread_postcount=thread_postcount+1, thread_lastuser='".$userdata['user_id']."' WHERE thread_id='".$_GET['thread_id']."'");
			$result = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts+1 WHERE user_id='".$userdata['user_id']."'");
			$track = false;
			if ($_POST['track'] && !dbcount("(thread_id)", DB_THREAD_NOTIFY, "thread_id='".$_GET['thread_id']."' AND notify_user='".$userdata['user_id']."'")) {
				$result = dbquery("INSERT INTO ".DB_THREAD_NOTIFY." (thread_id, notify_datestamp, notify_user, notify_status) VALUES('".$_GET['thread_id']."', '".time()."', '".$userdata['user_id']."', '1')");
				echo $locale['452']."<br /><br />\n";
				$track = true;
			}
			redirect("postify.php?post=reply&error=0&forum_id=".$fdata['forum_id']."&thread_id=".$_GET['thread_id']."&post_id=$newpost_id".($track ? "&track=1" : ""));
		} else {
			redirect("viewthread.php?thread_id=".$_GET['thread_id']);
		}
	}
}

$rows = dbcount("(thread_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'");

$last_post = dbarray(dbquery("SELECT post_id FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' ORDER BY post_datestamp DESC LIMIT 1"));

if($fb4['layout_change'] && iMEMBER){
	$user_r = dbquery("SELECT * FROM ".DB_PREFIX."fb_users where user_id='".$userdata['user_id']."'");
	if(dbrows($user_r)){
		$user_d = dbarray($user_r);
		$layout = $user_d['user_layout'];
	} else {
		$user_r = dbquery("INSERT INTO ".DB_PREFIX."fb_users (user_id, user_layout, user_notes, user_warning, user_invisible, user_lv)
		VALUES('".$userdata['user_id']."', '".$fb4['forum_layout']."', '', '0', '0', '".time()."')");
		$layout = $fb4['forum_layout'];
	}
} else {
	$layout = $fb4['forum_layout'];
}

if($layout == "3"){
	include INFUSIONS."fusionboard4/includes/views/hybrid.php";
} elseif($layout == "2"){
	include INFUSIONS."fusionboard4/includes/views/vb.php";
} elseif($layout == "4"){
	include INFUSIONS."fusionboard4/includes/views/phpbb.php";
} else {
	include INFUSIONS."fusionboard4/includes/views/fusion.php";
}

if (iMOD) {
	echo "<table cellspacing='0' cellpadding='0' width='100%'>\n<tr>\n<td style='padding-top:5px'>";
	echo "<a href='#' onclick=\"javascript:setChecked('mod_form','delete_post[]',1);return false;\">".$locale['460']."</a> ::\n";
	echo "<a href='#' onclick=\"javascript:setChecked('mod_form','delete_post[]',0);return false;\">".$locale['461']."</a></td>\n";
	echo "<td align='right' style='padding-top:5px'><input type='submit' name='delete_posts' value='".$locale['517']."' class='button' onclick=\"return confirm('".$locale['518']."');\" /></td>\n";
	echo "</tr>\n</table>\n</form>\n";
}

if ($rows > $posts_per_page) {
	echo "<div align='center' style='padding-top:5px'>\n";
	echo makePageNav($_GET['rowstart'],$posts_per_page,$rows,3,FUSION_SELF."?thread_id=".$_GET['thread_id'].(isset($_GET['highlight']) ? "&amp;highlight=".urlencode($_GET['highlight']):"")."&amp;")."\n";
	echo "</div>\n";
}

function renderChildren($parent, $level=1){
	global $forum_parent,$fdata;
	$children = "";
	$p_res = dbquery("select * from ".DB_PREFIX."forums f
	left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
	where f2.forum_parent='$parent'");
	while($p_data = dbarray($p_res)){
		$children .= "<option value='".$p_data['forum_id']."'".($p_data['forum_id']==$fdata['forum_id'] ? " SELECTED" : "").">";
		for($i = $level; $i--; $i > 0){
			$children .= "--";
		}
		$children .= $p_data['forum_name']."</option>\n";
		$children .= renderChildren($p_data['forum_id'], ($level+1));
	}
	return $children;
}

$possibleParents = "";
$c_res = dbquery("select * from ".$db_prefix."forums where ".groupaccess('forum_access')." and forum_cat='0' ORDER BY forum_order ASC");
while($c_data = dbarray($c_res)){
	$possibleParents .= "<optgroup label='".$c_data['forum_name']."'>\n";
	$p_res = dbquery("select * from ".$db_prefix."forums f
	left join ".$db_prefix."fb_forums f2 on f2.forum_id=f.forum_id
	where f2.forum_parent='0' and f.forum_cat='".$c_data['forum_id']."'
	order by f.forum_order asc");
	while($p_data = dbarray($p_res)){
		$possibleParents .= "<option value='".$p_data['forum_id']."'".($p_data['forum_id']==$fdata['forum_id'] ? " SELECTED" : "").">".$p_data['forum_name']."</option>\n";
		$possibleParents .= renderChildren($p_data['forum_id']);
	}
	$possibleParents .= "</optgroup>\n";
}
if (iMOD) { echo "<form name='modopts' method='post' action='options.php?forum_id=".$fdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n"; }
echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
echo "<td style='padding-top:5px'>".$locale['540']."<br />\n";
echo "<select name='jump_id' class='textbox' onchange=\"jumpforum(this.options[this.selectedIndex].value);\">\n";
echo $possibleParents."</select></td>\n";

if (iMOD) {
	echo "<td align='right' style='padding-top:5px'>\n";
	echo $locale['520']."<br />\n<select name='step' class='textbox'>\n";
	echo "<option value='none'>&nbsp;</option>\n";
	echo "<option value='renew'>".$locale['527']."</option>\n";
	echo "<option value='delete'>".$locale['521']."</option>\n";
	echo "<option value='".($fdata['thread_locked'] ? "unlock" : "lock")."'>".($fdata['thread_locked'] ? $locale['523'] : $locale['522'])."</option>\n";
	echo "<option value='".($fdata['thread_sticky'] ? "nonsticky" : "sticky")."'>".($fdata['thread_sticky'] ? $locale['525'] : $locale['524'])."</option>\n";
	echo "<option value='move'>".$locale['526']."</option>\n";
	echo "<option value='split'>".$locale['fb215']."</option>\n";
	echo "<option value='merge'>".$locale['fb225']."</option>\n";
	echo "</select>\n<input type='submit' name='go' value='".$locale['528']."' class='button' />\n";
	echo "</td>\n";
}
echo "</tr>\n</table>\n"; if (iMOD) { echo "</form>\n"; }

if ($can_post || $can_reply) {
	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td align='right' style='padding-top:10px'>";
	if (!$fdata['thread_locked'] && $can_reply) {
		echo "<a href='post.php?action=reply&amp;forum_id=".$fdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'><img src='".get_image("reply")."' alt='".$locale['565']."' style='border:0px' /></a>\n";
	}
	if ($can_post) {
		echo "<a href='post.php?action=newthread&amp;forum_id=".$fdata['forum_id']."'><img src='".get_image("newthread")."' alt='".$locale['566']."' style='border:0px' /></a>\n";
	}
	echo "</td>\n</tr>\n</table>\n";
}
if((!iMEMBER && !$can_reply) || $fdata['thread_locked']){
	echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
}
closetable();
opentable($locale['fbs100']);
	echo "<table width='100%' class='tbl-border' cellspacing='1' cellpadding='0'>\n";
	echo "<tr>\n<td class='tbl2' colspan='2' align='center'>\n";
	?>
	<!-- ADDTHIS BUTTON BEGIN -->
	<script type="text/javascript">
	addthis_pub = 'YOUR-ACCOUNT-ID';
	</script><a href="http://www.addthis.com/bookmark.php" onMouseOver="return addthis_open(this, '', '[URL]', '[TITLE]')" onMouseOut="addthis_close()" onClick="return addthis_sendto()"><img src="http://s9.addthis.com/button1-bm.gif" width="125" height="16" border="0" alt="" /></a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>
	<!-- ADDTHIS BUTTON END -->
    <?php
	echo "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2' style='width:80px;'>".$locale['fbs101']."</td>\n";
	echo "<td class='tbl1'><input type='text' value='".$settings['siteurl']."forum/viewthread.php?thread_id=".$_GET['thread_id']."&amp;rowstart=".$_GET['rowstart']."' class='textbox' style='font-size:11px;width:100%' onClick='javascript:this.select();' /></td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2' style='width:80px;'>".$locale['fbs102']."</td>\n";
	echo "<td class='tbl1'><input type='text' value='[url=".$settings['siteurl']."forum/viewthread.php?thread_id=".$_GET['thread_id']."&amp;rowstart=".$_GET['rowstart']."]".$fdata['thread_subject']."[/url]' class='textbox' style='font-size:11px;width:100%' onClick='javascript:this.select();' /></td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2' style='width:80px;'>".$locale['fbs103']."</td>\n";
	echo "<td class='tbl1'><input type='text' value='<a href=\"".$settings['siteurl']."forum/viewthread.php?thread_id=".$_GET['thread_id']."&amp;rowstart=".$_GET['rowstart']."\">".$fdata['thread_subject']."</a>' class='textbox' style='font-size:11px;width:100%' onClick='javascript:this.select();' /></td>\n</tr>\n";
	echo "</table>\n";
closetable();

if (iMEMBER && $can_reply && !$fdata['thread_locked']) {
	require_once INCLUDES."bbcode_include.php";
	opentable($locale['512']);
	echo "<form name='inputform' method='post' action='".FUSION_SELF."?thread_id=".$_GET['thread_id']."'>\n";
	echo "<table cellpadding='0' cellspacing='1' class='tbl-border center'>\n<tr>\n";
	echo "<td align='center' class='tbl1'><textarea name='message' cols='70' rows='7' class='textbox' style='width:98%'></textarea><br />\n";
	echo display_bbcodes("360px", "message")."</td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td align='center' class='tbl2'><label><input type='checkbox' name='disable_smileys' value='1' />".$locale['513']."</label>
	<label><input type='checkbox' name='track' value='1' />".$locale['fb507']."</label></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td align='center' class='tbl1'><input type='submit' name='postquickreply' value='".$locale['514']."' class='button' /></td>\n";
	echo "</tr>\n</table>\n</form><!--sub_forum_thread-->\n";
	echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
	closetable();
}

echo "<script type='text/javascript'>function jumpforum(forum_id) {\n";
echo "document.location.href='".FORUM."viewforum.php?forum_id='+forum_id;\n";
echo "}\n"."function setChecked(frmName,chkName,val) {\n";
echo "dml=document.forms[frmName];\n"."len=dml.elements.length;\n"."for(i=0;i < len;i++) {\n";
echo "if(dml.elements[i].name == chkName) {\n"."dml.elements[i].checked = val;\n}\n}\n}\n";
echo "</script>\n";

list($postcount, $lastpid) = dbarraynum(dbquery("SELECT COUNT(post_id), MAX(post_id) FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' GROUP BY thread_id"));

if(isnum($postcount)){
	dbquery("UPDATE ".DB_THREADS." SET thread_postcount='$postcount', thread_lastpostid=$lastpid WHERE thread_id='".$_GET['thread_id']."'");
}
?>