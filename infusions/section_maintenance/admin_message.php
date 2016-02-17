<?php
/*---------------------------------------------------+
| PHP-Fusion 7 Content Management System
+----------------------------------------------------+
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
|----------------------------------------------------+
| Filename: admin_message.php
| Author: HobbyMan
| Web: http://www.php-fusion.hobbysites.net/
+----------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+----------------------------------------------------*/

if (file_exists(INFUSIONS."section_maintenance/locale/".$settings['locale'].".php")) {
	include INFUSIONS."section_maintenance/locale/".$settings['locale'].".php";
} else { include INFUSIONS."section_maintenance/locale/English.php"; }

include INFUSIONS."section_maintenance/infusion_db.php";

$sm_data = dbarray(dbquery("SELECT * FROM ".DB_SECTION_MAINTENANCE));
$adm_title = $locale['sma300'];
$url = "". fetch_url() . "";
$path_parts = pathinfo($url);

$time = "".strftime('%d/%m/%Y %H:%M', $sm_data['sma_datestamp']+($settings['timeoffset']*3600))."";

if (iADMIN) {

if ($sm_data['sma_reg'] == "1" && (FUSION_SELF == "register.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma116']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif ($sm_data['sma_cont'] == "1" && (FUSION_SELF == "contact.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma113']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_photo'] == "1" && (FUSION_SELF == "photogallery.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma102']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_articles'] == "1" && (FUSION_SELF == "articles.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma103']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_news'] == "1" && (FUSION_SELF == "news.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma104']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_news'] == "1" && (FUSION_SELF == "news_cats.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma104a']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_forum'] == "1" && ($path_parts['dirname'] == $settings['siteurl']."forum")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma105']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_inf'] == "1" && ($path_parts['dirname'] == "infusions")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma115']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_members'] == "1" && (FUSION_SELF == "members.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma106']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_prof'] == "1" && (FUSION_SELF == "profile.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma117']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
}elseif  ($sm_data['sma_down'] == "1" && (FUSION_SELF == "downloads" || FUSION_SELF == "download.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma107']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_pm'] == "1" && (FUSION_SELF == "messages.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma108']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_weblinks'] == "1" && (FUSION_SELF == "weblinks.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma109']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_submissions'] == "1" && (FUSION_SELF == "submit.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma110']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_faq'] == "1" && (FUSION_SELF == "faq.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma111']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_cust'] == "1" && (FUSION_SELF == "viewpage.php")) {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma114']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} elseif  ($sm_data['sma_all'] == "1") {
opentable($adm_title);
echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma228']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
closetable();
} else {

  $title = $locale['title'];
  $result = dbquery("SELECT inf_folder FROM ".DB_INFUSIONS." WHERE inf_title !='$title'");	
  if (dbrows($result)) {
	while($data = dbarray($result)) {
  if ($sm_data['sma_inf'] == "1" && ($settings['siteurl']."infusions/".$data['inf_folder'] == $path_parts['dirname'])) {
  opentable($title);
  echo "<div class='admin-message' align='center'><b>".$locale['sma229']." ".$locale['sma115']." ".$locale['sma305']." ".$sm_data['sma_sign']." ".$time."</b></div>";
  closetable();
   }
     }
       }
         }
           } else { echo ""; } $adm_title=""; $title="";

?>