<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com

	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/
require_once "../../maincore.php";
require_once THEMES . "templates/admin_header.php";
require_once LOCALE . LOCALESET . "admin/settings.php";

if (! checkrights ( "FB4" ) || ! defined ( "iAUTH" ) || $_GET ['aid'] != iAUTH) {
	redirect ( "../index.php" );
}

$current_version = "4.0.1";

// Check if locale file is available matching the current site locale setting.
if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	// Load the locale file matching the current site locale setting.
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	// Load the infusion's default locale file.
	include INFUSIONS . "fusionboard4/locale/English.php";
}

include INFUSIONS . "fusionboard4/includes/func.php";

$_GET ['section'] = (isset ( $_GET ['section'] ) ? $_GET ['section'] : "titles");

opentable ( $locale ['fb202'] );

renderNav ( false, false, array (INFUSIONS . "fusionboard4/admin.php" . $aidlink . "&amp;section=" . $_GET ['section'], $locale ['fb202'] ) );

echo "<script src='" . INFUSIONS . "fusionboard4/includes/js/fb4.js' type='text/javascript'></script>
	<br /><table cellpadding='0' cellspacing='1' class='tbl-border center'>\n<tr>\n";
echo "<td class='" . (preg_match ( "/titles/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=titles'>" . $locale ['fb200'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/labels/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=labels'>" . $locale ['fb821'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/ratings/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=ratings'>" . $locale ['fb850'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/awards/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=awards'>" . $locale ['fb201'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/images/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=images'>" . $locale ['fb204'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/forums/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=forums'>" . $locale ['fb206'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/warnings/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=warnings'>" . $locale ['fbw103'] . "</a></span></td>\n";
echo "<td class='" . (preg_match ( "/settings/i", $_GET ['section'] ) ? "tbl1" : "tbl2") . "' style='padding-left:10px;padding-right:10px;'><span class='small'><a href='" . FUSION_SELF . $aidlink . "&amp;section=settings'>" . $locale ['fb203'] . "</a></span></td>\n";
echo "</tr>\n</table>\n<br />\n";

closetable ();

if ($_GET ['section'] == "titles") {

	if (isset ( $_POST ['goTitle'] )) {

		$title_title = addslash ( stripinput ( $_POST ['title_title'] ) );
		$title_access = (isNum ( $_POST ['title_access'] ) ? $_POST ['title_access'] : "101");

		if (isset ( $_GET ['update'] ) && isNum ( $_GET ['update'] )) {

			$result = dbquery ( "select * from " . $db_prefix . "fb_titles where title_id='" . $_GET ['update'] . "'" );
			if (! dbrows ( $result ))
				redirect ( FUSION_SELF . $aidlink );

			$query = dbquery ( "update " . $db_prefix . "fb_titles set title_title='$title_title', title_access='$title_access' where title_id='" . $_GET ['update'] . "'" );
			redirect ( FUSION_SELF . $aidlink );

		} else {

			$query = dbquery ( "insert into " . $db_prefix . "fb_titles (title_title, title_status, title_access) VALUES('$title_title', '1', '$title_access')" );
			redirect ( FUSION_SELF . $aidlink );

		}

	}

	if (isset ( $_GET ['del'] ) && isnum ( $_GET ['del'] )) {

		$query = dbquery ( "delete from " . $db_prefix . "fb_titles where title_id='" . $_GET ['del'] . "'" );
		redirect ( FUSION_SELF . $aidlink );

	}

	if (isset ( $_GET ['disable'] ) && isnum ( $_GET ['disable'] )) {

		$query = dbquery ( "update " . $db_prefix . "fb_titles set title_status='0' where title_id='" . $_GET ['disable'] . "'" );
		redirect ( FUSION_SELF . $aidlink );

	}

	if (isset ( $_GET ['enable'] ) && isnum ( $_GET ['enable'] )) {

		$query = dbquery ( "update " . $db_prefix . "fb_titles set title_status='1' where title_id='" . $_GET ['enable'] . "'" );
		redirect ( FUSION_SELF . $aidlink );

	}

	if (isset ( $_GET ['edit'] ) && isnum ( $_GET ['edit'] )) {

		$result = dbquery ( "select * from " . $db_prefix . "fb_titles where title_id='" . $_GET ['edit'] . "'" );
		if (! dbrows ( $result ))
			redirect ( FUSION_SELF . $aidlink );
		$data = dbarray ( $result );

		$title = stripslash ( $data ['title_title'] );
		$access = $data ['title_access'];

		$action = FUSION_SELF . $aidlink . "&update=" . $_GET ['edit'];
		$table = $locale ['fb301'];
		$button = $locale ['fb303'];

	} else {

		$title = "";
		$access = "101";

		$action = FUSION_SELF . $aidlink;
		$table = $locale ['fb300'];
		$button = $locale ['fb302'];

	}

	opentable ( $table );

	$user_groups = getusergroups ();
	$access_opts = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($access == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$access_opts .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	echo "<form action='$action' method='post' name='titleForm'>
		<table width='250' cellspacing='1' cellpadding='0' class='tbl-border center'>
		<tr>
			<td class='tbl1'>" . $locale ['fb304'] . ":</td>
			<td class='tbl2'>
				<input type='text' name='title_title' class='textbox' value='$title' style='width:150px;'>
			</td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb305'] . ":</td>
			<td class='tbl2'>
				<select name='title_access' class='textbox' style='width:150px;'>
				$access_opts
				</select>
			</td>
		</tr>
		<tr>
			<td class='tbl2' style='text-align:center;' colspan='2'>
				<input type='submit' name='goTitle' class='button' value='$button'>
			</td>
		</tr>
		</table>
		</form>\n";

	closetable ();

	opentable ( $locale ['fb200'] );

	$result = dbquery ( "select * from " . $db_prefix . "fb_titles order by title_access desc, title_title asc" );
	if (dbrows ( $result )) {

		echo "<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
			<tr>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb304'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb305'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb306'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb307'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb308'] . "</td>
			</tr>\n";

		while ( $data = dbarray ( $result ) ) {

			echo "<tr>
					<td class='tbl1'>" . stripslash ( $data ['title_title'] ) . "</td>
					<td class='tbl1'>" . getgroupname ( $data ['title_access'] ) . "</td>
					<td class='tbl1'>" . number_format ( dbrows ( dbquery ( "select * from " . DB_PREFIX . "users where user_title='" . $data ['title_id'] . "'" ) ) ) . "</td>
					<td class='tbl1'>" . ($data ['title_status'] ? "<a href='" . FUSION_SELF . $aidlink . "&amp;disable=" . $data ['title_id'] . "'>" . $locale ['fb311'] . "</a>" : "<a href='" . FUSION_SELF . $aidlink . "&amp;enable=" . $data ['title_id'] . "'>" . $locale ['fb312'] . "</a>") . "
					</td>
					<td class='tbl1'>
						<a href='" . FUSION_SELF . $aidlink . "&amp;del=" . $data ['title_id'] . "'>" . $locale ['fb310'] . "</a> &middot;
						<a href='" . FUSION_SELF . $aidlink . "&amp;edit=" . $data ['title_id'] . "'>" . $locale ['fb309'] . "</a>
					</td>
				</tr>\n";

		}

		echo "</table>\n";

	} else {

		echo "<div style='text-align:center;'>" . $locale ['fb313'] . "</div>\n";

	}

	echo "<div style='text-align:right; margin-top:5px;'>" . showPoweredBy () . "</div>";
	closetable ();

} elseif ($_GET ['section'] == "awards") {

	opentable ( $locale ['fb710'] );
	if (isset ( $_POST ['user_id'] ) && isNum ( $_POST ['user_id'] )) {
		$image = stripinput ( $_POST ['award_image'] );
		$desc = addslash ( stripinput ( $_POST ['award_desc'] ) );
		$result = dbquery ( "insert into " . DB_PREFIX . "fb_awards (award_user, award_image, award_desc) VALUES('" . $_POST ['user_id'] . "', '$image', '$desc')" );
		redirect ( FUSION_SELF . $aidlink . "&section=awards&status=add" );
	}
	if (isset ( $_GET ['del'] ) && isNum ( $_GET ['del'] )) {
		$query = dbquery ( "delete from " . DB_PREFIX . "fb_awards where award_id='" . $_GET ['del'] . "'" );
		redirect ( FUSION_SELF . $aidlink . "&section=awards&status=del" );
	}
	if (! isset ( $_POST ['search_users'] ) || ! isset ( $_POST ['search_criteria'] )) {
		echo "<form name='searchform' method='post' action='" . FUSION_SELF . $aidlink . "&section=awards'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='450' class='center'>\n";
		echo "<tr>\n<td align='center' class='tbl'>" . $locale ['fb711'] . "<br /><br />\n";
		echo "<input type='text' name='search_criteria' class='textbox' style='width:300px' />\n</td>\n";
		echo "</tr>\n<tr>\n<td align='center' class='tbl'>\n";
		echo "<label><input type='radio' name='search_type' value='user_name' checked='checked' />" . $locale ['fb713'] . "</label>\n";
		echo "<label><input type='radio' name='search_type' value='user_id' />" . $locale ['fb712'] . "</label></td>\n";
		echo "</tr>\n<tr>\n<td align='center' class='tbl'><input type='submit' name='search_users' value='" . $locale ['fb714'] . "' class='button' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
	} elseif (isset ( $_POST ['search_users'] ) && isset ( $_POST ['search_criteria'] )) {
		$mysql_search = "";
		if ($_POST ['search_type'] == "user_id" && isnum ( $_POST ['search_criteria'] )) {
			$mysql_search .= "user_id='" . $_POST ['search_criteria'] . "' ";
		} elseif ($_POST ['search_type'] == "user_name" && preg_match ( "/^[-0-9A-Z_@\s]+$/i", $_POST ['search_criteria'] )) {
			$mysql_search .= "user_name LIKE '" . $_POST ['search_criteria'] . "%' ";
		}
		if ($mysql_search) {
			$result = dbquery ( "SELECT user_id, user_name FROM " . DB_USERS . " WHERE " . $mysql_search . " ORDER BY user_name" );
		}
		if (isset ( $result ) && dbrows ( $result )) {
			echo "<form name='awardForm' method='post' action='" . FUSION_SELF . $aidlink . "&section=awards'>\n";
			echo "<table cellpadding='0' cellspacing='1' width='450' class='tbl-border center'>\n";
			$i = 0;
			$users = "";
			while ( $data = dbarray ( $result ) ) {
				$row_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
				$i ++;
				$users .= "<tr>\n<td class='$row_color'><label><input type='radio' name='user_id' value='" . $data ['user_id'] . "' onClick='awardRender(this.value);' /> " . $data ['user_name'] . "</label></td>\n</tr>";
			}
			if ($i > 0) {
				$awardImages = makefileopts ( makefilelist ( INFUSIONS . "fusionboard4/images/awards/", ".|..|index.php" ) );
				echo "<tr>\n<td class='tbl2'><strong>" . $locale ['fb713'] . "</strong></td>\n</tr>\n";
				echo $users . "<tr><td class='tbl1' style='font-weight:bold; text-align:center;'>" . $locale ['fb721'] . "</td></tr>
				<tr><td class='tbl2'>" . $locale ['fb723'] . " <select name='award_image' class='textbox' style='margin:2px;' onChange='document.getElementById(\"imagepreview\").src=\"" . INFUSIONS . "fusionboard4/images/awards/\"+this.value;'>$awardImages</select>&nbsp;
				<img src='" . INFUSIONS . "fusionboard4/images/awards/award_star_bronze_1.png' alt='' id='imagepreview'><br />
				" . $locale ['fb724'] . " <input type='text' name='award_desc' class='textbox' style='margin:2px;'><br />
				<input type='submit' name='addAward' class='button' value='" . $locale ['fb725'] . "' style='margin:2px;'></td></tr>
				<tr><td class='tbl1' style='font-weight:bold; text-align:center;'>" . $locale ['fb722'] . "</td></tr>
				<tr>\n<td align='center' class='tbl'>\n";
				echo "<div id='awardContent'></div>\n";
				echo "</td>\n</tr>\n";
			} else {
				echo "<tr>\n<td align='center' class='tbl'>" . $locale ['fb718'] . "<br /><br />\n";
				echo "<a href='" . FUSION_SELF . $aidlink . "&amp;section=awards'>" . $locale ['fb719'] . "</a>\n</td>\n</tr>\n";
			}
			echo "</table>\n</form>\n";
		} else {
			echo "<table cellpadding='0' cellspacing='1' width='450' class='tbl-border center'>\n";
			echo "<tr>\n<td align='center' class='tbl'>" . $locale ['fb718'] . "<br /><br />\n";
			echo "<a href='" . FUSION_SELF . $aidlink . "&amp;section=awards'>" . $locale ['fb719'] . "</a>\n</td>\n</tr>\n</table>\n";
		}
	}
	echo "<div style='text-align:right; margin-top:5px;'>" . showPoweredBy () . "</div>";
	closetable ();

} elseif ($_GET ['section'] == "images") {

	include LOCALE . LOCALESET . "admin/image_uploads.php";

	if (isset ( $_GET ['ifolder'] ) && $_GET ['ifolder'] == "icons") {
		$afolder = INFUSIONS . "fusionboard4/images/forum_icons/";
	} elseif (isset ( $_GET ['ifolder'] ) && $_GET ['ifolder'] == "awards") {
		$afolder = INFUSIONS . "fusionboard4/images/awards/";
	} elseif (isset ( $_GET ['ifolder'] ) && $_GET ['ifolder'] == "post") {
		$afolder = INFUSIONS . "fusionboard4/images/post_icons/";
	} else {
		$_GET ['ifolder'] = "icons";
		$afolder = INFUSIONS . "fusionboard4/images/forum_icons/";
	}

	if (isset ( $_GET ['status'] )) {
		if ($_GET ['status'] == "del") {
			$title = $locale ['400'];
			$message = "<strong>" . $locale ['401'] . "</strong>";
		} elseif ($_GET ['status'] == "upn") {
			$title = $locale ['420'];
			$message = "<strong>" . $locale ['425'] . "</strong>";
		} elseif ($_GET ['status'] == "upy") {
			$title = $locale ['420'];
			$message = "<img src='" . $afolder . stripinput ( $_GET ['img'] ) . "' alt='" . stripinput ( $_GET ['img'] ) . "' /><br /><br />\n<strong>" . $locale ['426'] . "</strong>";
		}
		opentable ( $title );
		echo "<div style='text-align:center'>" . $message . "</div>\n";
		closetable ();
	}

	if (isset ( $_GET ['del'] )) {
		unlink ( $afolder . stripinput ( $_GET ['del'] ) );
		if ($settings ['tinymce_enabled'] == 1) {
			include INCLUDES . "buildlist.php";
		}
		redirect ( FUSION_SELF . $aidlink . "&section=images&ifolder=" . $_GET ['ifolder'] );
	} else if (isset ( $_POST ['uploadimage'] )) {
		$error = "";
		$image_types = array (".gif", ".GIF", ".jpeg", ".JPEG", ".jpg", ".JPG", ".png", ".PNG" );
		$imgext = strrchr ( $_FILES ['myfile'] ['name'], "." );
		$imgname = $_FILES ['myfile'] ['name'];
		$imgsize = $_FILES ['myfile'] ['size'];
		$imgtemp = $_FILES ['myfile'] ['tmp_name'];
		if (! in_array ( $imgext, $image_types )) {
			redirect ( FUSION_SELF . $aidlink . "&status=upn&ifolder=" . $_GET ['ifolder'] );
		} elseif (is_uploaded_file ( $imgtemp )) {
			move_uploaded_file ( $imgtemp, $afolder . $imgname );
			chmod ( $afolder . $imgname, 0644 );
			if ($settings ['tinymce_enabled'] == 1)
				include INCLUDES . "buildlist.php";
			redirect ( FUSION_SELF . $aidlink . "&section=images&status=upy&ifolder=" . $_GET ['ifolder'] . "&img=$imgname" );
		}
	} else {
		opentable ( $locale ['420'] );
		echo "<form name='uploadform' method='post' action='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=" . $_GET ['ifolder'] . "' enctype='multipart/form-data'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='350' class='center'>\n<tr>\n";
		echo "<td width='80' class='tbl'>" . $locale ['421'] . "</td>\n";
		echo "<td class='tbl'><input type='file' name='myfile' class='textbox' style='width:250px;' /></td>\n";
		echo "</tr>\n<tr>\n";
		echo "<td align='center' colspan='2' class='tbl'>\n";
		echo "<input type='submit' name='uploadimage' value='" . $locale ['420'] . "' class='button' style='width:100px;' /></td>\n";
		echo "</tr>\n</table>\n</form>\n";
		closetable ();

		if (isset ( $_GET ['view'] )) {
			opentable ( $locale ['440'] );
			echo "<div style='text-align:center'><br />\n";
			$image_ext = strrchr ( $afolder . stripinput ( $_GET ['view'] ), "." );
			if (in_array ( $image_ext, array (".gif", ".GIF", ".ico", ".jpg", ".JPG", ".jpeg", ".JPEG", ".png", ".PNG" ) )) {
				echo "<img src='" . $afolder . stripinput ( $_GET ['view'] ) . "' alt='" . stripinput ( $_GET ['view'] ) . "' /><br /><br />\n";
			} else {
				echo $locale ['441'] . "<br /><br />\n";
			}
			echo "<a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=" . $_GET ['ifolder'] . "&amp;del=" . stripinput ( $_GET ['view'] ) . "'>" . $locale ['442'] . "</a><br /><br />\n<a href='" . FUSION_SELF . $aidlink . "&amp;section=images'>" . $locale ['402'] . "</a><br /><br />\n</div>\n";
			closetable ();
		} else {
			$image_list = makefilelist ( $afolder, ".|..|imagelist.js|index.php", true );
			if ($image_list) {
				$image_count = count ( $image_list );
			}
			opentable ( $locale ['460'] );
			echo "<table cellpadding='0' cellspacing='1' width='450' class='tbl-border center'>\n<tr>\n";
			echo "<td align='center' colspan='2' class='tbl2'>\n";
			echo "<span style='font-weight:" . ($_GET ['ifolder'] == "icons" ? "bold" : "normal") . "'><a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=icons'>" . $locale ['fb250'] . "</a></span> |\n";
			echo "<span style='font-weight:" . ($_GET ['ifolder'] == "awards" ? "bold" : "normal") . "'><a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=awards'>" . $locale ['fb251'] . "</a></span> |\n";
			echo "<span style='font-weight:" . ($_GET ['ifolder'] == "post" ? "bold" : "normal") . "'><a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=post'>" . $locale ['fb252'] . "</a></span>\n";
			echo "</td>\n</tr>\n";
			if ($image_list) {
				for($i = 0; $i < $image_count; $i ++) {
					if ($i % 2 == 0) {
						$row_color = "tbl1";
					} else {
						$row_color = "tbl2";
					}
					echo "<tr>\n<td class='$row_color'>$image_list[$i]</td>\n";
					echo "<td align='right' width='1%' class='$row_color' style='white-space:nowrap'><a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=" . $_GET ['ifolder'] . "&amp;view=" . $image_list [$i] . "'>" . $locale ['461'] . "</a> -\n";
					echo "<a href='" . FUSION_SELF . $aidlink . "&amp;section=images&amp;ifolder=" . $_GET ['ifolder'] . "&amp;del=" . $image_list [$i] . "' onclick=\"return confirm('" . $locale ['470'] . "');\">" . $locale ['462'] . "</a></td>\n";
					echo "</tr>\n";
				}
			} else {
				echo "<tr>\n<td align='center' class='tbl1'>" . $locale ['463'] . "</td>\n</tr>\n";
			}
			echo "</table>\n";
			echo "<div style='text-align:right; margin-top:5px;'>" . showPoweredBy () . "</div>";
			closetable ();
		}
	}

} elseif ($_GET ['section'] == "settings") {

	if (isset ( $_POST ['goSettings'] )) {

		$user_titles = (isnum ( $_POST ['user_titles'] ) ? $_POST ['user_titles'] : 0);
		$user_titles_custom = (isnum ( $_POST ['user_titles_custom'] ) ? $_POST ['user_titles_custom'] : 0);
		$user_titles_custom_access = (isnum ( $_POST ['user_titles_custom_access'] ) ? $_POST ['user_titles_custom_access'] : 101);
		$user_titles_profile = (isnum ( $_POST ['user_titles_profile'] ) ? $_POST ['user_titles_profile'] : 0);
		$user_titles_posts = (isnum ( $_POST ['user_titles_posts'] ) ? $_POST ['user_titles_posts'] : 0);

		$forum_layout = (isnum ( $_POST ['forum_layout'] ) ? $_POST ['forum_layout'] : 1);
		$layout_change = (isnum ( $_POST ['layout_change'] ) ? $_POST ['layout_change'] : 0);

		$fboard_on = (isnum ( $_POST ['fboard_on'] ) ? $_POST ['fboard_on'] : 0);
		$show_latest = (isnum ( $_POST ['show_latest'] ) ? $_POST ['show_latest'] : 0);
		$max_attach = (isnum ( $_POST ['max_attach'] ) ? $_POST ['max_attach'] : 1);

		$latestno = (isnum ( $_POST ['latestno'] ) ? $_POST ['latestno'] : 1);
		$latestscroll = (isnum ( $_POST ['latestscroll'] ) ? $_POST ['latestscroll'] : 0);
		$spell_check = (isnum ( $_POST ['spell_check'] ) ? $_POST ['spell_check'] : 0);
		$attach_count = (isnum ( $_POST ['attach_count'] ) ? $_POST ['attach_count'] : 0);
		$latest_popup = (isnum ( $_POST ['latest_popup'] ) ? $_POST ['latest_popup'] : 0);
		$title_default = (isnum ( $_POST ['title_default'] ) ? $_POST ['title_default'] : 1);

		$show_medals = (isnum ( $_POST ['show_medals'] ) ? $_POST ['show_medals'] : 0);

		$forum_icons = (isnum ( $_POST ['forum_icons'] ) ? $_POST ['forum_icons'] : 0);
		$post_icons = (isnum ( $_POST ['post_icons'] ) ? $_POST ['post_icons'] : 0);

		$threads_per_page = (isnum ( $_POST ['threads_per_page'] ) ? $_POST ['threads_per_page'] : 0);
		$posts_per_page = (isnum ( $_POST ['posts_per_page'] ) ? $_POST ['posts_per_page'] : 0);
		$avatar_max_w = (isnum ( $_POST ['avatar_max_w'] ) ? $_POST ['avatar_max_w'] : 0);
		$avatar_max_h = (isnum ( $_POST ['avatar_max_h'] ) ? $_POST ['avatar_max_h'] : 0);
		$avatar_max_size = (isnum ( $_POST ['avatar_max_size'] ) ? $_POST ['avatar_max_size'] : 0);

		$stat_guests = (isnum ( $_POST ['stat_guests'] ) ? $_POST ['stat_guests'] : 0);
		$show_ratings = (isnum ( $_POST ['show_ratings'] ) ? $_POST ['show_ratings'] : 0);
		$rating_opacity = (isnum ( str_replace ( ".", "", $_POST ['rating_opacity'] ) ) ? $_POST ['rating_opacity'] : 0);
		$award_alert = (isnum ( $_POST ['award_alert'] ) ? $_POST ['award_alert'] : 0);
		$award_box = (isnum ( $_POST ['award_box'] ) ? $_POST ['award_box'] : 0);

		$latest_post = (isnum ( $_POST ['latest_post'] ) ? $_POST ['latest_post'] : 0);
		$subforum_view = (isnum ( $_POST ['subforum_view'] ) ? $_POST ['subforum_view'] : 0);

		$announce_enable = (isnum ( $_POST ['announce_enable'] ) ? $_POST ['announce_enable'] : 0);
		$announce_create = (isnum ( $_POST ['announce_create'] ) ? $_POST ['announce_create'] : 0);
		$announce_reply = (isnum ( $_POST ['announce_reply'] ) ? $_POST ['announce_reply'] : 0);
		$announce_polls = (isnum ( $_POST ['announce_polls'] ) ? $_POST ['announce_polls'] : 0);

		$vb_nav = (isnum ( $_POST ['vb_nav'] ) ? $_POST ['vb_nav'] : 0);
		$forum_rules = (isset ( $_POST ['forum_rules'] ) ? addslash ( $_POST ['forum_rules'] ) : "");

		$forum_notify = (isnum ( $_POST ['forum_notify'] ) ? $_POST ['forum_notify'] : 0);
		$fn_access = (isnum ( $_POST ['fn_access'] ) ? $_POST ['fn_access'] : 0);

		$no_avatar = (isnum ( $_POST ['no_avatar'] ) ? $_POST ['no_avatar'] : 0);
		$buttons = (isnum ( $_POST ['buttons'] ) ? $_POST ['buttons'] : 0);

		$buddy_enable = (isnum ( $_POST ['buddy_enable'] ) ? $_POST ['buddy_enable'] : 1);
		$group_enable = (isnum ( $_POST ['group_enable'] ) ? $_POST ['group_enable'] : 1);
		$stat_bday = (isnum ( $_POST ['stat_bday'] ) ? $_POST ['stat_bday'] : 1);
		$stat_visitor = (isnum ( $_POST ['stat_visitor'] ) ? $_POST ['stat_visitor'] : 1);
		$group_create = (isnum ( $_POST ['group_create'] ) ? $_POST ['group_create'] : 101);

		$boxover_ratings = (isnum ( $_POST ['boxover_ratings'] ) ? $_POST ['boxover_ratings'] : 0);
		$show_ulevel = (isnum ( $_POST ['show_ulevel'] ) ? $_POST ['show_ulevel'] : 0);
		$show_status = (isnum ( $_POST ['show_status'] ) ? $_POST ['show_status'] : 0);

		$show_chat = "0";

		$i = 0;
		$w_can_see = "";
		$explode = $_POST ['w_can_see'];
		foreach ( $explode as $group ) {
			$w_can_see .= ($i > 0 ? "|" : "") . $group;
			$i ++;
		}
		$i = 0;
		$w_can_give = "";
		$explode = $_POST ['w_can_give'];
		foreach ( $explode as $group ) {
			$w_can_give .= ($i > 0 ? "|" : "") . $group;
			$i ++;
		}
		$i = 0;
		$w_protected = "";
		$explode = $_POST ['w_protected'];
		foreach ( $explode as $group ) {
			$w_protected .= ($i > 0 ? "|" : "") . $group;
			$i ++;
		}

		$w_enabled = (isnum ( $_POST ['w_enabled'] ) ? $_POST ['w_enabled'] : 0);
		$w_see_own = (isnum ( $_POST ['w_see_own'] ) ? $_POST ['w_see_own'] : 0);

		$query = ("update " . $db_prefix . "fb_settings set show_chat='$show_chat', show_status='$show_status', show_ulevel='$show_ulevel', boxover_ratings='$boxover_ratings',
		w_see_own='$w_see_own', w_enabled='$w_enabled', w_protected='$w_protected', w_can_give='$w_can_give', w_can_see='$w_can_see',
		buddy_enable='$buddy_enable', group_enable='$group_enable', stat_bday='$stat_bday', stat_visitor='$stat_visitor', group_create='$group_create',
		buttons='$buttons', no_avatar='$no_avatar', fn_access='$fn_access', forum_notify='$forum_notify', vb_nav='$vb_nav', forum_rules='$forum_rules', announce_enable='$announce_enable',
		announce_create='$announce_create', announce_reply='$announce_reply', announce_polls='$announce_polls',
		subforum_view='$subforum_view', latest_post='$latest_post', award_box='$award_box', award_alert='$award_alert', rating_opacity='$rating_opacity', show_ratings='$show_ratings', stat_guests='$stat_guests',
		threads_per_page='$threads_per_page', posts_per_page='$posts_per_page', avatar_max_w='$avatar_max_w', avatar_max_h='$avatar_max_h',
		avatar_max_size='$avatar_max_size', forum_icons='$forum_icons', post_icons='$post_icons', show_medals='$show_medals',
		title_default='$title_default', latest_popup='$latest_popup', attach_count='$attach_count', spell_check='$spell_check', latestno='$latestno', latestscroll='$latestscroll',
		show_latest='$show_latest', fboard_on='$fboard_on', user_titles='$user_titles', user_titles_custom='$user_titles_custom', user_titles_profile='$user_titles_profile',
		user_titles_posts='$user_titles_posts', user_titles_custom_access='$user_titles_custom_access', forum_layout='$forum_layout', layout_change='$layout_change', max_attach='$max_attach'");

		redirect ( FUSION_SELF . $aidlink . "&section=settings&error=" . (dbquery ( $query ) ? "0" : "1") );

	}

	opentable ( $locale ['fb203'] );

	add_to_head ( "<script type='text/javascript'>
		window.onload=setTimeout(\"hideall()\", 750);
		function hideall(){
		for (var i = 1; i<=12; i++) {
			if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
		}
		}
		function showhide(msg_id) {
		   document.getElementById(msg_id).style.display = document.getElementById(msg_id).style.display == 'none' ? 'block' : 'none';
		   document.getElementById(msg_id).style.width = '500px';
		   document.getElementById(msg_id).style.align = 'center';
		}
		</script>" );

	$fb4 = dbarray ( dbquery ( "select * from " . $db_prefix . "fb_settings" ) );

	$user_groups = getusergroups ();
	$access_opts = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($fb4 ['user_titles_custom_access'] == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$access_opts .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	$titleOpts = "";
	$title_res = dbquery ( "select * from " . $db_prefix . "fb_titles where title_access='101' order by title_title" );
	while ( $title_data = dbarray ( $title_res ) ) {
		$titleOpts .= "<option value='" . $title_data ['title_id'] . "'" . ($title_data ['title_id'] == $fb4 ['title_default'] ? " SELECTED" : "") . "'>" . stripslash ( $title_data ['title_title'] ) . "</option>\n";
	}

	$user_groups = getusergroups ();
	$announce_create = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($fb4 ['announce_create'] == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$announce_create .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	$user_groups = getusergroups ();
	$announce_reply = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($fb4 ['announce_reply'] == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$announce_reply .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	$user_groups = getusergroups ();
	$create_list = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($fb4 ['group_create'] == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$create_list .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	$user_groups = getusergroups ();
	$fn_access = "";
	$sel = "";
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($fb4 ['fn_access'] == $user_group ['0'] ? " selected='selected'" : "");
		if ($user_group ['0'] == "0")
			continue;
		$fn_access .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
	}

	require_once INCLUDES . "html_buttons_include.php";

	echo "<form action='" . FUSION_SELF . $aidlink . "&section=settings' method='post' name='settingsForm'>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu1\");'>" . $locale ['fb405'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu1'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb412'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='fboard_on' class='textbox'>
				<option value='1'" . ($fb4 ['fboard_on'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['fboard_on'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb406'] . "</td>
			<td class='tbl2'><select name='forum_layout' class='textbox'>
				<option value='1'" . ($fb4 ['forum_layout'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb407'] . "</option>
				<option value='2'" . ($fb4 ['forum_layout'] == "2" ? " SELECTED" : "") . ">" . $locale ['fb408'] . "</option>
				<option value='3'" . ($fb4 ['forum_layout'] == "3" ? " SELECTED" : "") . ">" . $locale ['fb409'] . "</option>
				<option value='4'" . ($fb4 ['forum_layout'] == "4" ? " SELECTED" : "") . ">" . $locale ['fb438'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb411'] . "</td>
			<td class='tbl2'><select name='layout_change' class='textbox'>
				<option value='1'" . ($fb4 ['layout_change'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['layout_change'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb410'] . "</td>
			<td class='tbl2'><input type='text' name='max_attach' class='textbox' value='" . $fb4 ['max_attach'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb417'] . "</td>
			<td class='tbl2'><select name='spell_check' class='textbox'>
				<option value='1'" . ($fb4 ['spell_check'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['spell_check'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb418'] . "</td>
			<td class='tbl2'><select name='attach_count' class='textbox'>
				<option value='1'" . ($fb4 ['attach_count'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['attach_count'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb431'] . "</td>
			<td class='tbl2'><select name='show_ratings' class='textbox'>
				<option value='1'" . ($fb4 ['show_ratings'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['show_ratings'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr><tr>
			<td class='tbl1'>" . $locale ['fb432'] . "</td>
			<td class='tbl2'><input type='text' name='rating_opacity' class='textbox' style='width:75px;' value='" . $fb4 ['rating_opacity'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb461'] . "</td>
			<td class='tbl2'><select name='boxover_ratings' class='textbox'>
				<option value='1'" . ($fb4 ['boxover_ratings'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['boxover_ratings'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb460'] . "</td>
			<td class='tbl2'><select name='show_ulevel' class='textbox'>
				<option value='1'" . ($fb4 ['show_ulevel'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['show_ulevel'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb462'] . "</td>
			<td class='tbl2'><select name='show_status' class='textbox'>
				<option value='1'" . ($fb4 ['show_status'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['show_status'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu2\");'>" . $locale ['fb425'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu2'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb426'] . "</td>
			<td class='tbl2' style='width:250px;'><input type='text' name='threads_per_page' class='textbox' style='width:75px;' value='" . $fb4 ['threads_per_page'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb427'] . "</td>
			<td class='tbl2'><input type='text' name='posts_per_page' class='textbox' style='width:75px;' value='" . $fb4 ['posts_per_page'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb428'] . "</td>
			<td class='tbl2'><input type='text' name='avatar_max_w' class='textbox' style='width:75px;' value='" . $fb4 ['avatar_max_w'] . "'> X
			<input type='text' name='avatar_max_h' class='textbox' style='width:75px;' value='" . $fb4 ['avatar_max_h'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb429'] . "</td>
			<td class='tbl2'><input type='text' name='avatar_max_size' style='width:75px;' class='textbox' value='" . $fb4 ['avatar_max_size'] . "'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb437'] . "</td>
			<td class='tbl2'><select name='subforum_view' class='textbox'>
				<option value='1'" . ($fb4 ['subforum_view'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['subforum_view'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb444'] . "</td>
			<td class='tbl2'><select name='no_avatar' class='textbox'>
				<option value='1'" . ($fb4 ['no_avatar'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['no_avatar'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		</table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu3\");'>" . $locale ['fb422'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu3'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb423'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='forum_icons' class='textbox'>
				<option value='1'" . ($fb4 ['forum_icons'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['forum_icons'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb424'] . "</td>
			<td class='tbl2'><select name='post_icons' class='textbox'>
				<option value='1'" . ($fb4 ['post_icons'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['post_icons'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb445'] . "</td>
			<td class='tbl2'><select name='buttons' class='textbox'>
				<option value='1'" . ($fb4 ['buttons'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['buttons'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu4\");'>" . $locale ['fb416'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu4'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb413'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='show_latest' class='textbox'>
				<option value='1'" . ($fb4 ['show_latest'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['show_latest'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb415'] . "</td>
			<td class='tbl2'><select name='latestscroll' class='textbox'>
				<option value='1'" . ($fb4 ['latestscroll'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['latestscroll'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb419'] . "</td>
			<td class='tbl2'><select name='latest_popup' class='textbox'>
				<option value='1'" . ($fb4 ['latest_popup'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['latest_popup'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb436'] . "</td>
			<td class='tbl2'><select name='latest_post' class='textbox'>
				<option value='1'" . ($fb4 ['latest_post'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['latest_post'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb414'] . "</td>
			<td class='tbl2'><input type='text' name='latestno' class='textbox' value='" . $fb4 ['latestno'] . "'></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu5\");'>" . $locale ['fb200'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu5'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb400'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='user_titles' class='textbox'>
				<option value='1'" . ($fb4 ['user_titles'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['user_titles'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb401'] . "</td>
			<td class='tbl2'><select name='user_titles_custom' class='textbox'>
				<option value='1'" . ($fb4 ['user_titles_custom'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['user_titles_custom'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb402'] . "</td>
			<td class='tbl2'><select name='user_titles_custom_access' class='textbox'>
			$access_opts</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb403'] . "</td>
			<td class='tbl2'><select name='user_titles_profile' class='textbox'>
				<option value='1'" . ($fb4 ['user_titles_profile'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['user_titles_profile'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb404'] . "</td>
			<td class='tbl2'><select name='user_titles_posts' class='textbox'>
				<option value='1'" . ($fb4 ['user_titles_posts'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['user_titles_posts'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb420'] . "</td>
			<td class='tbl2'><select name='title_default' class='textbox'>
			$titleOpts
			</select></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu6\");'>" . $locale ['fb433'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu6'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb433'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='show_medals' class='textbox'>
				<option value='1'" . ($fb4 ['show_medals'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['show_medals'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb434'] . "</td>
			<td class='tbl2'><select name='award_alert' class='textbox'>
				<option value='1'" . ($fb4 ['award_alert'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['award_alert'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb435'] . "</td>
			<td class='tbl2'><select name='award_box' class='textbox'>
				<option value='1'" . ($fb4 ['award_box'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['award_box'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr></table>

		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu9\");'>" . $locale ['fb439'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu9'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb440'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='forum_notify' class='textbox'>
				<option value='1'" . ($fb4 ['forum_notify'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['forum_notify'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb441'] . "</td>
			<td class='tbl2'><select name='fn_access' class='textbox'>
				$fn_access
			</select></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu7\");'>" . $locale ['fb900'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu7'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb9071'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='announce_enable' class='textbox'>
				<option value='1'" . ($fb4 ['announce_enable'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['announce_enable'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb904'] . "</td>
			<td class='tbl2'><select name='announce_create' class='textbox'>
				$announce_create
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb905'] . "</td>
			<td class='tbl2'><select name='announce_reply' class='textbox'>
				$announce_reply
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb906'] . "</td>
			<td class='tbl2'><select name='announce_polls' class='textbox'>
				<option value='1'" . ($fb4 ['announce_polls'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['announce_polls'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu8\");'>" . $locale ['fb907'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu8'>
		<tr>
			<td class='tbl1'>" . $locale ['fb908'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='vb_nav' class='textbox'>
				<option value='1'" . ($fb4 ['vb_nav'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['vb_nav'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' colspan='2'>" . $locale ['fb909'] . ":</td>
		</tr>
		<tr>
			<td class='tbl2' style='width:500px;' colspan='2'>
				<textarea name='forum_rules' cols='50' rows='10' class='textbox' style='width:320px'>" . stripslash ( $fb4 ['forum_rules'] ) . "</textarea>
			</td>
		</tr>
		<tr>
			<td class='tbl' colspan='2'>
			" . display_html ( "settingsForm", "forum_rules", true, true, true ) . "
			</td>
		</tr>
		</table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu10\");'>" . $locale ['fb446'] . " &amp; " . $locale ['fb450'] . "</td>
		</tr></table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu10'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb447'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='buddy_enable' class='textbox'>
				<option value='1'" . ($fb4 ['buddy_enable'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['buddy_enable'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb448'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='group_enable' class='textbox'>
				<option value='1'" . ($fb4 ['group_enable'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['group_enable'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb449'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='group_create' class='textbox'>
				$create_list
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb451'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='stat_bday' class='textbox'>
				<option value='2'" . ($fb4 ['stat_bday'] == "2" ? " SELECTED" : "") . ">" . $locale ['fb452'] . "</option>
				<option value='1'" . ($fb4 ['stat_bday'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb453'] . "</option>
				<option value='0'" . ($fb4 ['stat_bday'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb454'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fb455'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='stat_visitor' class='textbox'>
				<option value='1'" . ($fb4 ['stat_visitor'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['stat_visitor'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb430'] . "</td>
			<td class='tbl2'><select name='stat_guests' class='textbox'>
				<option value='1'" . ($fb4 ['stat_guests'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['stat_guests'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		</table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl2' colspan='2' style='text-align:center;cursor:pointer;' onClick='showhide(\"smenu11\");'>" . $locale ['fbw105'] . "</td>
		</tr></table>";
	$user_groups = getusergroups ();
	$w_can_see = "";
	$w_can_give = "";
	$w_protected = "";
	array_push ( $user_groups, array ("mod", $locale ['userf1'] ) );
	$wcs = explode ( "|", $fb4 ['w_can_see'] );
	$wcg = explode ( "|", $fb4 ['w_can_give'] );
	$wp = explode ( "|", $fb4 ['w_protected'] );
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$w_can_see .= "<option value='" . $user_group ['0'] . "'" . (in_array ( $user_group ['0'], $wcs ) ? " SELECTED" : "") . ">" . $user_group ['1'] . "</option>\n";
		if ($user_group ['0'] > 0 || $user_group ['0'] == "mod") {
			$w_protected .= "<option value='" . $user_group ['0'] . "'" . (in_array ( $user_group ['0'], $wp ) ? " SELECTED" : "") . ">" . $user_group ['1'] . "</option>\n";
			if (($user_group ['0'] > 0 && $user_group ['0'] !== "101") || $user_group ['0'] == "mod") {
				$w_can_give .= "<option value='" . $user_group ['0'] . "'" . (in_array ( $user_group ['0'], $wcg ) ? " SELECTED" : "") . ">" . $user_group ['1'] . "</option>\n";
			}
		}
	}
	echo "<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0' id='smenu11'>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fbw120'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='w_enabled' class='textbox'>
				<option value='1'" . ($fb4 ['w_enabled'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['w_enabled'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fbw117'] . "<br /><span class='small'>" . $locale ['fbw122'] . "</span><br />
			<span class='small'>" . $locale ['fbw122'] . "</span><br />
			<span class='small'>" . $locale ['fba105'] . "</span></td>
			<td class='tbl2' style='width:250px;'><select name=w_can_see[] class='textbox' size='5' multiple>
				$w_can_see
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fbw118'] . "<br />
			<span class='small'>" . $locale ['fbw122'] . "</span><br />
			<span class='small'>" . $locale ['fba106'] . "</span></td>
			<td class='tbl2' style='width:250px;'><select name=w_can_give[] class='textbox' size='5' multiple>
				$w_can_give
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fbw119'] . "</td>
			<td class='tbl2' style='width:250px;'><select name='w_see_own' class='textbox'>
				<option value='1'" . ($fb4 ['w_see_own'] == "1" ? " SELECTED" : "") . ">" . $locale ['fb4001'] . "</option>
				<option value='0'" . ($fb4 ['w_see_own'] == "0" ? " SELECTED" : "") . ">" . $locale ['fb4002'] . "</option>
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' style='width:250px;'>" . $locale ['fbw121'] . "<br />
			<span class='small'>" . $locale ['fbw122'] . "</span><br />
			<span class='small'>" . $locale ['fba107'] . "</span></td>
			<td class='tbl2' style='width:250px;'><select name=w_protected[] class='textbox' size='5' multiple>
				$w_protected
			</select></td>
		</tr>
		</table>
		<table width='500' class='tbl-border center' cellspacing='1' cellpadding='0'>
		<tr>
			<td class='tbl1' colspan='2' style='text-align:center;'><input type='submit' name='goSettings' value='" . $locale ['fb303'] . "' class='button'></td>
		</tr>
		</table>
		</form>\n";
	echo "<div style='text-align:right; margin-top:5px;'>" . showPoweredBy () . "</div>";
	closetable ();

/*
} elseif($_GET['section'] == "update"){

	include INFUSIONS."fusionboard4/includes/version_checker/version_checker.php";

	opentable($locale['fb801'].$current_version);
		echo checkversion($current_version, "100%", "dashed", "file", true);
	closetable();
*/
} elseif ($_GET ['section'] == "labels") {

	if (isset ( $_POST ['goLabel'] )) {

		$label_user = (isNum ( $_POST ['label_user'] ) ? $_POST ['label_user'] : 0);
		$label_group = (isNum ( $_POST ['label_group'] ) ? $_POST ['label_group'] : 0);
		$label_style = addslash ( $_POST ['label_style'] );

		if (isset ( $_GET ['update'] ) && isNum ( $_GET ['update'] )) {

			$result = dbquery ( "select * from " . DB_PREFIX . "fb_labels where label_id='" . $_GET ['update'] . "'" );
			if (! dbrows ( $result ))
				redirect ( FUSION_SELF . $aidlink . "&section=labels" );

			$query = dbquery ( "update " . DB_PREFIX . "fb_labels set label_user='$label_user', label_group='$label_group', label_style='$label_style' where label_id='" . $_GET ['update'] . "'" );
			redirect ( FUSION_SELF . $aidlink . "&section=labels" );

		} else {

			$query = dbquery ( "insert into " . DB_PREFIX . "fb_labels (label_user, label_group, label_style) VALUES('$label_user', '$label_group', '$label_style')" );
			redirect ( FUSION_SELF . $aidlink . "&section=labels" );

		}

	}

	if (isset ( $_GET ['del'] ) && isNum ( $_GET ['del'] )) {

		$result = dbquery ( "delete from " . DB_PREFIX . "fb_labels where label_id='" . $_GET ['del'] . "'" );
		redirect ( FUSION_SELF . $aidlink . "&section=labels" );

	}

	if (isset ( $_GET ['edit'] ) && isNum ( $_GET ['edit'] )) {

		$result = dbquery ( "select * from " . DB_PREFIX . "fb_labels where label_id='" . $_GET ['edit'] . "'" );
		if (! dbrows ( $result ))
			redirect ( FUSION_SELF . $aidlink . "&section=labels" );
		$data = dbarray ( $result );

		$label_user = $data ['label_user'];
		$label_group = $data ['label_group'];
		$label_style = stripslash ( $data ['label_style'] );

		$action = FUSION_SELF . $aidlink . "&section=labels&update=" . $_GET ['edit'];
		$table = $locale ['fb811'];
		$button = $locale ['fb813'];

	} else {

		$label_user = "";
		$label_group = "";
		$label_style = "";

		$action = FUSION_SELF . $aidlink . "&section=labels";
		$table = $locale ['fb810'];
		$button = $locale ['fb812'];

	}

	opentable ( $table );

	$user_opts = "";
	$sel = "";
	$user_res = dbquery ( "select * from " . DB_USERS . " order by user_level desc, user_name asc" );
	while ( $user_data = dbarray ( $user_res ) ) {
		$sel = ($user_data ['user_id'] == $label_user ? " SELECTED" : "");
		$user_opts .= "<option value='" . $user_data ['user_id'] . "'$sel>" . $user_data ['user_name'] . "</option>\n";
	}

	$user_groups = getusergroups ();
	$group_opts = "<optgroup label='" . $locale ['fb831'] . "'>\n";
	$sel = "";
	$i = 0;
	while ( list ( $key, $user_group ) = each ( $user_groups ) ) {
		$sel = ($label_group == $user_group ['0'] ? " selected='selected'" : "");
		if ($i == 4)
			$group_opts .= "</optgroup>\n<optgroup label='" . $locale ['fb832'] . "'>\n";
		$group_opts .= "<option value='" . $user_group ['0'] . "'$sel>" . $user_group ['1'] . "</option>\n";
		$i ++;
	}
	$group_opts .= "</optgroup>\n";

	echo "<form action='$action' method='post' name='labelForm'>
		<table width='400' cellspacing='1' cellpadding='0' class='tbl-border center'>
		<tr>
			<td class='tbl1'>" . $locale ['fb814'] . "</td>
			<td class='tbl2' colspan='2'><select name='label_user' class='textbox' style='width:150px;'>
			<option value=''>---</option>
			$user_opts
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' colspan='3' style='text-align:center'>" . $locale ['fb816'] . "</td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb815'] . "</td>
			<td class='tbl2' colspan='2'><select name='label_group' class='textbox' style='width:150px;'>
			<option value=''>---</option>
			$group_opts
			</select></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb817'] . "</td>
			<td class='tbl2' style='width:150px;'><textarea name='label_style' class='textbox' rows='3' style='width:150px;'>$label_style</textarea></td>
			<td class='tbl2' style='width:100px; vertical-align:top;'>
			 <input type='button' value='B' class='button' onclick=\"addText('label_style','font-weight:bold;','','labelForm');return false;\">
			<input type='button' value='I' class='button' onclick=\"addText('label_style','font-style:italic;','','labelForm');return false;\">
			<input type='button' value='U' class='button' onclick=\"addText('label_style','text-decoration:underline;','','labelForm');return false;\">
			</td>
		</tr>
		<tr>
			<td class='tbl1' colspan='3' style='text-align:center'>
			<input type='submit' name='goLabel' value='$button' class='button'></td>
		</tr>
		</table>
		</form>\n";

	closetable ();

	tablebreak ();

	opentable ( $locale ['fb821'] );

	echo "<table width='80%' cellspacing='1' cellpadding='0' class='tbl-border center'>
		<tr>
			<td class='tbl1' style='font-weight:bold;'>" . $locale ['fb822'] . "</td>
			<td class='tbl1' style='font-weight:bold;'>" . $locale ['fb823'] . "</td>
			<td class='tbl1' style='font-weight:bold;'>" . $locale ['fb824'] . "</td>
		</tr>
		<tr>
			<td class='tbl2' style='font-weight:bold;' colspan='3'>" . $locale ['fb825'] . "</td>
		</tr>\n";

	$result = dbquery ( "select * from " . DB_PREFIX . "fb_labels l
		left join " . DB_USERS . " u on u.user_id=l.label_user
		where l.label_user!='0' order by u.user_level desc, u.user_name asc" );
	if (dbrows ( $result )) {

		while ( $data = dbarray ( $result ) ) {

			echo "<tr>
					<td class='tbl1'>" . $data ['user_name'] . "</td>
					<td class='tbl1'><span style='" . $data ['label_style'] . "'>" . $data ['user_name'] . "</span>
					<td class='tbl1'><a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;edit=" . $data ['label_id'] . "'>" . $locale ['fb827'] . "</a> ::
					<a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;del=" . $data ['label_id'] . "'>" . $locale ['fb828'] . "</a></td>
				</tr>\n";

		}

	} else {

		echo "<tr>
				<td class='tbl1' colspan='3' style='text-align:center;'>" . $locale ['fb829'] . "</td>
			</tr>\n";

	}

	echo "<tr>
			<td class='tbl2' style='font-weight:bold;' colspan='3'>" . $locale ['fb826'] . "</td>
		</tr>\n";

	$results = false;

	$result = dbquery ( "select * from " . DB_PREFIX . "fb_labels
		where (label_group > 100 and label_group < 104) or (label_group=0 and label_user=0) order by label_group desc" );
	if (dbrows ( $result )) {

		$results = true;
		while ( $data = dbarray ( $result ) ) {

			echo "<tr>
					<td class='tbl1'>" . getgroupname ( $data ['label_group'] ) . "</td>
					<td class='tbl1'><span style='" . $data ['label_style'] . "'>" . getgroupname ( $data ['label_group'] ) . "</span>
					<td class='tbl1'><a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;edit=" . $data ['label_id'] . "'>" . $locale ['fb827'] . "</a> ::
					<a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;del=" . $data ['label_id'] . "'>" . $locale ['fb828'] . "</a></td>
				</tr>\n";

		}

	}

	$result = dbquery ( "select * from " . DB_PREFIX . "fb_labels l
		left join " . DB_USER_GROUPS . " g on g.group_id=l.label_group
		where l.label_user='0' and (l.label_group != '0' and l.label_group != '101' and l.label_group != '102'
									and l.label_group !='103') order by l.label_group desc, g.group_name asc" );
	if (dbrows ( $result )) {

		$results = true;
		while ( $data = dbarray ( $result ) ) {

			echo "<tr>
					<td class='tbl1'>" . $data ['group_name'] . "</td>
					<td class='tbl1'><span style='" . $data ['label_style'] . "'>" . $data ['group_name'] . "</span>
					<td class='tbl1'><a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;edit=" . $data ['label_id'] . "'>" . $locale ['fb827'] . "</a> ::
					<a href='" . FUSION_SELF . $aidlink . "&amp;section=labels&amp;del=" . $data ['label_id'] . "'>" . $locale ['fb828'] . "</a></td>
				</tr>\n";

		}

	}

	if (! $results) {

		echo "<tr>
				<td class='tbl1' colspan='3' style='text-align:center;'>" . $locale ['fb829'] . "</td>
			</tr>\n";

	}

	echo "</table>\n";

	closetable ();
	tablebreak ();
	opentable ( $locale ['fba100'] );

	if (isset ( $_POST ['label_update'] )) {

		$index = (isnum ( $_POST ['label_index'] ) ? $_POST ['label_index'] : 0);
		$post = (isnum ( $_POST ['label_post'] ) ? $_POST ['label_post'] : 0);
		$panel = (isnum ( $_POST ['label_panel'] ) ? $_POST ['label_panel'] : 0);

		$result = dbquery ( "update " . DB_PREFIX . "fb_settings set label_index='$index', label_post='$post', label_panel='$panel'" );
		redirect ( FUSION_SELF . $aidlink . "&amp;section=labels" );

	}

	echo "<form action='" . FUSION_SELF . $aidlink . "&amp;section=labels' method='post'>\n";
	echo "<label><input type='checkbox' name='label_index' value='1'" . ($fb4 ['label_index'] ? " CHECKED" : "") . "> " . $locale ['fba101'] . "</label<br />\n";
	echo "<label><input type='checkbox' name='label_post' value='1'" . ($fb4 ['label_post'] ? " CHECKED" : "") . "> " . $locale ['fba102'] . "</label<br />\n";
	echo "<label><input type='checkbox' name='label_panel' value='1'" . ($fb4 ['label_panel'] ? " CHECKED" : "") . "> " . $locale ['fba103'] . "</label<br />\n";
	echo "<input type='submit' name='label_update' class='button' value='" . $locale ['fba104'] . "'>\n</form>\n";

	closetable ();

} elseif ($_GET ['section'] == "ratings") {

	if (isset ( $_POST ['goRating'] )) {

		$type_name = addslash ( stripinput ( $_POST ['type_name'] ) );
		$type_icon = stripinput ( $_POST ['type_icon'] );

		if (isset ( $_GET ['update'] ) && isNum ( $_GET ['update'] )) {

			$result = dbquery ( "select * from " . DB_PREFIX . "fb_rate_type where type_id='" . $_GET ['update'] . "'" );
			if (! dbrows ( $result ))
				redirect ( FUSION_SELF . $aidlink . "&section=ratings" );

			$query = dbquery ( "update " . DB_PREFIX . "fb_rate_type set type_name='$type_name', type_icon='$type_icon'
			where type_id='" . $_GET ['update'] . "'" );
			redirect ( FUSION_SELF . $aidlink . "&section=ratings" );

		} else {

			$query = dbquery ( "insert into " . DB_PREFIX . "fb_rate_type (type_name, type_icon) VALUES('$type_name', '$type_icon')" );
			redirect ( FUSION_SELF . $aidlink . "&section=ratings" );

		}

	}

	if (isset ( $_GET ['del'] ) && isNum ( $_GET ['del'] )) {

		$result = dbquery ( "delete from " . DB_PREFIX . "fb_rate_type where type_id='" . $_GET ['del'] . "'" );
		$result = dbquery ( "delete from " . DB_PREFIX . "fb_rate where rate_type='" . $_GET ['del'] . "'" );
		redirect ( FUSION_SELF . $aidlink . "&section=ratings" );

	}

	if (isset ( $_GET ['edit'] ) && isNum ( $_GET ['edit'] )) {

		$result = dbquery ( "select * from " . DB_PREFIX . "fb_rate_type where type_id='" . $_GET ['edit'] . "'" );
		if (! dbrows ( $result ))
			redirect ( FUSION_SELF . $aidlink . "&section=ratings" );
		$data = dbarray ( $result );

		$type_name = stripslash ( $data ['type_name'] );
		$type_icon = $data ['type_icon'];

		$action = FUSION_SELF . $aidlink . "&section=ratings&update=" . $_GET ['edit'];
		$panel = $locale ['fb852'];
		$button = $locale ['fb854'];

	} else {

		$type_name = "";
		$type_icon = "";

		$action = FUSION_SELF . $aidlink . "&section=ratings";
		$panel = $locale ['fb851'];
		$button = $locale ['fb853'];

	}

	opentable ( $panel );

	$iconOpts = makefileopts ( makefilelist ( INFUSIONS . "fusionboard4/images/forum_icons/", ".|..|index.php|Thumbs.db" ), $type_icon );

	echo "<form action='$action' name='ratingForm' method='post'>
		<table width='300' cellspacing='1' cellpadding='0' class='tbl-border center'>
		<tr>
			<td class='tbl1'>" . $locale ['fb855'] . "</td>
			<td class='tbl2'><input type='text' name='type_name' class='textbox' value='$type_name'></td>
		</tr>
		<tr>
			<td class='tbl1'>" . $locale ['fb856'] . "</td>
			<td class='tbl2'><select name='type_icon' class='textbox'>
			$iconOpts
			</select></td>
		</tr>
		<tr>
			<td class='tbl1' colspan='2' style='text-align:center;'>
			<input type='submit' name='goRating' value='$button' class='button'>
			</td>
		</tr>
		</table>
		</form>\n";

	closetable ();

	tablebreak ();

	opentable ( $locale ['fb860'] );

	$result = dbquery ( "select * from " . DB_PREFIX . "fb_rate_type" );
	if (dbrows ( $result )) {

		echo "<table width='300' cellspacing='1' cellpadding='0' class='tbl-border center'>
			<tr>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb855'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb856'] . "</td>
				<td class='tbl2' style='font-weight:bold;'>" . $locale ['fb857'] . "</td>
			</tr>\n";

		while ( $data = dbarray ( $result ) ) {

			echo "<tr>
					<td class='tbl1'>" . stripslash ( $data ['type_name'] ) . "</td>
					<td class='tbl1'><img src='" . INFUSIONS . "fusionboard4/images/forum_icons/" . $data ['type_icon'] . "' alt=''></td>
					<td class='tbl1'><a href='" . FUSION_SELF . $aidlink . "&amp;section=ratings&amp;edit=" . $data ['type_id'] . "'>" . $locale ['fb858'] . "</a> ::
					<a href='" . FUSION_SELF . $aidlink . "&amp;section=ratings&amp;del=" . $data ['type_id'] . "' onclick=\"return confirm('" . $locale ['fb869'] . "');\">" . $locale ['fb859'] . "</a></td>
				</tr>\n";

		}

		echo "</table>\n";

	} else {

		echo "<div align='center'>" . $locale ['fb861'] . "</div>\n";

	}

	closetable ();

} elseif ($_GET ['section'] == "forums") {

	include INFUSIONS . "fusionboard4/includes/forumadmin.php";

} elseif ($_GET ['section'] == "warnings") {

	if (isset ( $_POST ['goRule'] ) && isNum ( $_GET ['level'] ) && isset ( $_GET ['level'] )) {

		$pm = isset ( $_POST ['rule_pm'] ) ? addslash ( stripinput ( $_POST ['rule_pm'] ) ) : "";
		$email = isset ( $_POST ['rule_email'] ) ? addslash ( stripinput ( $_POST ['rule_email'] ) ) : "";
		$bantime = (isset ( $_POST ['rule_bantime'] ) && isNum ( $_POST ['rule_bantime'] ) ? $_POST ['rule_bantime'] : 0);
		$perma = (isset ( $_POST ['rule_perma'] ) && isNum ( $_POST ['rule_perma'] ) ? $_POST ['rule_perma'] : 0);

		$result = dbquery ( "update " . DB_PREFIX . "fb_warn_rules set rule_pm='$pm', rule_email='$email', rule_bantime='$bantime', rule_perma='$perma' where rule_level='" . $_GET ['level'] . "'" );
		redirect ( FUSION_SELF . $aidlink . "&section=warnings" );

	}

	opentable ( $locale ['fbw104'] );

	$result = dbquery ( "select * from " . DB_PREFIX . "fb_warn_rules order by rule_level asc" );
	?>
<script>
function editRule(level){
  $('#warn_' + level).html("<img src='<?php
	echo INFUSIONS;
	?>fusionboard4/images/ajax-loader.gif' alt='' style='vertical-align:middle;'>");
  $('#warn_' + level).load("<?php
	echo INFUSIONS;
	?>fusionboard4/includes/warning.inc.php?mode=ajax&level="+level+"&sid="+Math.random());
}
</script>
<?php
	echo "<table width='100%' cellspacing='0' cellpadding='5' border='0' style='border-bottom:1px solid #ccc;text-align:center;'>\n";
	echo "<tr>\n<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw106'] . "</td>\n";
	echo "<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw107'] . "</td>\n";
	echo "<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw108'] . "</td>\n";
	echo "<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw109'] . "</td>\n";
	echo "<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw110'] . "</td>\n";
	echo "<td style='font-weight:bold;' width='16.6%'>" . $locale ['fbw111'] . "</td>\n</tr>\n";
	echo "</table>\n";

	while ( $data = dbarray ( $result ) ) {

		echo "<div id='warn_" . $data ['rule_level'] . "'>\n";
		echo "<table width='100%' cellspacing='0' cellpadding='5' border='0' style='text-align:center;'>\n";
		echo "<tr>\n<td style='font-weight:bold;' width='16.6%'>" . $data ['rule_level'] . "</td>\n";
		echo "<td width='16.6%'>" . ($data ['rule_pm'] ? $locale ['fb4001'] : $locale ['fb4002']) . "</td>\n";
		echo "<td width='16.6%'>" . ($data ['rule_email'] ? $locale ['fb4001'] : $locale ['fb4002']) . "</td>\n";
		echo "<td width='16.6%'>" . ($data ['rule_bantime'] ? $data ['rule_bantime'] : $locale ['fb4002']) . "</td>\n";
		echo "<td width='16.6%'>" . ($data ['rule_perma'] ? $locale ['fb4001'] : $locale ['fb4002']) . "</td>\n";
		echo "<td width='16.6%'><a onClick=\"editRule(" . $data ['rule_level'] . ")\" style='cursor:pointer;'>" . $locale ['fbw111'] . "</a></td>\n</tr>\n";
		echo "</table>\n</div>\n";

	}

	echo "<br /><br /><span class='small'>" . $locale ['fba108'] . "</span>\n";

	closetable ();

}

require_once THEMES . "templates/footer.php";
?>