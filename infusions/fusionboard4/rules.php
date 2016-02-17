<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
require_once "../../maincore.php";
require_once THEMES . "templates/header.php";
include LOCALE . LOCALESET . "forum/main.php";
include INFUSIONS . "fusionboard4/includes/func.php";

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}

add_to_title ( " :: " . $locale ['fb909'] );

opentable ( $locale ['fb909'] );

renderNav ( false, false, array (INFUSIONS . "fusionboard4/rules.php", $locale ['fb909'] ) );

if ($fb4 ['forum_rules']) {
	
	echo stripslash ( $fb4 ['forum_rules'] );

} else {
	
	echo "<div align='center'>" . $locale ['fb917'] . "</div>\n";

}

closetable ();

require_once THEMES . "templates/footer.php";
?>