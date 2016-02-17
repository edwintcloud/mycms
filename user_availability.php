<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System                   |
| Copyright (C) 2002 - 2008 Nick Jones                   |
| http://www.php-fusion.co.uk/                           |
+--------------------------------------------------------+
| Filename: register.php                                 |
| Author: Nick Jones (Digitanium)                        |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------+
| Filename: user_availability.php                                 |
| Version: 0.6                                           |
| Author: Barspin   (barspin@blendtek.net)                                     |
+--------------------------------------------------------+
| Ajax mods:                                             |
|********************************************************|
| Username availability                                  |
| Web: www.roshanbh.com.np                               |
| Author: Roshan Bhattarai                               |
|                                                        |
| Passwords strength                                     |
| Web: http://digitalspaghetti.me.uk/digitalspaghetti    |
| Author: Tane Piper (digitalspaghetti@gmail.com)        |
| License: www.opensource.org/licenses/mit-license.php   |
|                                                        |
| Email validation                                       |
| Web: www.livevalidation.com                            |
| Author: Alec Hill                                      |
| License: www.opensource.org/licenses/mit-license.php   |
+--------------------------------------------------------*/
require "maincore.php";


$query = "SELECT * FROM ".DB_PREFIX."users where user_name = '$_REQUEST[username]'";

$result = mysql_query($query);
if(mysql_num_rows($result)>0){

echo "no";
}else{
echo "yes";
}


?>