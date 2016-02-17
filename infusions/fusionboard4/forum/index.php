<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");

/**
 * collapsible forums
 * 
 * @var	boolean
 */
$collapsible = false;

if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

if (!isset($lastvisited) || !isnum($lastvisited)) { $lastvisited = time(); }

add_to_title($locale['global_200'].$locale['400']);

if($fb4['show_latest']){
	include INFUSIONS."fb_threads_list_panel/fb_threads_list_panel.php";
} else {
	echo "<script src='".INFUSIONS."fusionboard4/includes/js/boxover.js' type='text/javascript'></script>\n";
}

opentable($locale['400']);

renderNav(false,false,array(FORUM."index.php", $locale['fb916']));

$forum_list = ""; $current_cat = "";
$result = dbquery(
	"SELECT f.*, f3.forum_icon, f4.forum_collapsed, f2.forum_name AS forum_cat_name, u.user_id, u.user_name
	FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat = f2.forum_id
	LEFT JOIN ".DB_USERS." u ON f.forum_lastuser = u.user_id
	LEFT JOIN ".DB_PREFIX."fb_forums f3 on f3.forum_id=f.forum_id
	LEFT JOIN ".DB_PREFIX."fb_forums f4 on f4.forum_id=f2.forum_id
	WHERE ".groupaccess('f.forum_access')." AND f.forum_cat!='0' AND f3.forum_parent='0' GROUP BY forum_id ORDER BY f2.forum_order ASC, f.forum_order ASC"
);
$rows = dbrows($result);
$i=0;
if (dbrows($result) != 0) {
	while ($data = dbarray($result)) {
		if ($data['forum_cat_name'] != $current_cat) {
			if($i > 0) echo "</table>".($collapsible ? "</div>" : "")."<br />\n";
			$current_cat = $data['forum_cat_name'];
			echo "<!--pre_forum_idx--><table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
			$state = ($data['forum_collapsed'] ? "off" : "on");
			$boxname = "forum".$data['forum_id'];
			if($fb4['forum_icons']){
				echo "<tr>\n<td colspan='6' class='forum-caption' style='padding:7px;'><div style='float:right'>".($collapsible ? panelbutton($state,$boxname) : "")."</div>\n
				<!--forum_cat_name_cell-->".$data['forum_cat_name']."</td>\n</tr>\n";
				echo ($collapsible ? "</table>".panelstate($state, $boxname)."<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n" : "");
				echo "<tr><td colspan='3' class='tbl2'>".$locale['401']."</td>\n";
			} else {
				echo "<tr>\n<td colspan='5' class='forum-caption' style='padding:7px;'><div style='float:right'>".($collapsible ? panelbutton($state,$boxname) : "")."</div>\n
				<!--forum_cat_name_cell-->".$data['forum_cat_name']."</td>\n</tr>\n";
				echo ($collapsible ? "</table>".panelstate($state, $boxname)."<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n" : "");
				echo "<tr><td colspan='2' class='tbl2'>".$locale['401']."</td>\n";
			}
			echo "<td class='tbl2' style='white-space:nowrap;width:33%;'>".$locale['404']."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['402']."</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$locale['403']."</td>\n";
			echo "</tr>\n";
			
		}
		$i++;
		$moderators = "";
		if ($data['forum_moderators']) {
			$mod_groups = explode(".", $data['forum_moderators']);
			foreach ($mod_groups as $mod_group) {
				if ($moderators) $moderators .= ", ";
				$moderators .= $mod_group<101 ? "<a href='".BASEDIR."profile.php?group_id=".$mod_group."'>".getgroupname($mod_group)."</a>" : getgroupname($mod_group);
			}
		}
		$forum_match = "\|".$data['forum_lastpost']."\|".$data['forum_id'];
		if ($data['forum_lastpost'] > $lastvisited) {
			if (iMEMBER && preg_match("({$forum_match}\.|{$forum_match}$)", $userdata['user_threads'])) {
				$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
			} else {
				$fim = "<img src='".get_image("foldernew")."' alt='".$locale['560']."' />";
			}
		} else {
			$fim = "<img src='".get_image("folder")."' alt='".$locale['561']."' />";
		}
		echo "<tr>\n";
		echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap' style='padding:7px;'>$fim</td>\n";
		if($data['forum_icon']){
			$ficon = ($data['forum_icon'] !== "" ? $data['forum_icon'] : "folder.png");
		} else {
			$ficon = "folder.png";
		}
		
		if($fb4['forum_icons']){
			$ficon = "<img src='".INFUSIONS."fusionboard4/images/forum_icons/$ficon' alt='' />";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap; padding:7px;'>$ficon</td>\n";
		}
		echo "<td class='tbl1' style='padding:7px;'><a href='viewforum.php?forum_id=".$data['forum_id']."' style='font-size:12px; text-decoration:underline; font-weight:bold;'>
		<!--forum_name_cell-->".$data['forum_name']."</a><br />\n";
		if ($data['forum_description'] || $moderators) {
			echo "<span class='small'>".$data['forum_description'].($data['forum_description'] && $moderators ? "<br />\n" : "");
			echo ($moderators ? "<strong>".$locale['411']."</strong>".$moderators."</span>\n" : "</span>\n")."\n";
		}
		$c_res = dbquery("select * from ".DB_PREFIX."forums f
		left join ".DB_PREFIX."fb_forums f2 on f2.forum_id=f.forum_id
		where ".groupaccess("f.forum_access")." AND f2.forum_parent='".$data['forum_id']."'");
		if(dbrows($c_res)){
			if($fb4['subforum_view']){
				echo "<br /><span class='small'><strong>".$locale['fb552']."</strong> ";
				$i = dbrows($c_res);
				while($c_data = dbarray($c_res)){
					$i--;
					echo "<a href='".FORUM."viewforum.php?forum_id=".$c_data['forum_id']."'>".$c_data['forum_name']."</a>";
					if($i > 0) echo ", ";
				}
				echo "</span>";
			} else {
				$counter = 0; $rows = 3;
				$sfimage1 = (file_exists(THEME."images/folder_open.png") ? THEME."images/folder_open.png" : INFUSIONS."fusionboard4/images/folder_open.png");
				$sfimage2 = (file_exists(THEME."images/subforum.png") ? THEME."images/subforum.png" : INFUSIONS."fusionboard4/images/subforum.png");
				echo "<br /><img src='$sfimage1' alt='' style='vertical-align:middle;'> <span class='small' style='font-weight:bold;'>".$locale['fb552']."</span><br />";
				echo "<table cellspacing='0' cellpadding='0' border='0' width='100%' style='padding-left:6px;'><tr>
				<td width='50%' class='small' style='padding-left:3px; padding-right:3px; vertical-align:top;'>";
				while($c_data = dbarray($c_res)){
					if ($counter != 0 && ($counter % $rows == 0)) { echo "</td><td width='50%' class='small' style='padding-left:3px; padding-right:3px; vertical-align:top;'>\n"; }
					echo "<img src='$sfimage2' alt='' style='vertical-align:middle;'> 
					<a href='".FORUM."viewforum.php?forum_id=".$c_data['forum_id']."' class='small'>".$c_data['forum_name']."</a><br />";
					$counter++;
				}
				echo "</td></tr></table>";
			}
		}
		echo "</td>\n";
		echo "<td class='tbl2' style='white-space:nowrap; padding:5px;' width='1%' nowrap='nowrap'>";
		$posts = $data['forum_postcount']; $threads = $data['forum_threadcount'];
		$children = array();
		if(dbrows($c_res)){
			while($child_data = dbarray($c_res)){
				array_push($children, $child_data['forum_id']);
				findChildren($child_data['forum_id']);
			}
		}
		if(count($children)){
			$where = ""; $counter = count($children); $normalWhere = "";
			foreach($children as $child){
				$where .= "t.forum_id='$child' ".($counter > 1 ? "OR " : "");
				$normalWhere .= "forum_id='$child' ".($counter > 1 ? "OR " : "");
				$counter--;
			}
			$posts = $posts + dbcount("(post_id)", DB_POSTS, $normalWhere);
			$threads = $threads + dbcount("(thread_id)", DB_THREADS, $normalWhere);
			$childrenForums = dbquery("select * from ".$db_prefix."threads t
			left join ".$db_prefix."users u on u.user_id=t.thread_lastuser
			left join ".$db_prefix."posts p on p.post_id=t.thread_lastpostid
			where ($where OR t.forum_id='".$data['forum_id']."') 
			order by t.thread_lastpost desc limit 1");
			$childrenData = dbarray($childrenForums);
			if (!dbrows($childrenForums) && !$data['forum_lastpost']) {
				echo $locale['405']."</td>\n";
			} else {
				echo "<b><a href='viewthread.php?thread_id=".$childrenData['thread_id']."' style='text-decoration:underline;'>".trimlink($childrenData['thread_subject'], 30)."</a></b><br />";
				echo "".$locale['406']."<a href='".BASEDIR."profile.php?lookup=".$childrenData['thread_lastuser']."'>".showLabel($childrenData['user_id'], false, "index")."</a><br />
				<div align='right'>".timePassed($childrenData['thread_lastpost'], false)."
				<a href='".FORUM."viewthread.php?thread_id=".$childrenData['thread_id']."&amp;pid=".$childrenData['thread_lastpostid']."#post_".$childrenData['thread_lastpostid']."' title='Go To Last Post'><b>&raquo;</b></a></div></td>\n";
			}
		} else {
			if ($data['forum_lastpost'] == 0) {
				echo $locale['405']."</td>\n";
			} else {
				$threadData = dbarray(dbquery("select * from ".$db_prefix."threads t
				left join ".$db_prefix."posts p on p.post_id=t.thread_lastpostid
				left join ".DB_USERS." u on u.user_id=p.post_author
				where t.thread_lastpost='".$data['forum_lastpost']."'"));
				echo "<b><a href='viewthread.php?thread_id=".$threadData['thread_id']."' style='text-decoration:underline;'>".trimlink($threadData['thread_subject'], 30)."</a></b><br />";
				echo "".$locale['406']."<a href='".BASEDIR."profile.php?lookup=".$data['forum_lastuser']."' style='text-decoration:underline;'>".showLabel($threadData['user_id'], false, "index")."</a><br />
				<div align='right'>".timePassed($data['forum_lastpost'], false)."
				<a href='".FORUM."viewthread.php?thread_id=".$threadData['thread_id']."&amp;pid=".$threadData['thread_lastpostid']."#post_".$threadData['thread_lastpostid']."' title='Go To Last Post'><b>&raquo;</b></a></div></td>\n";
			}
		}
		echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>".$threads."</td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$posts."</td>\n";
		echo "</tr>\n";
	}
} else {
	echo "<tr>\n<td colspan='5' class='tbl1'>".$locale['407']."</td>\n</tr>\n";
}
echo "</table></div><!--sub_forum_idx_table-->\n<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
echo "<td class='forum'><br />\n";
echo "<img src='".get_image("foldernew")."' alt='".$locale['560']."' style='vertical-align:middle;' /> - ".$locale['409']."<br />\n";
echo "<img src='".get_image("folder")."' alt='".$locale['561']."' style='vertical-align:middle;' /> - ".$locale['410']."\n";
echo "</td></tr>\n<tr>\n";
echo "<td align='right' valign='bottom' class='forum'>\n";
echo "<form name='searchform' method='get' action='".BASEDIR."search.php?stype=forums'>\n";
echo "<input type='text' name='stext' class='textbox' style='width:150px' />\n";
echo "<input type='submit' name='search' value='".$locale['550']."' class='button' />\n";
echo "</form>\n</td>\n</tr>\n</table><!--sub_forum_idx-->\n";

	$newest = dbarray(dbquery("select * from ".DB_USERS." order by user_id desc limit 1"));
	
	$search = array(
						"{POSTS}",
						"{THREADS}",
						"{USERS}",
						"{NEW}",
						"{ONLINE}",
						"{COUNT}",
						"{DATE}",
						"{RATINGS}"
					);
	$replace = array(
						"<b>".number_format(dbrows(dbquery("SELECT * FROM ".DB_POSTS)))."</b>",
						"<b>".number_format(dbrows(dbquery("SELECT * FROM ".DB_THREADS)))."</b>",
						"<b>".number_format(dbrows(dbquery("SELECT * FROM ".DB_USERS)))."</b>",
						"<b><a href='".BASEDIR."profile.php?lookup=".$newest['user_id']."'>".$newest['user_name']."</a></b>",
						"<b>".number_format(dbrows(dbquery("SELECT * FROM ".DB_PREFIX."online")))."</b>",
						"<b>".number_format($fb4['stat_moau'])."</b>",
						"<b>".strftime("%B %d %Y, %I:%M %p", $fb4['stat_moau_date'])."</b>",
						"<b>".number_format(dbrows(dbquery("SELECT * FROM ".DB_PREFIX."fb_rate")))."</b>"
					 );

	$result = dbquery(
	"SELECT ton.*, tu.user_id,user_name,user_level FROM ".DB_ONLINE." ton
	LEFT JOIN ".DB_USERS." tu ON ton.online_user=tu.user_id"
	);
	$guests = 0; $members = array();
	while ($data = dbarray($result)) {
		if ($data['online_user'] !== "0") {
			array_push($members, array($data['user_id'], $data['user_name'], $data['user_level']));
		}
	}
	
	$state="on"; $boxname="statistics";
	
	echo "<br />
	<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
	<tr>
		<td colspan='2' class='forum-caption' style='padding:7px;'><div style='float:right'>".panelbutton($state,$boxname)."</div>\n".$locale['fb554']."</td>
	</tr>
	</table>
	".panelstate($state, $boxname)."
	<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
	<tr>
		<td colspan='2' class='tbl2' style='font-weight:bold'>".str_replace($search,$replace,$locale['fb559'])."</td>
	</tr>
	<tr>\n";
		
		$levels = dbquery("select * from ".DB_PREFIX."fb_labels 
		where (label_group > 100 and label_group < 104) or (label_group=0 and label_user=0) order by label_group desc");
		$groups = dbquery("select * from ".DB_PREFIX."fb_labels l
		left join ".DB_USER_GROUPS." g on g.group_id=l.label_group
		where l.label_user='0' and (l.label_group != '0' and l.label_group != '101' and l.label_group != '102' 
									and l.label_group !='103') order by g.group_name asc");
		$counter = dbrows($levels)+dbrows($groups); $labels = "";
		if(dbrows($levels)){
			while($level_data = dbarray($levels)){
				$labels .= "<span style='".$level_data['label_style']."'>".getgroupname($level_data['label_group'])."</span>".($counter > 1 ? ", " : "");
				$counter--;
			}
		}
		if(dbrows($groups)){
			while($group_data = dbarray($groups)){
				$labels .= "<span style='".$group_data['label_style']."'>".$group_data['group_name']."</span>".($counter > 1 ? ", " : "");
				$counter--;
			}
		}
		echo "<td class='tbl2' width='32' rowspan='".($labels ? 3 : 2)."'><img src='".INFUSIONS."fusionboard4/images/users.png' alt=''></td>
		<td class='tbl1' style='padding:6px;'>";
		if($labels) echo $labels."</td></tr><tr><td class='tbl2' style='padding:6px;'>";
		
		if (count($members)) {
			$i = 1;
			while (list($key, $member) = each($members)) {
				echo "<a href='".BASEDIR."profile.php?lookup=".$member[0]."'>".showLabel($member[0])."</a>";
				if ($i != count($members)) { echo ",\n"; }
				$i++;
			}
		}
		$guests = dbquery("select * from ".DB_ONLINE." where online_user='0'"); $i = dbrows($guests);
		if (dbrows($guests) && $fb4['stat_guests']){
			if(count($members)) echo ", ";
			while($guest = dbarray($guests)){
				$ip = explode(".", $guest['online_ip']);
				$label_res = dbquery("select * from ".DB_PREFIX."fb_labels where label_user='0' and label_group='0' limit 1");
				if(dbrows($label_res)){
					$label_data = dbarray($label_res);
					echo "<span style='".stripslash($label_data['label_style'])."'>".(iADMIN ? $guest['online_ip'] : ($ip[0].".".$ip[1].".xxx.xxx"))."</span>";
				} else {
					echo (iADMIN ? $guest['online_ip'] : ($ip[0].".".$ip[1].".xxx.xxx"));
				}
				if($i > 1) echo ", ";
				$i--;
			}
		}
		echo "</td></tr>\n";
		echo "<tr><td class='tbl1' style='padding:6px;'>\n";
		if($fb4['stat_visitor']){
			echo $locale['fb560']."<br />\n";
			$todayOnline = explode(".", $fb4['stat_today_users']);
			$i = (count($todayOnline)-1);
			foreach($todayOnline as $user){
				if($user){
					echo "<a href='".BASEDIR."profile.php?lookup=$user'>".showLabel($user)."</a>";
					if($i > 1) echo ", ";
					$i--;
				}
			}
			echo "<br /><br />";
		}
		echo $locale['fb566'];
		$topPosters = dbquery("select * from ".DB_USERS." where user_posts>0 order by user_posts desc limit 5");
		$i = 0;
		while($topPoster = dbarray($topPosters)){
			if($i > 0) echo " | ";
			echo "<a href='".BASEDIR."profile.php?lookup=".$topPoster['user_id']."'>".showLabel($topPoster['user_id'])."</a> (".number_format($topPoster['user_posts']).")";
			$i++;
		}
		echo "</td>
	</tr>\n";
	if($fb4['stat_bday'] > 0){
		echo "<tr>
			<td colspan='2' class='tbl2' style='font-weight:bold'>".($fb4['stat_bday'] == "1" ? $locale['fb562'] : $locale['fb564'].strftime("%B"))."</td>
		</tr>
		<tr>
			<td class='tbl2' width='32'><img src='".INFUSIONS."fusionboard4/images/birthday.png' alt=''>
			</td>
			<td class='tbl1' style='padding:6px;'>";
			if($fb4['stat_bday'] == "1"){
				$today = strftime("%m-%d", time());
				$result = dbquery("select * from ".DB_USERS." where user_birthdate like '%$today'");
			} else {
				$today = strftime("%m", time());
				$result = dbquery("select * from ".DB_USERS." where user_birthdate like '%-$today-%'");
			}
			if(dbrows($result)){
				$i = dbrows($result);
				while($data = dbarray($result)){
					echo "<a href='".BASEDIR."profile.php?lookup=".$data['user_id']."'>".showLabel($data['user_id'])."</a> [";
					$birthday = explode("-", $data['user_birthdate']);
					$thisYear = strftime("%Y", time());
					echo $thisYear-$birthday[0];
					echo "]".($i > 1 ? ", " : "");
					$i--;
				}
			} else {
				echo ($fb4['stat_bday'] == "1" ? $locale['fb563'] : $locale['fb565']);
			}
			echo "</td>
		</tr>\n";
	}
	echo "<tr>
		<td colspan='2' class='tbl2' style='font-weight:bold'>".$locale['fb558']."</td>
	</tr>
	<tr>
		<td class='tbl2' width='32'><img src='".INFUSIONS."fusionboard4/images/stats.png' alt=''></td>
		<td class='tbl1' style='padding:6px;'>".str_replace($search,$replace,$locale['fb555'])."<br />\n";
		if($fb4['show_ratings']) echo str_replace($search,$replace,$locale['fb569'])."<br />\n";
		echo str_replace($search,$replace,$locale['fb556'])."<br />
		".str_replace($search,$replace,$locale['fb557'])."<br />
		".str_replace($search,$replace,$locale['fb561'])."</td>
	</tr>
	</table></div>\n";

echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";

closetable();
?>