<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: news.php
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
// If register_globals is turned off, extract super globals (php 4.2.0+)
if (ini_get('register_globals') != 1) {
	if ((isset($_POST) == true) && (is_array($_POST) == true)) extract($_POST, EXTR_OVERWRITE);
	if ((isset($_GET) == true) && (is_array($_GET) == true)) extract($_GET, EXTR_OVERWRITE);
}

require_once "maincore.php";
require_once THEMES."templates/header.php";
include BASEDIR."advanced_news/locale/ans.php";
include BASEDIR."advanced_news/news_scripts.php";



// Predefined variables, do not edit these values
if ($settings['news_style'] == "1") { $i = 0; $rc = 0; $ncount = 1; $ncolumn = 1; $news_[0] = ""; $news_[1] = ""; $news_[2] = ""; } else { $i = 1; }

// Number of news displayed
$items_per_page = $settings['newsperpage'];

add_to_title($locale['global_200'].$locale['global_077']);

opentable($locale['ans000']);

/*------------------------------------------------------------------------------------------------------------------------*/
$items_per_page = 4;				//display's the amount of news to be displayed when using the standard (default php-fusion layout)
$min = 6;						//minimum visible news items to display
$max = 11;						//maximum number of news items hidden
$news_legend = 0;					//controls the legend display for fixed news (not scolling news or vertical) - [0 = Off / 1 = On] 
$news_display_type = 1; 			//choose the news display type - [0 = scrolling news / 1 = fixed advanced news / 2 = default php-fusion news layout (vertical mode)] 
$scrolling_ticker_height = "200px";		//1 = displays the news via a popup plus the boxover ballon tips are active / 0 = basic news display
$scrolling_ticker_cat_color = "#007E5C";	//set the color of the category names from here
$scrolling_ticker_boxover = 1;		//1 = displays the news via a popup plus the boxover ballon tips are active / 0 = basic news display
$scrolling_category = 1;			//set category display [0 = off / 1 = on]
$scrolling_news_author = 1;			//show author in scrolling news? [0 = off / 1 = on]
$scrolling_ticker_date = 1;			//show date in scrolling news? [0 = off / 1 = on]
$scrolling_ticker_comments = 1;		//show commentcount in scrolling news? [0 = off / 1 = on]
$scrolling_ticker_reads = 1;			//show readcounts in scrolling news? [0 = off / 1 = on]
$scrolling_background_image = 1;		//display a background image for your scrolling news [0 = off / 1 = on]
$shoutbox5 = 1;					//if you're using Fuzed Themes Shoutbox v.5.00 this must be set to a value of 1
/*------------------------------------------------------------------------------------------------------------------------*/

if (isset($readmore) && !empty($readmore) && isNum($readmore) && isset($op) && $op == "del") {
	if (!checkrights("N") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("index.php"); }
	$result = dbquery("DELETE FROM ".DB_NEWS." WHERE news_id='$readmore'");
	$result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_item_id='$readmore' and comment_type='N'");
	$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='$readmore' and rating_type='N'");
	redirect(FUSION_SELF);
} elseif (!isset($readmore)) {
if ($news_display_type == 1){
if ($news_legend == 1) {
echo "<table cellSpacing='1' class='tbl-border' cellPadding='3' width='100%' border='0'>";
	echo "<tr><td align='center' class='tbl2' width='100%' colspan='2'>".$locale['ans100']."</td>
</tr>
<tr>
<td class='tbl1' align='left' width='65%'>
<img src='".BASEDIR."advanced_news/images/icon_readpopup.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans101']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans101']."] body=[".$locale['pop100']."] delay=[0] fade=[on]\">[?]</span>
</td>
<td class='tbl1' align='left' width='35%'>
<img src='".BASEDIR."advanced_news/images/icon_addnews.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans102']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans102']."] body=[".$locale['pop101']."] delay=[0] fade=[on]\">[?]</span>
</td>
</tr>
<tr>
<td class='tbl1' align='left' width='65%'>
<img src='".BASEDIR."advanced_news/images/icon_readnormal.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans103']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans103']."] body=[".$locale['pop102']."] delay=[0] fade=[on]\">[?]</span>
</td>
<td class='tbl1' align='left' width='35%'>
<img src='".BASEDIR."advanced_news/images/icon_postcomment.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans104']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans104']."] body=[".$locale['pop103']."] delay=[0] fade=[on]\">[?]</span>
</td>
</tr>
<tr>
<td class='tbl1' align='left' width='65%'>
<img src='".BASEDIR."advanced_news/images/icon_nocomment.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans105']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans105']."] body=[".$locale['pop104']."] delay=[0] fade=[on]\">[?]</span>
</td>
<td class='tbl1' align='left' width='35%'>
<img src='".BASEDIR."advanced_news/images/icon_printnews.gif' style='vertical-align: middle;' border='0' width='13' height='15' alt=''> ".$locale['ans106']." <span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_info.gif' style='vertical-align:middle;'> ".$locale['ans106']."] body=[".$locale['pop105']."] delay=[0] fade=[on]\">[?]</span>
</td>";
	} 
