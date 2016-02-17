<?php
include "../../../../maincore.php";
if (! isset ( $_GET ['q'] ) || ! isNum ( $_GET ['q'] ) || ! $_GET ['sid'])
	die ( "Access Denied" );
if (! iADMIN)
	die ( "Access Denied" );

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}
include INFUSIONS . "fusionboard4/includes/func.php";

$result = dbquery ( "select * from " . $db_prefix . "fb_awards where award_user='" . $_GET ['q'] . "'" );
if (dbrows ( $result )) {
	
	while ( $data = dbarray ( $result ) ) {
		
		echo "<img src='images/awards/" . $data ['award_image'] . "' alt='" . stripslash ( $data ['award_desc'] ) . "' title='" . stripslash ( $data ['award_desc'] ) . "' border='0'> ";
		echo "<a href='admin.php" . $aidlink . "&amp;section=awards&amp;del=" . $data ['award_id'] . "'>delete</a><br />\n";
	
	}

} else {
	
	echo "<div class='center'>" . $locale ['fb720'] . "</div>\n";

}
?>