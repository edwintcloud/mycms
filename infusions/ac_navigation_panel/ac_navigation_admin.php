<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2009 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ac_navigation_admin.php
| Author: © 2009 ptown67
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../../maincore.php";
require_once THEMES."templates/admin_header.php";

include INFUSIONS."ac_navigation_panel/infusion_db.php";

if (!checkrights("ACN") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../index.php"); }

if (file_exists(INFUSIONS."ac_navigation_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."ac_navigation_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."ac_navigation_panel/locale/German.php";
}
if (isset($_GET['action']) && $_GET['action'] == "refresh") {
$i = 1; $k = 1;
$result = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' ORDER BY ac_order");
while ($data = dbarray($result)) {
$result2 = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order='$i' WHERE ac_id='".$data['ac_id']."'");
$result2 = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='".$data['ac_id']."' ORDER BY ac_order");
while ($data2 = dbarray($result2)) {
$result3 = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order='$k' WHERE ac_id='".$data2['ac_id']."'");
$k++;
}
$i++; $k = 1;
}
redirect(FUSION_SELF.$aidlink);
}
if (isset($_GET['status']) && !isset($message)) {
if ($_GET['status'] == "savecn") {
$message = $locale['acn_admin_msg_cat'];
} elseif ($_GET['status'] == "savecu") {
$message = $locale['acn_admin_msg_cat_edit'];
} elseif ($_GET['status'] == "savefn") {
$message = $locale['acn_admin_msg_link'];
} elseif ($_GET['status'] == "savefu") {
$message = $locale['acn_admin_msg_link_edit'];
} elseif ($_GET['status'] == "delcn") {
$message = $locale['acn_admin_msg_cat_nodel']."<br />\n<span class='small'>".$locale['acn_admin_msg_cat_links']."</span>";
} elseif ($_GET['status'] == "delcy") {
$message = $locale['acn_admin_msg_cat_del'];
} elseif ($_GET['status'] == "delfy") {
$message = $locale['acn_admin_msg_link_del'];
}
if ($message) {	echo "<div class='admin-message'>".$message."</div>\n"; }
}
if (isset($_POST['save_cat'])) {
$cat_name = trim(stripinput($_POST['cat_name']));
if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['t']) && $_GET['t'] == "cat")) {
$cat_access = isnum($_POST['cat_access']) ? $_POST['cat_access'] : 0;
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_name='$cat_name', ac_access='$cat_access' WHERE ac_id='".$_GET['ac_id']."'");
redirect(FUSION_SELF.$aidlink."&status=savecu");
} else {
if ($cat_name) {
$cat_order = isnum($_POST['cat_order']) ? $_POST['cat_order'] : "";
if(!$cat_order) $cat_order=dbresult(dbquery("SELECT MAX(ac_order) FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0'"),0)+1;
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_cat='0' AND ac_order>='$cat_order'");	
$result = dbquery("INSERT INTO ".DB_AC_NAVIGATION." (ac_cat, ac_name, ac_access, ac_order) VALUES ('0', '$cat_name', '$cat_access', '$cat_order')");
redirect(FUSION_SELF.$aidlink."&status=savecn");
}
}
} elseif (isset($_POST['save_ac'])) {
$ac_name = trim(stripinput($_POST['ac_name']));
$ac_url = $_POST['ac_url'];
$ac_target = isnum($_POST['ac_target']) ? $_POST['ac_target'] : 0;
$ac_cat = isnum($_POST['ac_cat']) ? $_POST['ac_cat'] : 0;
if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['t']) && $_GET['t'] == "ac")) {
$ac_access = isnum($_POST['ac_access']) ? $_POST['ac_access'] : 0;
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_name='$ac_name', ac_cat='$ac_cat', ac_url='$ac_url', ac_target='$ac_target', ac_access='$ac_access' WHERE ac_id='".$_GET['ac_id']."'");
redirect(FUSION_SELF.$aidlink."&status=savefu");
} else {
if ($ac_name) {
$ac_order = isnum($_POST['ac_order']) ? $_POST['ac_order'] : "";
if(!$ac_order) $ac_order=dbresult(dbquery("SELECT MAX(ac_order) FROM ".DB_AC_NAVIGATION." WHERE ac_cat='$ac_cat'"),0)+1;
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_cat='$ac_cat' AND ac_order>='$ac_order'");	
$result = dbquery("INSERT INTO ".DB_AC_NAVIGATION." (ac_cat, ac_name, ac_url, ac_target, ac_access, ac_order) VALUES ('$ac_cat', '$ac_name', '$ac_url', '$ac_target', '$ac_access', '$ac_order')");
redirect(FUSION_SELF.$aidlink."&status=savefn");
} else {
redirect(FUSION_SELF.$aidlink);
}
}
} elseif ((isset($_GET['action']) && $_GET['action'] == "mu") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['order']) && isnum($_GET['order']))) {
if (isset($_GET['t']) && $_GET['t'] == "cat") {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' AND ac_order='".$_GET['order']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_id='".$data['ac_id']."'");
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_id='".$_GET['ac_id']."'");
} elseif ((isset($_GET['t']) && $_GET['t'] == "ac") && (isset($_GET['cat']) && isnum($_GET['cat']))) {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='$cat' AND ac_order='".$_GET['order']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_id='".$data['ac_id']."'");
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_id='".$_GET['ac_id']."'");
}
redirect(FUSION_SELF.$aidlink);

} elseif ((isset($_GET['action']) && $_GET['action'] == "md") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['order']) && isnum($_GET['order']))) {
if (isset($_GET['t']) && $_GET['t'] == "cat") {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' AND ac_order='".$_GET['order']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_id='".$data['ac_id']."'");
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_id='".$_GET['ac_id']."'");
} elseif ((isset($_GET['t']) && $_GET['t'] == "ac") && (isset($_GET['cat']) && isnum($_GET['cat']))) {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='".$_GET['cat']."' AND ac_order='".$_GET['order']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_id='".$data['ac_id']."'");
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order+1 WHERE ac_id='".$_GET['ac_id']."'");
}
redirect(FUSION_SELF.$aidlink);

} elseif ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['t']) && $_GET['t'] == "cat")) {
if (!dbcount("(ac_id)", DB_AC_NAVIGATION, "ac_cat='".$_GET['ac_id']."'")) {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_cat='0' AND ac_order>'".$data['ac_order']."'");
$result = dbquery("DELETE FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'");
redirect(FUSION_SELF.$aidlink."&status=delcy");
} else {
redirect(FUSION_SELF.$aidlink."&status=delcn");
}

} elseif ((isset($_GET['action']) && $_GET['action'] == "delete") && (isset($_GET['ac_id']) && isnum($_GET['ac_id'])) && (isset($_GET['t']) && $_GET['t'] == "ac")) {
if (!dbcount("(thread_id)", DB_THREADS, "ac_id='".$_GET['ac_id']."'")) {
$data = dbarray(dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'"));
$result = dbquery("UPDATE ".DB_AC_NAVIGATION." SET ac_order=ac_order-1 WHERE ac_cat='".$data['ac_cat']."' AND ac_order>'".$data['ac_order']."'");
$result = dbquery("DELETE FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'");
redirect(FUSION_SELF.$aidlink."&status=delfy");
} else {
redirect(FUSION_SELF.$aidlink."&status=delfn");
}

} else {
if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['ac_id']) && isnum($_GET['ac_id']))) {
if (isset($_GET['t']) && $_GET['t'] == "cat") {
$result = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'");
if (dbrows($result)) {
$data = dbarray($result);
$cat_name = $data['ac_name'];
$cat_access = $data['ac_access'];
$cat_title = $locale['acn_admin_cat_edit'];
$cat_action = FUSION_SELF.$aidlink."&amp;action=edit&amp;ac_id=".$data['ac_id']."&amp;t=cat";
$ac_title = $locale['acn_admin_link'];
$ac_action = FUSION_SELF.$aidlink;"&amp;action=edit&amp;ac_id=".$data['ac_id']."&amp;t=cat";
} else {
redirect(FUSION_SELF.$aidlink);
}

} elseif (isset($_GET['t']) && $_GET['t'] == "ac") {
$result = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_id='".$_GET['ac_id']."'");
if (dbrows($result)) {
$data = dbarray($result);
$ac_name = $data['ac_name'];
$ac_url = $data['ac_url'];
$ac_target = ($data['ac_target']=="1" ? " checked='checked'" : "");
$ac_cat = $data['ac_cat'];
$ac_access = $data['ac_access'];
$ac_title = $locale['acn_admin_link_edit'];
$ac_action = FUSION_SELF.$aidlink."&amp;action=edit&amp;ac_id=".$data['ac_id']."&amp;t=ac";
$cat_title = $locale['acn_admin_cat'];
$cat_action = FUSION_SELF.$aidlink;
} else {
redirect(FUSION_SELF.$aidlink);
}
}

} else {
$cat_name = "";
$cat_order = "";
$cat_title = $locale['acn_admin_cat'];
$cat_action = FUSION_SELF.$aidlink;
$ac_name = "";
$ac_url = "";
$ac_target = 0;
$ac_cat = 0;
$ac_order = "";
$ac_access = 0;
$ac_title = $locale['acn_admin_link'];
$ac_action = FUSION_SELF.$aidlink;
}

