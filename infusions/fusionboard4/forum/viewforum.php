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

if (!isset($lastvisited) || !isnum($lastvisited)) { $lastvisited = time(); }

if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php"); }

if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

$threads_per_page = $fb4['threads_per_page'];
$posts_per_page = $fb4['posts_per_page'];

add_to_title($locale['global_200'].$locale['400']);

$result = dbquery(
	"SELECT f.*, f3.*, f2.forum_name AS forum_cat_name FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat=f2.forum_id
	LEFT JOIN ".DB_PREFIX."fb_forums f3 on f3.forum_id=f.forum_id
	WHERE f.forum_id='".$_GET['forum_id']."'"
);
if (dbrows($result)) {
	$fdata = dbarray($result);
	if (!checkgroup($fdata['forum_access']) || !$fdata['forum_cat']) { redirect("index.php"); }
} else {
	redirect("index.php");
}

if ($fdata['forum_post']) {
	$can_post = checkgroup($fdata['forum_post']);
} else {
	$can_post = false;
}

if (iSUPERADMIN) { define("iMOD", true); }

if (!defined("iMOD") && iMEMBER && $fdata['forum_moderators']) {
	$mod_groups = explode(".", $fdata['forum_moderators']);
	foreach ($mod_groups as $mod_group) {
		if (!defined("iMOD") && checkgroup($mod_group)) { define("iMOD", true); }
	}
}

if (!defined("iMOD")) { define("iMOD", false); }

add_to_title($locale['global_201'].$fdata['forum_name']);

if (isset($_POST['delete_threads']) && iMOD) {
	$thread_ids = "";
	if (isset($_POST['check_mark']) && is_array($_POST['check_mark'])) {
		foreach ($_POST['check_mark'] as $thisnum) {
			if (isnum($thisnum)) { $thread_ids .= ($thread_ids ? "," : "").$thisnum; }
		}
	}
	if ($thread_ids) {
		$result = dbquery("SELECT post_author, COUNT(post_id) as num_posts FROM ".DB_POSTS." WHERE thread_id IN (".$thread_ids.") GROUP BY post_author");
		if (dbrows($result)) {
			while ($pdata = dbarray($result)) {
				$result2 = dbquery("UPDATE ".DB_USERS." SET user_posts=user_posts-".$pdata['num_posts']." WHERE user_id='".$pdata['post_author']."'");
			}
		}
		$result = dbquery("SELECT attach_name FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id IN (".$thread_ids.")");
		if (dbrows($result)) {
			while ($data = dbarray($result)) {
				unlink(FORUM."attachments/".$data['attach_name']);
			}
		}
		$result = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id IN (".$thread_ids.")");
		$deleted_posts = mysql_affected_rows();
		$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id IN (".$thread_ids.")");
		$deleted_threads = mysql_affected_rows();
		$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id IN (".$thread_ids.")");
		$result = dbquery("DELETE FROM ".DB_FORUM_ATTACHMENTS." WHERE thread_id IN (".$thread_ids.")");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLL_OPTIONS." WHERE thread_id IN (".$thread_ids.")");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLL_VOTERS." WHERE thread_id IN (".$thread_ids.")");
		$result = dbquery("DELETE FROM ".DB_FORUM_POLLS." WHERE thread_id IN (".$thread_ids.")");
		$result = dbquery("SELECT post_datestamp, post_author FROM ".DB_POSTS." WHERE forum_id='".$_GET['forum_id']."' ORDER BY post_datestamp DESC LIMIT 1");
		if (dbrows($result)) {
			$ldata = dbarray($result);
			$forum_lastpost = "forum_lastpost='".$ldata['post_datestamp']."', forum_lastuser='".$ldata['post_author']."'";
		} else {
			$forum_lastpost = "forum_lastpost='0', forum_lastuser='0'";
		}
		$result = dbquery("UPDATE ".DB_FORUMS." SET ".$forum_lastpost.", forum_postcount=forum_postcount-".$deleted_posts.", forum_threadcount=forum_threadcount-".$deleted_threads." WHERE forum_id='".$_GET['forum_id']."'");
	}
	$rows_left = dbcount("(thread_id)", "threads", "forum_id='".$_GET['forum_id']."'") - 3;
	if ($rows_left <= $_GET['rowstart'] && $_GET['rowstart'] > 0) {
		$_GET['rowstart'] = ((ceil($rows_left / $threads_per_page)-1) * $threads_per_page);
	}
	redirect(FUSION_SELF."?forum_id=".$_GET['forum_id']."&rowstart=".$_GET['rowstart']);
}

