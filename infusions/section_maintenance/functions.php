<?php
/*---------------------------------------------------+
| PHP-Fusion 7 Content Management System
+----------------------------------------------------+
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
|----------------------------------------------------+
| Filename: functions.php
| Author: HobbyMan
| Web: http://php-fusion.hobbysites.net/
+----------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+----------------------------------------------------*/

if (file_exists(INFUSIONS."section_maintenance/locale/".$settings['locale'].".php.php")) {
	include INFUSIONS."section_maintenance/locale/".$settings['locale'].".php";
} else { include INFUSIONS."section_maintenance/locale/English.php"; }

include INFUSIONS."section_maintenance/infusion_db.php";

//check compatibility mode
if (!function_exists("fetch_url")) {
   function fetch_url() {
      $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
      return $url;
   }
}

$smastatus = dbarray(dbquery("SELECT * FROM ".DB_SECTION_MAINTENANCE));
 if ($smastatus['sma_show_admmsg'] == 0) {include INFUSIONS."section_maintenance/admin_message.php";} 

$url = "". fetch_url() . "";
$path_parts = pathinfo($url);
$goto = BASEDIR."infusions/section_maintenance/section_closed.php";

If (!iADMIN) {

// Contact
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "register.php")) redirect($goto);
if ($smastatus['sma_reg'] == "1" && (FUSION_SELF == "register.php")) redirect($goto);

// Contact
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "contact.php")) redirect($goto);
if ($smastatus['sma_cont'] == "1" && (FUSION_SELF == "contact.php")) redirect($goto);

//Photgallery
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "photogallery.php")) redirect($goto);
if ($smastatus['sma_photo'] == "1" && (FUSION_SELF == "photogallery.php")) redirect($goto);

// Articles
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "articles.php")) redirect($goto);
if ($smastatus['sma_articles'] == "1" && (FUSION_SELF == "articles.php")) redirect($goto);

// News
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "news.php")) redirect($goto);
if ($smastatus['sma_news'] == "1" && (FUSION_SELF == "news.php")) redirect($goto);
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "news_cats.php")) redirect($goto);
if ($smastatus['sma_news'] == "1" && (FUSION_SELF == "news_cats.php")) redirect($goto);

// Forum
if ($smastatus['sma_all'] == "1" && ($path_parts['dirname'] == $settings['siteurl']."forum")) redirect($goto);
if ($smastatus['sma_forum'] == "1" && ($path_parts['dirname'] == $settings['siteurl']."forum")) redirect($goto);

// Memberlist
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "members.php")) redirect($goto);
if ($smastatus['sma_members'] == "1" && (FUSION_SELF == "members.php")) redirect($goto);

// Downloads
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "downloads.php")) redirect($goto);
if ($smastatus['sma_down'] == "1" && (FUSION_SELF == "downloads.php")) redirect($goto);
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "download.php")) redirect($goto);
if ($smastatus['sma_down'] == "1" && (FUSION_SELF == "download.php")) redirect($goto);

// Profiles
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "profile.php")) redirect($goto);
if ($smastatus['sma_prof'] == "1" && (FUSION_SELF == "profile.php")) redirect($goto);

// Private Messaging
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "messages.php")) redirect($goto);
if ($smastatus['sma_pm'] == "1" && (FUSION_SELF == "messages.php")) redirect($goto);

// Weblinks
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "weblinks.php")) redirect($goto);
if ($smastatus['sma_weblinks'] == "1" && (FUSION_SELF == "weblinks.php")) redirect($goto);

// Submissions
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "submit.php")) redirect($goto);
if ($smastatus['sma_submissions'] == "1" && (FUSION_SELF == "submit.php")) redirect($goto);

// FAQ
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "faq.php")) redirect($goto);
if ($smastatus['sma_faq'] == "1" && (FUSION_SELF == "faq.php")) redirect($goto);

// Custom Pages
if ($smastatus['sma_all'] == "1" && (FUSION_SELF == "viewpage.php")) redirect($goto);
if ($smastatus['sma_cust'] == "1" && (FUSION_SELF == "viewpage.php")) redirect($goto);

// All Infusions
if ($smastatus['sma_all'] == "1" || $smastatus['sma_inf'] == "1") { 
$title = $locale['title'];
$result = dbquery("SELECT inf_folder FROM ".DB_INFUSIONS." WHERE inf_title !='$title'");	

if (dbrows($result)) {
	while($data = dbarray($result)) {
	
if ($smastatus['sma_all'] == "1" && ($settings['siteurl']."infusions/".$data['inf_folder'] == $path_parts['dirname'])) redirect($goto);
if ($smastatus['sma_inf'] == "1" && ($settings['siteurl']."infusions/".$data['inf_folder'] == $path_parts['dirname'])) redirect($goto);

  }
    }
      }
        } else { echo ""; } 

?>