if ($news_legend == 1){
echo "</table><hr>";
}
	echo "<table class='tbl-border' border='0' cellpadding='0' cellspacing='1' width='100%'>
<tr>
<td align='center' class='tbl2' width='15%'><b>".$locale['ans107']."</b></td>
<td class='tbl2' width='55%'><b>".$locale['ans108']."</b> ".$locale['ans109']."</td>
<td align='center' class='tbl2' width='10%'><b>".$locale['ans110']."</b></td>
<td align='center' class='tbl2' width='20%'><b>".$locale['ans111']."</b></td>\n";

	$news_count = 1;	/* used to keep count of how many news items have been listed */
	$result = dbquery(
		"SELECT tn.*, tc.*, user_id, user_name FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
		LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
		WHERE ".groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().")
		ORDER BY news_sticky DESC, news_datestamp DESC LIMIT ".($min + $max)
	);
		while ($data = dbarray($result)) {
		if ($news_count % 2 == 0) { $row_color = "tbl2"; } else { $row_color = "tbl1"; }
		if (file_exists(IMAGES_NC.$data['news_cat_image'])) {
			$news_cat_image = "<a href='news_cats.php?cat_id=".$data['news_cat_id']."'><img src='".IMAGES_NC.$data['news_cat_image']."' alt='".$data['news_cat_name']."' align='left'></a>";
		} else {
			$news_cat_image = "";
		}
		$itemsubject = trimlink($data['news_subject'], 50);
		$title = $data['news_subject'];
		$sh_title = $title;
		$n_id = $data['news_id'];
		$v_id = $data['news_reads'];
		$comments_n = dbcount("(comment_id)", DB_COMMENTS, "comment_type='N' AND comment_item_id=$n_id");
		$date = $data['news_datestamp'];
		$n_date = showdate( "%d-%m-%y", $date);

		echo "<tr>
<td align='center' class='$row_color' width='15%'><a href='".BASEDIR."news_cats.php?cat_id=".$data['news_cat_id']."'><b>".$data['news_cat_name']."</b></a><br>$n_date</td>
<td class='$row_color' width='55%'>
<a href='".BASEDIR."advanced_news/ans.php?readmore=".$data['news_id']."' name='news_".$data['news_id']."' id='news_".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','300','yes');return false\" title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_news.gif' style='vertical-align:middle;'> ".str_replace("]", "]]", str_replace("[", "[[", trimlink($data['news_subject'], 30)))."] body=[".str_replace("<img src='".substr(IMAGES_N, strlen(BASEDIR)), "<img src='".IMAGES_N, str_replace("]", "]]", str_replace("[", "[[", trimlink(nl2br(stripslashes($data['news_news'])), 450))))."] delay=[0] fade=[on]\"><b>$itemsubject</b></a><br>".$locale['ans112']." $v_id ";

		if ($data['news_allow_comments']) {
			$result2 = dbquery(
				"SELECT tcm.*,user_name FROM ".DB_COMMENTS." tcm
				LEFT JOIN ".DB_USERS." tcu ON tcm.comment_name=tcu.user_id
				WHERE comment_item_id='".$data['news_id']."' AND comment_type='N'
				ORDER BY comment_datestamp ASC LIMIT 1"
			);
			if (dbrows($result2) != 0) {
				$data2 = dbarray($result2);
				$data2['comment_message'] = str_replace("]", "]]", str_replace("[", "[[", $data2['comment_message']));
			if ($shoutbox5 == 1){
			echo "- <a href='news.php?readmore=$n_id' style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_commentposted.gif' style='vertical-align:middle;'> ".$locale['pop108']."] body=[".$locale['pop109']."<b>".$data2['user_name']."</b><hr>".phpentities(parsesmileys(parseubb(trimlink($data2['comment_message'], 150))))."] delay=[0] fade=[on]\">".$locale['ans113']." $comments_n</a>\n";
}else if ($shoutbox5 == 0){
			echo "- <a href='news.php?readmore=$n_id' style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_commentposted.gif' style='vertical-align:middle;'> ".$locale['pop108']."] body=[".$locale['pop109']."<b>".$data2['user_name']."</b><hr>".phpentities(parsesmileys(parseubb(trimlink($data2['comment_message'], 150))))."] delay=[0] fade=[on]\">".$locale['ans113']." $comments_n</a>\n";
}
			}
			$post_comment = "<a href='news.php?readmore=$n_id'><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_comment.gif' style='vertical-align:middle;'> ".$locale['ans116']."] body=[".$locale['pop103']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_postcomment.gif' border='0' alt=''></span></a>&nbsp;";
		} else {
			$post_comment = "<span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_nocomment.gif' style='vertical-align:middle;'> ".$locale['ans118']."] body=[".$locale['pop104']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_nocomment.gif' border='0' alt=''></span>&nbsp;";
		}

		echo "</td>
