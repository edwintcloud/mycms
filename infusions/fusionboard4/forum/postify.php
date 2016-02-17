<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");
add_to_title($locale['global_204']);

if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php"); }

if (!isset($_GET['error']) || !isnum($_GET['error']) || $_GET['error'] == 0 || $_GET['error'] > 4) { $_GET['error'] = 0; $errorb = ""; }
else if ($_GET['error'] == 1) { $errorb = $locale['440a']; }
else if ($_GET['error'] == 2) { $errorb = $locale['440b']; }
else if ($_GET['error'] == 3) { $errorb = $locale['441']; }
else if ($_GET['error'] == 4) { $errorb = $locale['450']; }

$valid_get = array("on", "off", "new", "reply", "edit", "none");

$_GET['forum'] = (isset($_GET['forum']) && in_array($_GET['forum'], $valid_get) ? $_GET['forum'] : "");
if (!in_array($_GET['post'], $valid_get)) { redirect("index.php"); }

if (($_GET['post'] == "on" || $_GET['post'] == "off") && $settings['thread_notify']) {
	$output = false;
	if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }
	$result = dbquery(
		"SELECT tt.*, tf.* FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		WHERE tt.thread_id='".$_GET['thread_id']."'"
	);
	if (dbrows($result)) {
		$data = dbarray($result);
		if (checkgroup($data['forum_access'])) {
			$output = true;
			opentable($locale['451']);
			echo "<div style='text-align:center'><br />\n";
			if ($_GET['post'] == "on" && !dbcount("(thread_id)", DB_THREAD_NOTIFY, "thread_id='".$_GET['thread_id']."' AND notify_user='".$userdata['user_id']."'")) {
				$result = dbquery("INSERT INTO ".DB_THREAD_NOTIFY." (thread_id, notify_datestamp, notify_user, notify_status) VALUES('".$_GET['thread_id']."', '".time()."', '".$userdata['user_id']."', '1')");
				echo $locale['452']."<br /><br />\n";
			} else {
				$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id='".$_GET['thread_id']."' AND notify_user='".$userdata['user_id']."'");
				echo $locale['453']."<br /><br />\n";
			}
			echo $locale['fb602']."<script type='text/javascript'>
			<!--
			function delayer(){ window.location = 'viewthread.php?forum_id=".$_GET['forum_id']."&thread_id=".$_GET['thread_id']."' }
			setTimeout('delayer()', 3000);
			//-->
			</script>";
			echo "<a href='viewthread.php?forum_id=".$_GET['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>".$locale['447']."</a> ::\n";
			echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['448']."</a> ::\n";
			echo "<a href='index.php'>".$locale['449']."</a><br /><br />\n</div>\n";
			echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
			closetable();
		}
	}
	if (!$output) redirect("index.php");
	
} elseif (($_GET['forum'] == "on" || $_GET['forum'] == "off") && ($fb4['forum_notify'] && checkgroup($fb4['fn_access']))) {

	$output = false;
	$result = dbquery(
		"SELECT * FROM ".DB_FORUMS." WHERE forum_id='".$_GET['forum_id']."'"
	);
	if (dbrows($result)) {
		$data = dbarray($result);
		if (checkgroup($data['forum_access'])) {
			$output = true;
			opentable($locale['fb620']);
			echo "<div style='text-align:center'><br />\n";
			if ($_GET['forum'] == "on" && !dbcount("(forum_id)", DB_PREFIX."fb_forum_notify", "forum_id='".$_GET['forum_id']."' AND notify_user='".$userdata['user_id']."'")) {
				$result = dbquery("INSERT INTO ".DB_PREFIX."fb_forum_notify (forum_id, notify_datestamp, notify_user, notify_status) VALUES('".$_GET['forum_id']."', '".time()."', '".$userdata['user_id']."', '1')");
				echo $locale['fb621']."<br /><br />\n";
			} else {
				$result = dbquery("DELETE FROM ".DB_PREFIX."fb_forum_notify WHERE forum_id='".$_GET['forum_id']."' AND notify_user='".$userdata['user_id']."'");
				echo $locale['fb622']."<br /><br />\n";
			}
			echo $locale['fb602']."<script type='text/javascript'>
			<!--
			function delayer(){ window.location = 'viewforum.php?forum_id=".$_GET['forum_id']."' }
			setTimeout('delayer()', 3000);
			//-->
			</script>";
			echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['448']."</a> ::\n";
			echo "<a href='index.php'>".$locale['449']."</a><br /><br />\n</div>\n";
			echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
			closetable();
		}
	}
	if (!$output) redirect("index.php");
	
} else if ($_GET['post'] == "new") {
	add_to_title($locale['global_201'].$locale['401']);
	opentable($locale['401']);
	echo "<div style='text-align:center'><br />\n";
	if ($errorb) {
		echo $errorb."<br /><br />\n";
	} else {
		echo $locale['442']."<br /><br />\n";
	}
	if ($_GET['error'] < 3) {
		if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }
		
		if ($fb4['forum_notify']) {
			$result = dbquery(
				"SELECT tn.*, tu.user_id,user_name,user_email FROM ".DB_PREFIX."fb_forum_notify tn
				LEFT JOIN ".DB_USERS." tu ON tn.notify_user=tu.user_id
				WHERE forum_id='".$_GET['forum_id']."' AND notify_user!='".$userdata['user_id']."'
			");
			if (dbrows($result)) {
				require_once INCLUDES."sendmail_include.php";
				$data2 = dbarray(dbquery("SELECT thread_subject FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'"));
				$link = $settings['siteurl']."forum/viewthread.php?thread_id=".$_GET['thread_id'];
				while ($data = dbarray($result)) {
					$message_el1 = array("{USERNAME}", "{THREAD_SUBJECT}", "{THREAD_URL}");
					$message_el2 = array($data['user_name'], $data2['thread_subject'], $link);
					$message_subject = str_replace("{THREAD_SUBJECT}", $data2['thread_subject'], $locale['550']);
					$message_content = str_replace($message_el1, $message_el2, $locale['551']);
					sendemail($data['user_name'],$data['user_email'],$settings['siteusername'],$settings['siteemail'],$message_subject,$message_content);
				}
			}
		}
		
		echo $locale['fb601']."<script type='text/javascript'>
		<!--
		function delayer(){ window.location = 'viewthread.php?thread_id=".$_GET['thread_id']."' }
		setTimeout('delayer()', 3000);
		//-->
		</script>";
		echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."'>".$locale['447']."</a> ::\n";
	}
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['448']."</a> ::\n";
	echo "<a href='index.php'>".$locale['449']."</a><br /><br /></div>\n";
	echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
	closetable();
} else if ($_GET['post'] == "reply") {
	if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }
	add_to_title($locale['global_201'].$locale['403']);
	opentable($locale['403']);
	echo "<div style='text-align:center'><br />\n";
	if ($errorb) {
		echo $errorb."<br /><br />\n";
	} else {
		echo $locale['443']."<br /><br />\n";
		if(isset($_GET['track']) && $_GET['track'] == "1"){
			echo $locale['452']."<br /><br />\n";
		}
	}
	if ($_GET['error'] < "2") {
		if (!isset($_GET['post_id']) || !isnum($_GET['post_id'])) { redirect("index.php"); }
		if ($settings['thread_notify']) {
			$result = dbquery(
				"SELECT tn.*, tu.user_id,user_name,user_email FROM ".DB_THREAD_NOTIFY." tn
				LEFT JOIN ".DB_USERS." tu ON tn.notify_user=tu.user_id
				WHERE thread_id='".$_GET['thread_id']."' AND notify_user!='".$userdata['user_id']."' AND notify_status='1'
			");
			if (dbrows($result)) {
				require_once INCLUDES."sendmail_include.php";
				$data2 = dbarray(dbquery("SELECT thread_subject FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'"));
				$post = dbarray(dbquery("SELECT * FROM ".DB_POSTS." where thread_id='".$_GET['thread_id']."' order by post_datestamp desc limit 1"));
				$link = $settings['siteurl']."forum/viewthread.php?forum_id=".$_GET['forum_id']."&thread_id=".$_GET['thread_id']."&pid=".$_GET['post_id']."#post_".$_GET['post_id'];
				while ($data = dbarray($result)) {

					$message_el1 = array("{USERNAME}", "{THREAD_SUBJECT}", "{THREAD_URL}");
					$message_el2 = array($data['user_name'], $data2['thread_subject'], $link);
					$message_subject = str_replace("{THREAD_SUBJECT}", $data2['thread_subject'], $locale['550']);
					$message_content = str_replace($message_el1, $message_el2, $locale['551']);
					$message_content .= "\n\n".$locale['fb513']."\n---------------------------------------------------------\n";
					$message_content .= trimlink($post['post_message'], 200);
					sendemail($data['user_name'],$data['user_email'],$settings['siteusername'],$settings['siteemail'],$message_subject,$message_content);
				}
				$result = dbquery("UPDATE ".DB_THREAD_NOTIFY." SET notify_status='0' WHERE thread_id='".$_GET['thread_id']."' AND notify_user!='".$userdata['user_id']."'");
			}
		}
		if ($fb4['forum_notify']) {
			$result = dbquery(
				"SELECT tn.*, tu.user_id,user_name,user_email FROM ".DB_PREFIX."fb_forum_notify tn
				LEFT JOIN ".DB_USERS." tu ON tn.notify_user=tu.user_id
				WHERE forum_id='".$_GET['forum_id']."' AND notify_user!='".$userdata['user_id']."' AND notify_status='1'
			");
			if (dbrows($result)) {
				require_once INCLUDES."sendmail_include.php";
				$data2 = dbarray(dbquery("SELECT thread_subject FROM ".DB_THREADS." WHERE thread_id='".$_GET['thread_id']."'"));
				$link = $settings['siteurl']."forum/viewthread.php?forum_id=".$_GET['forum_id']."&thread_id=".$_GET['thread_id']."&pid=".$_GET['post_id']."#post_".$_GET['post_id'];
				while ($data = dbarray($result)) {
					
					if(!dbcount("(thread_id)", DB_THREAD_NOTIFY, "thread_id='".$_GET['thread_id']."' and notify_user='".$data['notify_user']."'")){
					
					$message_el1 = array("{USERNAME}", "{THREAD_SUBJECT}", "{THREAD_URL}");
					$message_el2 = array($data['user_name'], $data2['thread_subject'], $link);
					$message_subject = str_replace("{THREAD_SUBJECT}", $data2['thread_subject'], $locale['550']);
					$message_content = str_replace($message_el1, $message_el2, $locale['551']);
					sendemail($data['user_name'],$data['user_email'],$settings['siteusername'],$settings['siteemail'],$message_subject,$message_content);
					
					$result = dbquery("UPDATE ".DB_PREFIX."fb_forum_notify SET notify_status='0' WHERE forum_id='".$_GET['forum_id']."' AND notify_user='".$data['notify_user']."'");
					
					}
				}
			}
		}
		echo $locale['fb601']."<script type='text/javascript'>
		<!--
		function delayer(){ window.location = 'viewthread.php?thread_id=".$_GET['thread_id']."&pid=".$_GET['post_id']."#post_".$_GET['post_id']."' }
		setTimeout('delayer()', 3000);
		//-->
		</script>";
		echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."&amp;pid=".$_GET['post_id']."#post_".$_GET['post_id']."'>".$locale['447']."</a> ::\n";
	} else {
		if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }
		$data = dbarray(dbquery("SELECT post_id FROM ".DB_POSTS." WHERE thread_id='".$_GET['thread_id']."' ORDER BY post_id DESC"));
		echo $locale['fb601']."<script type='text/javascript'>
		<!--
		function delayer(){ window.location = 'viewthread.php?thread_id=".$_GET['thread_id']."&pid=".$_GET['post_id']."#post_".$_GET['post_id']."' }
		setTimeout('delayer()', 3000);
		//-->
		</script>";
		echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."&amp;pid=".$data['post_id']."#post_".$data['post_id']."'>".$locale['447']."</a> ::\n";
	}
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['448']."</a> ::\n";
	echo "<a href='index.php'>".$locale['449']."</a><br /><br />\n</div>\n";
	echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
	closetable();
} else if ($_GET['post'] == "edit") {
	if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php"); }
	add_to_title($locale['global_201'].$locale['409']);
	opentable($locale['409']);
	echo "<div style='text-align:center'><br />\n";
	if ($errorb) {
		echo $errorb."<br /><br />\n";
	} else {
		echo $locale['446']."<br /><br />\n";
	}
	echo $locale['fb601']."<script type='text/javascript'>
	<!--
	function delayer(){ window.location = 'viewthread.php?thread_id=".$_GET['thread_id']."&pid=".$_GET['post_id']."#post_".$_GET['post_id']."' }
	setTimeout('delayer()', 3000);
	//-->
	</script>";
	echo "<a href='viewthread.php?thread_id=".$_GET['thread_id']."&amp;pid=".$_GET['post_id']."#post_".$_GET['post_id']."'>".$locale['447']."</a> ::\n";
	echo "<a href='viewforum.php?forum_id=".$_GET['forum_id']."'>".$locale['448']."</a> ::\n";
	echo "<a href='index.php'>".$locale['449']."</a><br /><br />\n</div>\n";
	echo "<div style='text-align:right; margin-top:5px;'>".showPoweredBy()."</div>";
	closetable();
}
?>