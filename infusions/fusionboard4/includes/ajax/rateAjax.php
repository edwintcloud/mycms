<?php
include "../../../../maincore.php";
include INCLUDES . "output_handling_include.php";
include INFUSIONS . "fusionboard4/includes/func.php";
if (! iMEMBER)
	die ( "Access Denied" );

if ((isset ( $_GET ['post'] ) && isNum ( $_GET ['post'] )) && (isset ( $_GET ['from'] ) && isNum ( $_GET ['from'] )) && (isset ( $_GET ['to'] ) && isNum ( $_GET ['to'] )) && (isset ( $_GET ['type'] ) && isNum ( $_GET ['type'] ))) {
	if ($_GET ['from'] !== $_GET ['to']) {
		if (! dbcount ( "(rate_by)", DB_PREFIX . "fb_rate", "rate_type='" . $_GET ['type'] . "' and rate_user='" . $_GET ['to'] . "' and rate_post='" . $_GET ['post'] . "' and rate_by='" . $_GET ['from'] . "'" )) {
			$result = "insert into " . DB_PREFIX . "fb_rate (rate_type, rate_user, rate_post, rate_by) VALUES('" . $_GET ['type'] . "', '" . $_GET ['to'] . "', '" . $_GET ['post'] . "', '" . $_GET ['from'] . "')";
			if (dbquery ( $result )) {
				echo "rated!";
			} else {
				echo "failed";
			}
		} else {
			echo "failed";
		}
	} else {
		echo "failed";
	}
} else {
	echo "failed";
}
?>