<td align='center' class='$row_color' width='10%'>
<a href='".BASEDIR."advanced_news/ans.php?readmore=".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','300','yes');return false\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_newspopup.gif' style='vertical-align:middle;'> ".$locale['ans114']."] body=[".$locale['pop100']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_readpopup.gif' border='0' alt=''></span></a>&nbsp;
<a href='".BASEDIR."news.php?readmore=".$data['news_id']."' onclick=\"NewWindow(this.href,'name','0','0','yes');return false\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_newsfull.gif' style='vertical-align:middle;'> ".$locale['ans115']."] body=[".$locale['pop102']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_readnormal.gif' border='0' alt=''></span></a>
</td>
<td align='center' class='$row_color' width='20%'>$post_comment";

		if (iMEMBER) {
			echo "<a href='".BASEDIR."submit.php?stype=n'><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_submitnews.gif' style='vertical-align:middle;'> ".$locale['ans117']."] body=[".$locale['pop101']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_addnews.gif' alt='' border='0'></span></a>
<a href='print.php?type=N&amp;item_id=".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','570','yes');return false\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_print.gif' style='vertical-align:middle;'> ".$locale['ans119']."] body=[".$locale['pop105']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_printnews.gif' alt='' border='0'></span></a>&nbsp;";
		} else {
			echo "<a href='print.php?type=N&amp;item_id=".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','570','yes');return false\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_print.gif' style='vertical-align:middle;'> ".$locale['ans119']."] body=[".$locale['pop105']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_printnews.gif' alt='' border='0'></span></a>&nbsp;";
		}

		if (checkrights("N")) {
			echo "<a href='javascript:void(0)' onclick=\"document.editnews.news_id.value=".$data['news_id']."; document.editnews.submit(); return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_editnews.gif' style='vertical-align:middle;'> ".$locale['admin100']." <font color='red'>".$data['news_id']."</font>] body=[".$locale['pop107']."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_editnews.gif' alt='' border='0'></span></a>&nbsp;";
			echo "<a href='javascript:void(0)' onclick=\"if (confirm('".$locale['admin102']."')) location.href='".BASEDIR."news.php{$aidlink}&amp;readmore=".$data['news_id']."&amp;op=del'; return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_deletenews.gif' style='vertical-align:middle;'> ".$locale['admin101']." <font color='red'>".$data['news_id']."</font>] body=[".phpentities($locale['pop110'])."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_deletenews.gif' alt='' border='0'></span></a>&nbsp;";
			echo "<a href='".BASEDIR."administration/news.php".$aidlink."'><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_writenews.gif' style='vertical-align:middle;'> <font color='#1D679F'>".$locale['admin103']."</font>] body=[".phpentities($locale['pop111'])."] delay=[0] fade=[on]\"><img src='".BASEDIR."advanced_news/images/icon_writenews.gif' alt='' border='0'></span></a>";

		}

		if ($news_count == $min) { /* Check to see if we should end the visiable news and start the hidden news */
			echo "</td>
</tr>
</table>
<br>
<div style='margin-bottom:-4px;'><img src='".BASEDIR."advanced_news/images/adv_bluearrow.gif' alt='' border='0'>&nbsp;&nbsp;<a href='#' onclick=\"showhide('news'); return(false);\"><strong>".$locale['ans120']."</strong></a></div><div id='news' style='display: none;'><br>";




	echo "<table style='margin-top:4px;' class='tbl-border' border='0' cellpadding='0' cellspacing='1' width='100%'>
<tr>
<td align='center' class='tbl2' width='15%'><b>".$locale['ans107']."</b></td>
<td class='tbl2' width='55%'><b>".$locale['ans108']."</b> ".$locale['ans109']."</td>
<td align='center' class='tbl2' width='10%'><b>".$locale['ans110']."</b></td>
<td align='center' class='tbl2' width='20%'><b>".$locale['ans111']."</b></td>\n";
		}
		$news_count ++;
	}

echo "</tr>
</table>
".($news_count >= $min ? "</div>\n" : "")."<br>";

	$filename = INFUSIONS."news_archive/";
	if (file_exists($filename)) {

		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".INFUSIONS."news_archive/news_archive.php'>
<input type='submit' value='".$locale['ans121']."' class='button'>
</form>
</td>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
	} else {
		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='25%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
	}  

