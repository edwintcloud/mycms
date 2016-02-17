<?php
include "../../../maincore.php";
if(!isset($_GET['user']) || !isNum($_GET['user'])) die("Access Denied!");

$result = dbquery("select * from ".DB_USERS." where user_id='".$_GET['user']."'");
if(!dbrows($result)) die("Access Denied!");
$data = dbarray($result);

// The file
$filename = IMAGES."avatars/".$data['user_avatar'];
$path_info = pathinfo($filename);
$ext = $path_info['extension'];

// Get new dimensions
list($width, $height) = getimagesize($filename);
$new_width = ((isset($_GET['size']) && isNum($_GET['size'])) ? $_GET['size'] : 50);
$new_height = ($height * ($new_width/$height));

// Content type
if($ext == "jpg"){
	header('Content-type: image/jpeg');
} elseif($ext == "png"){
	header('Content-type: image/png');
} elseif($ext == "gif"){
	header('Content-type: image/gif');
}

// Resample
if($ext == "jpg"){
	$image_p = imagecreatetruecolor($new_width, $new_height);
	$image = imagecreatefromjpeg($filename);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	// Output
	imagejpeg($image_p, null, 100);
} elseif($ext == "png"){
	$image_p = imagecreatetruecolor($new_width, $new_height);
	$image = imagecreatefrompng($filename);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	// Output
	imagepng($image_p, null, 100);
} elseif($ext == "gif"){
	$image_p = imagecreatetruecolor($new_width, $new_height);
	$image = imagecreatefromgif($filename);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	// Output
	imagegif($image_p, null, 100);
}
?>
