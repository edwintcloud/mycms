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

if (! iADMIN)
	redirect ( BASEDIR . "index.php" );

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}

opentable ( $locale ['fb750'] );

if (isset ($_POST['install_options'])){
	/* Tracking software -- Comment out if you don't want it */
	if(isset($_POST['infuse'])){
		$uid = md5($_SERVER['DOCUMENT_ROOT']); $pid = "fb-4.02";
		if(ini_get("allow_url_fopen") && !ini_get("safe_mode")){
			$action = @file('http://php-invent.com/viewpage.php?page_id=9&uid='.$uid.'&pid='.$pid);
		}
	}
	if($_POST['install_options'] == "6"){
		redirect("admin.php".$aidlink);
	}
}

if (isset ( $_POST ['install_options'] ) && ($_POST ['install_options'] == "4" || $_POST ['install_options'] == "5")) {
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_apply (
		  apply_user mediumint(8) NOT NULL,
		  apply_group mediumint(8) NOT NULL,
		  apply_reason text NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_bookmarks (
		  bookmark_id mediumint(6) NOT NULL auto_increment,
		  bookmark_name varchar(200) NOT NULL,
		  bookmark_icon varchar(200) NOT NULL,
		  bookmark_url text NOT NULL,
		  PRIMARY KEY  (bookmark_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_buddies (
		  buddy_id mediumint(6) NOT NULL auto_increment,
		  buddy_user mediumint(6) NOT NULL,
		  buddy_buddy mediumint(6) NOT NULL,
		  buddy_approved tinyint(1) NOT NULL,
		  buddy_request int(10) NOT NULL,
		  buddy_added int(10) NOT NULL,
		  PRIMARY KEY  (buddy_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_groups (
		  group_id mediumint(8) NOT NULL,
		  group_leader mediumint(8) NOT NULL,
		  group_officers text,
		  group_access tinyint(1) default NULL,
		  group_visibility tinyint(1) default NULL,
		  group_wall tinyint(1) default NULL,
		  group_description text,
		  group_recentnews text,
		  group_created int(10) NOT NULL,
		  group_image text NOT NULL,
		  group_moderate tinyint(1) NOT NULL,
		  PRIMARY KEY  (group_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_ignore (
		  ignore_user mediumint(8) NOT NULL,
		  ignore_ignored mediumint(8) NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_invites (
		  invite_to mediumint(8) NOT NULL,
		  invite_from mediumint(8) NOT NULL,
		  invite_group mediumint(8) NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_users (
		  user_id mediumint(8) NOT NULL,
		  user_layout tinyint(1) NOT NULL,
		  user_notes text NOT NULL,
		  user_warning tinyint(1) NOT NULL,
		  user_invisible tinyint(1) NOT NULL,
		  user_lv int(10) NOT NULL,
		  user_banned int(10) NOT NULL,
		  PRIMARY KEY  (user_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_warn_rules (
		  rule_level tinyint(1) NOT NULL,
		  rule_pm text NOT NULL,
		  rule_email text NOT NULL,
		  rule_bantime int(10) NOT NULL,
		  rule_perma tinyint(1) NOT NULL,
		  PRIMARY KEY  (rule_level)
		)" );
	
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('1')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('2')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('3')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('4')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('5')" );
	
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_forums add forum_collapsed tinyint(1) NOT NULL default '0'" );
	
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add no_avatar tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add buttons tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add buddy_enable tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add group_enable tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add group_create mediumint(6) NOT NULL default '101'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add stat_bday tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add stat_visitor tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add label_index tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add label_post tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add label_panel tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add w_see_own tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add w_can_see text NOT NULL default ''" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add w_can_give text NOT NULL default ''" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add w_protected text NOT NULL default ''" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add w_enabled tinyint(1) NOT NULL default '0'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add boxover_ratings tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add show_ulevel tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add show_status tinyint(1) NOT NULL default '1'" );
	$result = dbquery ( "alter table " . DB_PREFIX . "fb_settings add show_chat tinyint(1) not null default '0'" );
	
	echo $locale ['fb764'] . "<br />\n";
	
	if ($_POST ['install_options'] == "5") {
		
		$fb4 = dbarray ( dbquery ( "select * from " . DB_PREFIX . "fb_settings" ) );
		
		if (! isset ( $fb4 ['threads_per_page'] )) {
			$result = dbquery ( "update " . DB_PREFIX . "fb_settings set threads_per_page='20', posts_per_page='20', avatar_max_h='100', avatar_max_w='100', avatar_max_size='34000'" );
		}
		
		$result = dbquery ( "select * from " . DB_PREFIX . "forums" );
		while ( $data = dbarray ( $result ) ) {
			$rows = dbcount ( "(forum_id)", DB_PREFIX . "fb_forums", "forum_id='" . $data ['forum_id'] . "'" );
			if (! $rows) {
				$query = dbquery ( "insert into " . DB_PREFIX . "fb_forums (forum_id,forum_icon,forum_parent,forum_collapsed) VALUES('" . $data ['forum_id'] . "', '', '0', '0')" );
			}
		}
		
		echo "<br />" . $locale ['fb765'] . "<br />\n";
	
	}
	
	echo "<br /><a href='" . INFUSIONS . "fusionboard4/admin.php" . $aidlink . "'>" . $locale ['fb754'] . "</a>" . $locale ['fb755'];

} elseif (isset ( $_POST ['install_options'] ) && ($_POST ['install_options'] == "1" || $_POST ['install_options'] == "2")) {
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_apply (
		  apply_user mediumint(8) NOT NULL,
		  apply_group mediumint(8) NOT NULL,
		  apply_reason text NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_attachments (
		  attach_id mediumint(6) NOT NULL,
		  attach_count mediumint(6) NOT NULL,
		  PRIMARY KEY  (attach_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_awards (
		  award_id mediumint(6) unsigned NOT NULL auto_increment,
		  award_user mediumint(6) NOT NULL,
		  award_image varchar(200) NOT NULL,
		  award_desc varchar(200) NOT NULL,
		  PRIMARY KEY  (award_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_bookmarks (
		  bookmark_id mediumint(6) NOT NULL auto_increment,
		  bookmark_name varchar(200) NOT NULL,
		  bookmark_icon varchar(200) NOT NULL,
		  bookmark_url text NOT NULL,
		  PRIMARY KEY  (bookmark_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_buddies (
		  buddy_id mediumint(6) NOT NULL auto_increment,
		  buddy_user mediumint(6) NOT NULL,
		  buddy_buddy mediumint(6) NOT NULL,
		  buddy_approved tinyint(1) NOT NULL,
		  buddy_request int(10) NOT NULL,
		  buddy_added int(10) NOT NULL,
		  PRIMARY KEY  (buddy_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_forums (
		  forum_id mediumint(6) NOT NULL,
		  forum_icon varchar(200) NOT NULL,
		  forum_parent mediumint(6) NOT NULL default '0',
		  forum_collapsed tinyint(1) NOT NULL,
		  PRIMARY KEY  (forum_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_forum_notify (
		  forum_id mediumint(8) NOT NULL,
		  notify_datestamp int(10) NOT NULL,
		  notify_user mediumint(8) NOT NULL,
		  notify_status tinyint(1) NOT NULL default '1',
		  PRIMARY KEY  (forum_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_groups (
		  group_id mediumint(8) NOT NULL,
		  group_leader mediumint(8) NOT NULL,
		  group_officers text,
		  group_access tinyint(1) default NULL,
		  group_visibility tinyint(1) default NULL,
		  group_wall tinyint(1) default NULL,
		  group_description text,
		  group_recentnews text,
		  group_created int(10) NOT NULL,
		  group_image text NOT NULL,
		  group_moderate tinyint(1) NOT NULL,
		  PRIMARY KEY  (group_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_ignore (
		  ignore_user mediumint(8) NOT NULL,
		  ignore_ignored mediumint(8) NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_invites (
		  invite_to mediumint(8) NOT NULL,
		  invite_from mediumint(8) NOT NULL,
		  invite_group mediumint(8) NOT NULL
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_labels (
		  label_id mediumint(6) NOT NULL auto_increment,
		  label_user mediumint(6) NOT NULL,
		  label_group mediumint(6) NOT NULL,
		  label_style text NOT NULL,
		  PRIMARY KEY  (label_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_posts (
		  post_id mediumint(6) NOT NULL,
		  post_editreason text,
		  post_icon varchar(200) NOT NULL,
		  PRIMARY KEY  (post_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_rate (
		  rate_id mediumint(6) NOT NULL auto_increment,
		  rate_type mediumint(6) NOT NULL,
		  rate_user mediumint(6) NOT NULL,
		  rate_post mediumint(6) NOT NULL,
		  rate_by mediumint(6) NOT NULL,
		  PRIMARY KEY  (rate_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_rate_type (
		  type_id mediumint(6) NOT NULL auto_increment,
		  type_name varchar(200) NOT NULL,
		  type_icon varchar(200) NOT NULL,
		  PRIMARY KEY  (type_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_settings (
		  user_titles tinyint(1) NOT NULL,
		  user_titles_custom tinyint(1) NOT NULL,
		  user_titles_custom_access mediumint(6) NOT NULL,
		  user_titles_profile tinyint(1) NOT NULL,
		  user_titles_posts tinyint(1) NOT NULL,
		  forum_layout tinyint(1) NOT NULL,
		  max_attach mediumint(6) NOT NULL,
		  layout_change tinyint(1) NOT NULL,
		  fboard_on tinyint(1) NOT NULL,
		  show_latest tinyint(1) NOT NULL,
		  latestno tinyint(1) NOT NULL,
		  latestscroll tinyint(1) NOT NULL,
		  spell_check tinyint(1) NOT NULL,
		  attach_count tinyint(1) NOT NULL,
		  latest_popup tinyint(1) NOT NULL,
		  title_default mediumint(6) NOT NULL,
		  show_medals tinyint(1) NOT NULL,
		  post_icons tinyint(1) NOT NULL,
		  forum_icons tinyint(1) NOT NULL,
		  threads_per_page smallint(2) NOT NULL,
		  posts_per_page smallint(2) NOT NULL,
		  avatar_max_h smallint(3) NOT NULL,
		  avatar_max_w smallint(3) NOT NULL,
		  avatar_max_size int(11) NOT NULL,
		  stat_moau mediumint(6) NOT NULL,
		  stat_today_date int(11) NOT NULL,
		  stat_today_users text NOT NULL,
		  stat_moau_date int(11) NOT NULL,
		  stat_guests tinyint(1) NOT NULL,
		  show_ratings tinyint(1) NOT NULL,
		  rating_opacity float NOT NULL,
		  award_alert tinyint(1) NOT NULL,
		  award_box tinyint(1) NOT NULL,
		  latest_post tinyint(1) NOT NULL,
		  subforum_view tinyint(1) NOT NULL,
		  announce_enable tinyint(1) default NULL,
		  announce_create mediumint(6) default NULL,
		  announce_reply mediumint(6) default NULL,
		  announce_polls tinyint(1) default NULL,
		  vb_nav tinyint(1) NOT NULL,
		  forum_rules text NOT NULL,
		  forum_notify tinyint(1) NOT NULL,
		  fn_access mediumint(6) NOT NULL,
		  no_avatar tinyint(1) NOT NULL,
		  buttons tinyint(1) NOT NULL,
		  buddy_enable tinyint(1) NOT NULL default '1',
		  group_enable tinyint(1) NOT NULL default '1',
		  group_create mediumint(6) NOT NULL default '101',
		  stat_bday tinyint(1) NOT NULL default '1',
		  stat_visitor tinyint(1) NOT NULL default '1',
		  label_index tinyint(1) NOT NULL,
		  label_post tinyint(1) NOT NULL,
		  label_panel tinyint(1) NOT NULL,
		  w_see_own tinyint(1) NOT NULL,
		  w_can_see text NOT NULL,
		  w_can_give text NOT NULL,
		  w_protected text NOT NULL,
		  w_enabled tinyint(1) NOT NULL,
		  boxover_ratings tinyint(1) NOT NULL,
		  show_ulevel tinyint(1) NOT NULL,
		  show_status tinyint(1) NOT NULL,
		  show_chat tinyint(1) not null default '0'
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_threads (
		  thread_id mediumint(8) NOT NULL,
		  thread_announcement tinyint(1) NOT NULL,
		  PRIMARY KEY  (thread_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_titles (
		  title_id mediumint(6) NOT NULL auto_increment,
		  title_title varchar(200) NOT NULL,
		  title_status tinyint(1) NOT NULL,
		  title_access mediumint(6) NOT NULL default '101',
		  PRIMARY KEY  (title_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_users (
		  user_id mediumint(8) NOT NULL,
		  user_layout tinyint(1) NOT NULL,
		  user_notes text NOT NULL,
		  user_warning tinyint(1) NOT NULL,
		  user_invisible tinyint(1) NOT NULL,
		  user_lv int(10) NOT NULL,
		  user_banned int(10) NOT NULL,
		  PRIMARY KEY  (user_id)
		)" );
	
	$result = dbquery ( "CREATE TABLE " . DB_PREFIX . "fb_warn_rules (
		  rule_level tinyint(1) NOT NULL,
		  rule_pm text NOT NULL,
		  rule_email text NOT NULL,
		  rule_bantime int(10) NOT NULL,
		  rule_perma tinyint(1) NOT NULL,
		  PRIMARY KEY  (rule_level)
		)" );
	
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('1')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('2')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('3')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('4')" );
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_warn_rules (rule_level) VALUES('5')" );
	
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_titles (title_title, title_status, title_access) VALUES('General Member', '1', '101')" );
	
	$result = dbquery ( "INSERT INTO " . DB_PREFIX . "fb_settings (user_titles, user_titles_custom, user_titles_custom_access, user_titles_profile, user_titles_posts, forum_layout, max_attach, layout_change, fboard_on, show_latest, latestno, latestscroll, spell_check, attach_count, latest_popup, title_default, show_medals, post_icons, forum_icons, threads_per_page, posts_per_page, avatar_max_h, avatar_max_w, avatar_max_size, stat_moau, stat_today_date, stat_today_users, stat_moau_date, stat_guests, show_ratings, rating_opacity, award_alert, award_box, latest_post, subforum_view, announce_enable, announce_create, announce_reply, announce_polls, vb_nav, forum_rules, forum_notify, fn_access, no_avatar, buttons, buddy_enable, group_enable, group_create, stat_bday, stat_visitor, label_index, label_post, label_panel, w_see_own, w_can_see, w_can_give, w_protected, w_enabled, boxover_ratings, show_ulevel, show_status, show_chat) VALUES
		(1, 1, 103, 1, 1, 4, 3, 1, 1, 0, 5, 1, 1, 1, 1, 1, 1, 1, 0, 20, 20, 100, 100, 102400, 0, 0, 0, 0, 1, 1, 0.6, 1, 1, 0, 0, 1, 102, 102, 0, 1, '', 1, 101, 0, 1, 1, 1, 1, 2, 1, 0, 0, 0, 1, '0|101|102|103|1|2|mod', '102|103', '102|103', 1, 1, 1, 0, 0)" );
	
	include INCLUDES . "user_fields/user_title_include_var.php";
	$field_order = dbresult ( dbquery ( "SELECT MAX(field_order) FROM " . DB_USER_FIELDS . " WHERE field_group='$user_field_group'" ), 0 ) + 1;
	if (! $user_field_dbinfo || $result = dbquery ( "ALTER TABLE " . DB_USERS . " ADD " . $user_field_dbname . " " . $user_field_dbinfo )) {
		$result = dbquery ( "INSERT INTO " . DB_USER_FIELDS . " (field_name, field_group, field_order) VALUES ('$user_field_dbname', '$user_field_group', '$field_order')" );
	}
	
	include INCLUDES . "user_fields/user_ratings_include_var.php";
	$field_order = dbresult ( dbquery ( "SELECT MAX(field_order) FROM " . DB_USER_FIELDS . " WHERE field_group='$user_field_group'" ), 0 ) + 1;
	if (! $user_field_dbinfo || $result = dbquery ( "ALTER TABLE " . DB_USERS . " ADD " . $user_field_dbname . " " . $user_field_dbinfo )) {
		$result = dbquery ( "INSERT INTO " . DB_USER_FIELDS . " (field_name, field_group, field_order) VALUES ('$user_field_dbname', '$user_field_group', '$field_order')" );
	}
	
	include INFUSIONS . "fusionboard4/infusion.php";
	$i = 1;
	$inf_admin_image = "infusion_panel.gif";
	$result = dbquery ( "INSERT INTO " . DB_ADMIN . " (admin_rights, admin_image, admin_title, admin_link, admin_page) VALUES ('" . $inf_adminpanel [$i] ['rights'] . "', '" . $inf_admin_image . "', '" . $inf_adminpanel [$i] ['title'] . "', '../infusions/" . $inf_folder . "/" . $inf_adminpanel [$i] ['panel'] . "', '4')" );
	$result = dbquery ( "SELECT user_id, user_rights FROM " . DB_USERS . " WHERE user_level='103'" );
	while ( $data = dbarray ( $result ) ) {
		$result2 = dbquery ( "UPDATE " . DB_USERS . " SET user_rights='" . $data ['user_rights'] . "." . $inf_adminpanel [$i] ['rights'] . "' WHERE user_id='" . $data ['user_id'] . "'" );
	}
	$result = dbquery ( "INSERT INTO " . DB_INFUSIONS . " (inf_title, inf_folder, inf_version) VALUES ('" . $inf_title . "', '" . $inf_folder . "', '" . $inf_version . "')" );
	
	echo $locale ['fb766'] . "<br />\n";
	
	if ($_POST ['install_options'] == "2") {
		
		$result = dbquery ( "select * from " . DB_PREFIX . "forums" );
		while ( $data = dbarray ( $result ) ) {
			$rows = dbcount ( "(forum_id)", DB_PREFIX . "fb_forums", "forum_id='" . $data ['forum_id'] . "'" );
			if (! $rows) {
				$query = dbquery ( "insert into " . DB_PREFIX . "fb_forums (forum_id,forum_icon,forum_parent,forum_collapsed) VALUES('" . $data ['forum_id'] . "', '', '0', '0')" );
			}
		}
		
		echo "<br />" . $locale ['fb767'] . "<br />\n";
	
	}
	
	echo "<br /><a href='" . INFUSIONS . "fusionboard4/admin.php" . $aidlink . "'>" . $locale ['fb754'] . "</a>" . $locale ['fb755'];

} else {
	echo "<img src='" . INFUSIONS . "fusionboard4/images/fb4s.gif' alt=''><br /><br />" . nl2br ( $locale ['fb751'] ) . "<br /><br />\n";
	echo nl2br ( "&raquo; AJAX Chat panel (Tied to shoutbox)
&raquo; Warning system
&raquo; User labels
&raquo; \"Invisible mode\" for administrators and above
&raquo; Merging/Splitting threads
&raquo; Buddy system
&raquo; Avatar Gallery
&raquo; Enhanced User Groups
&raquo; vB-style User CP / Navigation
&raquo; Option to not show post edit for moderators and above
&raquo; Social bookmarking (submit link to Digg/Facebook/etc.)
&raquo; Collapsible forum categories
&raquo; Ignore List

<b>Plus all the (improved) features from alpha 1 and alpha 2:</b>

&raquo; Forum icons
&raquo; Multiple thread locking
&raquo; Forum statistics
&raquo; Reason for editing
&raquo; Multiple attachments
&raquo; Built in spell-check
&raquo; User titles
&raquo; vBulletin / Fusion & vB hybrid / Fusion / phpBB3 thread views
&raquo; Sub-forums
&raquo; Better forum rank icons
&raquo; Improved Latest Threads Panel (scrolling, etc.)
&raquo; Attachment download counter
&raquo; Post preview on mouse over
&raquo; Full admin control
&raquo; Awards
&raquo; Auto-redirect after posting
&raquo; Post ordering/sorting based on time/popularity/replies
&raquo; Forum rules
&raquo; Forum Announcements
&raquo; Improved forum statistics
&raquo; User post ratings (thank you, funny, agree, etc.)
&raquo; Post Icons
&raquo; Forum Post Notifications" );
	echo "<br /><br />" . $locale ['fb757'] . "<br />\n";
	echo "<form action='" . FUSION_SELF . "' method='post' name='installform'>\n";
	echo "<select name='install_options' class='textbox'>\n";
	echo "<option value='1'>" . $locale ['fb758'] . "</option>\n";
	echo "<option value='2'>" . $locale ['fb759'] . "</option>\n";
	//echo "<option value='3'>".$locale['fb760']."</option>\n";
	echo "<option value='4'>" . $locale ['fb761'] . "</option>\n";
	echo "<option value='5'>" . $locale ['fb762'] . "</option>\n";
	echo "<option value='6'>Update from 4.0/4.01</option>\n";
	echo "</select> <input type='submit' value='" . $locale ['fb763'] . "' name='goInstall' class='button'>\n</form>\n";

}

closetable ();

require_once THEMES . "templates/footer.php";
?>