<?php
include "../../../../maincore.php";
if (! $_GET ['sid'])
	die ( "Access Denied" );
if (! iADMIN)
	die ( "Access Denied" );

if (file_exists ( INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php" )) {
	include INFUSIONS . "fusionboard4/locale/" . $settings ['locale'] . ".php";
} else {
	include INFUSIONS . "fusionboard4/locale/English.php";
}
include INFUSIONS . "fusionboard4/includes/func.php";

if (isset ( $_GET ['user'] ) && isNum ( $_GET ['user'] )) {
	$image = stripinput ( $_GET ['image'] );
	$desc = addslash ( stripinput ( $_GET ['desc'] ) );
	$result = dbquery ( "insert into " . DB_PREFIX . "fb_awards (award_user, award_image, award_desc) VALUES('" . $_GET ['user'] . "', '$image', '$desc')" );
}

if (isset ( $_GET ['del'] ) && isNum ( $_GET ['del'] )) {
	$query = dbquery ( "delete from " . DB_PREFIX . "fb_awards where award_id='" . $_GET ['del'] . "'" );
}
?>