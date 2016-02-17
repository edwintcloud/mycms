<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: user_shouts-stat_include.php
| Author: Digitanium
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (file_exists(INFUSIONS."fusionboard4/locale/".$settings['locale'].".php")) {
	include INFUSIONS."fusionboard4/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."fusionboard4/locale/English.php";
}

if ($profile_method == "input") {
	//Nothing here
} elseif ($profile_method == "display") {
	echo "<tr>\n";
	echo "<td width='50%' class='tbl2' style='text-align:center;vertical-align:top;'><script src='".INFUSIONS."fusionboard4/includes/js/boxover.js' type='text/javascript'></script>
	<span style='font-size:12px;font-weight:bold;'>".$locale['fb865']."</span><br />";
	
	$recieves = dbquery("select *, count(t.type_name) as total from ".DB_PREFIX."fb_rate r
	left join ".DB_PREFIX."fb_rate_type t on r.rate_type=t.type_id
	where r.rate_user='".$user_data['user_id']."' group by r.rate_type order by total desc");
	if(dbrows($recieves)){
		$counter = 0;
		while($recieve = dbarray($recieves)){
			$user_res = dbquery("select *, count(rate_by) as total from ".DB_PREFIX."fb_rate r
			left join ".DB_USERS." u on u.user_id=r.rate_by
			where r.rate_user='".$user_data['user_id']."' and r.rate_type='".$recieve['rate_type']."' group by r.rate_by order by total desc");
			$counter = 0; $users = "<b>".$locale['fb862']."</b><br />";
			while($user_d = dbarray($user_res)){
				$users .= ($counter !== 0 ? "<br />" : "").$user_d['user_name']." (".$user_d['total']."x)";
				$counter++;
			}
			if($counter !== "0") echo "<br />";
			echo "<span title='header=[".$recieve['type_name']."] body=[$users]'><img src='".INFUSIONS."fusionboard4/images/forum_icons/".$recieve['type_icon']."' style='vertical-align:middle;'> x ".$recieve['total']."</span>\n";
			$counter++;
		}
	} else {
	
		echo $locale['fb867'];
	
	}
	
	echo "</td>
	<td width='50%' class='tbl2' style='text-align:center;vertical-align:top;'><span style='font-size:12px;font-weight:bold;'>".$locale['fb866']."</span><br />";

	$sends = dbquery("select *, count(t.type_name) as total from ".DB_PREFIX."fb_rate r
	left join ".DB_PREFIX."fb_rate_type t on r.rate_type=t.type_id
	where r.rate_by='".$user_data['user_id']."' group by r.rate_type order by total desc");
	if(dbrows($sends)){
		$counter = 0;
		while($send = dbarray($sends)){
			$user_res = dbquery("select *, count(rate_user) as total from ".DB_PREFIX."fb_rate r
			left join ".DB_USERS." u on u.user_id=r.rate_user
			where r.rate_by='".$user_data['user_id']."' and r.rate_type='".$send['rate_type']."' group by r.rate_user order by total desc");
			$counter = 0; $users = "<b>".$locale['fb868']."</b><br />";
			while($user_d = dbarray($user_res)){
				$users .= ($counter !== 0 ? "<br />" : "").$user_d['user_name']." (".$user_d['total']."x)";
				$counter++;
			}
			if($counter !== "0") echo "<br />";
			echo "<span title='header=[".$send['type_name']."] body=[$users]'><img src='".INFUSIONS."fusionboard4/images/forum_icons/".$send['type_icon']."' style='vertical-align:middle;'> x ".$send['total']."</span>\n";
			$counter++;
		}
	} else {
	
		echo $locale['fb867'];
	
	}
	
	echo "</td>
	</tr>\n";
	
} elseif ($profile_method == "validate_insert") {
	//Nothing here
} elseif ($profile_method == "validate_update") {
	//Nothing here
}
?>