<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
require_once "../../maincore.php";
require_once THEMES . "templates/header.php";
include LOCALE . LOCALESET . "forum/main.php";
include INFUSIONS . "fusionboard4/includes/func.php";

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}

if (! iMEMBER)
	redirect ( FORUM );

define ( "USER_CP", TRUE );

/* User CP Navigation Styling */
echo "<style type='text/css'>
.navtitle { font-size:13px;font-weight:bold; padding:5px; }
.navsection { font-weight:bold; }
.bold { font-weight:bold; }
.fields { text-align:left; width:440px; border: 1px; border-style:solid; border-color:#ccc; padding:8px; margin:5px; }
.users { padding:6px; border:1px solid #ccc; width:230px; height:70px; }
</style>\n";

$_GET ['section'] = (isset ( $_GET ['section'] ) ? stripinput ( $_GET ['section'] ) : "intro");
$section = (isset ( $_GET ['section'] ) ? stripinput ( $_GET ['section'] ) : "intro");

opentable ( $locale ['fb922'] );

renderNav ( false, false, array (INFUSIONS . "fusionboard4/usercp.php", $locale ['fb922'] ) );
add_to_title ( " :: " . $locale ['fb922'] );
if (isset($_COOKIE["fusion_box_usercp"])) {
  if ($_COOKIE["fusion_box_usercp"] == "none") {
     $state = "off";
  } else {
     $state = "on";
  }
} else {
	$state = "on";
}

echo "<img src='".get_image("panel_".($state == "on" ? "off" : "on"))."' id='b_usercp' class='panelbutton' alt='' onclick=\"javascript:flipBox('usercp')\" />";

echo "<table width='100%' cellspacing='0' cellpadding='5' border='0'>\n";
echo "<tr>\n<td style='width:200px;vertical-align:top;".($state == "off" ? "display:none" : "")."'id='box_usercp'>\n";

/* User CP Navigation Start */
echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc100'] . "</td>\n</tr>\n";
echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc101'] . "</td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=details'>" . $locale ['uc102'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=avatar'>" . $locale ['uc103'] . "</a></td>\n</tr>\n";

if ($fb4 ['buddy_enable'] || $fb4 ['group_enable'])
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc104'] . "</td>\n</tr>\n";
if ($fb4 ['buddy_enable'])
	echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=buddies'>" . $locale ['uc105'] . "</a></td>\n</tr>\n";
if ($fb4 ['group_enable'])
	echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=groups'>" . $locale ['uc106'] . "</a></td>\n</tr>\n";
$requests = dbcount ( "(invite_to)", DB_PREFIX . "fb_invites", "invite_to='" . $userdata ['user_id'] . "'" );
if ($fb4 ['group_enable'])
	echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=requests'>" . $locale ['uc325'] . "" . ($requests ? " <b>($requests)</b>" : "") . "</a></td>\n</tr>\n";

echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc107'] . "</td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=signature'>" . $locale ['uc108'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=email'>" . $locale ['uc109'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=options'>" . $locale ['uc110'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=ignore'>" . $locale ['uc111'] . "</a></td>\n</tr>\n";

echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc112'] . "</td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=pm&folder=inbox'>" . $locale ['uc113'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=pm&folder=outbox'>" . $locale ['uc114'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=pm&folder=archive'>" . $locale ['uc115'] . "</a></td>\n</tr>\n";

echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc116'] . "</td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=subscriptions&type=thread'>" . $locale ['uc117'] . "</a></td>\n</tr>\n";
echo "<tr>\n<td class='tbl1'>" . THEME_BULLET . " <a href='" . FUSION_SELF . "?section=subscriptions&type=forum'>" . $locale ['uc118'] . "</a></td>\n</tr>\n";

echo "</table>\n";
/* User CP Navigation End */

echo "</td>\n<td style='vertical-align:top; padding-left:5px;'>\n";

/* User CP Sections Begin */

echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";

/* User CP -> Start Page */
if ($section == "intro") {
	
	$result = dbquery ( "
				select t.*, f.*, p.*, tu.user_id, tu.user_name, tu2.user_id as lastpost_id, tu2.user_name as lastpost_name, COUNT(post_id)-1 as replies from " . DB_THREAD_NOTIFY . " tn
				left join " . DB_THREADS . " t on t.thread_id=tn.thread_id
				left join " . DB_FORUMS . " f on f.forum_id=t.forum_id
				left join " . DB_POSTS . " p on p.thread_id=t.thread_id
				left join " . DB_USERS . " tu on tu.user_id=t.thread_author
				left join " . DB_USERS . " tu2 on t.thread_lastuser = tu2.user_id
				where tn.notify_user='" . $userdata ['user_id'] . "' and " . groupaccess ( 'forum_access' ) . " and thread_lastpost > '$lastvisited'
				group by p.thread_id order by tn.notify_status asc, t.thread_lastpost desc
			" );
	$rows = dbrows ( $result );
	
	$unviewed = dbcount ( "(thread_id)", DB_THREAD_NOTIFY, "notify_user='" . $userdata ['user_id'] . "' and notify_status='0'" );
	
	echo "<tr>\n<td class='tbl2 navtitle'" . ($rows ? " colspan='" . ($fb4 ['post_icons'] ? 6 : 5) . "'" : "") . ">" . $locale ['uc150'] . " ($unviewed)</td>\n</tr>\n";
	add_to_title ( " :: " . $locale ['uc150'] );
	if ($rows) {
		
		echo "<tr>\n<td class='tbl1 bold'" . ($fb4 ['post_icons'] ? " colspan='2'" : "") . "' style='width:1%'>&nbsp;</td>\n";
		echo "<td class='tbl1 bold'>" . $locale ['uc152'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:150px;'>" . $locale ['uc153'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['uc154'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['uc155'] . "</td>\n</tr>\n";
		
		while ( $data = dbarray ( $result ) ) {
			if ($data ['thread_locked']) {
				$image = get_image ( "folderlock" );
				$titlebold = "";
			} else {
				$thread_match = $data ['thread_id'] . "\|" . $data ['thread_lastpost'] . "\|" . $data ['forum_id'];
				if ($data ['thread_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match ( "(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata ['user_threads'] )) {
						$image = get_image ( "folder" );
						$titlebold = "";
					} else {
						$image = get_image ( "foldernew" );
						$titlebold = "font-weight:bold;";
					}
				} else {
					$image = get_image ( "folder" );
					$titlebold = "";
				}
			}
			
			echo "<tr>\n<td class='tbl1'><img src='$image' alt='' /></td>\n";
			if ($fb4 ['post_icons']) {
				$post_res = dbquery ( "select * from " . DB_PREFIX . "fb_posts where post_id='" . $data ['post_id'] . "'" );
				if (dbrows ( $post_res )) {
					$post_data = dbarray ( $post_res );
					if ($post_data ['post_icon']) {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/" . $post_data ['post_icon'] . "' alt=''>";
					} else {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/page_white.png' alt=''>";
					}
				} else {
					$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/page_white.png' alt=''>";
				}
				echo "<td class='tbl1'>$ficon</td>\n";
			}
			
			echo "<td class='tbl2'><span style='font-size:14px;$titlebold'>";
			echo "<a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "' style='text-decoration:underline;'>" . $data ['thread_subject'] . "</a></span><br />\n";
			echo "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['user_id'] . "' class='alt'>" . $data ['user_name'] . "</a><br />\n";
			echo "<a href='" . FORUM . "postify.php?post=off&amp;forum_id=" . $data ['forum_id'] . "&thread_id=" . $data ['thread_id'] . "'>" . $locale ['uc156'] . "</a></td>\n";
			echo "<td class='tbl1'>" . timePassed ( $data ['thread_lastpost'] ) . "<br />\n";
			echo "by <a href='" . BASEDIR . "profile.php?lookup=" . $data ['lastpost_id'] . "'>" . $data ['lastpost_name'] . "</a><br />\n<div style='text-align:right'>";
			echo "<a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "&amp;pid=" . $data ['thread_lastpostid'] . "#post_" . $data ['thread_lastpostid'] . "' title='" . $locale ['fb615'] . "'><b>»»</b></a></div></td>\n";
			echo "<td class='tbl2' style='text-align:center;'>" . number_format ( $data ['replies'] ) . "</td>\n";
			echo "<td class='tbl1' style='text-align:center;'>" . number_format ( $data ['thread_views'] ) . "</td>\n</tr>\n";
		}
	
	} else {
		echo "<tr>\n<td class='tbl1'>\n<div align='center'>" . $locale ['uc151'] . "</div>\n</td>\n</tr>\n";
	}
	
	echo "<tr>\n<td class='tbl1' style='text-align:right;'" . ($fb4 ['post_icons'] ? " colspan='6'" : " colspan='5'") . ">\n<a href='" . FUSION_SELF . "?section=subscriptions&type=thread'>" . $locale ['uc157'] . "</a>\n</td>\n</tr>\n";
	
	if (time () - $userdata ['user_joined'] < 7 * 24 * 3600) {
		$timePassed = ", " . timePassed ( $userdata ['user_joined'], false );
	} else {
		$timePassed = "";
	}
	
	echo "</table><br /><table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
	echo "<tr>\n<td class='tbl2 navtitle' colspan='2'>" . $locale ['uc125'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' style='width:50%;text-align:right;'>" . $locale ['uc126'] . "</td>\n<td class='tbl1'>" . $userdata ['user_email'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2' style='width:50%;text-align:right;'>" . $locale ['uc127'] . "</td>\n<td class='tbl2'>" . showdate ( "longdate", $userdata ['user_joined'] ) . $timePassed . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' style='width:50%;text-align:right;'>" . $locale ['uc129'] . "</td>\n<td class='tbl1'>" . number_format ( ($userdata ['user_posts'] / ((time () - $userdata ['user_joined']) / (3600 * 24))), 1 ) . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2' style='width:50%;text-align:right;'>" . $locale ['uc130'] . "</td>\n<td class='tbl2'>" . number_format ( $userdata ['user_posts'] ) . "</td>\n</tr>\n";
	
/* User CP -> Edit Details */
} elseif ($section == "details") {
	
	$user_data = $userdata;
	
	if (isset ( $_POST ['update_profile'] )) {
		require_once INFUSIONS . "fusionboard4/includes/updateprofile.php";
	}
	
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "updated") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $locale ['uc197'] . "</td>\n</tr>\n";
	}
	
	echo "<form action='" . FUSION_SELF . "?section=details' method='post' name='profileForm' enctype='multipart/form-data'>\n";
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc170'] . "</td>\n</tr>\n";
	add_to_title ( " :: " . $locale ['uc170'] );
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc171'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'><div align='center'>\n<fieldset class='fields'><legend><a name=''>" . $locale ['uc172'] . "</a></legend>\n";
	echo $locale ['uc173'] . "<br /><br />\n<input type='button' name='editEmail' class='button' value='" . $locale ['uc174'] . "' onClick='document.location.href=\"" . FUSION_SELF . "?section=email\"' />\n";
	echo "</fieldset>\n</div>\n</td>\n</tr>\n";
	echo "</table>\n<br />";
	
	echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc180'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'><div align='center'>\n";
	if (dbcount ( "(field_id)", DB_USER_FIELDS, "field_name='user_web'" )) {
		echo "<fieldset class='fields'><legend><a name=''>" . $locale ['uc181'] . "</a></legend>\n";
		echo $locale ['uc182'] . "<br />\n<input type='text' name='user_web' class='textbox' value='" . $userdata ['user_web'] . "' style='width:250px;'>\n";
		echo "</fieldset>\n";
	}
	
	$im = array ("user_msn", "user_aim", "user_skype", "user_yahoo", "user_icq" );
	$imlocale = array ("user_msn" => $locale ['uc186'], "user_aim" => $locale ['uc187'], "user_skype" => $locale ['uc188'], "user_yahoo" => $locale ['uc189'], "user_icq"=>$locale['uc190'] );
	
	echo "<fieldset class='fields'><legend><a name=''>" . $locale ['uc185'] . "</a></legend>\n";
	echo $locale ['uc191'] . "<br /><br />\n";
	echo "<table width='100%' cellspacing='0' cellpadding='4' border='0'>\n<tr>\n";
	$counter = 0;
	$columns = 2;
	foreach ( $im as $client ) {
		if (dbcount ( "(field_id)", DB_USER_FIELDS, "field_name='$client'" )) {
			if ($counter != 0 && ($counter % $columns == 0)) {
				echo "</tr>\n<tr>\n";
			}
			echo "<td>" . $imlocale [$client] . "<br /><input type='text' name='$client' class='textbox' value='" . $userdata [$client] . "'></td>\n";
			$counter ++;
		}
	}
	echo "</tr>\n</table>\n";
	echo "</fieldset>\n";
	
	echo "</div>\n</td>\n</tr>\n";
	echo "</table><br />\n";
	
	echo "<table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
	echo "<tr>\n<td class='tbl2 navsection' colspan='2'>" . $locale ['uc195'] . "</td>\n</tr>\n";
	
	echo "<tr>\n<td class='tbl1'><div align='center'>\n<fieldset class='fields'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>\n";
	
	include LOCALE . LOCALESET . "user_fields.php";
	
	$dontshow = "";
	array_push ( $im, "user_web" );
	foreach ( $im as $id ) {
		if ($dontshow !== "") {
			$dontshow .= " and ";
		}
		$dontshow .= "field_name!='$id'";
	}
	
	$profile_method = "input";
	$result3 = dbquery ( "SELECT * FROM " . DB_USER_FIELDS . " WHERE (field_group='2' or field_group='1') and ($dontshow) ORDER BY field_order" );
	if (dbrows ( $result3 )) {
		while ( $data3 = dbarray ( $result3 ) ) {
			if (file_exists ( LOCALE . LOCALESET . "user_fields/" . $data3 ['field_name'] . ".php" )) {
				include LOCALE . LOCALESET . "user_fields/" . $data3 ['field_name'] . ".php";
			}
			if (file_exists ( INCLUDES . "user_fields/" . $data3 ['field_name'] . "_include.php" )) {
				include INCLUDES . "user_fields/" . $data3 ['field_name'] . "_include.php";
			}
		}
	}
	
	echo "</table>\n</fieldset>\n";
	echo "<br /><input type='submit' name='update_profile' class='button' value='" . $locale ['uc196'] . "'>\n";
	echo "</div>\n</td>\n</tr>\n</form>\n";
	
/* User CP -> Edit Avatar */
} elseif ($section == "avatar") {
	
	if (isset ( $_POST ['option'] )) {
		
		if ($_POST ['option'] == "noav") {
			
			$result = dbquery ( "update " . DB_USERS . " set user_avatar='' where user_id='" . $userdata ['user_id'] . "'" );
			if (file_exists ( IMAGES . "avatars/" . $userdata ['user_avatar'] ) && ! ereg ( "infusions", $userdata ['user_id'] )) {
				unlink ( IMAGES . "avatars/" . $userdata ['user_avatar'] );
			}
			redirect ( FUSION_SELF . "?section=avatar&status=updated" );
		
		} elseif ($_POST ['option'] == "gallery") {
			
			if (isset ( $_POST ['avatarGallery'] )) {
				$image = stripinput ( $_POST ['avatarGallery'] );
			} else {
				redirect ( FUSION_SELF . "?section=avatars" );
			}
			if (! verify_image ( INFUSIONS . "fusionboard4/images/avatars/$image" )) {
				redirect ( FUSION_SELF . "?section=avatars" );
			}
			copy ( INFUSIONS . "fusionboard4/images/avatars/$image", IMAGES . "avatars/$image" );
			$result = dbquery ( "update " . DB_USERS . " set user_avatar='$image' where user_id='" . $userdata ['user_id'] . "'" );
			if ($result) {
				redirect ( FUSION_SELF . "?section=avatar&status=updated" );
			}
		
		} elseif ($_POST ['option'] == "custom") {
			
			if (! empty ( $_FILES ['user_avatar'] ['name'] ) && is_uploaded_file ( $_FILES ['user_avatar'] ['tmp_name'] )) {
				$newavatar = $_FILES ['user_avatar'];
				$avatarext = strrchr ( $newavatar ['name'], "." );
				$avatarname = substr ( $newavatar ['name'], 0, strrpos ( $newavatar ['name'], "." ) );
				if (preg_check ( "/^[-0-9A-Z_\[\]]+$/i", $avatarname ) && preg_check ( "/(\.gif|\.GIF|\.jpg|\.JPG|\.jpeg|\.JPEG|\.png|\.PNG)$/", $avatarext ) && $newavatar ['size'] <= $fb4 ['avatar_max_size']) {
					$avatarname = $avatarname . "[" . $userdata ['user_id'] . "]" . $avatarext;
					move_uploaded_file ( $newavatar ['tmp_name'], IMAGES . "avatars/" . $avatarname );
					chmod ( IMAGES . "avatars/" . $avatarname, 0644 );
					$set_avatar = "user_avatar='" . $avatarname . "'";
					if ($size = @getimagesize ( IMAGES . "avatars/" . $avatarname )) {
						if ($size ['0'] > $fb4 ['avatar_max_w'] || $size ['1'] > $fb4 ['avatar_max_h']) {
							unlink ( IMAGES . "avatars/" . $avatarname );
							$set_avatar = "user_avatar=''";
						} elseif (! verify_image ( IMAGES . "avatars/" . $avatarname )) {
							unlink ( IMAGES . "avatars/" . $avatarname );
							$set_avatar = "user_avatar=''";
						}
					} else {
						unlink ( IMAGES . "avatars/" . $avatarname );
						$set_avatar = "user_avatar=''";
					}
				} else {
					$set_avatar = "user_avatar=''";
				}
				$result = dbquery ( "update " . DB_USERS . " set $set_avatar where user_id='" . $userdata ['user_id'] . "'" );
				if ($result) {
					redirect ( FUSION_SELF . "?section=avatar&status=updated" );
				}
			} elseif (isset ( $_POST ['avatarWeb'] ) && $_POST ['avatarWeb'] !== "http://www.") {
				
				if (verify_image ( stripinput ( $_POST ['avatarWeb'] ) )) {
					
					$avatarname = strrchr ( stripinput ( $_POST ['avatarWeb'] ), "/" );
					$avatarname = str_replace ( "/", "", $avatarname );
					$avatarext = strrchr ( $avatarname, "." );
					$avatarname = substr ( $avatarname, 0, strrpos ( $avatarname, "." ) );
					
					if (preg_match ( "/^[-0-9A-Z_\[\]]+$/i", $avatarname ) && preg_match ( "/(\.gif|\.GIF|\.jpg|\.JPG|\.jpeg|\.JPEG|\.png|\.PNG)$/", $avatarext )) {
						
						$avatarname = $avatarname . "[" . $userdata ['user_id'] . "]" . $avatarext;
						$image = stripinput ( $_POST ['avatarWeb'] );
						copy ( $image, INFUSIONS . "fusionboard4/images/avatarst/" . $avatarname );
						
						createthumb ( INFUSIONS . "fusionboard4/images/avatarst/" . $avatarname, IMAGES . "avatars/" . $avatarname, $fb4 ['avatar_max_w'], $fb4 ['avatar_max_h'] );
						
						unlink ( INFUSIONS . "fusionboard4/images/avatarst/" . $avatarname );
						
						$result = dbquery ( "update " . DB_USERS . " set user_avatar='$avatarname' where user_id='" . $userdata ['user_id'] . "'" );
						
						redirect ( FUSION_SELF . "?section=avatar&status=updated" );
					
					} else {
						
						redirect ( FUSION_SELF . "?section=avatar" );
					
					}
				
				} else {
					
					redirect ( FUSION_SELF . "?section=avatar" );
				
				}
			}
		}
	}
	
	echo "<form action='" . FUSION_SELF . "?section=avatar' method='post' name='profileForm' enctype='multipart/form-data'>\n";
	
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "updated") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $locale ['uc214'] . "</td>\n</tr>\n";
	}
	
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc200'] . "</td>\n</tr>\n";
	add_to_title ( " :: " . $locale ['uc200'] );
	echo "<tr>\n<td class='tbl1'><div align='center'>\n<fieldset class='fields'><legend><a name=''>" . $locale ['uc202'] . "</a></legend>\n";
	
	if ($userdata ['user_avatar']) {
		echo "<div style='float:left;'><img src='" . IMAGES . "avatars/" . $userdata ['user_avatar'] . "' alt='' style='padding-right:10px;'></div>\n";
	} else {
		echo "<div style='float:left;'><img src='" . IMAGES . "noav.gif' alt='' style='padding-right:10px;'></div>\n";
	}
	
	echo $locale ['uc203'] . "<br />\n<br />\n<label><input type='radio' name='option' value='noav'" . (! $userdata ['user_avatar'] ? " CHECKED" : "") . ">" . $locale ['uc204'] . "</label>";
	
	echo "</fieldset>\n";
	echo "<fieldset class='fields'><legend><a name=''>" . $locale ['uc205'] . "</a></legend>\n";
	
	$folder = INFUSIONS . "fusionboard4/images/avatars/";
	
	$galleryFiles = makefilelist ( $folder, ".|..|index.php|Thumbs.db" );
	$galleryOpts = makefileopts ( $galleryFiles, $userdata ['user_avatar'] );
	
	echo $locale ['uc206'] . "<br />\n<br />\n<label><input type='radio' name='option' value='gallery'" . (in_array ( $userdata ['user_avatar'], $galleryFiles ) ? " CHECKED" : "") . ">" . $locale ['uc208'] . "</label>\n<br />\n";
	echo "<br />\n<br />\n<select name='avatarGallery' class='textbox' style='width:200px;' size='10' onChange='document.getElementById(\"preview\").src=\"" . INFUSIONS . "fusionboard4/images/avatars/\"+this.value'>\n";
	echo "<option value=''>" . $locale ['uc207'] . "</option>\n$galleryOpts</select>
			<img src='" . THEME . "images/blank.gif' alt='' id='preview' />\n";
	
	echo "</fieldset>\n";
	echo "<fieldset class='fields'><legend><a name=''>" . $locale ['uc209'] . "</a></legend>\n";
	
	echo $locale ['uc210'] . "<br />\n<br />\n<label><input type='radio' name='option' value='custom'" . ($userdata ['user_avatar'] ? " CHECKED" : "") . ">" . $locale ['uc213'] . "</label>\n";
	echo "<br />\n<br />\n" . $locale ['uc211'] . "<br /><input type='text' name='avatarWeb' value='http://www.' style='width:300px;'>\n";
	echo "<br />\n<br />\n" . $locale ['uc212'] . "<br /><input type='file' name='user_avatar' style='width:300px;'>\n";
	
	echo "</fieldset>\n";
	echo "<br />\n<input type='submit' name='update_profile' class='button' value='" . $locale ['uc201'] . "'>\n";
	echo "</div>\n</td>\n</tr>\n</form>\n";
	
/* User CP -> Buddies */
} elseif ($section == "buddies" && $fb4 ['buddy_enable']) {
	
	if (isset ( $_POST ['username'] )) {
		
		$username = stripinput ( $_POST ['username'] );
		if ($username == $userdata ['user_name'])
			redirect ( FUSION_SELF . "?section=buddies&status=notfound" );
		$rows = dbcount ( "(user_id)", DB_USERS, "user_name='$username'" );
		if (! $rows)
			redirect ( FUSION_SELF . "?section=buddies&status=notfound" );
		$data = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_name='$username'" ) );
		if (dbcount ( "(buddy_id)", DB_PREFIX . "fb_buddies", "(buddy_user='" . $userdata ['user_id'] . "' and buddy_buddy='" . $data ['user_id'] . "') or 
				(buddy_user='" . $data ['user_id'] . "' and buddy_buddy='" . $userdata ['user_id'] . "')" ))
			redirect ( FUSION_SELF . "?section=buddies" );
		if (! checkIgnore ( $data ['user_id'] )) {
			$result = dbquery ( "insert into " . DB_PREFIX . "fb_buddies (buddy_user, buddy_buddy, buddy_approved, buddy_request) values('" . $userdata ['user_id'] . "', '" . $data ['user_id'] . "', '0', '" . time () . "')" );
			$subject = $locale ['uc238'] . $userdata ['user_name'];
			$message = $locale ['uc239'];
			$message = str_replace ( array ("{USER}", "{URL}" ), array ($userdata ['user_name'], $settings ['siteurl'] . "infusions/fusionboard4/usercp.php?section=buddies" ), $message );
			$result = dbquery ( "insert into " . DB_MESSAGES . " (message_to, message_from, message_subject, message_message, message_smileys, message_read, message_datestamp, message_folder) 
					VALUES('" . $data ['user_id'] . "', '" . $userdata ['user_id'] . "', '$subject', '$message', '0', '0', '" . time () . "', '0')" );
		}
		redirect ( FUSION_SELF . "?section=buddies&status=sent" );
	
	}
	
	if (isset ( $_GET ['accept'] ) && isNum ( $_GET ['accept'] )) {
		
		$rows = dbcount ( "(buddy_id)", DB_PREFIX . "fb_buddies", "buddy_id='" . $_GET ['accept'] . "' and buddy_buddy='" . $userdata ['user_id'] . "'" );
		if (! $rows)
			redirect ( FUSION_SELF . "?section=buddies" );
		$query = dbquery ( "update " . DB_PREFIX . "fb_buddies set buddy_approved='1', buddy_added='" . time () . "' where buddy_id='" . $_GET ['accept'] . "'" );
		if ($query)
			redirect ( FUSION_SELF . "?section=buddies&status=accepted" );
	
	}
	
	if (isset ( $_GET ['deny'] ) && isNum ( $_GET ['deny'] )) {
		
		$rows = dbcount ( "(buddy_id)", DB_PREFIX . "fb_buddies", "buddy_id='" . $_GET ['deny'] . "' and buddy_buddy='" . $userdata ['user_id'] . "' where buddy_id='" . $_GET ['deny'] . "'" );
		if (! $rows)
			redirect ( FUSION_SELF . "?section=buddies" );
		$query = dbquery ( "delete from " . DB_PREFIX . "fb_buddies where buddy_id='" . $_GET ['deny'] . "'" );
		redirect ( FUSION_SELF . "?section=buddies&status=denied" );
	
	}
	
	if (isset ( $_GET ['cancel'] ) && isNum ( $_GET ['cancel'] )) {
		
		$rows = dbcount ( "(buddy_id)", DB_PREFIX . "fb_buddies", "buddy_id='" . $_GET ['cancel'] . "' and (buddy_buddy='" . $userdata ['user_id'] . "' or buddy_user='" . $userdata ['user_id'] . "')" );
		if (! $rows)
			redirect ( FUSION_SELF . "?section=buddies" );
		$query = dbquery ( "delete from " . DB_PREFIX . "fb_buddies where buddy_id='" . $_GET ['cancel'] . "'" );
		redirect ( FUSION_SELF . "?section=buddies&status=canceled" );
	
	}
	
	if (isset ( $_GET ['status'] )) {
		if ($_GET ['status'] == "notfound") {
			$status = "<div class='admin-message'>" . $locale ['uc227'] . "</div>";
		}
		if ($_GET ['status'] == "sent") {
			$status = "<div class='admin-message'>" . $locale ['uc228'] . "</div>";
		}
		if ($_GET ['status'] == "accepted") {
			$status = "<div class='admin-message'>" . $locale ['uc235'] . "</div>";
		}
		if ($_GET ['status'] == "denied") {
			$status = "<div class='admin-message'>" . $locale ['uc236'] . "</div>";
		}
		if ($_GET ['status'] == "canceled") {
			$status = "<div class='admin-message'>" . $locale ['uc237'] . "</div>";
		}
	} else {
		$status = "";
	}
	
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_buddies where (buddy_user='" . $userdata ['user_id'] . "' or buddy_buddy='" . $userdata ['user_id'] . "') and buddy_approved='1'" );
	$rows = dbrows ( $result );
	
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc220'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc220'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'>$status<div align='center'" . ($rows > 4 ? " style='height:200px;overflow-y:scroll;'" : "") . ">\n";
	echo "<fieldset class='fields' style='width:500px;'>\n";
	echo "<legend><a name=''>" . $locale ['uc220'] . "</a></legend>\n";
	
	if ($rows) {
		
		echo "<table width='100%' cellspacing='0' cellpadding='5' border='0'>\n<tr>\n";
		$columns = 2;
		$counter = 0;
		while ( $data = dbarray ( $result ) ) {
			if ($counter != 0 && ($counter % $columns == 0)) {
				echo "</tr>\n<tr>\n";
			}
			if ($data ['buddy_user'] !== $userdata ['user_id']) {
				$user = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_id='" . $data ['buddy_user'] . "'" ) );
			} else {
				$user = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_id='" . $data ['buddy_buddy'] . "'" ) );
			}
			echo "<td>\n<div class='tbl2 users'>\n";
			if ($user ['user_avatar']) {
				list ( $width, $height ) = getimagesize ( IMAGES . "avatars/" . $user ['user_avatar'] );
				$new_width = 70;
				$new_height = ($height * ($new_width / $height));
				echo "<div style='float:left;height:70px;'>\n<img src='" . IMAGES . "avatars/" . $user ['user_avatar'] . "' alt='' style='padding-right:5px;width:" . $new_width . "px;height:" . $new_height . "px'>\n</div>\n";
			} else {
				echo "<div style='float:left;height:70px;'>\n<img src='" . IMAGES . "noav.gif' alt='' style='padding-right:5px;width:70px;height:70px'>\n</div>\n";
			}
			echo "<div style='float:right;'><a href='" . FUSION_SELF . "?section=buddies&amp;cancel=" . $data ['buddy_id'] . "'>" . $locale ['uc232'] . "</a></div>\n";
			echo "<a href='" . BASEDIR . "profile.php?lookup=" . $user ['user_id'] . "'>" . showLabel ( $user ['user_id'] ) . "</a>\n";
			if ($fb4 ['user_titles']) {
				echo "<br />" . showtitle ( $user ) . "\n";
			}
			echo "<br /><span class='small'>Last Seen: " . timepassed ( $user ['user_lastvisit'], false ) . "</span><br /><br />\n";
			echo "</div>\n</td>\n";
			$counter ++;
		}
		echo "</tr>\n</table>\n";
	
	} else {
		
		echo "<div align='center'>" . $locale ['uc223'] . "</div>\n";
	
	}
	
	echo "</fieldset>\n</div>\n</td></tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc222'] . "</td>\n</tr>\n<tr>\n<td class='tbl1'>\n";
	echo "<div align='center'>\n";
	echo "<fieldset class='fields' style='width:500px;'><legend><a name=''>" . $locale ['uc222'] . "</a></legend>\n";
	
	add_to_title ( " :: " . $locale ['uc220'] );
	echo "<form action='" . FUSION_SELF . "?section=buddies' method='post' name='addform'>\n";
	echo $locale ['uc225'] . "<br />\n<br />\n<input type='text' name='username' class='textbox' style='width:200px;'>\n";
	echo "<input type='submit' name='addBuddy' value='" . $locale ['uc222'] . "' class='button'><br /><br />\n" . $locale ['uc226'] . "\n</form>\n";
	
	echo "</fieldset>\n</div>\n</td>\n</tr>\n";
	echo "</table>\n<br /><table width='100%' cellspacing='1' cellpadding='0' class='tbl-border'>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc221'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'><div align='center'>\n<fieldset class='fields' style='width:500px;'><legend><a name=''>" . $locale ['uc221'] . "</a></legend>\n";
	
	$result = dbquery ( "select b.*, u.*, u2.user_id as to_id, u2.user_name as to_name from " . DB_PREFIX . "fb_buddies b
			left join " . DB_USERS . " u on u.user_id=b.buddy_user
			left join " . DB_USERS . " u2 on u2.user_id=b.buddy_buddy
			where (b.buddy_user='" . $userdata ['user_id'] . "' or b.buddy_buddy='" . $userdata ['user_id'] . "') and b.buddy_approved='0'" );
	if (dbrows ( $result )) {
		$i = 0;
		while ( $data = dbarray ( $result ) ) {
			if ($i > 0)
				echo "<br />\n";
			if ($data ['user_id'] == $userdata ['user_id']) {
				echo $locale ['uc229'] . "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['to_id'] . "'>" . $data ['to_name'] . "</a> " . $locale ['uc230'] . " | ";
				echo "<a href='" . FUSION_SELF . "?section=buddies&amp;cancel=" . $data ['buddy_id'] . "'>" . $locale ['uc232'] . "</a>\n";
			} else {
				echo "<a href='" . BASEDIR . "profile.php?lookup='" . $data ['user_id'] . "' style='font-weight:bold;'>" . $data ['user_name'] . "</a>" . $locale ['uc231'] . "\n";
				echo "<a href='" . FUSION_SELF . "?section=buddies&amp;accept=" . $data ['buddy_id'] . "' style='font-weight:bold;'>" . $locale ['uc233'] . "</a> | ";
				echo "<a href='" . FUSION_SELF . "?section=buddies&amp;deny=" . $data ['buddy_id'] . "' style='font-weight:bold;'>" . $locale ['uc234'] . "</a>\n";
			}
			$i ++;
		}
	} else {
		echo "<div align='center'>" . $locale ['uc224'] . "</div>\n";
	}
	
	echo "</fieldset>\n</div>\n</td>\n</tr>\n";
	
/* User CP -> Subscribed Threads */
} elseif ($section = "subscriptions" && (isset ( $_GET ['type'] ) && $_GET ['type'] == "thread")) {
	$result = dbquery ( "
				select t.*, f.*, p.*, tu.user_id, tu.user_name, tu2.user_id as lastpost_id, tu2.user_name as lastpost_name, COUNT(post_id)-1 as replies from " . DB_THREAD_NOTIFY . " tn
				left join " . DB_THREADS . " t on t.thread_id=tn.thread_id
				left join " . DB_FORUMS . " f on f.forum_id=t.forum_id
				left join " . DB_POSTS . " p on p.thread_id=t.thread_id
				left join " . DB_USERS . " tu on tu.user_id=t.thread_author
				left join " . DB_USERS . " tu2 on t.thread_lastuser = tu2.user_id
				where tn.notify_user='" . $userdata ['user_id'] . "' and " . groupaccess ( 'forum_access' ) . "
				group by p.thread_id order by tn.notify_status asc, t.thread_lastpost desc
			" );
	$rows = dbrows ( $result );
	
	$unviewed = dbcount ( "(thread_id)", DB_THREAD_NOTIFY, "notify_user='" . $userdata ['user_id'] . "' and notify_status='0'" );
	
	add_to_title ( " :: " . $locale ['uc158'] );
	
	echo "<tr>\n<td class='tbl2 navtitle'" . ($rows ? " colspan='" . ($fb4 ['post_icons'] ? 6 : 5) . "'" : "") . ">" . $locale ['uc158'] . " ($unviewed)</td>\n</tr>\n";
	
	if ($rows) {
		
		echo "<tr>\n<td class='tbl1 bold'" . ($fb4 ['post_icons'] ? " colspan='2'" : "") . "' style='width:1%'>&nbsp;</td>\n";
		echo "<td class='tbl1 bold'>" . $locale ['uc152'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:150px;'>" . $locale ['uc153'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['uc154'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['uc155'] . "</td>\n</tr>\n";
		
		while ( $data = dbarray ( $result ) ) {
			if ($data ['thread_locked']) {
				$image = get_image ( "folderlock" );
				$titlebold = "";
			} else {
				$thread_match = $data ['thread_id'] . "\|" . $data ['thread_lastpost'] . "\|" . $data ['forum_id'];
				if ($data ['thread_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match ( "(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata ['user_threads'] )) {
						$image = get_image ( "folder" );
						$titlebold = "";
					} else {
						$image = get_image ( "foldernew" );
						$titlebold = "font-weight:bold;";
					}
				} else {
					$image = get_image ( "folder" );
					$titlebold = "";
				}
			}
			
			echo "<tr>\n<td class='tbl1'><img src='$image' alt='' /></td>\n";
			if ($fb4 ['post_icons']) {
				$post_res = dbquery ( "select * from " . DB_PREFIX . "fb_posts where post_id='" . $data ['post_id'] . "'" );
				if (dbrows ( $post_res )) {
					$post_data = dbarray ( $post_res );
					if ($post_data ['post_icon']) {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/" . $post_data ['post_icon'] . "' alt=''>";
					} else {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/page_white.png' alt=''>";
					}
				} else {
					$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/post_icons/page_white.png' alt=''>";
				}
				echo "<td class='tbl1'>$ficon</td>\n";
			}
			
			echo "<td class='tbl2'><span style='font-size:14px;$titlebold'>";
			echo "<a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "' style='text-decoration:underline;'>" . $data ['thread_subject'] . "</a></span><br />\n";
			echo "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['user_id'] . "' class='alt'>" . $data ['user_name'] . "</a><br />\n";
			echo "<a href='" . FORUM . "postify.php?post=off&amp;forum_id=" . $data ['forum_id'] . "&thread_id=" . $data ['thread_id'] . "'>" . $locale ['uc156'] . "</a></td>\n";
			echo "<td class='tbl1'>" . timePassed ( $data ['thread_lastpost'] ) . "<br />\n";
			echo "by <a href='" . BASEDIR . "profile.php?lookup=" . $data ['lastpost_id'] . "'>" . $data ['lastpost_name'] . "</a><br />\n<div style='text-align:right'>";
			echo "<a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "&amp;pid=" . $data ['thread_lastpostid'] . "#post_" . $data ['thread_lastpostid'] . "' title='" . $locale ['fb615'] . "'><b>»»</b></a></div></td>\n";
			echo "<td class='tbl2' style='text-align:center;'>" . number_format ( $data ['replies'] ) . "</td>\n";
			echo "<td class='tbl1' style='text-align:center;'>" . number_format ( $data ['thread_views'] ) . "</td>\n</tr>\n";
		}
	
	} else {
		echo "<tr>\n<td class='tbl1'>\n<div align='center'>" . $locale ['uc159'] . "</div>\n</td>\n</tr>\n";
	}
	
/* User CP -> Groups */
} elseif ($_GET ['section'] == "groups" && $fb4 ['group_enable']) {
	
	add_to_title ( " :: " . $locale ['uc250'] );
	
	include INFUSIONS . "fusionboard4/groups.php";
	
/* User CP -> Requests */
} elseif ($_GET ['section'] == "requests") {
	
	add_to_title ( " :: " . $locale ['uc325'] );
	
	if (isset ( $_GET ['ignore'] ) && isNum ( $_GET ['ignore'] )) {
		$result = dbquery ( "delete from " . DB_PREFIX . "fb_invites where invite_to='" . $userdata ['user_id'] . "' and invite_group='" . $_GET ['ignore'] . "'" );
		redirect ( FUSION_SELF . "?section=requests" );
	}
	
	$requests = dbcount ( "(invite_to)", DB_PREFIX . "fb_invites", "invite_to='" . $userdata ['user_id'] . "'" );
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc325'] . "</td>\n</tr>\n";
	if ($requests) {
		
		$result = dbquery ( "select * from " . DB_PREFIX . "fb_invites where invite_to='" . $userdata ['user_id'] . "'" );
		while ( $data = dbarray ( $result ) ) {
			
			$invite_from = dbarray ( dbquery ( "select * from " . DB_USERS . " where user_id='" . $data ['invite_from'] . "'" ) );
			echo "<tr>\n<td class='tbl1'><div style='float:right;'><a href='" . INFUSIONS . "fusionboard4/groups.php?acceptinvite=" . $data ['invite_group'] . "'>" . $locale ['uc328'] . "</a> :: ";
			echo "<a href='" . FUSION_SELF . "?section=requests&amp;ignore=" . $data ['invite_group'] . "'>" . $locale ['uc329'] . "</a></div>\n";
			echo "<b><a href='" . BASEDIR . "profile.php?lookup=" . $invite_from ['user_id'] . "'>" . $invite_from ['user_name'] . "</a></b>" . $locale ['uc327'] . "<br />\n";
			$group = dbarray ( dbquery ( "select * from " . DB_USER_GROUPS . " where group_id='" . $data ['invite_group'] . "'" ) );
			echo "&nbsp;&nbsp;&raquo; <a href='" . INFUSIONS . "fusionboard4/groups.php?view=" . $group ['group_id'] . "'><b>" . stripslash ( $group ['group_name'] ) . "</b></a></td>\n</tr>\n";
		
		}
	
	} else {
		
		echo "<tr>\n<td class='tbl1'>" . $locale ['uc326'] . "</td>\n</tr>\n";
	
	}
	
/* User CP -> Settings -> Signature */
} elseif ($_GET ['section'] == "signature") {
	if (isset ( $_POST ['update_profile'] )) {
		$result = dbquery ( "update " . DB_USERS . " set user_sig='" . (isset ( $_POST ['user_sig'] ) ? stripinput ( trim ( $_POST ['user_sig'] ) ) : "") . "' where user_id='" . $userdata ['user_id'] . "'" );
		redirect ( FUSION_SELF . "?section=signature&status=updated" );
	}
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "updated") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $locale ['uc334'] . "</td>\n</tr>\n";
	}
	add_to_title ( " :: " . $locale ['uc108'] );
	require_once INCLUDES . "bbcode_include.php";
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc108'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc331'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' style='padding:7px;'><form action='" . FUSION_SELF . "?section=signature' method='post' name='sigform'>\n";
	echo nl2br ( parseubb ( parsesmileys ( $userdata ['user_sig'] ), "b|i|u||center|small|url|mail|img|color" ) ) . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc332'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' align='center'><textarea name='user_sig' cols='60' rows='5' class='textbox' style='width:295px'>" . (isset ( $userdata ['user_sig'] ) ? $userdata ['user_sig'] : "") . "</textarea><br />\n";
	echo display_bbcodes ( "300px", "user_sig", "sigform", "smiley|b|i|u||center|small|url|mail|img|color" ) . "<br />";
	echo "<input type='submit' name='update_profile' value='" . $locale ['uc333'] . "' class='button'>\n</form>\n</td>\n</tr>\n";
	/* User CP -> Settings -> Email & Password */
} elseif ($_GET ['section'] == "email") {
	add_to_title ( " :: " . $locale ['uc109'] );
	if (isset ( $_POST ['update_profile'] )) {
		include INFUSIONS . "fusionboard4/includes/update_pass.php";
	}
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "updated") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $locale ['uc343'] . "</td>\n</tr>\n";
	}
	if (isset ( $error ) && $error) {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $error . "</td>\n</tr>\n";
	}
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc109'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' style='padding:10px;'>\n";
	include LOCALE . LOCALESET . "edit_profile.php";
	include LOCALE . LOCALESET . "user_fields.php";
	echo "<form action='" . FUSION_SELF . "?section=email' method='post' name='inputform'>";
	echo "<div align='center'>\n<fieldset class='fields'><legend><a name=''>" . $locale ['uc340'] . "</a></legend>\n";
	echo $locale ['410'] . "<br />\n<br /><table width='100%' cellspacing='3' cellpadding='0' border='0'>\n";
	//echo "<tr>\n<td>".$locale['u001'].":<span style='color:#ff0000'>*</span></td>\n<td><input type='text' name='user_name' value='".$userdata['user_name']."' maxlength='30' class='textbox' style='width:150px;' /></td>\n</tr>\n";
	echo "<tr>\n<td width='25%'>" . $locale ['420'] . ":<span style='color:#ff0000'>*</span></td>\n<td><input type='password' name='user_password' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
	echo "<tr>\n<td>" . $locale ['u003'] . ":</td>\n<td><input type='password' name='user_new_password' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
	echo "<tr>\n<td>" . $locale ['u004'] . ":</td>\n<td><input type='password' name='user_new_password2' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
	
	echo "<tr>\n<td>" . $locale ['u005'] . ":</td>\n<td><input type='text' name='user_email' value='" . $userdata ['user_email'] . "' maxlength='100' class='textbox' style='width:150px;' /></td>\n</tr>\n";
	echo "</table>\n";
	echo "</fieldset>\n";
	if (iADMIN) {
		echo "<fieldset class='fields'><legend><a name=''>" . $locale ['uc341'] . "</a></legend>\n";
		echo "<table width='100%' cellspacing='3' cellpadding='0' border='0'>\n";
		if ($userdata ['user_admin_password']) {
			echo "<tr>\n<td>" . $locale ['421'] . ":</td>\n<td><input type='password' name='user_admin_password' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
		}
		echo "<tr>\n<td width='33%'>" . $locale ['422'] . ":</td>\n<td><input type='password' name='user_new_admin_password' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
		echo "<tr>\n<td>" . $locale ['423'] . ":</td>\n<td><input type='password' name='user_new_admin_password2' maxlength='20' class='textbox' style='width:150px;' /></td>\n</tr>\n";
		echo "</table><input type='hidden' name='user_hash' value='" . $userdata ['user_password'] . "' />\n";
		echo "</fieldset>\n";
	}
	echo "<input type='submit' name='update_profile' value='" . $locale ['uc342'] . "' class='button'>\n";
	echo "</div>\n</form>\n</td>\n</tr>\n";
	/* User CP -> Settings -> Options */
} elseif ($_GET ['section'] == "options") {
	add_to_title ( " :: " . $locale ['uc110'] );
	$user_data = $userdata;
	if (isset ( $_POST ['update_profile'] )) {
		$result = dbquery ( "SELECT * FROM " . DB_USER_FIELDS . " WHERE field_group='3' and field_name!='user_sig' ORDER BY field_order" );
		$db_values = "user_name='" . $userdata ['user_name'] . "'";
		if (dbrows ( $result )) {
			$profile_method = "validate_update";
			while ( $data = dbarray ( $result ) ) {
				if (file_exists ( LOCALE . LOCALESET . "user_fields/" . $data ['field_name'] . ".php" )) {
					include LOCALE . LOCALESET . "user_fields/" . $data ['field_name'] . ".php";
				}
				if (file_exists ( INCLUDES . "user_fields/" . $data ['field_name'] . "_include.php" )) {
					include INCLUDES . "user_fields/" . $data ['field_name'] . "_include.php";
				}
			}
		}
		$result = dbquery ( "update " . DB_USERS . " set $db_values where user_id='" . $userdata ['user_id'] . "'" );
		if ($result)
			redirect ( FUSION_SELF . "?section=options&status=updated" );
	}
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "updated") {
		echo "<tr>\n<td class='tbl1' style='padding:20px; text-align:center; font-weight:bold;'>" . $locale ['uc345'] . "</td>\n</tr>\n";
	}
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc110'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'>\n";
	echo "<form action='" . FUSION_SELF . "?section=options' method='post' name='inputform'>";
	echo "<div align='center'>\n<fieldset class='fields'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'>\n";
	
	include LOCALE . LOCALESET . "user_fields.php";
	
	$profile_method = "input";
	$result3 = dbquery ( "SELECT * FROM " . DB_USER_FIELDS . " WHERE field_group='3' and field_name!='user_sig' ORDER BY field_order" );
	if (dbrows ( $result3 )) {
		while ( $data3 = dbarray ( $result3 ) ) {
			if (file_exists ( LOCALE . LOCALESET . "user_fields/" . $data3 ['field_name'] . ".php" )) {
				include LOCALE . LOCALESET . "user_fields/" . $data3 ['field_name'] . ".php";
			}
			if (file_exists ( INCLUDES . "user_fields/" . $data3 ['field_name'] . "_include.php" )) {
				include INCLUDES . "user_fields/" . $data3 ['field_name'] . "_include.php";
			}
		}
	}
	
	echo "</table>\n</fieldset>\n<input type='submit' name='update_profile' class='button' value='" . $locale ['uc342'] . "'>\n</form>\n";
	echo "</td>\n</tr>\n";
	
/* User CP -> Settings -> Ignore List */
} elseif ($_GET ['section'] == "ignore") {
	add_to_title ( " :: " . $locale ['uc111'] );
	if (isset ( $_POST ['ignore_ignored'] )) {
		$ignore = stripinput ( $_POST ['ignore_ignored'] );
		$search = dbquery ( "select * from " . DB_USERS . " where user_name='$ignore'" );
		if (! dbrows ( $search ))
			redirect ( FUSION_SELF . "?section=ignore&status=notfound" );
		$data = dbarray ( $search );
		if (dbcount ( "(ignore_user)", DB_PREFIX . "fb_ignore", "ignore_user='" . $userdata ['user_id'] . "' and ignore_ignored='" . $data ['user_id'] . "'" )) {
			redirect ( FUSION_SELF . "?section=ignore" );
		}
		$result = dbquery ( "insert into " . DB_PREFIX . "fb_ignore (ignore_user, ignore_ignored) VALUES('" . $userdata ['user_id'] . "', '" . $data ['user_id'] . "')" );
		redirect ( FUSION_SELF . "?section=ignore&status=added" );
	}
	if (isset ( $_GET ['unignore'] ) && isNum ( $_GET ['unignore'] )) {
		$result = dbquery ( "delete from " . DB_PREFIX . "fb_ignore where ignore_user='" . $userdata ['user_id'] . "' and ignore_ignored='" . $_GET ['unignore'] . "'" );
		redirect ( FUSION_SELF . "?section=ignore" );
	}
	echo "<tr>\n<td class='tbl2 navtitle'>" . $locale ['uc111'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc346'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1'>\n";
	$result = dbquery ( "select * from " . DB_PREFIX . "fb_ignore i
			left join " . DB_USERS . " u on u.user_id=i.ignore_ignored
			where i.ignore_user='" . $userdata ['user_id'] . "'" );
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "notfound") {
		$status = "<div class='admin-message'>" . $locale ['uc348'] . "</div><br />";
	}
	if (isset ( $_GET ['status'] ) && $_GET ['status'] == "added") {
		$status = "<div class='admin-message'>" . $locale ['uc352'] . "</div><br />";
	}
	if (dbrows ( $result )) {
		while ( $data = dbarray ( $result ) ) {
			echo "<a href='" . BASEDIR . "profile.php?lookup=" . $data ['user_id'] . "'>" . $data ['user_name'] . "</a> :: <a href='" . FUSION_SELF . "?section=ignore&amp;unignore=" . $data ['user_id'] . "'>" . $locale ['uc354'] . "</a><br />\n";
		}
	} else {
		echo "<div align='center'>" . $locale ['uc349'] . "</div>\n";
	}
	echo "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl2 navsection'>" . $locale ['uc347'] . "</td>\n</tr>\n";
	echo "<tr>\n<td class='tbl1' align='center'>\n";
	echo "<div align='center' style='width:350px;'>\n<form action='" . FUSION_SELF . "?section=ignore' method='post' name='ignoreform'>\n";
	echo $locale ['uc353'] . "<br />\n<br />\n";
	echo $locale ['uc350'] . " <input type='text' name='ignore_ignored' class='textbox' style='width:220px;'>&nbsp;";
	echo "<input type='submit' name='goIgnore' class='button' value='" . $locale ['uc351'] . "'>\n";
	echo "</form>\n</div>\n";
	echo "</td>\n</tr>\n";
	/* User CP -> Private Messages */
} elseif ($_GET ['section'] == "pm") {
	
	include INFUSIONS . "fusionboard4/includes/messaging.php";
	
/* User CP -> Subscriptions -> Forums */
} elseif ($section = "subscriptions" && (isset ( $_GET ['type'] ) && $_GET ['type'] == "forum")) {
	
	add_to_title ( " :: " . $locale ['uc355'] );
	
	$result = dbquery ( "
				select t.*, f.*, p.*, tu.user_id, tu.user_name, tu2.user_id as lastpost_id, tu2.user_name as lastpost_name, COUNT(t.thread_author) as posts, COUNT(t.thread_id) as threads from " . DB_PREFIX . "fb_forum_notify fn
				left join " . DB_THREADS . " t on t.forum_id=fn.forum_id
				left join " . DB_FORUMS . " f on f.forum_id=t.forum_id
				left join " . DB_POSTS . " p on p.thread_id=t.thread_id
				left join " . DB_USERS . " tu on tu.user_id=t.thread_author
				left join " . DB_USERS . " tu2 on t.thread_lastuser = tu2.user_id
				where fn.notify_user='" . $userdata ['user_id'] . "' and " . groupaccess ( 'forum_access' ) . "
				group by f.forum_id order by fn.notify_status asc, t.thread_lastpost desc
			" );
	$rows = dbrows ( $result );
	
	$unviewed = dbcount ( "(forum_id)", DB_PREFIX . "fb_forum_notify", "notify_user='" . $userdata ['user_id'] . "' and notify_status='0'" );
	
	echo "<tr>\n<td class='tbl2 navtitle'" . ($rows ? " colspan='" . ($fb4 ['forum_icons'] ? 6 : 5) . "'" : "") . ">" . $locale ['uc355'] . " ($unviewed)</td>\n</tr>\n";
	
	if ($rows) {
		
		echo "<tr>\n<td class='tbl1 bold'" . ($fb4 ['forum_icons'] ? " colspan='2'" : "") . "' style='width:1%'>&nbsp;</td>\n";
		echo "<td class='tbl1 bold'>" . $locale ['uc152'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:150px;'>" . $locale ['uc153'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['fb901'] . "</td>\n";
		echo "<td class='tbl1 bold' style='width:1%'>" . $locale ['uc356'] . "</td>\n</tr>\n";
		
		while ( $data = dbarray ( $result ) ) {
			if ($data ['thread_locked']) {
				$image = get_image ( "folderlock" );
				$titlebold = "";
			} else {
				$thread_match = $data ['thread_id'] . "\|" . $data ['thread_lastpost'] . "\|" . $data ['forum_id'];
				if ($data ['thread_lastpost'] > $lastvisited) {
					if (iMEMBER && preg_match ( "(^\.{$thread_match}$|\.{$thread_match}\.|\.{$thread_match}$)", $userdata ['user_threads'] )) {
						$image = get_image ( "folder" );
						$titlebold = "";
					} else {
						$image = get_image ( "foldernew" );
						$titlebold = "font-weight:bold;";
					}
				} else {
					$image = get_image ( "folder" );
					$titlebold = "";
				}
			}
			
			echo "<tr>\n<td class='tbl1'><img src='$image' alt='' /></td>\n";
			if ($fb4 ['forum_icons']) {
				$forum_res = dbquery ( "select * from " . DB_PREFIX . "fb_forums where forum_id='" . $data ['forum_id'] . "'" );
				if (dbrows ( $forum_res )) {
					$forum_data = dbarray ( $forum_res );
					if ($forum_data ['forum_icon']) {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/forum_icons/" . $forum_data ['forum_icon'] . "' alt=''>";
					} else {
						$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/forum_icons/folder.png' alt=''>";
					}
				} else {
					$ficon = "<img src='" . INFUSIONS . "fusionboard4/images/forum_icons/folder.png' alt=''>";
				}
				echo "<td class='tbl1'>$ficon</td>\n";
			}
			
			echo "<td class='tbl2'><span style='font-size:14px;$titlebold'>";
			echo "<a href='" . FORUM . "viewforum.php?forum_id=" . $data ['forum_id'] . "' style='text-decoration:underline;'>" . $data ['forum_name'] . "</a></span><br />\n";
			echo "<a href='" . FORUM . "postify.php?post=none&amp;forum=off&amp;forum_id=" . $data ['forum_id'] . "'>" . $locale ['uc156'] . "</a></td>\n";
			echo "<td class='tbl1'><a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "'>" . trimlink ( stripslash ( $data ['thread_subject'] ), 20 ) . "</a><br />\n";
			echo timePassed ( $data ['thread_lastpost'] ) . "<br />\n";
			echo "by <a href='" . BASEDIR . "profile.php?lookup=" . $data ['lastpost_id'] . "'>" . $data ['lastpost_name'] . "</a>\n<div style='text-align:right'>";
			echo "<a href='" . FORUM . "viewthread.php?thread_id=" . $data ['thread_id'] . "&amp;pid=" . $data ['thread_lastpostid'] . "#post_" . $data ['thread_lastpostid'] . "' title='" . $locale ['fb615'] . "'><b>»»</b></a></div></td>\n";
			echo "<td class='tbl2' style='text-align:center;'>" . number_format ( $data ['threads'] - 1 ) . "</td>\n";
			echo "<td class='tbl1' style='text-align:center;'>" . number_format ( $data ['posts'] ) . "</td>\n</tr>\n";
		}
	
	} else {
		echo "<tr>\n<td class='tbl1'>\n<div align='center'>" . $locale ['uc357'] . "</div>\n</td>\n</tr>\n";
	}
}

echo "</table>\n";
/* User CP Sections End */

echo "</td>\n</tr>\n</table>\n";

closetable ();

require_once THEMES . "templates/footer.php";
?>