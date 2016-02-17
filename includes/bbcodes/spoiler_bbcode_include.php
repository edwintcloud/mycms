<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: spoiler_bbcode_include.php
| Author: SoulSmasher
| Version 1.1
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

//bu þekilde 2 kere heade eklenmesinden kurtuluruz
//This way, it prevents to add the code twice
if(!function_exists("spoilerhead")) {
function spoilerhead() {
$spoiler_adding="<script type='text/javascript' src='".INCLUDES."sgbeal-spoilers.jquery.js'></script>
<style type='text/css'>
.jqSpoiler {
	background-image:url(".IMAGES."bbcode_spoiler.png);
	border:1px dotted red;
}

.jqSpoiler span {
	visibility: hidden;
}

.jqSpoiler.reveal {
	background-image: none;
	border: none;
}

.jqSpoiler.reveal span {
	visibility: visible;
} 

</style>
<script type='text/javascript'>
$(document).ready(function(){
	// Clickable spoiler:
	$('.jqSpoilerClick').initSpoilers({method:'click'})
		.addClass('jqSpoiler');
});

</script>";
return $spoiler_adding;
}
$head_spoiler=spoilerhead();
add_to_head($head_spoiler);
}


$text = preg_replace('#\[spoiler\](.*?)\[/spoiler\]#si', '<strong>'.$locale['bb_spoiler_warntext'].'</strong><br /><span class=\'jqSpoilerClick\'><span>\\1</span></span>
', $text);
?>