function create_options($selected, $hide=array(), $off=false) {
global $locale; $option_list = ""; $options = getusergroups();
if ($off) { $option_list = "<option value='0'>".$locale['531']."</option>\n"; }
while(list($key, $option) = each($options)){
if (!in_array($option['0'], $hide)) {
$sel = ($selected == $option['0'] ? " selected='selected'" : "");
$option_list .= "<option value='".$option['0']."'$sel>".$option['1']."</option>\n";
}
}
return $option_list;
}

if (!isset($_GET['t']) || $_GET['t'] != "ac") {
opentable($locale['acn_admin_cat']);
echo "<form name='addcat' method='post' action='$cat_action'>\n";
echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
echo "<td class='tbl'>".$locale['acn_admin_cat_name']."<br />\n";
echo "<input type='text' name='cat_name' value='".$cat_name."' class='textbox' style='width:230px;' /></td>\n";
echo "<td width='50' class='tbl'>";

if (!isset($_GET['action']) || $_GET['action'] != "edit") {
echo $locale['acn_admin_cat_order']."<br />\n<input type='text' name='cat_order' value='".$cat_order."' class='textbox' style='width:45px;' />";
}
echo "</td>\n</tr>\n";

if (isset($_GET['action']) && $_GET['action'] == "edit") {
echo "<tr><td class='tbl'>".$locale['acn_admin_cat_access']."<br />\n";
echo "<select name='cat_access' class='textbox' style='width:225px;'>\n".create_options($cat_access, array(), false)."</select></td></tr>\n";
}

echo "<tr>\n";
echo "<td align='center' colspan='2' class='tbl'>\n";
echo "<input type='submit' name='save_cat' value='".$locale['acn_admin_cat_save']."' class='button' /></td>\n";
echo "</tr>\n</table>\n</form>\n";
closetable();
}

