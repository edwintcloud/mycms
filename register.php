<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: register.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "maincore.php";
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."register.php";
include LOCALE.LOCALESET."user_fields.php";
// start: secure code by phpfusion-freak.dk
if (file_exists(LOCALE.LOCALESET."register_security.php")) {
	include LOCALE.LOCALESET."register_security.php";
} else {
	include LOCALE."English/register_security.php";
}
// end: secure code by phpfusion-freak.dk

// Database Connection -------------------------------------------------------------
// You should not need to change these two, unless you know what you are doing and know you need to.

if (iMEMBER || !$settings['enable_registration']) { redirect("news.php"); }

$newcon = new mysqli($ldb_host, $ldb_user, $ldb_pass, $ldb_name);

if (isset($_GET['activate'])) {
	if (!preg_check("/^[0-9a-z]{32}$/", $_GET['activate'])) { redirect("index.php"); }
	$result = dbquery("SELECT * FROM ".DB_NEW_USERS." WHERE user_code='".$_GET['activate']."'");
	if (dbrows($result)) {
		$data = dbarray($result);
		$user_info = unserialize($data['user_info']);
		$user_status = $settings['admin_activation'] == "1" ? "2" : "0";

		$profile_method = "validate_insert"; $db_fields = ""; $db_values = "";
		$result = dbquery("SELECT * FROM ".DB_USER_FIELDS." ORDER BY field_order");
		if (dbrows($result)) {
			while($data = dbarray($result)) {
				if (file_exists(LOCALE.LOCALESET."user_fields/".$data['field_name'].".php")) {
					include LOCALE.LOCALESET."user_fields/".$data['field_name'].".php";
				}
				if (file_exists(INCLUDES."user_fields/".$data['field_name']."_include.php")) {
					include INCLUDES."user_fields/".$data['field_name']."_include.php";
				}
			}
		}

		$result = dbquery("INSERT INTO ".DB_USERS." (user_name, user_password, user_admin_password, user_email, user_hide_email, user_avatar, user_posts, user_threads, user_joined, user_lastvisit, user_ip, user_rights, user_groups, user_level, user_status".$db_fields.") VALUES('".$user_info['user_name']."', '".$user_info['user_password']."', '', '".$user_info['user_email']."', '".$user_info['user_hide_email']."', '', '0', '0', '".time()."', '0', '".USER_IP."', '', '', '101', '$user_status'".$db_values.")");
		$result = dbquery("DELETE FROM ".DB_NEW_USERS." WHERE user_code='".$_GET['activate']."'");
		add_to_title($locale['global_200'].$locale['401']);
		opentable($locale['401']);
		if ($settings['admin_activation'] == "1") {
			echo "<div style='text-align:center'><br />\n".$locale['455']."<br /><br />\n".$locale['453']."<br /><br />\n</div>\n";
		} else {
			echo "<div style='text-align:center'><br />\n".$locale['455']."<br /><br />\n".$locale['452']."<br /><br />\n</div>\n";
		}
		closetable();
	} else {
		redirect("index.php");
	}
} elseif (isset($_POST['register'])) {
	if ($settings['display_validation'] == "1") {
		include_once INCLUDES."securimage/securimage.php";
	}

function sha_password($user,$pass){
$user = strtoupper($user);
$pass = strtoupper($pass);

return SHA1($user.':'.$pass);
}
	$error = ""; $db_fields = ""; $db_values = "";
	$username = stripinput(trim(preg_replace("/ +/i", " ", $_POST['username'])));
	$email = stripinput(trim(preg_replace("/ +/i", "", $_POST['email'])));
	$password1 = stripinput(trim(preg_replace("/ +/i", "", $_POST['password1'])));
	if($emulator != 0) {
	$game_username = stripinput(trim(preg_replace("/ +/i", " ", $_POST['game_username'])));
	$game_password1 = stripinput(trim(preg_replace("/ +/i", "", $_POST['game_password1'])));
	$game_password2 = sha_password($game_username,$_POST['game_password1']); }

	if ($username == "" || $password1 == "" || $email == "") {
		$error .= $locale['402']."<br />\n";
	}
	if($emulator != 0) {
	if ($game_username == "" || $game_password1 == "") {
		$error .= $locale['1402']."<br />\n";
	}
	if (!preg_match("/^[-0-9A-Z_@\s]+$/i", $game_username)) {
		$error .= $locale['1403']."<br />\n";
	}
	if (preg_match("/^[0-9A-Z@]{6,20}$/i", $game_password1)) {
		if ($game_password1 != $_POST['game_password2']) $error .= $locale['1404']."<br />\n";
	} else {
		$error .= $locale['1405']."<br />\n";
	}
	if($emulator != 0) {
	if($emulator == 2) {
	$cola = $newcon->query("SELECT * FROM account WHERE username='$game_username'"); } elseif($emulator == 1) { $cola = $newcon->query("SELECT * FROM accounts WHERE login='$game_username'"); }
	if ($cola->num_rows > 0) { $error = $locale['1407']."<br />\n"; }
	}
	}
	if (!preg_match("/^[-0-9A-Z_@\s]+$/i", $username)) {
		$error .= $locale['403']."<br />\n";
	}

	if (preg_match("/^[0-9A-Z@]{6,20}$/i", $password1)) {
		if ($password1 != $_POST['password2']) $error .= $locale['404']."<br />\n";
	} else {
		$error .= $locale['405']."<br />\n";
	}

	if (!preg_match("/^[-0-9A-Z_\.]{1,50}@([-0-9A-Z_\.]+\.){1,50}([0-9A-Z]){2,4}$/i", $email)) {
		$error .= $locale['406']."<br />\n";
	}

	$email_domain = substr(strrchr($email, "@"), 1);
	$result = dbquery("SELECT * FROM ".DB_BLACKLIST." WHERE blacklist_email='$email' OR blacklist_email='$email_domain'");
	if (dbrows($result) != 0) { $error = $locale['411']."<br />\n"; }

	$result = dbquery("SELECT * FROM ".DB_USERS." WHERE user_name='$username'");
	if (dbrows($result) != 0) { $error = $locale['407']."<br />\n"; }

	if ($settings['email_verification'] == "1") {
		$result = dbquery("SELECT * FROM ".DB_NEW_USERS);
		while ($new_users = dbarray($result)) {
			$user_info = unserialize($new_users['user_info']);
			if ($new_users['user_email'] == $email) { $error = $locale['409']."<br />\n"; }
			if ($user_info['user_name'] == $username) { $error = $locale['407']."<br />\n"; break; }
		}
	}

	if ($settings['display_validation'] == "1") {
		$securimage = new Securimage();
		if (!isset($_POST['captcha_code']) || $securimage->check($_POST['captcha_code']) == false) {
			$error .= $locale['410']."<br />\n";
		}
	}

	$user_hide_email = isnum($_POST['user_hide_email']) ? $_POST['user_hide_email'] : "1";

	if ($settings['email_verification'] == "0") {
		$user_offset = isset($_POST['user_offset']) ? is_numeric($_POST['user_offset']) ? $_POST['user_offset'] : "0" : "0";

		$profile_method = "validate_insert"; $db_fields = ""; $db_values = "";
		$result = dbquery("SELECT * FROM ".DB_USER_FIELDS." ORDER BY field_order");
		if (dbrows($result)) {
			while($data = dbarray($result)) {
				if (file_exists(LOCALE.LOCALESET."user_fields/".$data['field_name'].".php")) {
					include LOCALE.LOCALESET."user_fields/".$data['field_name'].".php";
				}
				if (file_exists(INCLUDES."user_fields/".$data['field_name']."_include.php")) {
					include INCLUDES."user_fields/".$data['field_name']."_include.php";
				}
			}
		}
	}

	if ($error == "") {
		if ($settings['email_verification'] == "1") {
			require_once INCLUDES."sendmail_include.php";
			mt_srand((double)microtime()*1000000); $salt = "";
			for ($i = 0; $i <= 7; $i++) { $salt .= chr(rand(97, 122)); }
			$user_code = md5($email.$salt);
			$activation_url = $settings['siteurl']."register.php?activate=".$user_code;
			if (sendemail($username,$email,$settings['siteusername'], $settings['siteemail'], $locale['449'], $locale['450'].$activation_url)) {
				$user_info = serialize(array(
					"user_name" => $username,
					"user_password" => md5(md5($password1)),
					"user_email" => $email,
					"user_hide_email" => isnum($_POST['user_hide_email']) ? $_POST['user_hide_email'] : "1"
				));
				$result = dbquery("INSERT INTO ".DB_NEW_USERS." (user_code, user_email, user_datestamp, user_info) VALUES('$user_code', '".$email."', '".time()."', '$user_info')");
				opentable($locale['400']);
				echo "<div style='text-align:center'><br />\n".$locale['454']."<br /><br />\n</div>\n";
				closetable();
			} else {
				opentable($locale['456']);
				echo "<div style='text-align:center'><br />\n".$locale['457']."<br /><br />\n</div>\n";
				closetable();
			}
		} else {
			$user_status = $settings['admin_activation'] == "1" ? "2" : "0";
			$result = dbquery("INSERT INTO ".DB_USERS." (user_name, user_password, user_admin_password, user_email, user_hide_email, user_avatar, user_posts, user_threads, user_joined, user_lastvisit, user_ip, user_rights, user_groups, user_level, game_acc, user_status".$db_fields.") VALUES('$username', '".md5(md5($password1))."', '', '".$email."', '$user_hide_email', '', '0', '0', '".time()."', '0', '".USER_IP."', '', '', '101', '$game_username', '$user_status'".$db_values.")");
			if($emulator != 0) {
			if($emulator == 2) {
			$newcon->query("INSERT INTO `account` (`username`,`sha_pass_hash`,`email`,`last_ip`,`v`,`s`,`sessionkey`,`expansion`,`gmlevel`) VALUES ('$game_username','$game_password2','".$email."','".USER_IP."','0','0','','24','".$acc_lvl."')"); }
			elseif($emulator == 1) { $newcon->query("INSERT INTO `accounts` (`login`,`password`,`email`,`lastip`,`gm`,`banned`,`flags`) VALUES ('$game_username','$game_password1','".$email."','".USER_IP."','".$acc_lvl."','0','24')"); }
			}
			opentable($locale['400']);
			if ($settings['admin_activation'] == "1") {
				echo "<div style='text-align:center'><br />\n".$locale['451']."<br /><br />\n".$locale['453']."<br /><br />\n</div>\n";
			} else {
				echo "<div style='text-align:center'><br />\n".$locale['451']."<br /><br />"; if($emulator != 0) { echo "\n".$locale['1452']."<br /><br />\n</div>\n"; }
				elseif($emulator == 0) { echo "\n".$locale['452']."<br /><br />\n</div>\n"; }
			}
			closetable();
		}
	} else {
		opentable($locale['456']);
		echo "<div style='text-align:center'><br />\n".$locale['458']."<br /><br />\n$error<br />\n<a href='".FUSION_SELF."'>".$locale['459']."</a></div></br>\n";
		closetable();
	}
} else {
	if ($settings['email_verification'] == "0") {
		$offset_list = "";
		for ($i = -13; $i < 17; $i++) {
			if ($i > 0) { $offset = "+".$i; } else { $offset = $i; }
			$offset_list .= "<option".($offset == "0" ? " selected='selected'" : "").">".$offset."</option>\n";
		}
	}
?>
<script src="js/jquery.js" type="text/javascript" language="javascript"></script>
<script language="javascript">
//<!---------------------------------+
//  Developed by Roshan Bhattarai
//  Visit http://roshanbh.com.np for this script and more.
//  This notice MUST stay intact for legal use
// --------------------------------->
$(document).ready(function()
{
	$("#username").blur(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox").removeClass().addClass('messagebox').text('Checking...').fadeIn("slow");
		//check the username exists or not from ajax
		$.post("user_availability.php",{ username:$(this).val() } ,function(data)
        {
		  if(data=='no') //if username not avaiable
		  {
		  	$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
			{
			  //add message and change the class of the box and start fading
			  $(this).html('Unavailable').addClass('messageboxerror').fadeTo(900,1);
			});
          }
		  else
		  {
		  	$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
			{
			  //add message and change the class of the box and start fading
			  $(this).html('Available').addClass('messageboxok').fadeTo(900,1);
			});
		  }

        });

	});
});
</script>
<style type="text/css">
.top {
margin-bottom: 15px;
}
.messagebox{
	position:absolute;
	width:60px;
	margin-left:10px;
	border:1px solid #c93;
	background:#ffc;
	padding:3px;
}
.messageboxok{
	position:absolute;
	width:auto;
	margin-left:10px;
	border:1px solid #349534;
	background:#C9FFCA;
	padding:3px;
	font-weight:bold;
	color:#008000;

}
.messageboxerror{
	position:absolute;
	width:auto;
	margin-left:10px;
	border:1px solid #CC0000;
	background:#F7CBCA;
	padding:3px;
	font-weight:bold;
	color:#CC0000;
}

