<?php

// Theme by PhpFusionBox.com

 if (!defined("IN_FUSION")) { die("Access Denied"); }
 define("THEME_BULLET", "<span class='bullet'>&middot;</span>");
 require_once INCLUDES."theme_functions_include.php";
require_once BASEDIR."themes/version.php";
//Theme Settings
$theme_width = "96%";

// Scrolling Download Menu Script


 function render_page($license=false) {
 	global $settings, $locale, $userdata, $aidlink, $theme_width, $bottom_navbar_string, $ver;

	//Header
	echo "<table cellpadding='0' cellspacing='0' width='$theme_width' align='center'><tr>";
	echo "<td class='full-header'>&nbsp;</td>";
	echo "<td class='sub-header'>".showsublinks("","white")."";
	echo "<td class='sub-headerright'>&nbsp;</td>";
	echo "</tr></table>";
	echo "<table cellpadding='0' cellspacing='0' width='100%'><tr>";
	echo "<td class='ad'>&nbsp;</td>";
	echo "</tr></table>";


	//Content

	echo "<table cellpadding='0' cellspacing='0' width='$theme_width' align='center'><tr>\n";
	if (LEFT) { echo "<td class='side-border-left' valign='top'>".LEFT."</td>"; }
	echo "<td class='main-bg' valign='top'>".U_CENTER.CONTENT.L_CENTER."</td>";
	if (RIGHT) { echo "<td class='side-border-right' valign='top'>".RIGHT."</td>"; }
	echo "</tr>\n</table>\n";

 	//Footer

 	echo "<table cellpadding='0' cellspacing='0' width='$theme_width' align='center'>\n<tr>\n";
	echo "<td><img src='".THEME."/images/pfboxfooterleft.jpg' align='right' alt='image' /></td>\n";
  	echo "<td align='left' class='bottom-footer' width='50%'>ArcSite CMS v$ver by The_Red<br/>Built Using <a target='_blank' href='http://www.php-fusion.co.uk/'>PHP-Fusion v7.00.07</a><br/></td>\n";
	echo "<td align='right' class='bottom-footer' width='50%'>$bottom_navbar_string<br/>".showcounter()."</td>\n";
 	echo "<td><img src='".THEME."/images/pfboxfooterright.jpg' align='left' alt='image' /></td>\n";
	echo "</tr>\n</table>\n";
 }

 function render_news($subject, $news, $info) {
 	echo "<table cellpadding='0' cellspacing='0' width='100%' align='center'>\n<tr>\n";
	echo "<td><img src='".THEME."/images/pfboxcapleft.jpg' alt='image' /></td>\n";
  	echo "<td class='capmain' width='100%' align='center'>".$subject."</td>\n";
	echo "<td><img src='".THEME."/images/pfboxcapright.jpg' alt='image' /></td>\n";
 	echo "</tr>\n</table>\n";
 	echo "<table width='100%' cellpadding='0' cellspacing='0' align='center'>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
 	echo "<td class='main-body'>".$news."</td>\n";
	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
 	echo "</tr>\n</table>\n";
	echo "<table width='100%' cellpadding='0' cellspacing='0' align='center'>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
 	echo "<td align='center' class='news-footer'>\n";
	echo newsposter($info," &middot;").newsopts($info,"&middot;").itemoptions("N",$info['news_id']);
 	echo "</td>\n";
	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
 	echo "</tr>\n</table>\n";
	echo "<table cellspacing='0' cellpadding='0' width='100%' class='spacer'>\n<tr>\n";
	echo "<td align='left'><img src='".THEME."/images/pfboxsidebleft.jpg' alt='image' /></td>";
	echo "<td align='center' class='pfboxsideb' width='100%'></td>";
	echo "<td align='right'><img src='".THEME."/images/pfboxsidebright.jpg' alt='image' /></td>";
	echo "</tr>\n</table>\n";
 }


 function render_article($subject, $article, $info) {

	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n<tr>\n";
	echo "<td><img src='".THEME."/images/pfboxcapleft.jpg' alt='image' /></td>\n";
 	echo "<td class='capmain' width='100%'>".$subject."</td>\n";
	echo "<td><img src='".THEME."/images/pfboxcapright.jpg' alt='image' /></td>\n";
 	echo "</tr>\n</table>\n";
 	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
 	echo "<td class='main-body middle-border'>".($info['article_breaks'] == "y" ? nl2br($article) : $article)."</td>\n";
 	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
	echo "<td align='center' class='news-footer'>\n";
 	echo articleposter($info," &middot;").articleopts($info,"&middot;").itemoptions("A",$info['article_id']);
 	echo "</td>\n";
	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
	echo "</tr>\n</table>\n";
	echo "<table cellspacing='0' cellpadding='0' width='100%' class='spacer'>\n<tr>\n";
	echo "<td align='left'><img src='".THEME."/images/pfboxsidebleft.jpg' alt='image' /></td>";
	echo "<td align='center' class='pfboxsideb' width='100%'></td>";
	echo "<td align='right'><img src='".THEME."/images/pfboxsidebright.jpg' alt='image' /></td>";
	echo "</tr>\n</table>\n";

 }



 function opentable($title) {

 	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td><img src='".THEME."/images/pfboxcapleft.jpg' alt='image' /></td>\n";
 	echo "<td class='capmain' width='100%' align='center'>".$title."</td>\n";
	echo "<td><img src='".THEME."/images/pfboxcapright.jpg' alt='image' /></td>\n";
 	echo "</tr>\n</table>\n";
 	echo "<table cellpadding='0' cellspacing='0' width='100%' align='center'>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
 	echo "<td class='main-body'>\n";

 }


 function closetable() {

 	echo "</td>\n";
	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
 	echo "</tr>\n</table>\n";
	echo "<table cellspacing='0' cellpadding='0' width='100%' class='spacer'>\n<tr>\n";
	echo "<td align='left'><img src='".THEME."/images/pfboxsidebleft.jpg' alt='image' /></td>";
	echo "<td align='center' class='pfboxsideb' width='100%'></td>";
	echo "<td align='right'><img src='".THEME."/images/pfboxsidebright.jpg' alt='image' /></td>";
	echo "</tr>\n</table>\n";

 }

 function openside($title, $collapse = false, $state = "on") {
 	global $panel_collapse; $panel_collapse = $collapse;
 	echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td><img src='".THEME."/images/pfboxscapleft.jpg' alt='image' /></td>\n";
  	echo "<td class='scapmain' width='100%' align='center'>$title</td>\n";
 	if ($collapse == true) {
 		$boxname = str_replace(" ", "", $title);
 		echo "<td class='scapmain' align='right'>".panelbutton($state, $boxname)."</td>\n";
 	}
	echo "<td><img src='".THEME."/images/pfboxscapright.jpg' alt='image' /></td>\n";
 	echo "</tr>\n</table>\n";
 	echo "<table cellpadding='0' cellspacing='0' width='100%' align='center'>\n<tr>\n";
	echo "<td class='pfboxbll' height='100%'>&nbsp;</td>\n";
 	echo "<td class='side-body'>\n";
	if ($collapse == true) { echo panelstate($state, $boxname); }
 }


 function closeside() {

	global $panel_collapse;
	if ($panel_collapse == true) { echo "</div>\n"; }
	echo "</td>\n";
	echo "<td class='pfboxblr' height='100%'>&nbsp;</td>\n";
	echo "</tr>\n</table>\n";
	echo "<table cellspacing='0' cellpadding='0' width='100%' class='spacer'>\n<tr>\n";
	echo "<td align='left'><img src='".THEME."/images/pfboxsidebleft.jpg' alt='image' /></td>\n";
	echo "<td align='center' class='pfboxsideb' width='100%'></td>\n";
	echo "<td align='right'><img src='".THEME."/images/pfboxsidebright.jpg' alt='image' /></td>\n";
	echo "</tr>\n</table>\n";
	}

?>
