<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if (! defined ( "IN_FUSION" ))
	die ( "Access Denied" );

if (dbrows ( dbquery ( "select * from " . $db_prefix . "fb_settings" ) )) {
	$fb4 = dbarray ( dbquery ( "select * from " . $db_prefix . "fb_settings" ) );
} else {
	$fb4 = array ("fboard_on" => "0" );
}

if (! function_exists ( "renderSubforums" )) {
	
	function renderSubforums($forum) {
		global $locale, $userdata, $db_prefix, $lastvisited, $fb4;
		
		$forum_list = "";
		$current_cat = "";
		$result = dbquery ( "SELECT f.*, f2.forum_name AS forum_cat_name, u.user_id, u.user_name
		FROM " . DB_FORUMS . " f
		LEFT JOIN " . DB_FORUMS . " f2 ON f.forum_cat = f2.forum_id
		LEFT JOIN " . DB_USERS . " u ON f.forum_lastuser = u.user_id
		LEFT JOIN " . DB_PREFIX . "fb_forums f3 on f3.forum_id=f.forum_id
		WHERE " . groupaccess ( 'f.forum_access' ) . " AND f3.forum_parent='" . $forum . "' GROUP BY forum_id ORDER BY f2.forum_order ASC, f.forum_order ASC" );
		if (dbrows ( $result ) != 0) {
			$state = "off";
			$boxname = "subforums";
			echo "<!--pre_forum_idx--><table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
			if ($fb4 ['forum_icons']) {
				echo "<tr>\n<td colspan='6' class='forum-caption' style='padding:7px;'><div style='float:right'>" . panelbutton ( $state, $boxname ) . "</div>\n
			<!--forum_cat_name_cell-->" . $locale ['fb553'] . "</td>\n</tr>";
				echo "</table>" . panelstate ( $state, $boxname ) . "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
				echo "<tr>\n<td colspan='3' class='tbl2' width='65%'>" . $locale ['401'] . "</td>\n";
			} else {
				echo "<tr>\n<td colspan='6' class='forum-caption' style='padding:7px;'><div style='float:right'>" . panelbutton ( $state, $boxname ) . "</div>\n
			<!--forum_cat_name_cell-->" . $locale ['fb553'] . "</td>\n</tr>";
				echo "</table>" . panelstate ( $state, $boxname ) . "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
				echo "<tr>\n<td colspan='2' class='tbl2' width='65%'>" . $locale ['401'] . "</td>\n";
			}
			echo "<td class='tbl2' width='35%' style='white-space:nowrap'>" . $locale ['404'] . "</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>" . $locale ['402'] . "</td>\n";
			echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>" . $locale ['403'] . "</td>\n";
			echo "</tr>\n";
			while ( $data = dbarray ( $result ) ) {
				$moderators = "";
				if ($data ['forum_moderators']) {
					$mod_groups = explode ( ".", $data ['forum_moderators'] );
					foreach ( $mod_groups as $mod_group ) {
						if ($moderators)
							$moderators .= ", ";
						$moderators .= $mod_group < 101 ? "<a href='" . BASEDIR . "profile.php?group_id=" . $mod_group . "'>" . getgroupname ( $mod_group ) . "</a>" : getgroupname ( $mod_group );
					}
				}
				$forum_match = "\|" . $data ['forum_lastpost'] . "\|" . $data ['forum_id'];
				if ($data ['forum_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match ( "({$forum_match}\.|{$forum_match}$)", $userdata ['user_threads'] )) {
						$fim = "<img src='" . get_image ( "folder" ) . "' alt='" . $locale ['561'] . "' />";
					} else {
						$fim = "<img src='" . get_image ( "foldernew" ) . "' alt='" . $locale ['560'] . "' />";
					}
				} else {
					$fim = "<img src='" . get_image ( "folder" ) . "' alt='" . $locale ['561'] . "' />";
				}
				echo "<tr>\n";
				echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap' style='padding:7px;'>$fim</td>\n";
				$iconQuery = dbquery ( "select * from " . $db_prefix . "fb_forums where forum_id='" . $data ['forum_id'] . "'" );
				if (dbrows ( $iconQuery )) {
					$iconData = dbarray ( $iconQuery );
					$ficon = ($iconData ['forum_icon'] !== "" ? $iconData ['forum_icon'] : "folder.png");
				} else {
					$ficon = "folder.png";
				}
				
				if ($fb4 ['forum_icons']) {
					$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/forum_icons/$ficon' alt='' />";
					echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap; padding:7px;'>$ficon</td>\n";
				}
				echo "<td class='tbl1' style='padding:5px;width:65%;'><a href='viewforum.php?forum_id=" . $data ['forum_id'] . "' style='font-size:12px; text-decoration:underline; font-weight:bold;'>
			<!--forum_name_cell-->" . $data ['forum_name'] . "</a><br />\n";
				if ($data ['forum_description'] || $moderators) {
					echo "<span class='small'>" . $data ['forum_description'] . ($data ['forum_description'] && $moderators ? "<br />\n" : "");
					echo ($moderators ? "<strong>" . $locale ['411'] . "</strong>" . $moderators . "</span>\n" : "</span>\n") . "\n";
				}
				$c_res = dbquery ( "select * from " . DB_PREFIX . "forums f
			left join " . DB_PREFIX . "fb_forums f2 on f2.forum_id=f.forum_id
			where " . groupaccess ( "f.forum_access" ) . " AND f2.forum_parent='" . $data ['forum_id'] . "'" );
				if (dbrows ( $c_res )) {
					if ($fb4 ['subforum_view']) {
						echo "<br /><span class='small'><strong>" . $locale ['fb552'] . "</strong> ";
						$i = dbrows ( $c_res );
						while ( $c_data = dbarray ( $c_res ) ) {
							$i --;
							echo "<a href='" . FORUM . "viewforum.php?forum_id=" . $c_data ['forum_id'] . "'>" . $c_data ['forum_name'] . "</a>";
							if ($i > 0)
								echo ", ";
						}
						echo "</span>";
					} else {
						$counter = 0;
						$rows = 3;
						$sfimage1 = (file_exists ( THEME . "images/folder_open.png" ) ? THEME . "images/folder_open.png" : INFUSIONS . "fusionboard4/images/folder_open.png");
						$sfimage2 = (file_exists ( THEME . "images/subforum.png" ) ? THEME . "images/subforum.png" : INFUSIONS . "fusionboard4/images/subforum.png");
						echo "<br /><img src='$sfimage1' alt='' style='vertical-align:middle;'> <span class='small' style='font-weight:bold;'>" . $locale ['fb552'] . "</span><br />";
						echo "<table cellspacing='0' cellpadding='0' border='0' width='100%' style='padding-left:10px;'><tr>
					<td width='50%' class='small' style='padding-left:3px; padding-right:3px; vertical-align:top;'>";
						while ( $c_data = dbarray ( $c_res ) ) {
							if ($counter != 0 && ($counter % $rows == 0)) {
								echo "</td><td width='50%' class='small' style='padding-left:3px; padding-right:3px; vertical-align:top;'>\n";
							}
							$subforum = (is_file ( THEME . "images/subforum.png" ) ? THEME . "images/subforum.png" : INFUSIONS . "fusionboard4/images/subforum.png");
							echo "<img src='$sfimage2' alt='' style='vertical-align:middle;'> 
						<a href='" . FORUM . "viewforum.php?forum_id=" . $c_data ['forum_id'] . "' class='small'>" . $c_data ['forum_name'] . "</a><br />";
							$counter ++;
						}
						echo "</td></tr></table>";
					}
				}
				echo "</td>\n";
				echo "<td class='tbl2' style='white-space:nowrap; padding:5px;width:35%;'>";
				$children = array ( );
				$child_res = dbquery ( "select * from " . $db_prefix . "fb_forums f
			left join " . $db_prefix . "forums f2 on f2.forum_id=f.forum_id
			where " . groupaccess ( "f2.forum_access" ) . " and f.forum_parent='" . $data ['forum_id'] . "'" );
				if (dbrows ( $child_res )) {
					while ( $child_data = dbarray ( $child_res ) ) {
						array_push ( $children, $child_data ['forum_id'] );
						findChildren ( $child_data ['forum_id'] );
					}
				}
				if (count ( $children )) {
					$where = "";
					$counter = count ( $children );
					foreach ( $children as $child ) {
						$where .= "t.forum_id='$child' " . ($counter > 1 ? "OR " : "");
						$counter --;
					}
					$childrenForums = dbquery ( "select * from " . $db_prefix . "threads t
				left join " . $db_prefix . "users u on u.user_id=t.thread_lastuser
				left join " . $db_prefix . "posts p on p.post_id=t.thread_lastpostid
				where ($where OR t.forum_id='" . $data ['forum_id'] . "') 
				order by t.thread_lastpost desc limit 1" );
					if (! dbrows ( $childrenForums )) {
						echo $locale ['405'] . "</td>\n";
					} else {
						$childrenData = dbarray ( $childrenForums );
						echo "<b><a href='viewthread.php?thread_id=" . $childrenData ['thread_id'] . "' style='text-decoration:underline;'>" . trimlink ( $childrenData ['thread_subject'], 30 ) . "</a></b><br />";
						echo "" . $locale ['406'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $childrenData ['thread_lastuser'] . "'>" . showLabel ( $childrenData ['user_id'], false, "index" ) . "</a><br />
					<div align='right'>" . timePassed ( $childrenData ['thread_lastpost'], false ) . "
					<a href='" . FORUM . "viewthread.php?thread_id=" . $childrenData ['thread_id'] . "&amp;pid=" . $childrenData ['thread_lastpostid'] . "#post_" . $childrenData ['thread_lastpostid'] . "' title='Go To Last Post'><b>»</b></a></div></td>\n";
					}
				} else {
					if ($data ['forum_lastpost'] == 0) {
						echo $locale ['405'] . "</td>\n";
					} else {
						$threadData = dbarray ( dbquery ( "select * from " . $db_prefix . "threads t
					left join " . $db_prefix . "posts p on p.post_id=t.thread_lastpostid
					where t.thread_lastpost='" . $data ['forum_lastpost'] . "'" ) );
						echo "<b><a href='viewthread.php?thread_id=" . $threadData ['thread_id'] . "' style='text-decoration:underline;'>" . trimlink ( $threadData ['thread_subject'], 30 ) . "</a></b><br />";
						echo "" . $locale ['406'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['forum_lastuser'] . "' style='text-decoration:underline;'>" . showLabel ( $data ['forum_lastuser'], false, "index" ) . "</a><br />
					<div align='right'>" . timePassed ( $data ['forum_lastpost'], false ) . "
					<a href='" . FORUM . "viewthread.php?thread_id=" . $threadData ['thread_id'] . "&amp;pid=" . $threadData ['thread_lastpostid'] . "#post_" . $threadData ['thread_lastpostid'] . "' title='Go To Last Post'><b>»</b></a></div></td>\n";
					}
				}
				echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>" . $data ['forum_threadcount'] . "</td>\n";
				echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>" . $data ['forum_postcount'] . "</td>\n";
				echo "</tr>\n";
			}
			echo "</table></div>";
		}
	
	}

	function useraccess($field) {
		global $data, $user;
		if ($data ['user_level'] == 0) {
			return "$field = '0'";
		} elseif ($data ['user_level'] == 103) {
			return "1 = 1";
		} elseif ($data ['user_level'] >= 102) {
			$res = "($field='0' OR $field='101' OR $field='102'";
		} elseif ($data ['user_level'] >= 101) {
			$res = "($field='0' OR $field='101'";
		}
		if (substr ( $data ['user_groups'], 1 ) != "" && $data ['user_level'] !== 103) {
			$res .= " OR $field='" . str_replace ( ".", "' OR $field='", substr ( $data ['user_groups'], 1 ) ) . "'";
		}
		$res .= ")";
		return $res;
	}
	
	function renderTools() {
		global $settings, $fb4, $userdata, $fdata, $layout, $db_prefix, $locale;
		echo "<style type='text/css'>
	.forumbutton {
		text-align:right;
		padding:5px;
		font-weight:bold;
	}
	.bborder{
		border:1px solid #000;
	}
	</style>
	<table cellspacing='1' cellpadding='0' cellspacing='1' class='tbl-border' align='right'>
			<tr>\n";
		if (iMEMBER && $settings ['thread_notify']) {
			echo "<td class='tbl2 forumbutton'>";
			if (dbcount ( "(thread_id)", DB_THREAD_NOTIFY, "thread_id='" . $_GET ['thread_id'] . "' AND notify_user='" . $userdata ['user_id'] . "'" )) {
				$result2 = dbquery ( "UPDATE " . DB_THREAD_NOTIFY . " SET notify_datestamp='" . time () . "', notify_status='1' WHERE thread_id='" . $_GET ['thread_id'] . "' AND notify_user='" . $userdata ['user_id'] . "'" );
				echo "<a href='postify.php?post=off&amp;forum_id=" . $fdata ['forum_id'] . "&amp;thread_id=" . $_GET ['thread_id'] . "'>" . $locale ['fb508'] . "</a>";
			} else {
				echo "<a href='postify.php?post=on&amp;forum_id=" . $fdata ['forum_id'] . "&amp;thread_id=" . $_GET ['thread_id'] . "'>" . $locale ['fb507'] . "</a>";
			}
			echo "</td>";
		}
		echo "<td class='tbl2 forumbutton'><a href='" . BASEDIR . "print.php?type=F&amp;thread=" . $_GET ['thread_id'] . "'>" . $locale ['519'] . "</a></td>";
		if (iMEMBER && $fb4 ['layout_change']) {
			$list = ($layout != "1" ? "<a href='" . FUSION_SELF . "?thread_id=" . $_GET ['thread_id'] . "&amp;view=1' style='font-weight:normal;'>" . $locale ['fb504'] . "</a>" : "<a name='selected'><b>" . $locale ['fb504'] . "</b></a>")."<br />\n";
			$list .= ($layout != "2" ? "<a href='" . FUSION_SELF . "?thread_id=" . $_GET ['thread_id'] . "&amp;view=2' style='font-weight:normal;'>" . $locale ['fb505'] . "</a>" : "<a name='selected'><b>" . $locale ['fb505'] . "</b></a>")."<br />\n";
			$list .= ($layout != "3" ? "<a href='" . FUSION_SELF . "?thread_id=" . $_GET ['thread_id'] . "&amp;view=3' style='font-weight:normal;'>" . $locale ['fb506'] . "</a>" : "<a name='selected'><b>" . $locale ['fb506'] . "</b></a>")."<br />\n";
			$list .= ($layout != "4" ? "<a href='" . FUSION_SELF . "?thread_id=" . $_GET ['thread_id'] . "&amp;view=4' style='font-weight:normal;'>" . $locale ['fb438'] . "</a>" : "<a name='selected'><b>" . $locale ['fb438'] . "</b></a>")."\n";
			?>
			<script type="text/javascript">
			function showHideThis( id ){
			object = document.getElementById( id );
			if(object.style.display == 'none'){ 
				object.style.display = 'block';
			} else {
					object.style.display = 'none';
				}
			}
			</script>
		 	<?php
			echo "<td class='tbl2 forumbutton' style='padding:0px;'><div class='tbl2 forumbutton' onclick='javascript:showHideThis(\"views\");'><a href='#'>".$locale['fb503']."</a></div>
			<div style='position:relative;'>\n";
			echo "<table cellspacing='0' cellpadding='0' class='tbl-border' id='views' style='display:none;top:0px;right:0px;position:absolute;'>\n";
			echo "<tr><td class='tbl1' style='text-align:left;'>$list</td></tr>\n";
			echo "</table>\n</div>
			</td>";
		}
		echo "</tr></table>\n";
	}
	
	function renderParents($parent) {
		global $db_prefix;
		$p_res = dbquery ( "select * from " . $db_prefix . "forums f
	left join " . $db_prefix . "fb_forums f2 on f2.forum_id=f.forum_id
	where f.forum_id='$parent'" );
		$caption = "";
		if (dbrows ( $p_res )) {
			
			$p_data = dbarray ( $p_res );
			if ($p_data ['forum_parent'] == "0")
				define ( "ROOTPARENT", $p_data ['forum_id'] );
			$caption .= " » <a href='" . FORUM . "viewforum.php?forum_id=" . $p_data ['forum_id'] . "'>" . $p_data ['forum_name'] . "</a>";
			$caption .= renderParents ( $p_data ['forum_parent'] );
		
		}
		return $caption;
	}
	
	function renderNav($forum = false, $announcement = false, $page = "") {
		
		global $locale, $settings, $fb4, $fdata, $db_prefix, $userdata;
		
		$caption = "";
		
		if ($fdata ['forum_parent'] !== "0") {
			$p_data = dbarray ( dbquery ( "select * from " . $db_prefix . "forums f
		left join " . $db_prefix . "fb_forums f2 on f2.forum_id=f.forum_id
		where f.forum_id='" . $fdata ['forum_parent'] . "'" ) );
			$caption .= renderParents ( $p_data ['forum_parent'] );
			if ($p_data ['forum_parent'] == "0")
				define ( "ROOTPARENT", $p_data ['forum_id'] );
			$caption .= " » <a href='" . FORUM . "viewforum.php?forum_id=" . $p_data ['forum_id'] . "'>" . $p_data ['forum_name'] . "</a>";
		}
		
		if (defined ( "ROOTPARENT" )) {
			$root = dbarray ( dbquery ( "SELECT f.*, f2.forum_name AS forum_cat_name FROM " . DB_FORUMS . " f
			LEFT JOIN " . DB_FORUMS . " f2 ON f.forum_cat=f2.forum_id
			WHERE f.forum_id='" . ROOTPARENT . "'" ) );
			$catName = $root ['forum_cat_name'];
		} else {
			$catName = $fdata ['forum_cat_name'];
		}
		
		if ($fb4 ['vb_nav']) {
			
			if ($fb4 ['forum_rules']) {
				$columns = 6;
			} else {
				$columns = 5;
			}
			$width = ceil ( 100 / $columns );
			$nav = "<tr>\n<td class='tbl2' colspan='2' style='padding:0px;'>\n";
			$nav .= "<table width='100%' cellspacing='0' cellspacing='0'><tr>";
			$nav .= "<td class='tbl2' style='text-align:center;width:$width%'>";
			if (iMEMBER) {
				$nav .= "<a href='" . INFUSIONS . "fusionboard4/usercp.php'>" . $locale ['fb922'] . "</a>";
			} else {
				$nav .= "<a href='" . BASEDIR . "register.php'>" . $locale ['fb910'] . "</a>";
			}
			$nav .= "</td>\n";
			if ($fb4 ['forum_rules']) {
				$nav .= "<td class='tbl2' style='text-align:center;width:$width%'>";
				$nav .= "<a href='" . INFUSIONS . "fusionboard4/rules.php'>" . $locale ['fb911'] . "</a></td>\n";
			}
			$nav .= "<td class='tbl2' style='text-align:center;width:$width%'><a href='" . BASEDIR . "faq.php'>" . $locale ['fb912'] . "</a></td>\n";
			$nav .= "<td class='tbl2' style='text-align:center;width:$width%'><a href='" . BASEDIR . "members.php'>" . $locale ['fb913'] . "</a></td>\n";
			$nav .= "<td class='tbl2' style='text-align:center;width:$width%'><a href='" . INFUSIONS . "fusionboard4/today.php'>" . $locale ['fb914'] . "</a></td>\n";
			$nav .= "<td class='tbl2' style='text-align:center;width:$width%'><a href='" . BASEDIR . "search.php'>" . $locale ['fb915'] . "</a></td>\n";
			$nav .= "</tr>\n</table>\n</td>\n</tr>\n";
			
			if (iMEMBER) {
				
				$login = "<td class='tbl2' style='padding:5px;white-space:nowrap;width:1%'>\n";
				$login .= $locale ['fb923'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $userdata ['user_id'] . "'><b>" . $userdata ['user_name'] . "</b></a>!";
				if (iADMIN && ! INVISIBLEMODE) {
					$login .= " <a href='" . FUSION_SELF . (FUSION_QUERY ? "?" . FUSION_QUERY . "&amp;invisible=on" : "?invisible=on") . "'>[" . $locale ['uc365'] . "]</a>";
				}
				$login .= "<br />\n";
				$login .= $locale ['fb924'] . "<span class='small'>" . timepassed ( $userdata ['user_lastvisit'] ) . "</span><br />\n";
				$unread = dbcount ( "(message_id)", DB_MESSAGES, "message_to='" . $userdata ['user_id'] . "' and message_read='0'" );
				$total = dbcount ( "(message_id)", DB_MESSAGES, "message_to='" . $userdata ['user_id'] . "' and message_folder='0'" );
				;
				$login .= "<a href='" . BASEDIR . "messages.php'>" . $locale ['fb925'] . "</a>: " . $unread . " " . $locale ['fb926'] . ", " . $total . " " . $locale ['fb927'] . "\n";
				$login .= "</td>\n";
			
			} else {
				$login = "<td class='tbl2' style='padding:5px;white-space:nowrap;width:1%'>\n<form name='loginform' method='post' action='" . FUSION_SELF . "'>\n";
				$login .= "<img src='" . INFUSIONS . "fusionboard4/images/user.png' alt='" . $locale ['global_101'] . "' title='" . $locale ['global_101'] . "'> ";
				$login .= "<input type='text' name='user_name' class='textbox' style='width:100px' /><br />\n";
				$login .= "<img src='" . INFUSIONS . "fusionboard4/images/key.png' alt='" . $locale ['global_102'] . "' title='" . $locale ['global_102'] . "'> ";
				$login .= " <input type='password' name='user_pass' class='textbox' style='width:100px' />\n";
				$login .= "<input type='checkbox' name='remember_me' value='y' /> \n";
				$login .= "<input type='submit' name='login' value='" . $locale ['global_104'] . "' class='button' />\n";
				$login .= "</form></td>\n";
			}
		
		} else {
			
			$columns = false;
			$nav = "";
			$login = "";
		
		}
		
		$sfimage1 = (file_exists ( THEME . "images/folder_open.png" ) ? THEME . "images/folder_open.png" : INFUSIONS . "fusionboard4/images/folder_open.png");
		$sfimage2 = (file_exists ( THEME . "images/subforum.png" ) ? THEME . "images/subforum.png" : INFUSIONS . "fusionboard4/images/subforum.png");
		
		if ($forum) {
			
			$caption = $catName . $caption;
			
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
		<tr>
			<td class='tbl1'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td width='1'><img src='$sfimage1' alt=''></td>
			<td style='padding-left:3px;'><a href='" . FORUM . "index.php'>" . $settings ['sitename'] . "</a> » " . $caption . "</td></tr>
			</table>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td style='width:31px;' align='right'><img src='$sfimage2' alt=''></td>
			<td style='padding-left:3px;'><b><a href='viewforum.php?forum_id=" . $fdata ['forum_id'] . "'>" . $fdata ['forum_name'] . "</a></b></td></tr>
			</table>
			</td>$login
		</tr>";
		
		} elseif ($announcement) {
			
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
		<tr>
			<td class='tbl1'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td width='1'><img src='$sfimage1' alt=''></td>
			<td style='padding-left:3px;'><a href='" . FORUM . "index.php'>" . $settings ['sitename'] . "</a> » " . $locale ['fb900'] . "</td></tr>
			</table>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td style='width:31px;' align='right'><img src='" . INFUSIONS . "fusionboard4/images/exclamation.png' alt=''></td>
			<td style='padding-left:3px;'><b><a href='" . FUSION_SELF . "?thread_id=" . $fdata ['thread_id'] . "&amp;rowstart=0'>" . $fdata ['thread_subject'] . "</a></b></td></tr>
			</table>
			</td>$login
		</tr>";
		
		} elseif ($page) {
			
			$caption = $catName . $caption . " » <a href='viewforum.php?forum_id=" . $fdata ['forum_id'] . "'>" . $fdata ['forum_name'] . "</a>";
			
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
		<tr>
			<td class='tbl1'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td width='1'><img src='$sfimage1' alt=''></td>
			<td style='padding-left:3px;'><a href='" . FORUM . "index.php'>" . $settings ['sitename'] . "</a></td></tr>
			</table>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td style='width:31px;' align='right'><img src='$sfimage2' alt=''></td>
			<td style='padding-left:3px;'><b><a href='" . $page [0] . "'>" . $page [1] . "</a></b></td></tr>
			</table>
			</td>$login
		</tr>";
		
		} else {
			
			$caption = $catName . $caption . " » <a href='viewforum.php?forum_id=" . $fdata ['forum_id'] . "'>" . $fdata ['forum_name'] . "</a>";
			
			echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
		<tr>
			<td class='tbl1'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td width='1'><img src='$sfimage1' alt=''></td>
			<td style='padding-left:3px;'><a href='" . FORUM . "index.php'>" . $settings ['sitename'] . "</a> » " . $caption . "</td></tr>
			</table>
			<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr><td style='width:31px;' align='right'><img src='$sfimage2' alt=''></td>
			<td style='padding-left:3px;'><b><a href='" . FUSION_SELF . "?thread_id=" . $fdata ['thread_id'] . "&amp;rowstart=0'>" . $fdata ['thread_subject'] . "</a></b></td></tr>
			</table>
			</td>$login
		</tr>";
		
		}
		
		echo $nav;
		
		if (INVISIBLEMODE) {
			echo "<tr>\n<td class='tbl2' style='text-align:center;font-weight:bold;padding:7px;' colspan='" . ($columns + 2) . "'>";
			echo $locale ['uc358'] . "<a href='" . FUSION_SELF . (FUSION_QUERY ? "?" . FUSION_QUERY . "&amp;invisible=off" : "?invisible=off") . "'>";
			echo $locale ['uc359'] . "</a>" . $locale ['uc360'];
			echo "</td>\n</tr>\n";
		}
		
		echo "</table><br />\n";
	}
	
	function renderPostNav($action = "", $announcement = false) {
		
		global $locale, $settings, $fb4, $fdata, $tdata, $db_prefix, $subject;
		
		$caption = "";
		
		if ($fdata ['forum_parent'] !== "0") {
			$p_data = dbarray ( dbquery ( "select * from " . $db_prefix . "forums f
		left join " . $db_prefix . "fb_forums f2 on f2.forum_id=f.forum_id
		where f.forum_id='" . $fdata ['forum_parent'] . "'" ) );
			$caption .= renderParents ( $p_data ['forum_parent'] );
			if ($p_data ['forum_parent'] == "0")
				define ( "ROOTPARENT", $p_data ['forum_id'] );
			$caption .= " » <a href='" . FORUM . "viewforum.php?forum_id=" . $p_data ['forum_id'] . "'>" . $p_data ['forum_name'] . "</a>";
		}
		
		if (defined ( "ROOTPARENT" )) {
			$root = dbarray ( dbquery ( "SELECT f.*, f2.forum_name AS forum_cat_name FROM " . DB_FORUMS . " f
			LEFT JOIN " . DB_FORUMS . " f2 ON f.forum_cat=f2.forum_id
			WHERE f.forum_id='" . ROOTPARENT . "'" ) );
			$catName = $root ['forum_cat_name'];
		} else {
			$catName = $fdata ['forum_cat_name'];
		}
		
		if ($announcement) {
			$caption = $locale ['fb900'];
		} else {
			$caption = $catName . $caption . " » <a href='viewforum.php?forum_id=" . $fdata ['forum_id'] . "'>" . $fdata ['forum_name'] . "</a>";
		}
		
		$sfimage1 = (file_exists ( THEME . "images/folder_open.png" ) ? THEME . "images/folder_open.png" : INFUSIONS . "fusionboard4/images/folder_open.png");
		$sfimage2 = (file_exists ( THEME . "images/subforum.png" ) ? THEME . "images/subforum.png" : INFUSIONS . "fusionboard4/images/subforum.png");
		
		echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>
	<tr>
		<td class='tbl1'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr><td width='1'><img src='$sfimage1' alt=''></td>
		<td style='padding-left:3px;'><a href='" . FORUM . "index.php'>" . $settings ['sitename'] . "</a> » " . $caption . "</td></tr>
		</table>
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr><td width='32' align='right'><img src='$sfimage2' alt=''></td>
		<td style='padding-left:3px;'>";
		if ($action == "edit") {
			echo $locale ['fb610'] . "<b><a href='viewthread.php?thread_id=" . $tdata ['thread_id'] . "&amp;rowstart=0'>" . $tdata ['thread_subject'] . "</a></b>";
		} elseif ($action == "reply") {
			echo $locale ['fb611'] . "<b><a href='viewthread.php?thread_id=" . $tdata ['thread_id'] . "&amp;rowstart=0'>" . $tdata ['thread_subject'] . "</a></b>";
		} elseif ($action == "newthread") {
			echo "<b>" . $locale ['fb612'] . "</b>";
		} else {
			echo "<b><a href='viewthread.php?thread_id=" . $tdata ['thread_id'] . "&amp;rowstart=0'>" . $subject . "</a></b>";
		}
		echo "</td></tr>
		</table>
		</td>
	</tr>
	</table><br />\n";
	}
	
	function timePassed($time, $showtime = 1) {
		global $locale, $settings;
		$mode = "short";
		$timepassed = (time () - $time);
		if ($timepassed > 7 * 24 * 3600) {
			if($timepassed > 14 * 24 * 3600){
				$timesince = showdate ( "%d-%m-%Y", $time );
			} else {
				$timesince = floor ( $timepassed / (7 * 24 * 3600) );
				$timesince .= ($timesince != "1" ? $locale ['fb980'] : $locale ['fb981']);
			}
		} elseif ($timepassed >= 24 * 3600 && $timepassed <= 48 * 3600 && $mode == "short"){
			$timesince = $locale['fb2_101'];
		} elseif ($timepassed <= 24 * 3600 && $mode == "short"){
			$timesince = $locale['fb2_100'];
		} elseif ($timepassed > 2 * 24 * 3600 && $mode == "short"){
			$timesince = floor ( $timepassed / (24 * 3600) );
			$timesince .= ($timesince != "1" ? $locale ['fb982'] : $locale ['fb983']);
		} elseif ($timepassed > 24 * 3600 && $mode !== "short") {
			$timesince = floor ( $timepassed / (24 * 3600) );
			$timesince .= ($timesince != "1" ? $locale ['fb982'] : $locale ['fb983']);
		} elseif ($timepassed > 3600 && $mode !== "short") {
			$timesince = floor ( $timepassed / 3600 );
			$timesince .= ($timesince != "1" ? $locale ['fb984'] : $locale ['fb985']);
		} elseif ($timepassed > 0 && $mode !== "short") {
			$timesince = floor ( $timepassed / 60 );
			$timesince .= ($timesince != "1" ? $locale ['fb986'] : $locale ['fb987']);
		} else {
			$timesince = showdate ( "%d-%m-%Y", $time );
		}
		if ($showtime || $mode == "short")
			$timesince .= "&nbsp;<span class='alt'>" . strftime ( "%I:%M %p", ($time + ($settings ['timeoffset'] * 3600)) ) . "</span>";
		return $timesince;
	
	}
	
	if (isset ( $_GET ['getfile'] ) && isnum ( $_GET ['getfile'] ) && $fb4 ['fboard_on']) {
		$result = dbquery ( "SELECT * FROM " . DB_FORUM_ATTACHMENTS . " WHERE attach_id='" . $_GET ['getfile'] . "'" );
		if (dbrows ( $result )) {
			if ($fb4 ['attach_count']) {
				$fb_res = dbquery ( "select * from " . DB_PREFIX . "fb_attachments WHERE attach_id='" . $_GET ['getfile'] . "'" );
				if (dbrows ( $fb_res )) {
					$fb_data = dbarray ( $fb_res );
					$fb_query = dbquery ( "update " . DB_PREFIX . "fb_attachments SET attach_count='" . ($fb_data ['attach_count'] + 1) . "' WHERE attach_id='" . $_GET ['getfile'] . "'" );
				} else {
					$fb_query = dbquery ( "insert into " . DB_PREFIX . "fb_attachments (attach_id,attach_count) VALUES('" . $_GET ['getfile'] . "', '1')" );
				}
			}
			require_once INCLUDES . "class.httpdownload.php";
			ob_end_clean ();
			$data = dbarray ( $result );
			$object = new httpdownload ( );
			$object->set_byfile ( FORUM . "attachments/" . $data ['attach_name'] );
			$object->use_resume = true;
			$object->download ();
		}
		exit ();
	}
	
	function renderAwards($user, $pre = "&nbsp;", $post = "") {
		global $db_prefix, $fb4, $locale;
		$result = dbquery ( "select * from " . $db_prefix . "fb_awards where award_user='$user'" );
		if (dbrows ( $result ) && $fb4 ['show_medals']) {
			echo $pre;
			while ( $data = dbarray ( $result ) ) {
				echo "<img src='" . INFUSIONS . "fusionboard4/images/awards/" . $data ['award_image'] . "'";
				if ($fb4 ['award_box']) {
					echo " title='header=[" . $locale ['fb724'] . "] body=[" . stripslash ( $data ['award_desc'] ) . "]'";
				}
				if ($fb4 ['award_alert']) {
					echo " onClick='alert(\"" . $locale ['fb724'] . stripslash ( $data ['award_desc'] ) . "\");'";
				}
				echo " alt='" . stripslash ( $data ['award_desc'] ) . "' title='" . stripslash ( $data ['award_desc'] ) . "' style='vertical-align:middle;' border='0'>";
			}
			echo $post;
		}
	}
	
	function findChildren($forum, $childArray = "") {
		if (! isset ( $childArray )) {
			global $children;
		}
		if (! isset ( $children )) {
			$children = array ( );
		}
		$child_res = dbquery ( "select * from " . DB_PREFIX . "fb_forums f
		left join " . DB_PREFIX . "forums f2 on f2.forum_id=f.forum_id
		where " . groupaccess ( "f2.forum_access" ) . " and f.forum_parent='$forum'" );
		if (dbrows ( $child_res )) {
			while ( $child_data = dbarray ( $child_res ) ) {
				array_push ( $children, $child_data ['forum_id'] );
				findChildren ( $child_data ['forum_id'] );
			}
		}
	}
	
	function showLabel($id, $style = "", $place = "") {
		global $fb4;
		$user = dbquery ( "select * from " . DB_USERS . " where user_id='$id'" );
		$label = 'label_' . $place;
		$show = ($place ? $fb4 [$label] : 1);
		if (dbrows ( $user )) {
			$userdata = dbarray ( $user );
			if ($show) {
				$label_res = dbquery ( "select * from " . DB_PREFIX . "fb_labels where label_user='$id'" );
				if (dbrows ( $label_res )) {
					$label_data = dbarray ( $label_res );
					return "<span style='$style" . stripslash ( $label_data ['label_style'] ) . "'>" . $userdata ['user_name'] . "</span>";
				} else {
					if (labelGroups ( "label_group", $userdata )) {
						$label_res = dbquery ( "select * from " . DB_PREFIX . "fb_labels where " . labelGroups ( "label_group", $userdata ) . " limit 1" );
					}
					if (isset ( $label_res ) && dbrows ( $label_res )) {
						$label_data = dbarray ( $label_res );
						return "<span style='$style" . stripslash ( $label_data ['label_style'] ) . "'>" . $userdata ['user_name'] . "</span>";
					} else {
						return "<span style='$style'>" . $userdata ['user_name'] . "</span>";
					}
				}
			} else {
				return $userdata ['user_name'];
			}
		} else {
			return "";
		}
	}
	
	function labelGroups($field, $data) {
		$res = "";
		if ($data ['user_level'] == 103) {
			$res = "$field='103'";
		} elseif ($data ['user_level'] >= 102) {
			$res = "$field='102'";
		}
		if (substr ( $data ['user_groups'], 1 ) != "" && ! $res) {
			$res = "($field='" . str_replace ( ".", "' OR $field='", substr ( $data ['user_groups'], 1 ) ) . "')";
		}
		return $res;
	}
	
	function display_image_fb($file, $width = 50, $height = 50, $caption = "") {
		$size = @getimagesize ( FORUM . "attachments/" . $file );
		
		if ($size [0] > $height || $size [1] > $width) {
			if ($size [0] < $size [1]) {
				$img_w = round ( ($size [0] * $width) / $size [1] );
				$img_h = $width;
			} elseif ($size [0] > $size [1]) {
				$img_w = $height;
				$img_h = round ( ($size [1] * $height) / $size [0] );
			} else {
				$img_w = $height;
				$img_h = $width;
			}
		} else {
			$img_w = $size [0];
			$img_h = $size [1];
		}
		
		if ($size [0] != $img_w || $size [1] != $img_h) {
			$res = "<a href='" . FORUM . "attachments/" . $file . "'>
		<img src='" . FORUM . "attachments/" . $file . "' width='" . $img_w . "' height='" . $img_h . "' style='border:0' /></a>";
		} else {
			$res = "<img src='" . FORUM . "attachments/" . $file . "' width='" . $img_w . "' height='" . $img_h . "' style='border:0' />";
		}
		
		return $res;
	}
	
	function showtitle($user) {
		
		global $db_prefix;
		$titleLookup = dbquery ( "select * from " . DB_PREFIX . "fb_titles where title_id='" . $user ['user_title'] . "' and (" . useraccess_better ( "title_access", $user ) . ")" );
		if (dbrows ( $titleLookup )) {
			$titleData = dbarray ( $titleLookup );
			$title = stripslash ( $titleData ['title_title'] );
		} else {
			$title = stripslash ( $user ['user_title'] );
		}
		
		return $title;
	
	}
	
	function useraccess_better($field, $level) {
		if ($level ['user_level'] == 0) {
			return "$field = '0'";
		} elseif ($level ['user_level'] == 103) {
			return "1 = 1";
		} elseif ($level ['user_level'] >= 102) {
			$res = "($field='0' OR $field='101' OR $field='102'";
		} elseif ($level ['user_level'] >= 101) {
			$res = "($field='0' OR $field='101'";
		}
		if (substr ( $level ['user_groups'], 1 ) != "" && $level ['user_level'] !== 103) {
			$res .= " OR $field='" . str_replace ( ".", "' OR $field='", substr ( $level ['user_groups'], 1 ) ) . "'";
		}
		$res .= ")";
		return $res;
	}
	
	function createthumb($name, $filename, $new_w, $new_h) {
		$avatarext = strrchr ( $name, "." );
		$system = explode ( ".", $avatarext );
		if (preg_match ( "/jpg|jpeg/", $system [1] )) {
			$src_img = imagecreatefromjpeg ( $name );
		}
		if (preg_match ( "/png/", $system [1] )) {
			$src_img = imagecreatefrompng ( $name );
		}
		if (preg_match ( "/gif/", $system [1] )) {
			$src_img = imagecreatefromgif ( $name );
		}
		$old_x = imageSX ( $src_img );
		$old_y = imageSY ( $src_img );
		if ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = $old_y * ($new_h / $old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w = $old_x * ($new_w / $old_y);
			$thumb_h = $new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
		$dst_img = ImageCreateTrueColor ( $thumb_w, $thumb_h );
		imagecopyresampled ( $dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y );
		if (preg_match ( "/png/", $system [1] )) {
			imagepng ( $dst_img, $filename );
		} elseif (preg_match ( "/jpg|jpeg/", $system [1] )) {
			imagejpeg ( $dst_img, $filename );
		} else {
			imagegif ( $dst_img, $filename );
		}
		imagedestroy ( $dst_img );
		imagedestroy ( $src_img );
	}
	
	function sendMessage($to, $from, $subject, $message) {
		
		global $locale;
		
		$result = "insert into " . DB_MESSAGES . " (message_to, message_from, message_subject, message_message, message_smileys, message_read, message_datestamp, message_folder) VALUES('$to', '$from', '$subject', '$message', '0', '0', '" . time () . "', '0')";
		if (dbquery ( $result )) {
			return true;
		} else {
			return false;
		}
	
	}
	
	function showStatus($user, $image = true) {
		global $locale, $fb4;
		$online = dbcount ( "(online_user)", DB_ONLINE, "online_user='$user'" );
		$status = ($online ? $locale ['fb260'] : $locale ['fb261']);
		if (file_exists ( THEME . "forum/online.gif" )) {
			$img = THEME . "forum/";
		} else {
			$img = INFUSIONS . "fusionboard4/images/status/";
		}
		if ($fb4 ['show_status']) {
			if ($image) {
				return "<img src='" . $img . ($online ? "online" : "offline") . ".gif' alt='$status' title='$status' style='vertical-align:middle;' />\n";
			} else {
				return $status . "\n";
			}
		} else {
			return "";
		}
	}
	
	function checkIgnore($user) {
		global $userdata;
		return dbcount ( "(ignore_user)", DB_PREFIX . "fb_ignore", "ignore_user='$user' and ignore_ignored='" . $userdata ['user_id'] . "'" );
	}
	
	function renderMods($image = 1, $text = 1) {
		global $data, $mod_groups, $settings;
		if ($data ['user_level'] >= 102) {
			echo $settings ['forum_ranks'] ? show_forum_rank ( $data ['user_posts'], $data ['user_level'], $image, $text ) : getuserlevel ( $data ['user_level'] );
		} else {
			foreach ( $mod_groups as $mod_group ) {
				$is_mod = false;
				if (! $is_mod && preg_match ( "(^\.{$mod_group}$|\.{$mod_group}\.|\.{$mod_group}$)", $data ['user_groups'] )) {
					$is_mod = true;
				}
			}
			if ($settings ['forum_ranks']) {
				echo $is_mod ? show_forum_rank ( $data ['user_posts'], 104, $image, $text ) : show_forum_rank ( $data ['user_posts'], $data ['user_level'], $image, $text );
			} else {
				echo $is_mod ? $locale ['user1'] : getuserlevel ( $data ['user_level'] );
			}
		}
	}
	
	/* Invisible mode */
	include INFUSIONS . "fusionboard4/includes/invisible.inc.php";
	
	/* Warning system */
	include INFUSIONS . "fusionboard4/includes/warning.inc.php";
	
	function showPoweredBy() {
		return "Forum powered by <a href='http://www.php-invent.com' target='_blank'>fusionBoard</a>";
	}

}
?>