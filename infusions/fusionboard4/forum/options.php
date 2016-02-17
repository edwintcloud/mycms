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

if (iMEMBER) {
	if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php"); }
	$data = dbarray(dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'"));
	if (!checkgroup($data['forum_access'])) { redirect("index.php"); }
	if (iSUPERADMIN) { define("iMOD", true); }
	if (!defined("iMOD") && iMEMBER && $data['forum_moderators']) {
		$mod_groups = explode(".", $data['forum_moderators']);
		foreach ($mod_groups as $mod_group) {
			if (!defined("iMOD") && checkgroup($mod_group)) { define("iMOD", true); }
		}
	}
	if (!defined("iMOD")) { define("iMOD", false); }
} else {
	define("iMOD", false);
}

if (isset($_POST['step']) && $_POST['step'] != "") { $_GET['step'] = $_POST['step']; }

if ((!iMOD && !iADMIN) || !checkgroup($data['forum_post'])) { redirect("index.php"); }

if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }

if (isset($_POST['canceldelete'])) { redirect("viewthread.php?forum_id=".$_GET['forum_id']."&thread_id=".$_GET['thread_id']); }

if (isset($_GET['step']) && $_GET['step'] == "renew") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_lastpost='".time()."' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['458']);
	echo "<div style='text-align:center'><br />\n".$locale['459']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "delete") {
	opentable($locale['400']);
	echo "<div style='text-align:center'><br />\n";
	if (!isset($_POST['deletethread'])) {
		echo "<form name='delform' method='post' action='".FUSION_SELF."?step=delete&amp;forum_id=".$_GET['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n";
		echo $locale['404']."<br /><br />\n";
		echo "<input type='submit' name='deletethread' value='".$locale['405']."' class='button' style='width:75px'>\n";
		echo "<input type='submit' name='canceldelete' value='".$locale['406']."' class='button' style='width:75px'><br /><br />\n";
		echo "</form>\n";
	} else {
		$result = dbquery("SELECT post_author, COUNT(post_id) as num_posts FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' GROUP BY post_author");
		if (dbrows($result)) {
			while ($pdata = dbarray($result)) {
				$result2 = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts-".$pdata['num_posts']." WHERE user_id='".$pdata['post_author']."'");
			}
		}
		
		$tdata = dbarray(dbquery("SELECT thread_id,thread_lastpost,thread_lastuser FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'"));

		$threads_count = dbcount("(forum_id)", DB_THREADS, "forum_id='".$_GET['forum_id']."'") - 1;
		$result = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."'");
		$del_posts = mysql_affected_rows();
		$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("SELECT * FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$_GET['thread_id']."'");
		if (dbrows($result) != 0) {
			while ($attach = dbarray($result)) {
				unlink(FORUM."attachments/".$attach['attach_name']);
			}
		}
		$result = dbquery("DELETE FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id='".$_GET['thread_id']."'");
		
		if ($threads_count > 0) {
			$result = dbquery("SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."' AND forum_lastpost='".$tdata['thread_lastpost']."' AND forum_lastuser='".$tdata['thread_lastuser']."'");
			if (dbrows($result)) {
				$result = dbquery("SELECT forum_id,post_author,post_datestamp FROM ".DB_POSTS." WHERE forum_id='".$_GET['forum_id']."' ORDER BY post_datestamp DESC LIMIT 1");
				$pdata = dbarray($result);
				$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata['post_datestamp']."', forum_postcount=forum_postcount-".$del_posts.", forum_threadcount=forum_threadcount-1, forum_lastuser='".$pdata['post_author']."' WHERE forum_id='".$_GET['forum_id']."'");
			}
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=0, forum_threadcount=0, forum_lastuser='0' WHERE forum_id='".$_GET['forum_id']."'");
		}
		echo $locale['401']."<br /><br />\n";
		echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n";
	}
	echo "</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "lock") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_locked='1' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['410']);
	echo "<div style='text-align:center'><br />\n".$locale['411']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "unlock") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_locked='0' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['420']);
	echo "<div style='text-align:center'><br />\n".$locale['421']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "sticky") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_sticky='1' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['430']);
	echo "<div style='text-align:center'><br />\n".$locale['431']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "nonsticky") {
	$result = dbquery("UPDATE ".DB_THREADS." SET thread_sticky='0' WHERE thread_id='".$_GET['thread_id']."'");
	opentable($locale['440']);
	echo "<div style='text-align:center'><br />".$locale['441']."<br /><br />\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a><br /><br />\n";
	echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	closetable();
} elseif (isset($_GET['step']) && $_GET['step'] == "move") {
	opentable($locale['450']);
	if (isset($_POST['move_thread'])) {
		if (!isset($_POST['new_forum_id']) || !isnum($_POST['new_forum_id'])) { redirect("index.php"); }
		
		if (!dbcount("(forum_id)", DB_FORUMS, "forum_id='".$_POST['new_forum_id']."'")) { redirect("../index.php"); }
		
		$result = dbquery("UPDATE ".DB_THREADS." SET forum_id='".$_POST['new_forum_id']."' WHERE thread_id='".$_GET['thread_id']."'");
		$result = dbquery("UPDATE ".DB_POSTS." SET forum_id='".$_POST['new_forum_id']."' WHERE thread_id='".$_GET['thread_id']."'");
		
		$post_count = dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'");
		
		$result = dbquery("SELECT thread_lastpost, thread_lastuser FROM ".DB_THREADS." WHERE forum_id='".$_GET['forum_id']."' ORDER BY thread_lastpost DESC LIMIT 0,1");
		if (dbrows($result)) {
			$pdata2 = dbarray($result);
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata2['thread_lastpost']."', forum_postcount=forum_postcount-".$post_count.", forum_threadcount=forum_threadcount-1, forum_lastuser='".$pdata2['thread_lastuser']."' WHERE forum_id='".$_GET['forum_id']."'");
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=forum_postcount-".$post_count.", forum_threadcount=forum_threadcount-1, forum_lastuser='0' WHERE forum_id='".$_GET['forum_id']."'");
		}

		$result = dbquery("SELECT thread_lastpost, thread_lastuser FROM ".DB_THREADS." WHERE forum_id='".$_POST['new_forum_id']."' ORDER BY thread_lastpost DESC LIMIT 0,1");
		if (dbrows($result)) {
			$pdata2 = dbarray($result);
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='".$pdata2['thread_lastpost']."', forum_postcount=forum_postcount+".$post_count.", forum_threadcount=forum_threadcount+1, forum_lastuser='".$pdata2['thread_lastuser']."' WHERE forum_id='".$_POST['new_forum_id']."'");
		} else {
			$result = dbquery("UPDATE ".DB_FORUMS." SET forum_lastpost='0', forum_postcount=forum_postcount+1, forum_threadcount=forum_threadcount+".$post_count.", forum_lastuser='0' WHERE forum_id='".$_POST['new_forum_id']."'");
		}
		
		echo "<div style='text-align:center'><br />\n".$locale['452']."<br /><br />\n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br />\n</div>\n";
	} else {
		$sel = "";
		function renderChildren($parent, $level=1){
			global $forum_parent;
			$children = "";
			$p_res = dbquery("select * from ".DB_PREFIX."forums f
			left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
			where f2.forum_parent='$parent'");
			while($p_data = dbarray($p_res)){
				$children .= "<option value='".$p_data['forum_id']."'>";
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
			where f2.forum_parent='0' and f.forum_cat='".$c_data['forum_id']."' ORDER BY forum_order");
			while($p_data = dbarray($p_res)){
				$possibleParents .= "<option value='".$p_data['forum_id']."'>".$p_data['forum_name']."</option>\n";
				$possibleParents .= renderChildren($p_data['forum_id']);
			}
			$possibleParents .= "</optgroup>\n";
		}
		echo "<form name='moveform' method='post' action='".FUSION_SELF."?step=move&forum_id=".$_GET['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border'>\n<tr>\n";
		echo "<td class='tbl2' width='150'>".$locale['451']."</td>\n";
		echo "<td class='tbl1'><select name='new_forum_id' class='textbox' style='width:250px;'>\n".$possibleParents."</select></td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td colspan='2' class='tbl2' style='text-align:center;'><input type='submit' name='move_thread' value='".$locale['450']."' class='button' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
	}
	closetable();
} elseif(isset($_GET['step']) && $_GET['step'] == "split"){
	if(isset($_POST['split_thread'])){
	
		$toMove = ""; $i = 0; $firstPost = "";
		foreach($_POST as $post=>$move){
			if(!ereg("move_", $post) || !$move) continue;
			$toMove .= ($i > 0 ? " OR " : "")."post_id='".str_replace('move_', '', $post)."'";
			if(!$i) $firstPost = dbarray(dbquery("select * from ".DB_POSTS." where post_id='".str_replace('move_', '', $post)."'"));
			$i++;
		}
		
		$subject = (isset($_POST['thread_subject']) ? addslash(stripinput($_POST['thread_subject'])) : "");
		$forum = (isset($_POST['thread_forum']) && isNum($_POST['thread_forum']) ? $_POST['thread_forum'] : $_GET['forum_id']);
		
		$result = dbquery("INSERT INTO ".DB_THREADS." (forum_id, thread_subject, thread_author, thread_views, thread_lastpost, thread_lastpostid, thread_lastuser, thread_postcount, thread_poll, thread_sticky, thread_locked) 
		VALUES('$forum', '$subject', '".$firstPost['post_author']."', '0', '".time()."', '0', '".$firstPost['post_author']."', '1', '0', '0', '0')");
		$threadid = mysql_insert_id();
		
		$result = dbquery("update ".DB_POSTS." set thread_id='$threadid', forum_id='$forum' where $toMove");
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='$threadid' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "thread_id='$threadid'");
		$result = dbquery("update ".DB_THREADS." set thread_lastpost='".$lastPost['post_datestamp']."', 
													 thread_lastpostid='".$lastPost['post_id']."',
													 thread_lastuser='".$lastPost['post_author']."',
													 thread_postcount='$postCount'
						   where thread_id='$threadid'");
						   
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where forum_id='$forum' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "forum_id='$forum'");
		$result = dbquery("update ".DB_FORUMS." set forum_lastpost='".$lastPost['post_datestamp']."',
													forum_threadcount=forum_threadcount+1,
													forum_lastuser='".$lastPost['post_author']."',
													forum_postcount='$postCount'
						   where forum_id='$forum'");
		
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='".$_GET['thread_id']."' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'");
		$result = dbquery("update ".DB_THREADS." set thread_lastpost='".$lastPost['post_datestamp']."', 
													 thread_lastpostid='".$lastPost['post_id']."',
													 thread_lastuser='".$lastPost['post_author']."',
													 thread_postcount='$postCount'
						   where thread_id='".$_GET['thread_id']."'");						 
		
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where forum_id='".$_GET['forum_id']."' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "forum_id='".$_GET['forum_id']."'");
		$result = dbquery("update ".DB_FORUMS." set forum_lastpost='".$lastPost['post_datestamp']."',
													forum_threadcount=forum_threadcount+1,
													forum_lastuser='".$lastPost['post_author']."',
													forum_postcount='$postCount'
						   where forum_id='".$_GET['forum_id']."'");
		
		opentable($locale['fb215']);
		
		echo "<div style='text-align:center'><br />".$locale['fb222']."<br />\n";
		echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."'>".$locale['fb223']."</a> :: \n";
		echo "<a href='viewthread.php?thread_id=$threadid'>".$locale['fb224']."</a> :: \n";
		echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a> :: \n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	
	} else {
	
		function renderChildren($parent, $level=1){
			global $forum_parent;
			$children = "";
			$p_res = dbquery("select * from ".DB_PREFIX."forums f
			left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
			where f2.forum_parent='$parent'");
			while($p_data = dbarray($p_res)){
				$sel = ($p_data['forum_id'] == $_GET['forum_id'] ? " SELECTED" : "");
				$children .= "<option value='".$p_data['forum_id']."'$sel>";
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
			where f2.forum_parent='0' and f.forum_cat='".$c_data['forum_id']."' ORDER BY forum_order");
			while($p_data = dbarray($p_res)){
				$sel = ($p_data['forum_id'] == $_GET['forum_id'] ? " SELECTED" : "");
				$possibleParents .= "<option value='".$p_data['forum_id']."'$sel>".$p_data['forum_name']."</option>\n";
				$possibleParents .= renderChildren($p_data['forum_id']);
			}
			$possibleParents .= "</optgroup>\n";
		}
		
		opentable($locale['fb215']);
		echo "<form action='".FUSION_SELF."?step=split&thread_id=".$_GET['thread_id']."&forum_id=".$_GET['forum_id']."' method='post' name='splitform'>\n";
		echo "<table width='350' cellspacing='1' cellpadding='0' class='tbl-border center'>\n";
		echo "<tr>\n<td class='tbl2'>".$locale['fb216']."</td>\n<td class='tbl1' width='1%'>";
		echo "<input name='thread_subject' class='textbox' type='textbox' style='width:220px;'></td>\n</tr>\n";
		echo "<tr>\n<td class='tbl2'>".$locale['fb217']."</td>\n<td class='tbl1'>";
		echo "<select name='thread_forum' class='textbox' style='width:220px;'>\n$possibleParents</select></td>\n</tr>\n";
		echo "</table>\n";
		closetable();
		tablebreak();
		opentable($locale['fb218']);
		$result = dbquery("select * from ".DB_POSTS." p
		left join ".DB_USERS." u on u.user_id=p.post_author
		where p.thread_id='".$_GET['thread_id']."'");
		$i = 0;
		while($data = dbarray($result)){
			$i++;
			if($i < 2) continue;
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
			echo "<tr>\n<td class='tbl2'>".$locale['fb219']."<b>".showdate("forumdate", $data['post_datestamp'])."</b>".$locale['fb220'];
			echo "<b>".$data['user_name']."</b></td><td class='tbl2' style='width:80px;white-space:nowrap;'>\n";
			echo "<input type='checkbox' name='move_".$data['post_id']."' value='1' /> ".$locale['fb221']."</td>\n</tr>\n";
			echo "<tr>\n<td class='tbl1' colspan='2'>".nl2br(trimlink($data['post_message'], 120))."</td>\n</tr>\n";
			echo "</table><br />\n";
		}
		echo "<div align='center'><input type='submit' name='split_thread' value='".$locale['fb215']."' class='button'></div>\n</form>\n";
	}
	closetable();
} elseif(isset($_GET['step']) && $_GET['step'] == "merge"){
	if(isset($_POST['merge_thread'])){
	
		$toMove = ""; $i = 0;
		foreach($_POST as $post=>$move){
			if(!ereg("move_", $post) || !$move) continue;
			$toMove .= ($i > 0 ? " OR " : "")."post_id='".str_replace('move_', '', $post)."'";
			$i++;
		}
		
		$merge_id = (isset($_POST['merge_id']) && isNum($_POST['merge_id']) ? $_POST['merge_id'] : 0);
		$result = dbquery("select * from ".DB_THREADS." where thread_id='$merge_id'");
		if(!dbrows($result)) redirect(FUSION_SELF."?step=merge&thread_id=".$_GET['thread_id']."&forum_id=".$_GET['forum_id']."&status=notfound");
		$data = dbarray($result); 
		
		$threadid = $data['thread_id'];
		
		$result = dbquery("update ".DB_POSTS." set thread_id='$threadid', forum_id='".$data['forum_id']."' where $toMove");
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='$threadid' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "thread_id='$threadid'");
		$result = dbquery("update ".DB_THREADS." set thread_lastpost='".$lastPost['post_datestamp']."', 
													 thread_lastpostid='".$lastPost['post_id']."',
													 thread_lastuser='".$lastPost['post_author']."',
													 thread_postcount='$postCount'
						   where thread_id='$threadid'");
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where forum_id='".$data['forum_id']."' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "forum_id='".$data['forum_id']."'");
		$result = dbquery("update ".DB_FORUMS." set forum_lastpost='".$lastPost['post_datestamp']."',
													forum_lastuser='".$lastPost['post_author']."',
													forum_postcount='$postCount'
						   where forum_id='".$data['forum_id']."'");
		
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where thread_id='".$_GET['thread_id']."' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'");
		$result = dbquery("update ".DB_THREADS." set thread_lastpost='".$lastPost['post_datestamp']."', 
													 thread_lastpostid='".$lastPost['post_id']."',
													 thread_lastuser='".$lastPost['post_author']."',
													 thread_postcount='$postCount'
						   where thread_id='".$_GET['thread_id']."'");		
		if(!dbcount("(post_id)", DB_POSTS, "thread_id='".$_GET['thread_id']."'")){ 
			$result = dbquery("delete from ".DB_THREADS." where thread_id='".$_GET['thread_id']."'"); 
			$result = dbquery("delete from ".DB_THREAD_NOTIFY." where thread_id='".$_GET['thread_id']."'"); 
			$delete=true; 
		} else { $delete=false; }
		$lastPost = dbarray(dbquery("select * from ".DB_POSTS." where forum_id='".$_GET['forum_id']."' order by post_id desc limit 1"));
		$postCount = dbcount("(post_id)", DB_POSTS, "forum_id='".$_GET['forum_id']."'");
		$result = dbquery("update ".DB_FORUMS." set forum_lastpost='".$lastPost['post_datestamp']."',
													forum_lastuser='".$lastPost['post_author']."',
													forum_postcount='$postCount'
						   where forum_id='".$_GET['forum_id']."'");				 
		
		opentable($locale['fb225']);
		
		echo "<div style='text-align:center'><br />".$locale['fb231']."<br />\n";
		if(!$delete) echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."'>".$locale['fb232']."</a> :: \n";
		echo "<a href='viewthread.php?thread_id=$threadid'>".$locale['fb233']."</a> :: \n";
		echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['402']."</a> :: \n";
		echo "<a href='index.php'>".$locale['403']."</a><br /><br /></div>\n";
	
	} else {
		
		opentable($locale['fb225']);
		if(isset($_GET['status']) && $_GET['status'] == "notfound") echo "<div align='center'>".$locale['fb234']."</div><br />\n";
		echo "<form action='".FUSION_SELF."?step=merge&thread_id=".$_GET['thread_id']."&forum_id=".$_GET['forum_id']."' method='post' name='splitform'>\n";
		echo "<table width='350' cellspacing='1' cellpadding='0' class='tbl-border center'>\n";
		echo "<tr>\n<td class='tbl2'>".$locale['fb226']."</td>\n<td class='tbl1' width='1%'>";
		echo "<input name='merge_id' class='textbox' type='textbox' style='width:100px;'></td>\n</tr>\n";
		echo "</table>\n";
		closetable();
		tablebreak();
		opentable($locale['fb218']);
		$result = dbquery("select * from ".DB_POSTS." p
		left join ".DB_USERS." u on u.user_id=p.post_author
		where p.thread_id='".$_GET['thread_id']."'");
		while($data = dbarray($result)){
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
			echo "<tr>\n<td class='tbl2'>".$locale['fb219']."<b>".showdate("forumdate", $data['post_datestamp'])."</b>".$locale['fb220'];
			echo "<b>".$data['user_name']."</b></td><td class='tbl2' style='width:80px;white-space:nowrap;'>\n";
			echo "<input type='checkbox' name='move_".$data['post_id']."' value='1' /> ".$locale['fb221']."</td>\n</tr>\n";
			echo "<tr>\n<td class='tbl1' colspan='2'>".nl2br(trimlink($data['post_message'], 120))."</td>\n</tr>\n";
			echo "</table><br />\n";
		}
		echo "<div align='center'><input type='submit' name='merge_thread' value='".$locale['fb225']."' class='button'></div>\n</form>\n";
	}
	closetable();
} else {
	redirect("index.php");
}

?>