if (!isset($_GET['t']) || $_GET['t'] != "cat") {
$cat_opts = ""; $sel = "";
$result2 = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' ORDER BY ac_order");
if (dbrows($result2)) {
while ($data2 = dbarray($result2)) {
if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_GET['t']) && $_GET['t'] == "ac")) { $sel = ($data2['ac_id'] == $ac_cat ? " selected='selected'" : ""); }
$cat_opts .= "<option value='".$data2['ac_id']."'".$sel.">".$data2['ac_name']."</option>\n";
}

opentable($locale['acn_admin_link']);
echo "<form name='addac' method='post' action='$ac_action'>\n";
echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
echo "<td colspan='2' class='tbl'>".$locale['acn_admin_link_name']."<br />\n";
echo "<input type='text' name='ac_name' value='".$ac_name."' class='textbox' style='width:285px;' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td colspan='2' class='tbl'>".$locale['acn_admin_link_url']."<br />\n";
echo "<input type='text' name='ac_url' value='".$ac_url."' class='textbox' style='width:285px;' /></td>\n";
echo "</tr>\n<tr>\n";
echo "<td colspan='2' class='tbl'>";
echo "<input type='checkbox' class='textbox' name='ac_target' value='1'".$ac_target." /> ".$locale['acn_admin_link_target']."</td>\n";
echo "</tr>\n<tr>\n";
echo "<td class='tbl'>".$locale['acn_admin_link_cat']."<br />\n";
echo "<select name='ac_cat' class='textbox' style='width:225px;'>\n".$cat_opts."</select></td>\n";
echo "<td width='55' class='tbl'>";
if (!isset($_GET['action']) || $_GET['action'] != "edit") {
echo $locale['acn_admin_link_order']."<br />\n<input type='text' name='ac_order' value='".$ac_order."' class='textbox' style='width:45px;' />";
echo "</td>\n</tr>\n<tr>\n";
echo "<td align='center' colspan='2' class='tbl'>\n";
echo "<input type='submit' name='save_ac' value='".$locale['acn_admin_link_save']."' class='button' />";
}
echo "</td>\n</tr>\n</table>\n";
if (isset($_GET['action']) && $_GET['action'] == "edit") {
echo "<table align='center' cellpadding='0' cellspacing='0' width='300'>\n<tr>\n";
echo "<td class='tbl'>".$locale['acn_admin_link_access']."<br />\n";
echo "<select name='ac_access' class='textbox' style='width:225px;'>\n".create_options($ac_access, array(), false)."</select></td>\n";
echo "</tr>\n";

if (!isset($_GET['action']) || $_GET['action'] != "edit") {
echo "<tr>\n<td align='center' colspan='2' class='tbl'>\n";
echo "<input type='submit' name='save_ac' value='".$locale['acn_admin_link_save']."' class='button' /></td>\n";
echo "</tr>\n</table>\n";
}
}
if (!isset($_GET['action'])) echo "\n</form>";
if (isset($_GET['action']) && $_GET['action'] == "edit") {
echo "<tr>\n";
echo "<td align='center' colspan='2' class='tbl'>\n";
echo "<input type='hidden' name='ac_id' value='".$data['ac_id']."' />\n";
echo "<input type='submit' name='save_ac' value='".$locale['acn_admin_link_save']."' class='button' /></td>\n";
echo "</tr>\n</table>\n</form>\n";
}
closetable();
}
}
opentable($locale['acn_admin_navigation']);
$i = 1; $k = 1;
echo "<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border'>\n";
$result = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='0' ORDER BY ac_order");
if (dbrows($result) != 0) {
echo "<tr>\n<td class='tbl2'><strong>".$locale['acn_admin_navigation_cat_link']."</strong></td>\n";
echo "<td align='center' colspan='2' width='1%' class='tbl2' style='white-space:nowrap'><strong>".$locale['acn_admin_navigation_order']."</strong></td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><strong>".$locale['acn_admin_navigation_options']."</strong></td>\n";
echo "</tr>\n";
$i = 1;
while ($data = dbarray($result)) {
echo "<tr>\n<td class='tbl2'><strong>".$data['ac_name']."</strong></td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$data['ac_order']."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>\n";
if (dbrows($result) != 1) {
$up = $data['ac_order'] - 1;	$down = $data['ac_order'] + 1;
if ($i == 1) {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=md&amp;order=$down&amp;ac_id=".$data['ac_id']."&amp;t=cat'><img src='".get_image("down")."' alt='".$locale['acn_admin_navigation_down']."' title='".$locale['acn_admin_navigation_down']."' style='border:0px;' /></a>\n";
} elseif ($i < dbrows($result)) {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=mu&amp;order=$up&amp;ac_id=".$data['ac_id']."&amp;t=cat'><img src='".get_image("up")."' alt='".$locale['acn_admin_navigation_up']."' title='".$locale['acn_admin_navigation_up']."' style='border:0px;' /></a>\n";
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=md&amp;order=$down&amp;ac_id=".$data['ac_id']."&amp;t=cat'><img src='".get_image("down")."' alt='".$locale['acn_admin_navigation_down']."' title='".$locale['acn_admin_navigation_down']."' style='border:0px;' /></a>\n";
} else {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=mu&amp;order=$up&amp;ac_id=".$data['ac_id']."&amp;t=cat'><img src='".get_image("up")."' alt='".$locale['acn_admin_navigation_up']."' title='".$locale['acn_admin_navigation_up']."' style='border:0px;' /></a>\n";
}
}
$i++;
echo "</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;ac_id=".$data['ac_id']."&amp;t=cat'>".$locale['acn_admin_navigation_edit']."</a> ::\n";
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;ac_id=".$data['ac_id']."&amp;t=cat' onclick=\"return confirm('".$locale['acn_admin_navigation_confirm']."');\">".$locale['acn_admin_navigation_delete']."</a></td>\n";
echo "</tr>\n";
$result2 = dbquery("SELECT * FROM ".DB_AC_NAVIGATION." WHERE ac_cat='".$data['ac_id']."' ORDER BY ac_order");
if (dbrows($result2)) {
$k = 1;
while ($data2 = dbarray($result2)) {
echo "<tr>\n";
echo "<td class='tbl1'><span class='alt'>".$data2['ac_name']."</span>\n";
echo "<br />\n";
echo ($data2['ac_url'] ? "<span class='small'>".$data2['ac_url']."</span>" : "")."</td>\n";
echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>".$data2['ac_order']."</td>\n";
echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>\n";
if (dbrows($result2) != 1) {
$up = $data2['ac_order'] - 1; $down = $data2['ac_order'] + 1;
if ($k == 1) {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=md&amp;order=$down&amp;ac_id=".$data2['ac_id']."&amp;t=ac&amp;cat=".$data2['ac_cat']."'><img src='".get_image("down")."' alt='".$locale['acn_admin_navigation_down']."' title='".$locale['acn_admin_navigation_down']."' style='border:0px;' /></a>\n";
} elseif ($k < dbrows($result2)) {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=mu&amp;order=$up&amp;ac_id=".$data2['ac_id']."&amp;t=ac&amp;cat=".$data2['ac_cat']."'><img src='".get_image("up")."' alt='".$locale['acn_admin_navigation_up']."' title='".$locale['acn_admin_navigation_up']."' style='border:0px;' /></a>\n";
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=md&amp;order=$down&amp;ac_id=".$data2['ac_id']."&amp;t=ac&amp;cat=".$data2['ac_cat']."'><img src='".get_image("down")."' alt='".$locale['acn_admin_navigation_down']."' title='".$locale['acn_admin_navigation_down']."' style='border:0px;' /></a>\n";
} else {
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=mu&amp;order=$up&amp;ac_id=".$data2['ac_id']."&amp;t=ac&amp;cat=".$data2['ac_cat']."'><img src='".get_image("up")."' alt='".$locale['acn_admin_navigation_up']."' title='".$locale['acn_admin_navigation_up']."' style='border:0px;' /></a>\n";
}
}
$k++;
echo "</td>\n";
echo "<td align='center' width='1%' class='tbl1' style='white-space:nowrap'><a href='".FUSION_SELF.$aidlink."&amp;action=edit&amp;ac_id=".$data2['ac_id']."&amp;t=ac'>".$locale['acn_admin_navigation_edit']."</a> ::\n";
echo "<a href='".FUSION_SELF.$aidlink."&amp;action=delete&amp;ac_id=".$data2['ac_id']."&amp;t=ac' onclick=\"return confirm('".$locale['acn_admin_navigation_confirm']."');\">".$locale['acn_admin_navigation_delete']."</a></td>\n";
echo "</tr>\n";
}
}
}
echo "<tr>\n<td align='center' colspan='5' class='tbl2'>[ <a href='".FUSION_SELF.$aidlink."&amp;action=refresh'>".$locale['acn_admin_navigation_refresh']."</a> ]</td>\n</tr>\n";
} else {
echo "<tr>\n<td align='center' class='tbl1'>".$locale['acn_admin_cat_no']."</td>\n</tr>\n";
}
echo "</table>\n";
closetable();

//RESPECT MY WORK AND DO NOT REMOVE THIS COPYRIGHT
opentable($locale['acn_title']);
echo "<table cellpadding='0' cellspacing='1' width='100%'>\n";
echo "<tr>\n<td align='center' class='tbl'>\n";
echo "AC-Navigation Panel v2.0 &copy; 2009 by <a href='mailto:info@ptown67.de'>ptown67</a> | Info & Support: <a href='http://www.ptown67.de' target='_blank'>www.ptown67.de</a>";
echo "</td>\n</tr>\n";
echo "</table>\n";
closetable();
}
require_once THEMES."templates/footer.php";
?>