/* fusionboard4 mod start */
if (isset($_POST['lock_threads']) && iMOD){
	$thread_ids = "";
	if (isset($_POST['check_mark']) && is_array($_POST['check_mark'])) {
		foreach ($_POST['check_mark'] as $thisnum) {
			if (isnum($thisnum)) { $thread_ids .= ($thread_ids ? "," : "").$thisnum; }
		}
	}
	if ($thread_ids) {
		$result = dbquery("UPDATE ".DB_THREADS." SET thread_locked='1' WHERE thread_id IN (".$thread_ids.")");
	}
	redirect(FUSION_SELF."?forum_id=".$_GET['forum_id']."&rowstart=".$_GET['rowstart']);
}
/* fusionboard4 mod end */

$rows = dbcount("(thread_id)", DB_THREADS, "forum_id='".$_GET['forum_id']."' AND thread_sticky='0'");

opentable($locale['450']);
echo "<!--pre_forum-->\n";

renderNav(true);

renderSubforums($_GET['forum_id']);

if ($rows > $threads_per_page || (iMEMBER && $can_post)) {
	if(isset($_POST['goSearch'])){
		$order_by = (isset($_POST['order_by']) ? stripinput($_POST['order_by']) : "");
		$sort_by = (isset($_POST['sort_by']) && $_POST['sort_by'] == "desc" ? "desc" : "asc");
		$timelimit = (isset($_POST['time']) && isNum($_POST['time']) ? $_POST['time'] : "");
		$get = "&amp;order_by=$order_by&amp;sort_by=$sort_by&amp;time=$timelimit";
	} elseif(isset($_GET['order_by'])){
		$order_by = (isset($_GET['order_by']) ? stripinput($_GET['order_by']) : "");
		$sort_by = (isset($_GET['sort_by']) && $_GET['sort_by'] == "desc" ? "desc" : "asc");
		$timelimit = (isset($_GET['time']) && isNum($_GET['time']) ? $_GET['time'] : "");
		$get = "&amp;order_by=$order_by&amp;sort_by=$sort_by&amp;time=$timelimit";
	} else {
		$get = "";
	}
	echo "<table cellspacing='0' cellpadding='0' width='100%'>\n<tr>\n";
	if ($rows > $threads_per_page) { echo "<td style='padding:4px 0px 4px 0px'>".makePageNav($_GET['rowstart'],$threads_per_page,$rows,3,FUSION_SELF."?forum_id=".$_GET['forum_id'].$get."&amp;")."</td>\n"; }
	if (iMEMBER && $can_post) { echo "<td align='right' style='padding:4px 0px 4px 0px'><a href='post.php?action=newthread&amp;forum_id=".$_GET['forum_id']."'><img src='".get_image("newthread")."' alt='".$locale['566']."' style='border:0px;' /></a></td>\n"; }
	echo "</tr>\n</table>\n";
} else {
	echo "<br /><br />\n";
}

if (iMOD) { echo "<form name='mod_form' method='post' action='".FUSION_SELF."?forum_id=".$_GET['forum_id']."&amp;rowstart=".$_GET['rowstart']."'>\n"; }
$columns = 5;
echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n<tr>\n";
if ($fb4['post_icons']){
echo "<td width='1%' class='tbl2' style='white-space:nowrap'>&nbsp;</td>\n"; $columns++;
}
echo "<td width='1%' class='tbl2' style='white-space:nowrap'>&nbsp;</td>\n";
echo "<td class='tbl2' width='80%'>".$locale['451']."</td>\n";
echo "<td width='20%' class='tbl2' style='white-space:nowrap'>".$locale['404']."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['453']."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['454']."</td>\n";
if (iMOD) { echo "<td width='1%' class='tbl2' style='white-space:nowrap'>&nbsp;</td>\n"; $columns++; }
echo "</tr>\n";