//******************************************************************************************************
//Check to see if the News Archive Infusion has been installed. If not, then default to News Categories
//******************************************************************************************************

}else if ($news_display_type == 0){
	echo "<script type='text/javascript' src='".BASEDIR."advanced_news/scroller.js'></script>";
	echo "<table cellSpacing='1' style='margin-bottom:6px;' class='tbl-border' cellPadding='0' width='100%' border='0'><tr>
<td align='center' class='tbl2' width='100%' colspan='2'>".$locale['snb100']."</td>
</tr>
</table>
<table class='tbl-border' border='0' cellpadding='0' cellspacing='1' width='100%'><tr>";
				if ($scrolling_background_image == 1){
	echo "<td id='scrolling_image' class='tbl2'>";
} else if ($scrolling_background_image == 0){
	echo "<td class='tbl2'>";
}
	echo "<div id='marqueecontainer' style='height:$scrolling_ticker_height' onMouseover='copyspeed=pausespeed' onMouseout='copyspeed=marqueespeed'>
<div id='vmarquee' style='position: absolute; width: 100%;'>";

	$scrolling_query = dbquery("SELECT tn.*, tu.user_id,user_name ,COUNT(comment_item_id) AS news_comments
	FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
			LEFT JOIN ".DB_COMMENTS." ON news_id=comment_item_id AND comment_type='N'
				GROUP BY news_id
					ORDER BY news_datestamp DESC LIMIT 0,10");

	while($data = dbarray($scrolling_query)) {
			if ($data['news_cat'] != 0) {
				$result2 = dbquery("SELECT * FROM ".DB_NEWS_CATS." WHERE news_cat_id='".$data['news_cat']."'");
				if (dbrows($result2)) {
					$data2 = dbarray($result2);
				}
			}
	$itemsubject = trimlink($data['news_subject'], 150);

				if ($scrolling_ticker_boxover == 1){
				if ($scrolling_category == 1){
			$ticker_content .= "<b>".$locale['ans107'].":</b> <a href='".BASEDIR."news_cats.php?cat_id=".$data2['news_cat_id']."'><font color='$scrolling_ticker_cat_color'><b>".$data2['news_cat_name']."</b></font></a>";
			$ticker_content .= "<br><img style='vertical-align:middle;' alt='' src='".BASEDIR."advanced_news/images/scroll_news_icon.gif'> <a href='".BASEDIR."advanced_news/ans.php?readmore=".$data['news_id']."' name='news_".$data['news_id']."' id='news_".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','300','yes');return false\" title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_news.gif' style='vertical-align:middle;'> ".str_replace("]", "]]", str_replace("[", "[[", trimlink($data['news_subject'], 30)))."] body=[".str_replace("<img src='".substr(IMAGES_N, strlen(BASEDIR)), "<img src='".IMAGES_N, str_replace("]", "]]", str_replace("[", "[[", trimlink(nl2br(stripslashes($data['news_news'])), 450))))."] delay=[0] fade=[on]\"><b>$itemsubject</b></a>";
}else if ($scrolling_category == 0){
			$ticker_content .= "<br><img style='vertical-align:middle;' alt='' src='".BASEDIR."advanced_news/images/scroll_news_icon.gif'> <a href='".BASEDIR."advanced_news/ans.php?readmore=".$data['news_id']."' name='news_".$data['news_id']."' id='news_".$data['news_id']."' onclick=\"NewWindow(this.href,'name','570','300','yes');return false\" title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_news.gif' style='vertical-align:middle;'> ".str_replace("]", "]]", str_replace("[", "[[", trimlink($data['news_subject'], 30)))."] body=[".str_replace("<img src='".substr(IMAGES_N, strlen(BASEDIR)), "<img src='".IMAGES_N, str_replace("]", "]]", str_replace("[", "[[", trimlink(nl2br(stripslashes($data['news_news'])), 450))))."] delay=[0] fade=[on]\"><b>$itemsubject</b></a>";
}
				if (checkrights("N")) {
			$ticker_content .= "<a href='javascript:void(0)' onclick=\"document.editnews.news_id.value=".$data['news_id']."; document.editnews.submit(); return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_editnews.gif' style='vertical-align:middle;'> ".$locale['admin100']." <font color='red'>".$data['news_id']."</font>] body=[".$locale['pop107']."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_editnews.gif' alt='' border='0'></span></a>&nbsp;<a href='javascript:void(0)' onclick=\"if (confirm('".$locale['admin102']."')) location.href='".BASEDIR."news.php{$aidlink}&amp;readmore=".$data['news_id']."&amp;op=del'; return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_deletenews.gif' style='vertical-align:middle;'> ".$locale['admin101']." <font color='red'>".$data['news_id']."</font>] body=[".phpentities($locale['pop110'])."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_deletenews.gif' alt='' border='0'></span></a>&nbsp;";
			$ticker_content .= "<a href='".BASEDIR."administration/news.php".$aidlink."'><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_writenews.gif' style='vertical-align:middle;'> <font color='#1D679F'>".$locale['admin103']."</font>] body=[".phpentities($locale['pop111'])."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_writenews.gif' alt='' border='0'></span></a><br>";
} else {
			$ticker_content .= "<br>";
}

}else if ($scrolling_ticker_boxover == 0){
				if ($scrolling_category == 1){	
		      $ticker_content .= "<b>".$locale['ans107'].":</b> <a href='".BASEDIR."news_cats.php?cat_id=".$data2['news_cat_id']."'><font color='$scrolling_ticker_cat_color'><b>".$data2['news_cat_name']."</b></font></a>";
			$ticker_content .= "<br><img style='vertical-align:middle;' alt='' src='".BASEDIR."advanced_news/images/scroll_news_icon.gif'> <a href='".BASEDIR."news.php?readmore=".$data['news_id']."'>$itemsubject</a>";
}else if ($scrolling_category == 0){
			$ticker_content .= "<br><img style='vertical-align:middle;' alt='' src='".BASEDIR."advanced_news/images/scroll_news_icon.gif'> <a href='".BASEDIR."news.php?readmore=".$data['news_id']."'>$itemsubject</a>";
}
				if (checkrights("N")) {
			$ticker_content .= " <a href='javascript:void(0)' onclick=\"document.editnews.news_id.value=".$data['news_id']."; document.editnews.submit(); return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_editnews.gif' style='vertical-align:middle;'> ".$locale['admin100']." <font color='red'>".$data['news_id']."</font>] body=[".$locale['pop107']."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_editnews.gif' alt='' border='0'></span></a>&nbsp;<a href='javascript:void(0)' onclick=\"if (confirm('".$locale['admin102']."')) location.href='".BASEDIR."news.php{$aidlink}&amp;readmore=".$data['news_id']."&amp;op=del'; return false;\"><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_deletenews.gif' style='vertical-align:middle;'> ".$locale['admin101']." <font color='red'>".$data['news_id']."</font>] body=[".phpentities($locale['pop110'])."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_deletenews.gif' alt='' border='0'></span></a>&nbsp;";
			$ticker_content .= "<a href='".BASEDIR."administration/news.php".$aidlink."'><span style='cursor:pointer; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_writenews.gif' style='vertical-align:middle;'> <font color='#1D679F'>".$locale['admin103']."</font>] body=[".phpentities($locale['pop111'])."] delay=[0] fade=[on]\"><img style='vertical-align:middle;' src='".BASEDIR."advanced_news/images/icon_writenews.gif' alt='' border='0'></span></a><br>";
} else {
			$ticker_content .= "<br>";
	}
}
				if($scrolling_news_author+$scrolling_ticker_date+$scrolling_ticker_comments+$scrolling_ticker_reads != "0" )  {
	$ticker_content .= "";
				if($scrolling_news_author == "1") {
	$ticker_content .= $locale['040'].$data[user_name];
				if($scrolling_ticker_date+$scrolling_ticker_comments+$scrolling_ticker_reads != "0"){
	$ticker_content .= "<br>";
				}
			}

				if($scrolling_ticker_date == "1") {
	$ticker_content .= showdate("shortdate", $data['news_datestamp']);
				if($scrolling_ticker_comments+$scrolling_ticker_reads != "0"){
	$ticker_content .= "<br>";
				}
			}

				if($scrolling_ticker_comments == "1") {
	$ticker_content .= $data['news_comments'] .$locale['ans113'];
				if($scrolling_ticker_reads != "0"){
	$ticker_content .= "<br>";
				}
			}

				if($scrolling_ticker_reads == "1") {
	$ticker_content .= $data['news_reads'] .$locale['ans112'];
			}

	$ticker_content .= "";
		 }
	$ticker_content .= "<br><br>";
	}
	echo $ticker_content;
	echo "</div></div></td></tr></table>";
	$filename = INFUSIONS."news_archive/";
	if (file_exists($filename)) {

		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".INFUSIONS."news_archive/news_archive.php'>
<input type='submit' value='".$locale['ans121']."' class='button'>
</form>
</td>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
	} else {
		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='25%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
	}

} else if ($news_display_type == 2) { //Vertical news display (standard format)

	if ($settings['news_style'] == "1") { $i = 0; $rc = 0; $ncount = 1; $ncolumn = 1; $news_[0] = ""; $news_[1] = ""; $news_[2] = ""; } else { $i = 1; }
	if (!isset($readmore)) {
	echo "<table cellSpacing='1' style='margin-bottom:6px;' class='tbl-border' cellPadding='0' width='100%' border='0'><tr>
<td align='center' class='tbl2' width='100%' colspan='2'>".$locale['snb101']."</td>
</tr>
</table>
<table class='tbl-border' border='0' cellpadding='0' cellspacing='1' width='100%'><tr>";
	echo "<td class='tbl1'>";
	$rows = dbcount("(news_id)", "news", groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().")");
	if (!isset($rowstart) || !isNum($rowstart)) $rowstart = 0;
	if ($rows != 0) {
		$result = dbquery(
			"SELECT tn.*, tc.*, user_id, user_name FROM ".DB_NEWS." tn
			LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
			LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
			WHERE ".groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().")
			ORDER BY news_sticky DESC, news_datestamp DESC LIMIT $rowstart,$items_per_page"
		);		
		$numrows = dbrows($result);
		if ($settings['news_style'] == "1") $nrows = round((dbrows($result) - 1) / 2);
		while ($data = dbarray($result)) {
			$news_cat_image = "";
			$news_subject = "<a name='news_".$data['news_id']."' id='news_".$data['news_id']."'></a>".stripslashes($data['news_subject']);
			if ($data['news_cat_image']) {
				$news_cat_image = "<a href='news_cats.php?cat_id=".$data['news_cat_id']."'><img src='".IMAGES_NC.$data['news_cat_image']."' alt='".$data['news_cat_name']."' align='left' style='border:0px;margin-top:3px;margin-right:5px'></a>";
			} else {
				$news_cat_image = "";
			}
			$news_news = $data['news_breaks'] == "y" ? nl2br(stripslashes($data['news_news'])) : stripslashes($data['news_news']);
			if ($news_cat_image != "") $news_news = $news_cat_image.$news_news;
			$news_info = array(
				"news_id" => $data['news_id'],
				"user_id" => $data['user_id'],
				"user_name" => $data['user_name'],
				"news_date" => $data['news_datestamp'], 
				"news_ext" => $data['news_extended'] ? "y" : "n",
				"news_reads" => $data['news_reads'],
				"news_comments" => dbcount("(comment_id)", DB_COMMENTS, "comment_type='N' AND comment_item_id='".$data['news_id']."'"),
				"news_allow_comments" => $data['news_allow_comments']
			);
			if ($settings['news_style'] == "1") {
				if ($rows <= 2 || $ncount == 1) {
					$news_[0] .= "<table width='100%' cellpadding='0' cellspacing='0'>\n";
					$news_[0] .= "<tr>\n<td class='tbl2'><b>$news_subject</b></td>\n</tr>\n";
					$news_[0] .= "<tr>\n<td class='tbl1' style='text-align:justify'>$news_news</td>\n</tr>\n";
					$news_[0] .= "<tr>\n<td align='center' class='tbl2'>\n";
					if (checkrights("N")) $news_[0] .= "<form name='editnews".$news_info['news_id']."' method='post' action='".ADMIN."news.php".$aidlink."&amp;news_id=".$news_info['news_id']."'>\n";
					$news_[0] .= "<span class='small2'><img src='".THEME."images/bullet.gif' alt=''> <a href='profile.php?lookup=".$news_info['user_id']."'>".$news_info['user_name']."</a> ".$locale['041'].showdate("longdate", $news_info['news_date'])." &middot;\n";
					if ($news_info['news_ext'] == "y" || $news_info['news_allow_comments']) {
						$news_[0] .= $news_info['news_ext'] == "y" ? "<a href='".FUSION_SELF."?readmore=".$news_info['news_id']."'>".$locale['042']."</a> &middot;\n" : "";
						$news_[0] .= $news_info['news_allow_comments'] ? "<a href='".FUSION_SELF."?readmore=".$news_info['news_id']."'>".$news_info['news_comments'].$locale['ans113']."</a> &middot;\n" : "";
						$news_[0] .= $news_info['news_reads'].$locale['ans112']." &middot;\n";
					}
					$news_[0] .= "<a href='print.php?type=N&amp;item_id=".$news_info['news_id']."'><img src='".THEME."images/printer.gif' alt='".$locale['ans106']."' style='border:0px;vertical-align:middle;'></a>";
					if (checkrights("N")) { $news_[0] .= " &middot; <input type='hidden' name='edit' value='edit'><a href='javascript:document.editnews".$news_info['news_id'].".submit();'><img src='".IMAGES."edit.gif' alt='".$locale['048']."' title='".$locale['048']."' style='vertical-align:middle;border:0px;'></a></span>\n</form>\n"; } else { $news_[0] .= "</span>\n"; }
					$news_[0] .= "</td>\n</tr>\n</table>\n";
					if ($ncount != $rows) $news_[0] .= "<div><img src='".THEME."images/blank.gif' alt='' width='1' height='8'></div>\n";
				} else {
					if ($i == $nrows && $ncolumn != 2) { $ncolumn = 2; $i = 0; }
					$row_color = ($rc % 2 == 0 ? "tbl2" : "tbl1");
					$news_[$ncolumn] .= "<table width='100%' cellpadding='0' cellspacing='0'>\n";
					$news_[$ncolumn] .= "<tr>\n<td class='tbl2'><b>$news_subject</b></td>\n</tr>\n";
					$news_[$ncolumn] .= "<tr>\n<td class='tbl1' style='text-align:justify'>$news_news</td>\n</tr>\n";
					$news_[$ncolumn] .= "<tr>\n<td align='center' class='tbl2'>\n";
					if (checkrights("N")) $news_[$ncolumn] .= "<form name='editnews".$news_info['news_id']."' method='post' action='".ADMIN."news.php".$aidlink."&amp;news_id=".$news_info['news_id']."'>\n";
					$news_[$ncolumn] .= "<span class='small2'><img src='".THEME."images/bullet.gif' alt=''> <a href='profile.php?lookup=".$news_info['user_id']."'>".$news_info['user_name']."</a> ".$locale['041'].showdate("longdate", $news_info['news_date']);
					if ($news_info['news_ext'] == "y" || $news_info['news_allow_comments']) {
						$news_[$ncolumn] .= "<br>\n";
						$news_[$ncolumn] .= $news_info['news_ext'] == "y" ? "<a href='".FUSION_SELF."?readmore=".$news_info['news_id']."'>".$locale['042']."</a> &middot;\n" : "";
						$news_[$ncolumn] .= $news_info['news_allow_comments'] ? "<a href='".FUSION_SELF."?readmore=".$news_info['news_id']."'>".$news_info['news_comments'].$locale['ans113']."</a> &middot;\n" : "";
						$news_[$ncolumn] .= $news_info['news_reads'].$locale['ans112']." &middot;\n";
					} else {
						$news_[$ncolumn] .= " &middot;\n";
					}
					$news_[$ncolumn] .= "<a href='print.php?type=N&amp;item_id=".$news_info['news_id']."'><img src='".THEME."images/printer.gif' alt='".$locale['ans106']."' style='border:0px;vertical-align:middle;'></a>\n";
					if (checkrights("N")) { $news_[$ncolumn] .= " &middot; <input type='hidden' name='edit' value='edit'><a href='javascript:document.editnews".$news_info['news_id'].".submit();'><img src='".IMAGES."edit.gif' alt='".$locale['048']."' title='".$locale['048']."' style='vertical-align:middle;border:0px;'></a></span>\n</form>\n"; } else { $news_[$ncolumn] .= "</span>\n"; }
					$news_[$ncolumn] .= "</td>\n</tr>\n</table>\n";
					if ($ncolumn == 1 && $i < ($nrows - 1)) $news_[$ncolumn] .= "<div><img src='".THEME."images/blank.gif' alt='' width='1' height='8'></div>\n";
					if ($ncolumn == 2 && $i < (dbrows($result) - $nrows - 2)) $news_[$ncolumn] .= "<div><img src='".THEME."images/blank.gif' alt='' width='1' height='8'></div>\n";
					$i++; $rc++;
				}
				$ncount++;
			} else {
				render_news($news_subject, $news_news, $news_info);
				if ($i != $numrows) { tablebreak(); } $i++;
			}
		}
		if ($settings['news_style'] == "1") {
			opentable($locale['046']);
			echo "<table cellpadding='0' cellspacing='0' style='width:100%'>\n<tr>\n<td colspan='3' style='width:100%'>\n";
			echo $news_[0];
			echo "</td>\n</tr>\n<tr>\n<td style='width:50%;vertical-align:top;'>\n";
			echo $news_[1];
			echo "</td>\n<td style='width:10px'><img src='".THEME."images/blank.gif' alt='' width='10' height='1'></td>\n<td style='width:50%;vertical-align:top;'>\n";
			echo $news_[2];
			echo "</td>\n</tr>\n</table>\n";
			closetable();
		}
		if ($rows > $items_per_page) echo "<br /><div align='center' style='margin-top:-5px;margin-bottom:4px;'>\n".makePageNav($rowstart,$items_per_page,$rows,3)."\n</div>\n";
	} else {
		opentable($locale['046']);
		echo "<center><br>\n".$locale['047']."<br><br>\n</center>\n";
		closetable();
	}
} else {
	include INCLUDES."comments_include.php";
	include INCLUDES."ratings_include.php";
	$result = dbquery(
		"SELECT tn.*, user_id, user_name FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
		WHERE news_id='$readmore'"
	);
	if (dbrows($result)!=0) {
		$data = dbarray($result);
		if (checkgroup($data['news_visibility'])) {
			$news_cat_image = "";
			if (!isset($_POST['post_comment']) && !isset($_POST['post_rating'])) {
				 $result2 = dbquery("UPDATE ".DB_NEWS." SET news_reads=news_reads+1 WHERE news_id='$readmore'");
				 $data['news_reads']++;
			}
			$news_subject = $data['news_subject'];
			if ($data['news_cat'] != 0) {
				$result2 = dbquery("SELECT * FROM ".DB_NEWS_CATS." WHERE news_cat_id='".$data['news_cat']."'");
				if (dbrows($result2)) {
					$data2 = dbarray($result2);
					$news_cat_image = "<a href='news_cats.php?cat_id=".$data2['news_cat_id']."'><img src='".IMAGES_NC.$data2['news_cat_image']."' alt='".$data2['news_cat_name']."' align='left' style='border:0px;margin-top:3px;margin-right:5px'></a>";
				}
			}
			$news_news = stripslashes($data['news_extended'] ? $data['news_extended'] : $data['news_news']);
			if ($data['news_breaks'] == "y") { $news_news = nl2br($news_news); }
			if ($news_cat_image != "") $news_news = $news_cat_image.$news_news;
			$news_info = array(
				"news_id" => $data['news_id'],
				"user_id" => $data['user_id'],
				"user_name" => $data['user_name'],
				"news_date" => $data['news_datestamp'],
				"news_ext" => "n",
				"news_reads" => $data['news_reads'],
				"news_comments" => dbcount("(comment_id)", DB_COMMENTS, "comment_type='N' AND comment_item_id='".$data['news_id']."'"),
				"news_allow_comments" => $data['news_allow_comments']
			);
			render_news($news_subject, $news_news, $news_info);
			if ($data['news_allow_comments']) showcomments("N","news","news_id",$readmore,FUSION_SELF."?readmore=$readmore");
			if ($data['news_allow_ratings']) showratings("N",$readmore,FUSION_SELF."?readmore=$readmore");
		} else {
			redirect(FUSION_SELF);
		}
	} else {
		redirect(FUSION_SELF);
	}
}
	echo "</td>
  </tr>
