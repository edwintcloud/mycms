<?php
/*---------------------------------------------------+
| PHP-Fusion 7 Content Management System
+----------------------------------------------------+
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
|----------------------------------------------------+
| Infusion: Section Maintenance Infusion v2.3
| Filename: section_closed.php
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

require_once "../../maincore.php";
require_once THEMES."templates/header.php";
include INFUSIONS."section_maintenance/infusion_db.php";

if (file_exists(INFUSIONS."section_maintenance/locale/".$settings['locale'].".php")) {
	include INFUSIONS."section_maintenance/locale/".$settings['locale'].".php";
} else { include INFUSIONS."section_maintenance/locale/English.php"; }

    $data_sm = dbarray(dbquery("SELECT * FROM ".DB_SECTION_MAINTENANCE));
    if ($data_sm['sma_temp'] == 0) { $temp = $locale['sma301'].$locale['sma234']; } else { $temp = $locale['sma301'].$locale['sma205']; }
    add_to_title($locale['global_200'].$temp);
	opentable($locale['sma300']);
	if($data_sm['sma_show_image'] == 1) {
    echo "<div align='center'><br /><img src='".INFUSIONS."section_maintenance/images/maintenance.jpg' width='347' border='1' alt='".$locale['sma205']."' /></div>\n"; }
    echo "<br /><table border='0' align='center' cellspacing='0' width='100%'>\n<tr>\n";
    
	echo "<td align='center'><b>".$temp."</b><br /></td></tr>";
	$message = nl2br(parseubb(parsesmileys(stripslashes($data_sm['sma_message']))));
	echo "<tr><td align='center' class='tbl'><br />".$message."</td></tr>";
	if ($data_sm['sma_temp'] == 0) {
	echo "<tr><td class='small' align='center'><br />".$locale['sma302']."<br /><br /></td></tr>\n"; }
		
	if($data_sm['sma_time'] != 0) { echo "<tr>\n<td align='center' class='small'><br />".$locale['sma304']." ".$data_sm['sma_time']."\n";
    if ($data_sm['sma_time'] >1) { $s = $locale['sma232']; } else { $s=""; }
    if ($data_sm['sma_period'] == 0) { echo "".$locale['sma220'].$s."";
    } elseif ($data_sm['sma_period'] == 1) { echo "".$locale['sma221'].$s."";
    } elseif ($data_sm['sma_period'] == 2) { echo "".$locale['sma222'].$s."";
    } elseif ($data_sm['sma_period'] == 3) { echo "".$locale['sma223'].$s."";
    } elseif ($data_sm['sma_period'] == 4) { echo "".$locale['sma224'].$s.""; }
    echo "</td>\n</tr>\n"; }
	if($data_sm['sma_show_sig'] == 1) {
	echo "<tr>\n<td align='left' class='small'>";
	echo "<br />".$locale['sma305']." ".$data_sm['sma_sign']."&nbsp;".$locale['sma218']."&nbsp;".strftime('%d/%m/%Y %H:%M',$data_sm['sma_datestamp']+($settings['timeoffset']*3600))."<br /></td>\n</tr>\n"; }	
    echo "</table>\n";
    closetable();
 
require_once THEMES."templates/footer.php";
?>