.LV_invalid {
    position:absolute;
	width:auto;
	margin-left:10px;
	border:1px solid #CC0000;
	background:#F7CBCA;
	padding:3px;
	font-weight:bold;
	color:#CC0000;
}

.LV_valid {
    position:absolute;
	width:auto;
	margin-left:10px;
	border:1px solid #349534;
	background:#C9FFCA;
	padding:3px;
	font-weight:bold;
	color:#008000;
}
</style>
<?php
opentable($locale['400']);
	if($emulator != 0) {
	echo "<div style='text-align:center'><font size=2><b><a href='forumreg.php'><font color=orange>Register For Forum Only</font></a> | <a href='gamereg.php'><font color=orange>Register Game Account</font></a></font></b></div></br>"; }
	echo "<div style='text-align:center'>".$locale['500']."\n";
	if ($settings['email_verification'] == "1") echo $locale['501']."\n";
	echo $locale['502'];
	if ($settings['email_verification'] == "1") echo "\n".$locale['503'];
	echo "</div><br />\n";
	echo "<form name='inputform' id='ajax' method='post' action='".FUSION_SELF."' onsubmit='return ValidateForm(this)'>\n";
	echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
	echo "<td class='tbl'>".$locale['u001']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='text' name='username' id='username' maxlength='30' class='textbox' style='width:200px;' /><span id='msgbox' style='display:none'></span></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>".$locale['u002']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='password' name='password1' id='password'  maxlength='20' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