$announcements = dbquery(
		"SELECT t.*, tu1.user_name AS user_author, tu2.user_name AS user_lastuser FROM ".DB_THREADS." t
		LEFT JOIN ".DB_USERS." tu1 ON t.thread_author = tu1.user_id
		LEFT JOIN ".DB_USERS." tu2 ON t.thread_lastuser = tu2.user_id
		LEFT JOIN ".DB_PREFIX."fb_threads t2 on t2.thread_id=t.thread_id
		LEFT JOIN ".DB_FORUMS." f on f.forum_id=t.thread_id
		WHERE ".groupaccess("f.forum_access")." and t2.thread_announcement='1' ORDER BY thread_lastpost DESC"
	);
if(dbrows($announcements)){

	echo "<tr>
		<td class='tbl2' colspan='$columns'><b>".$locale['fb900']."</b></td>
	</tr>";
	
	while($announcement = dbarray($announcements)){
	
			$thread_match = $announcement['thread_id']."\|".$announcement['thread_lastpost']."\|".$fdata['forum_id'];
			echo "<tr>\n";
			if ($announcement['thread_locked']) {
				echo "<td align='center' width='25' class='tbl2'><img src='".get_image("folderlock")."' alt='".$locale['564']."' /></td>";
				$titlebold = "";
			} else  {
				if ($announcement['thread_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match("(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata['user_threads'])) {
						$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
						$titlebold = "";
					} else {
						$folder = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
						$titlebold = " font-weight:bold;";
					}
				} else {
					$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
					$titlebold = "";
				}
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$folder</td>";
			}
			$threadPost = dbarray(dbquery("select * from ".DB_THREADS." t
			left join ".DB_POSTS." p on p.thread_id=t.thread_id
			where t.thread_id='".$announcement['thread_id']."' order by p.post_id asc limit 1"));
			$post_res = dbquery("select * from ".DB_PREFIX."fb_posts where post_id='".$threadPost['post_id']."'");
			if(dbrows($post_res)){
				$post_data = dbarray($post_res);
				if($post_data['post_icon']){
					$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/".$post_data['post_icon']."' alt=''>";
				} else {
					$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
				}
			} else {
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
			}
			if ($fb4['post_icons']){
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$ficon</td>";
			}
			$reps = ceil($announcement['thread_postcount'] / $posts_per_page);
			$threadsubject = "<span style='font-size:12px;'>".$locale['fb902']."<a href='viewthread.php?thread_id=".$announcement['thread_id']."' style='text-decoration:underline;$titlebold'>".$announcement['thread_subject']."</a></span>";
			if ($reps > 1) {
				$ctr = 0; $ctr2 = 1; $pages = "";
				while ($ctr2 <= $reps) {
					$pnum = "<a href='viewthread.php?thread_id=".$announcement['thread_id']."&amp;rowstart=$ctr'>$ctr2</a> ";
					$pages = $pages.$pnum; $ctr = $ctr + $posts_per_page; $ctr2++;
				}
				$threadsubject .= "&nbsp;<span class='small'>(".$locale['455'].trim($pages).")</span>";
			}
			echo "<td class='tbl1'>";
			echo $threadsubject;
			echo "<br />
			<span class='small'><a href='../profile.php?lookup=".$announcement['thread_author']."'>".showLabel($announcement['thread_author'], false, "index")."</a></span></td>\n";
			echo "<td class='tbl1' style='white-space:nowrap; text-align:right;'>
			".timePassed($announcement['thread_lastpost'], false)."<br />\n";
			echo "<span class='small'>".$locale['406']."<a href='../profile.php?lookup=".$announcement['thread_lastuser']."' style='text-decoration:underline;'>".showLabel($announcement['thread_lastuser'], false, "index")."</a></span></td>\n";
			echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$announcement['thread_views']."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".($announcement['thread_postcount']-1)."</td>\n";
			if (iMOD) { echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><input type='checkbox' name='check_mark[]' value='".$announcement['thread_id']."' /></td>\n"; }
			echo "</tr>\n";
	
	}
	
	echo "<tr>
		<td class='tbl2' colspan='$columns'><b>".$locale['fb901']."</b></td>
	</tr>";

}

if ($_GET['rowstart'] == 0) {
	$result = dbquery(
		"SELECT t.*, tu1.user_name AS user_author, tu2.user_name AS user_lastuser FROM ".DB_THREADS." t
		LEFT JOIN ".DB_USERS." tu1 ON t.thread_author = tu1.user_id
		LEFT JOIN ".DB_USERS." tu2 ON t.thread_lastuser = tu2.user_id
		WHERE t.forum_id='".$_GET['forum_id']."' AND thread_sticky='1' ORDER BY thread_lastpost DESC"
	);
	if (dbrows($result)) {
		while ($tdata = dbarray($result)) {
			$thread_match = $tdata['thread_id']."\|".$tdata['thread_lastpost']."\|".$fdata['forum_id'];
			echo "<tr>\n";
			if ($tdata['thread_locked']) {
				echo "<td align='center' width='25' class='tbl2'><img src='".get_image("folderlock")."' alt='".$locale['564']."' /></td>";
				$titlebold = "";
			} else  {
				if ($tdata['thread_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match("(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata['user_threads'])) {
						$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
						$titlebold = "";
					} else {
						$folder = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
						$titlebold = " font-weight:bold;";
					}
				} else {
					$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
					$titlebold = "";
				}
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$folder</td>";
			}
			$threadPost = dbarray(dbquery("select * from ".DB_THREADS." t
			left join ".DB_POSTS." p on p.thread_id=t.thread_id
			where t.thread_id='".$tdata['thread_id']."' order by p.post_id asc limit 1"));
			$post_res = dbquery("select * from ".DB_PREFIX."fb_posts where post_id='".$threadPost['post_id']."'");
			if(dbrows($post_res)){
				$post_data = dbarray($post_res);
				if($post_data['post_icon']){
					$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/".$post_data['post_icon']."' alt=''>";
				} else {
					$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
				}
			} else {
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
			}
			if ($fb4['post_icons']){
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$ficon</td>";
			}
			$reps = ceil($tdata['thread_postcount'] / $posts_per_page);
			$threadsubject = "<span style='font-size:12px;'><a href='viewthread.php?thread_id=".$tdata['thread_id']."' style='text-decoration:underline;$titlebold'>".$tdata['thread_subject']."</a></span>";
			if ($reps > 1) {
				$ctr = 0; $ctr2 = 1; $pages = "";
				while ($ctr2 <= $reps) {
					$pnum = "<a href='viewthread.php?thread_id=".$tdata['thread_id']."&amp;rowstart=$ctr'>$ctr2</a> ";
					$pages = $pages.$pnum; $ctr = $ctr + $posts_per_page; $ctr2++;
				}
				$threadsubject .= "&nbsp;<span class='small'>(".$locale['455'].trim($pages).")</span>";
			}
			echo "<td class='tbl1'><img src='".THEME."forum/stickythread.gif' alt='Sticky' style='vertical-align:middle;'>&nbsp;";
			echo $threadsubject;
			echo "<br />
			<span class='small'><a href='../profile.php?lookup=".$tdata['thread_author']."'>".showLabel($tdata['thread_author'], false, "index")."</a></span></td>\n";
			echo "<td class='tbl1' style='white-space:nowrap; text-align:right;'>
			".timePassed($tdata['thread_lastpost'], false)."<br />\n";
			echo "<span class='small'>".$locale['406']."<a href='../profile.php?lookup=".$tdata['thread_lastuser']."' style='text-decoration:underline;'>".showLabel($tdata['thread_lastuser'], false, "index")."</a></span></td>\n";
			echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$tdata['thread_views']."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".($tdata['thread_postcount']-1)."</td>\n";
			if (iMOD) { echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><input type='checkbox' name='check_mark[]' value='".$tdata['thread_id']."' /></td>\n"; }
			echo "</tr>\n";
		}
		$threadcount = dbrows($result);
	} else {
		$threadcount = 0;
	}
}

if ($rows) {
	if(isset($_POST['goSearch'])){
		$order_by = (isset($_POST['order_by']) ? stripinput($_POST['order_by']) : "");
		$sort_by = (isset($_POST['sort_by']) && $_POST['sort_by'] == "desc" ? "desc" : "asc");
		$timelimit = (isset($_POST['time']) && isNum($_POST['time']) ? ($_POST['time'] == "100" ? floor(time()/(24*3600)) : $_POST['time']) : "");
		$sort = "AND fp.post_datestamp>".(time()-($timelimit*24*3600));
		$result = dbquery(
			"SELECT t.*, count(fp.post_id) as thread_replies, fp.post_datestamp, tu1.user_name AS user_author, tu2.user_name AS user_lastuser FROM ".DB_THREADS." t
			LEFT JOIN ".DB_USERS." tu1 ON t.thread_author = tu1.user_id
			LEFT JOIN ".DB_USERS." tu2 ON t.thread_lastuser = tu2.user_id
			LEFT JOIN ".DB_POSTS." fp ON fp.thread_id=t.thread_id
			WHERE t.forum_id='".$_GET['forum_id']."' AND thread_sticky='0' $sort GROUP BY fp.thread_id 
			ORDER BY $order_by $sort_by, fp.post_id asc 
			LIMIT ".$_GET['rowstart'].",$threads_per_page"
		);
	} elseif(isset($_GET['order_by'])){
		$order_by = (isset($_GET['order_by']) ? stripinput($_GET['order_by']) : "");
		$sort_by = (isset($_GET['sort_by']) && $_GET['sort_by'] == "desc" ? "desc" : "asc");
		$timelimit = (isset($_GET['time']) && isNum($_GET['time']) ? ($_GET['time'] == "100" ? floor(time()/(24*3600)) : $_GET['time']) : "");
		$sort = "AND fp.post_datestamp>".(time()-($timelimit*24*3600));
		$result = dbquery(
			"SELECT t.*, count(fp.post_id) as thread_replies, fp.post_datestamp, tu1.user_name AS user_author, tu2.user_name AS user_lastuser FROM ".DB_THREADS." t
			LEFT JOIN ".DB_USERS." tu1 ON t.thread_author = tu1.user_id
			LEFT JOIN ".DB_USERS." tu2 ON t.thread_lastuser = tu2.user_id
			LEFT JOIN ".DB_POSTS." fp ON fp.thread_id=t.thread_id
			WHERE t.forum_id='".$_GET['forum_id']."' AND thread_sticky='0' $sort GROUP BY fp.thread_id 
			ORDER BY $order_by $sort_by, fp.post_id asc 
			LIMIT ".$_GET['rowstart'].",$threads_per_page"
		);
	} else {
		$sort = "ORDER BY thread_lastpost DESC";
		$result = dbquery(
			"SELECT t.*, tu1.user_name AS user_author, tu2.user_name AS user_lastuser FROM ".DB_THREADS." t
			LEFT JOIN ".DB_USERS." tu1 ON t.thread_author = tu1.user_id
			LEFT JOIN ".DB_USERS." tu2 ON t.thread_lastuser = tu2.user_id
			WHERE t.forum_id='".$_GET['forum_id']."' AND thread_sticky='0' $sort LIMIT ".$_GET['rowstart'].",$threads_per_page"
		);
	}
	$numrows = dbrows($result);
	$counter = 0;
	while ($tdata = dbarray($result)) {
		$thread_match = $tdata['thread_id']."\|".$tdata['thread_lastpost']."\|".$fdata['forum_id'];
		echo "<tr>\n";
		if ($tdata['thread_locked']) {
			echo "<td align='center' width='25' class='tbl2'><img src='".get_image("folderlock")."' alt='".$locale['564']."' /></td>";
			$titlebold = "";
		} else  {
			if ($tdata['thread_lastpost'] > $lastvisited) {
				if (iMEMBER && preg_match("(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata['user_threads'])) {
					$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
					$titlebold = "";
				} else {
					$folder = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
					$titlebold = " font-weight:bold;";
				}
			} else {
				$folder = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
				$titlebold = "";
			}
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$folder</td>";
		}
		$threadPost = dbarray(dbquery("select * from ".DB_THREADS." t
		left join ".DB_POSTS." p on p.thread_id=t.thread_id
		where t.thread_id='".$tdata['thread_id']."' order by p.post_id asc limit 1"));
		$post_res = dbquery("select * from ".DB_PREFIX."fb_posts where post_id='".$threadPost['post_id']."'");
		if(dbrows($post_res)){
			$post_data = dbarray($post_res);
			if($post_data['post_icon']){
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/".$post_data['post_icon']."' alt=''>";
			} else {
				$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
			}
		} else {
			$ficon = "<img src='".INFUSIONS."fusionboard4/images/post_icons/page_white.png'>";
		}
		if ($fb4['post_icons']){
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>$ficon</td>";
		}
		$reps = ceil($tdata['thread_postcount'] / $posts_per_page);
		$threadsubject = "<span style='font-size:12px;'><a href='viewthread.php?thread_id=".$tdata['thread_id']."' style='text-decoration:underline;$titlebold'>".$tdata['thread_subject']."</a></span>";
		if ($reps > 1) {
			$ctr = 0; $ctr2 = 1; $pages = "";
			while ($ctr2 <= $reps) {
				$pnum = "<a href='viewthread.php?thread_id=".$tdata['thread_id']."&amp;rowstart=$ctr'>$ctr2</a> ";
				$pages = $pages.$pnum; $ctr = $ctr + $posts_per_page; $ctr2++;
			}
			$threadsubject .= "&nbsp;<span class='small'>(".$locale['455'].trim($pages).")</span>";
		}
		echo "<td class='tbl1'>";
		echo $threadsubject;
		echo "<br />
		<span class='small'><a href='../profile.php?lookup=".$tdata['thread_author']."'>".showLabel($tdata['thread_author'], false, "index")."</a></span></td>\n";
		echo "<td class='tbl1' style='white-space:nowrap; text-align:right;'>
		".timePassed($tdata['thread_lastpost'], false)."<br />\n";
		echo "<span class='small'>".$locale['406']."<a href='../profile.php?lookup=".$tdata['thread_lastuser']."'>".showLabel($tdata['thread_lastuser'], false, "index")."</a></span></td>\n";
		echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$tdata['thread_views']."</td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".($tdata['thread_postcount']-1)."</td>\n";
		if (iMOD) { echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><input type='checkbox' name='check_mark[]' value='".$tdata['thread_id']."' /></td>\n"; }
		echo "</tr>\n";
		$counter++;
	}
	$totalThreads = dbcount("(thread_id)", DB_THREADS, "forum_id='".$_GET['forum_id']."' and thread_sticky='0'");
	if(isset($_POST['order_by'])){
		$_POST['order_by'] = $_POST['order_by'];
	} elseif(isset($_GET['order_by'])){
		$_POST['order_by'] = $_GET['order_by'];
	} else {
		$_POST['order_by'] = "";
	}
	if(isset($_POST['sort_by'])){
		$_POST['sort_by'] = $_POST['sort_by'];
	} elseif(isset($_GET['sort_by'])){
		$_POST['sort_by'] = $_GET['sort_by'];
	} else {
		$_POST['sort_by'] = "";
	}
	if(isset($_POST['time'])){
		$_POST['time'] = $_POST['time'];
	} elseif(isset($_GET['time'])){
		$_POST['time'] = $_GET['time'];
	} else {
		$_POST['time'] = "";
	}
	$orderByList = "<select name='order_by' class='textbox'>
		<option value='thread_lastpost'".($_POST['order_by'] == "thread_lastpost" ? " SELECTED" : "").">".$locale['fb940']."</option>
		<option value='thread_subject'".($_POST['order_by'] == "thread_subject" ? " SELECTED" : "").">".$locale['fb941']."</option>
		<option value='user_author'".($_POST['order_by'] == "user_author" ? " SELECTED" : "").">".$locale['fb942']."</option>
		<option value='thread_replies'".($_POST['order_by'] == "thread_replies" ? " SELECTED" : "").">".$locale['fb943']."</option>
		<option value='thread_views'".($_POST['order_by'] == "thread_views" ? " SELECTED" : "").">".$locale['fb944']."</option>
		<option value='post_datestamp'".($_POST['order_by'] == "thread_lastpost" ? " SELECTED" : "").">".$locale['fb945']."</option>
		<option value='user_lastuser'".($_POST['order_by'] == "user_lastuser" ? " SELECTED" : "").">".$locale['fb946']."</option>
	</select>\n";
	$sortByList = "<select name='sort_by' class='textbox'>
		<option value='desc'".($_POST['sort_by'] == "desc" ? " SELECTED" : "").">".$locale['fb950']."</option>
		<option value='asc'".($_POST['sort_by'] == "asc" ? " SELECTED" : "").">".$locale['fb951']."</option>
	</select>\n";
	$timeList = "<select name='time' class='textbox'>
		<option value='1'".($_POST['time'] == "1" ? " SELECTED" : "").">".$locale['fb960']."</option>
		<option value='5'".($_POST['time'] == "5" ? " SELECTED" : "").">".$locale['fb961']."</option>
		<option value='7'".($_POST['time'] == "7" ? " SELECTED" : "").">".$locale['fb962']."</option>
		<option value='10'".($_POST['time'] == "10" ? " SELECTED" : "").">".$locale['fb963']."</option>
		<option value='15'".($_POST['time'] == "15" ? " SELECTED" : "").">".$locale['fb964']."</option>
		<option value='20'".($_POST['time'] == "20" ? " SELECTED" : "").">".$locale['fb965']."</option>
		<option value='25'".($_POST['time'] == "25" ? " SELECTED" : "").">".$locale['fb966']."</option>
		<option value='30'".($_POST['time'] == "30" ? " SELECTED" : "").">".$locale['fb967']."</option>
		<option value='60'".($_POST['time'] == "60" ? " SELECTED" : "").">".$locale['fb968']."</option>
		<option value='90'".($_POST['time'] == "90" ? " SELECTED" : "").">".$locale['fb969']."</option>
		<option value='100'".($_POST['time'] == "100" ? " SELECTED" : "").">".$locale['fb970']."</option>
	</select>\n";
	echo "<tr>
		<td colspan='$columns' class='forum-caption' style='padding:7px;text-align:center;'>
		<form action='".FUSION_SELF."?forum_id=".$_GET['forum_id']."' method='post' name='sortForm'>
		".$locale['fb930'].$counter.$locale['fb931'].$totalThreads.$locale['fb932'].$orderByList.$locale['fb933'].$sortByList.$locale['fb934'].$timeList."&nbsp;
		<input type='submit' name='goSearch' value='".$locale['fb935']."' class='button'>
		</form>
		</td>
	</tr>\n";
	echo "</table><!--sub_forum_table-->\n";
} else {
	if (!$threadcount) {
		echo "<tr>\n<td colspan='7' class='tbl1' style='text-align:center'>".$locale['456']."</td>\n</tr>\n</table><!--sub_forum_table-->\n";
	} else {
		echo "</table><!--sub_forum_table-->\n";
	}
}

if (iMOD || ($fb4['forum_notify'] && checkgroup($fb4['fn_access']))) {
	echo "<table cellspacing='0' cellpadding='0' width='100%'>\n<tr>\n<td style='padding-top:5px'>";
	if ((isset($threadcount) || $rows) && iMOD) {
		echo "<a href='#' onclick=\"javascript:setChecked('mod_form','check_mark[]',1);return false;\">".$locale['460']."</a> ::\n";
		echo "<a href='#' onclick=\"javascript:setChecked('mod_form','check_mark[]',0);return false;\">".$locale['461']."</a>";
		if(($fb4['forum_notify'] && checkgroup($fb4['fn_access']))) echo " :: \n";
	}
	if(($fb4['forum_notify'] && checkgroup($fb4['fn_access']))){
		if (dbcount("(forum_id)", DB_PREFIX."fb_forum_notify", "forum_id='".$_GET['forum_id']."' AND notify_user='".$userdata['user_id']."'")) {
			$result2 = dbquery("UPDATE ".DB_PREFIX."fb_forum_notify SET notify_datestamp='".time()."', notify_status='1' WHERE forum_id='".$_GET['forum_id']."' AND notify_user='".$userdata['user_id']."'");
			echo "<a href='postify.php?post=none&forum=off&amp;forum_id=".$fdata['forum_id']."'>".$locale['fb443']."</a>";
		} else {
			echo "<a href='postify.php?post=none&forum=on&amp;forum_id=".$fdata['forum_id']."'>".$locale['fb442']."</a>";
		}
	}
	echo "</td>\n";
	echo "<td align='right' style='padding-top:5px'><input type='submit' name='delete_threads' value='".$locale['462']."' class='button' onclick=\"return confirm('".$locale['463']."');\" />&nbsp;&nbsp;<input type='submit' name='lock_threads' value='".$locale['fb550']."' class='button' onclick=\"return confirm('".$locale['fb551']."');\" /></td>\n";
	echo "</tr>\n</table>\n";
	if(iMOD) echo "</form>\n";
	if ((isset($threadcount) || $rows) && iMOD) {
		echo "<script type='text/javascript'>\n"."function setChecked(frmName,chkName,val) {\n";
		echo "dml=document.forms[frmName];\n"."len=dml.elements.length;\n"."for(i=0;i < len;i++) {\n";
		echo "if(dml.elements[i].name == chkName) {\n"."dml.elements[i].checked = val;\n}\n}\n}\n";
		echo "</script>\n";
	}
}

if ($rows > $threads_per_page || (iMEMBER && $can_post)) {
	echo "<table cellspacing='0' cellpadding='0' width='100%'>\n<tr>\n";
	if ($rows > $threads_per_page) { echo "<td style='padding-top:5px'>".makePageNav($_GET['rowstart'],$threads_per_page,$rows,3,FUSION_SELF."?forum_id=".$_GET['forum_id']."&amp;")."</td>\n"; }
	if (iMEMBER && $can_post) { echo "<td align='right' style='padding-top:5px'><a href='post.php?action=newthread&amp;forum_id=".$_GET['forum_id']."'><img src='".get_image("newthread")."' alt='".$locale['566']."' style='border:0px;' /></a></td>\n"; }
	echo "</tr>\n</table>\n";
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
echo "<div style='padding-top:5px'>\n".$locale['540']."<br />\n";
echo "<select name='jump_id' class='textbox' onchange=\"jumpforum(this.options[this.selectedIndex].value);\">";
echo $possibleParents."</select>\n</div>\n";

echo "<div><hr />\n";
echo "<img src='".get_image("foldernew")."' alt='".$locale['560']."' style='vertical-align:middle;' /> - ".$locale['470']."<br />\n";
echo "<img src='".get_image("folder")."' alt='".$locale['561']."' style='vertical-align:middle;' /> - ".$locale['472']."<br />\n";
echo "<img src='".get_image("folderlock")."' alt='".$locale['564']."' style='vertical-align:middle;' /> - ".$locale['473']."<br />\n";
echo "<img src='".get_image("stickythread")."' alt='".$locale['563']."' style='vertical-align:middle;' /> - ".$locale['474']."\n";
echo "</div><!--sub_forum-->\n";
echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
closetable();

echo "<script type='text/javascript'>\n"."function jumpforum(forumid) {\n";
echo "document.location.href='".FORUM."viewforum.php?forum_id='+forumid;\n}\n";
echo "</script>\n";

list($threadcount, $postcount) = dbarraynum(dbquery("SELECT COUNT(thread_id), SUM(thread_postcount) FROM ".DB_THREADS." WHERE forum_id='".$_GET['forum_id']."'"));
if(isnum($threadcount) && isnum($postcount)){
	dbquery("UPDATE ".DB_FORUMS." SET forum_postcount='$postcount', forum_threadcount='$threadcount' WHERE forum_id='".$_GET['forum_id']."'");
}

?>