<?php

if (!defined("IN_FUSION")) { die("Access Denied"); }

define("THEME_WIDTH", "900");
define("THEME_BULLET", "<img src='".THEME."images/bullet.gif' alt='' style='border:0' />");

require_once INCLUDES."theme_functions_include.php";
require_once BASEDIR."themes/version.php";
function render_page($license=false) {

	global $aidlink, $ver, $navbar_links, $settings, $main_style, $locale, $userdata, $lettering_string, $use_flash, $neon_banner_path, $flash_banner_path, $site_footer, $fusion_version;

echo "<table align='center' cellspacing='0' cellpadding='0' width='".THEME_WIDTH."' class='outer-border'>
<tr>
<td>
<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='full-header'>
  <table cellpadding='0' cellspacing='0' width='100%'>
    <tr>
      <p><td width='100%' height='111'>"; if ($use_flash == 1) { echo "<object type='application/x-shockwave-flash' data=$flash_banner_path width='898' height='242'>
<param name='movie' value=$flash_banner_path /><param name='wmode' value='transparent'>
 </object>";
 for($b = 0;$b <= Configuration::Get('lettering.begin.spaces');$b++) { echo "<img src='".BASEDIR."images/logo/wotlk/space.png' align='center' />"; }
if(Configuration::Get('lettering.message') != '') { echo $lettering_string; } } if ($use_flash == 0) { if($neon_banner_path != '') { echo "<img src='".$neon_banner_path."' align='center'>"; } } echo "</td></p>
    </tr>
  </table>
  </td></tr>
</tr></table>\n";


echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>
<td width='140'><img src='".THEME."images/menuleft.gif' alt='' style='border-left: 1px #a7a7a7 solid;' /></td>
<td align='center' class='sub-header-alti'>".showsublinks(" | ")."</td>
<td width='140'><img src='".THEME."images/menuright.gif' alt='' style='border-right: 1px #a7a7a7 solid;' /></td>
</tr>
</table>\n";


//echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";

//Content
	echo "<table cellpadding='0' cellspacing='0' class='border' width='".THEME_WIDTH."' align='center'>\n<tr>\n";
	if (LEFT) { echo "<td class='side-border-left' valign='top'>".LEFT."</td>"; }
	echo "<td class='main-bg' valign='top'>".U_CENTER.CONTENT.L_CENTER."</td>";
	if (RIGHT) { echo "<td class='side-border-right' valign='top'>".RIGHT."</td>"; }
	echo "</tr>\n</table>\n";


//footer

echo "<table cellpadding='0' cellspacing='0' width='".THEME_WIDTH."' align='center'>
<tr>
<td align='left' width='50%' height='241' class='footer'><br/>\n";
if (!$license) { echo "<br/>"; echo "ArcSite CMS v".$ver." Scripted by <font color=red>The_Red</font>"; echo " | \n"; } echo showcounter()."<br/>Built Using <a target='_blank' href='http://www.php-fusion.co.uk/'>PHP-Fusion v7.00.07</a><br/>
<a href='http://validator.w3.org/check?uri=referer'><img src='".THEME."images/vhtml.png' alt='XHTML' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='http://jigsaw.w3.org/css-validator/check/referer'><img src='".THEME."images/vcss.png' alt='CSS' /></a>
</td>
<td align='right' width='50%' class='footerright'>\n";
echo "$navbar_links<br/><br/><br/><br/>\n";
echo "</td>
</tr>
</table>
</td>
</tr>
</table>\n";

}

function render_news($subject, $news, $info) {

echo "<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td class='capmain'>$subject</td>
</tr>
<tr>
<td class='main-body'>$news</td>
</tr>
<tr>
<td align='center' class='news-footer'>\n";
echo newsposter($info," &middot;").newsopts($info,"&middot;").itemoptions("N",$info['news_id']);
echo "</td>
</tr>
</table>\n";

}

function render_article($subject, $article, $info) {

echo "<table width='100%' cellpadding='0' cellspacing='0'>
<tr>
<td class='capmain'>$subject</td>
</tr>
<tr>
<td class='main-body'>
".($info['article_breaks'] == "y" ? nl2br($article) : $article)."
</td>
</tr>
<tr>
<td align='center' class='news-footer'>\n";
echo articleposter($info," &middot;").articleopts($info,"&middot;").itemoptions("A",$info['article_id']);
echo "</td>
</tr>
</table>\n";

}

function opentable($title) {

echo "<table cellpadding='0' cellspacing='0' width='100%'>
<tr>
<td align='center' class='capmain'>$title</td>
</tr>
<tr>
<td class='main-body'>\n";

}

function closetable() {

echo "</td>
</tr>
</table>\n";

}

function openside($title) {

echo "<table cellpadding='0' cellspacing='0' width='100%' class='border'>
<tr>
<td class='scapmain'>· $title</td>
</tr>
<tr>
<td class='side-body'>\n";

}

function closeside() {

echo "</td>
</tr>
</table><br/>\n";

}
?>