?>
 <script src="js/digitalspaghetti.password.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript">
	jQuery('#password').pstrength();
</script>
<?php
	echo "<td class='tbl'>".$locale['u004']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='password' name='password2' id='password2' maxlength='20' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
?>
<script src="js/livevalidation.js" type="text/javascript"></script>
<script type="text/javascript">
var password2 = new LiveValidation('password2');
password2.add( Validate.Confirmation, { match: 'password' } );
password2.add( Validate.Presence, { validMessage: "Your passwords match" } );
</script>
<?php
	echo "<td class='tbl'>".$locale['u005']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='text' name='email' id='email' maxlength='100' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
?>
<script type="text/javascript">
var email = new LiveValidation( 'email' );
email.add( Validate.Presence );
email.add( Validate.Email );
</script>
<?php
	echo "<td class='tbl'>".$locale['u006']."</td>\n";
	echo "<td class='tbl'><label><input type='radio' name='user_hide_email' value='1' checked='checked' />".$locale['u007']."</label>\n";
	echo "<label><input type='radio' name='user_hide_email' value='0' />".$locale['u008']."</label></td>\n";
	echo "</tr>\n";
// start: secure code by phpfusion-freak.dk
    srand ((double)microtime()*1000000);
    $zahl = rand(1, 5);
    echo "<tr>
    <td class='tbl'>".$locale['secure_101'].":<span style='color:#ff0000'>*</span><br>".$locale['secure_ask'][$zahl]."</td>
    <td class='tbl'><input type='text' name='user_secure_con' maxlength='100' class='textbox' style='width:200px;'></td>
    </tr>\n
    <input type='hidden' name='user_secure_zahl' value='".$zahl."'>\n";
    unset($zahl);
    // end: secure code by phpfusion-freak.dk
	if ($settings['display_validation'] == "1") {
		echo "<tr>\n<td valign='top' class='tbl'>".$locale['504']."</td>\n<td class='tbl'>";
		echo "<img id='captcha' src='".INCLUDES."securimage/securimage_show.php' alt='".$locale['504']."' align='left' />\n";
    echo "<a href='".INCLUDES."securimage/securimage_play.php'><img src='".INCLUDES."securimage/images/audio_icon.gif' alt='' align='top' class='tbl-border' style='margin-bottom:1px' /></a><br />\n";
    echo "<a href='#' onclick=\"document.getElementById('captcha').src = '".INCLUDES."securimage/securimage_show.php?sid=' + Math.random(); return false\"><img src='".INCLUDES."securimage/images/refresh.gif' alt='' align='bottom' class='tbl-border' /></a>\n";
		echo "</td>\n</tr>\n<tr>";
		echo "<td class='tbl'>".$locale['505']."<span style='color:#ff0000'>*</span></td>\n";
		echo "<td class='tbl'><input type='text' name='captcha_code' class='textbox' style='width:100px' /></td>\n";
		echo "</tr>\n";
	}
	if($emulator != 0) {
	echo "<tr>\n<td class='tbl2'></td>\n";
	echo "<td class='tbl2'><strong>";
	echo "Game Account";
	echo "</strong></td>\n</tr>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>".$locale['u001']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='text' name='game_username' maxlength='30' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>".$locale['u002']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl' colspan='2'><input type='password' id='game_password1' name='game_password1' maxlength='20' class='textbox' style='width:200px;' /><br />\n";
	echo "<div style='width: 100px;'></div>";
	echo "</tr>\n<tr>\n";
	echo "<td class='tbl'>".$locale['u004']."<span style='color:#ff0000'>*</span></td>\n";
	echo "<td class='tbl'><input type='password' name='game_password2' maxlength='20' class='textbox' style='width:200px;' /></td>\n";
	echo "</tr>\n"; }

	if ($settings['email_verification'] == "0") {
		$result2 = dbquery("SELECT * FROM ".DB_USER_FIELDS." WHERE field_group != '4' GROUP BY field_group");
		while($data2 = dbarray($result2)) {
			$result3 = dbquery("SELECT * FROM ".DB_USER_FIELDS." WHERE field_group='".$data2['field_group']."' ORDER BY field_order");
			if (dbrows($result3)) {
				echo "<tr>\n<td class='tbl2'></td>\n";
				echo "<td class='tbl2'><strong>";
				if ($data2['field_group'] == 1) {
					echo $locale['u044'];
				} elseif ($data2['field_group'] == 2) {
					echo $locale['u045'];
				} elseif ($data2['field_group'] == 3) {
					echo $locale['u046'];
				}
				echo "</strong></td>\n</tr>\n";
				while($data3 = dbarray($result3)) {
					$profile_method = "input";
					if (file_exists(LOCALE.LOCALESET."user_fields/".$data3['field_name'].".php")) {
						include LOCALE.LOCALESET."user_fields/".$data3['field_name'].".php";
					}
					if (file_exists(INCLUDES."user_fields/".$data3['field_name']."_include.php")) {
						include INCLUDES."user_fields/".$data3['field_name']."_include.php";
					}
				}
			}
		}
	}

	if ($settings['enable_terms'] == 1) {
		echo "<tr>\n<td class='tbl'>".$locale['508'] ."<span style='color:#ff0000'>*</span></td>\n";
		echo "<td class='tbl'><input type='checkbox' id='agreement' name='agreement' value='1' onclick='checkagreement()' /> <span class='small'><label for='agreement'>".$locale['509'] ."</label></span></td>\n";
		echo "</tr>\n";
	}
	echo "<tr>\n<td align='center' colspan='2'><br />\n";
	echo "<input type='submit' name='register' value='".$locale['506']."' class='button'".($settings['enable_terms'] == 1 ? " disabled='disabled'" : "")." />\n";
	echo "</td>\n</tr>\n</table>\n</form><p align='right' <a href='http://english-167720622732.spampoison.com'><img src='http://pics5.inxhost.com/images/sticker.gif' border='0' width='80' height='15'/></a>\n";
	closetable();
	echo "<script type='text/javascript'>
function ValidateForm(frm) {
	if (frm.username.value==\"\") {
		alert(\"".$locale['550']."\");
		return false;
	}
	if (frm.password1.value==\"\") {
		alert(\"".$locale['551']."\");
		return false;
	}
	if (frm.email.value==\"\") {
		alert(\"".$locale['552']."\");
		return false;
	}
}
</script>\n";

	if ($settings['enable_terms'] == 1) {
		echo "<script language='JavaScript' type='text/javascript'>
			function checkagreement() {
				if(document.inputform.agreement.checked) {
					document.inputform.register.disabled=false;
				} else {
					document.inputform.register.disabled=true;
				}
			}
		</script>";
	}
}

require_once THEMES."templates/footer.php";
?>
