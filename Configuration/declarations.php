<?php
require_once "mainconfig.php";
$emulator = Configuration::Get('emulator');
$ldb_name = Configuration::Get('logon.db.name');
$ldb_host = Configuration::Get('logon.db.host');
$ldb_user = Configuration::Get('logon.db.user');
$ldb_pass = Configuration::Get('logon.db.pass');
$acc_lvl = Configuration::Get('game.acc.level');
$web_host = Configuration::Get('website.db.host');
$web_user = Configuration::Get('website.db.user');
$web_pass = Configuration::Get('website.db.pass');
$web_name = Configuration::Get('website.db.name');
$db_prefix = Configuration::Get('website.db.prefix');
define("DB_PREFIX", Configuration::Get('website.db.prefix'));
$cdb_host = Configuration::Get('characters.db.host');
$cdb_user = Configuration::Get('characters.db.user');
$cdb_pass = Configuration::Get('characters.db.pass');
$cdb_name = Configuration::Get('characters.db.name');
$wdb_host = Configuration::Get('world.db.host');
$wdb_user = Configuration::Get('world.db.user');
$wdb_pass = Configuration::Get('world.db.pass');
$wdb_name = Configuration::Get('world.db.name');
if(Configuration::Get('navbar.link.count') != 0) { $navbar_links = "|"; } else { $navbar_links = ""; }
for($p = 1;$p <= Configuration::Get('navbar.link.count');$p++) {
if(Configuration::Get('navbar.link'.$p) != '|' && Configuration::Get('navbar.link'.$p) != null && Configuration::Get('navbar.link'.$p) != '') {
$link_info = explode("|", Configuration::Get('navbar.link'.$p));
$navbar_links .= " <a href='".$folder_level.$link_info[1]."'>".$link_info[0]."</a> |"; } }
$use_flash = Configuration::Get('use.flash.banner');
$flash_banner_path = $folder_level.Configuration::Get('flash.banner.location');
$neon_banner_path = $folder_level.Configuration::Get('neon.theme.banner.path');
$bloody_banner_path = $folder_level.Configuration::Get('bloody.theme.banner.path');
$bloody_header_path = $folder_level.Configuration::Get('bloody.theme.header.path');
$agreed = Configuration::Get('use.agreement');
if(Configuration::Get('lettering.type') == 1) { $letters = "wotlk"; } elseif(Configuration::Get('lettering.type') == 2) { $letters = "tbc"; } elseif(Configuration::Get('lettering.type') == 3) { $letters = "reg"; }
$letter_string = array_change_key_case(str_split(Configuration::Get('lettering.message'), 1), CASE_LOWER);
while(in_array(' ', $letter_string)) {
$numar = array_search(' ', $letter_string);
$letter_string[$numar] = 'space'; }
$lettering_string = "";
for($i = 0;$i < count($letter_string);$i++) {
$lettering_string .= "<img src='".$folder_level."images/logo/".$letters."/".$letter_string[$i].".png' align='center'>"; }
?>
