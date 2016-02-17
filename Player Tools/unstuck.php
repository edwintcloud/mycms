<?php
/*-------------------------------------------------------+
| ArcSite with PHP-Fusion CMS Core
| Copyright (C) 2010
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ./Player Tools/unstuck.php
| Author: The Red
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../maincore.php";
require_once THEMES."templates/header.php";
include LOCALE.LOCALESET."playertools.php";

if ($emulator == 0) { redirect("../news.php"); }

$logcon = mysql_connect($ldb_host, $ldb_user, $ldb_pass, true);
$logsel = mysql_select_db($ldb_name, $logcon);
if (!$logcon) {
	die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to establish connection to MySQL</b><br />".mysql_errno()." : ".mysql_error()."</div>");
} elseif (!$logsel) {
	die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to select <font color=orange>logon</font> database</b><br />".mysql_errno()." : ".mysql_error()."</div>");
}
$charcon = mysql_connect($cdb_host, $cdb_user, $cdb_pass, true);
$charsel = mysql_select_db($cdb_name, $charcon);
if (!$charcon) {
	die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to establish connection to MySQL</b><br />".mysql_errno()." : ".mysql_error()."</div>");
} elseif (!$charsel) {
	die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to select <font color=orange>character</font> database</b><br />".mysql_errno()." : ".mysql_error()."</div>");
}
if($emulator == 1) {
$gameacc_pull = mysql_query("SELECT * FROM accounts WHERE login = '".$userdata ['game_acc']."'", $logcon);
if (!$gameacc_pull) {
	echo mysql_error();
	return false;
} else {
	return $gameacc_pull;
}
$gaco = dbarray($gameacc_pull);
$char_pull = mysql_query("SELECT * FROM characters WHERE acct = '".$gaco ['acct']."'", $charcon);
if (!$char_pull) {
	echo mysql_error();
	return false;
} else {
	return $char_pull;
}
} elseif($emulator == 2) {
$gameacc_pull = mysql_query("SELECT * FROM account WHERE username = '".$userdata ['game_acc']."'", $logcon);
if (!$gameacc_pull) {
	echo mysql_error();
	return false;
} else {
	return $gameacc_pull;
}
$gaco = dbarray($gameacc_pull);
$char_pull = mysql_query("SELECT * FROM characters WHERE account = '".$gaco ['id']."'", $charcon);
if (!$char_pull) {
	echo mysql_error();
	return false;
} else {
	return $char_pull;
}
}
function mysql_fetch_all($char_pull) {
   while($rotw = mysql_fetch_array($char_pull)) {
       $return[] = $rotw;
   }
   return $return;
}

$charlist = mysql_fetch_all($char_pull);

if (isset($_POST['register'])) {
	$error = ""; $db_fields = ""; $db_values = "";
	$character = stripinput(trim(preg_replace("/ +/i", " ", $_POST['character'])));

	if ($character == "") {
		$error .= $locale['pt003']."<br />\n";
	}

	if ($error == "") {
		opentable($locale['pt001']);
		echo "<div style='text-align:center'><br />\n".$locale['pt004']."<br /><br />\n".$locale['pt005']."<br /><br />\n</div>\n";
		closetable();
	} else {
		opentable($locale['pt0002']);
		echo "<div style='text-align:center'><br />\n".$locale['pt006']."<br /><br />\n$error<br />\n<a href='".FUSION_SELF."'>".$locale['pt007']."</a></div></br>\n";
		closetable();
	}
} else {
?>
<style type="text/css">
.pltb {
	position:absolute;
	width:400px;
	height:auto;
	text-align:center;
	font-family:verdana;
	font-size:12px;
    z-index:10;
	left: 50%;
    margin-left: -200px;
}

fieldset {
    padding:8px;
    border:1px solid #ccc;
}
</style>dfsgsg
<?php opentable($locale['pt001']); ?>
<p></p><p></p>
<div class="pltb">
  <fieldset>
<?php
echo "<form name='inputform' method='post' action='".FUSION_SELF."' onsubmit='return ValidateForm(this)'>\n";
echo "<table cellpadding='0' cellspacing='0' class='center'>\n<tr>\n";
echo "<td class='tbl'>".$locale['pt008']."<span style='color:#ff0000'>*</span></td>\n";
echo "<td class='tbl'><select name='character'>";
for($i = 0; $i < count($charlist); $i++) {
	echo "<option>".$charlist[$i]."</option>";
}
echo "</select></td>\n</tr>\n";
closetable();
?>
  </fieldset>
</div>

<?php
echo "<script type='text/javascript'>
function ValidateForm(frm) {
	if (frm.character.value==\"\") {
		alert(\"".$locale['pt003']."\");
		return false;
	}
}
</script>\n";

}
require_once THEMES."templates/footer.php";
?>