</table>\n";

	$filename = INFUSIONS."news_archive/";
	if (file_exists($filename)) {

		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".INFUSIONS."news_archive/news_archive.php'>
<input type='submit' value='".$locale['ans121']."' class='button'>
</form>
</td>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='50%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
	} else {
		echo "<table style='margin-top:6px;' cellpadding='0' cellspacing='1' width='100%' class='tbl-border'><tr>
<td style='padding-bottom:6px;' align='center' class='tbl1' width='25%'>
<form method='POST' action='".BASEDIR."news_cats.php'>
<input type='submit' value='".$locale['ans122']."' class='button'>
</form>
</td></tr>
</table>
<div align='right'><span style='cursor:default; text-decoration: none;' title=\"header=[<img src='".BASEDIR."advanced_news/images/adv_copyright.gif' style='vertical-align:middle;'> ".$locale['ans124']."] body=[".$locale['pop106']."] delay=[0] fade=[on]\">&copy;</span></div>";
}  
	}
} else {
	include INCLUDES."comments_include.php";
	include INCLUDES."ratings_include.php";
	$result = dbquery(
		"SELECT tn.*, user_id, user_name FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
		WHERE news_id='".$_GET['readmore']."' AND news_draft='0'"
	);
	if (dbrows($result)!=0) {
		$data = dbarray($result);
		if (checkgroup($data['news_visibility'])) {
			$news_cat_image = "";
			if (!isset($_POST['post_comment']) && !isset($_POST['post_rating'])) {
				 $result2 = dbquery("UPDATE ".DB_NEWS." SET news_reads=news_reads+1 WHERE news_id='".$_GET['readmore']."'");
				 $data['news_reads']++;
			}
			$news_subject = $data['news_subject'];
			if ($data['news_cat']) {
				$result2 = dbquery("SELECT * FROM ".DB_NEWS_CATS." WHERE news_cat_id='".$data['news_cat']."'");
				if (dbrows($result2)) {
					$data2 = dbarray($result2);
					$news_cat_image = "<a href='news_cats.php?cat_id=".$data2['news_cat_id']."'><img src='".get_image("nc_".$data2['news_cat_name'])."' alt='".$data2['news_cat_name']."' class='news-category' /></a>";
				}
			}
			$news_news = stripslashes($data['news_extended'] ? $data['news_extended'] : $data['news_news']);
			if ($data['news_breaks'] == "y") { $news_news = nl2br($news_news); }
			if ($news_cat_image != "") $news_news = $news_cat_image.$news_news;
			$news_info = array(
				"news_id" => $data['news_id'],
				"user_id" => $data['user_id'],
				"user_name" => $data['user_name'],
				"news_date" => $data['news_datestamp'],
				"news_ext" => "n",
				"news_reads" => $data['news_reads'],
				"news_comments" => dbcount("(comment_id)", DB_COMMENTS, "comment_type='N' AND comment_item_id='".$data['news_id']."'"),
				"news_allow_comments" => $data['news_allow_comments']
			);
			add_to_title($locale['global_201'].$news_subject);
			echo "<!--news_pre_readmore-->";
			render_news($news_subject, $news_news, $news_info);
			echo "<!--news_sub_readmore-->";
			if ($data['news_allow_comments']) { showcomments("N", DB_NEWS, "news_id", $_GET['readmore'], FUSION_SELF."?readmore=".$_GET['readmore']); }
			if ($data['news_allow_ratings']) { showratings("N", $_GET['readmore'], FUSION_SELF."?readmore=".$_GET['readmore']); }
		} else {
			redirect(FUSION_SELF);
		}
	} else {
		redirect(FUSION_SELF);
	}
}
if (checkrights("N")) {
	echo "<form name='editnews' method='post' action='".ADMIN."news.php".$aidlink."&amp;action=edit&amp;news_id='>
<input type='hidden' name='news_id' value=''>
<input type='hidden' name='edit'>
</form>\n";
	}
closetable();

require_once THEMES."templates/footer.php";
?>