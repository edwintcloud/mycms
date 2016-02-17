<?php
/*
	fusionBoard 4.0
	php-Invent Team
	http://www.php-invent.com
	
	Developer: Ian Unruh (SoBeNoFear)
	ianunruh@gmail.com
*/ 
if(!defined("IN_FUSION")) die("Access Denied");
add_to_head("<script type='text/javascript'>
function giveRating(post, from, to, type){
  $('#rb_' + post).html(\"<img src='".INFUSIONS."fusionboard4/images/ajax-loader.gif' alt='' style='vertical-align:middle;'>\");
  $('#rb_' + post).load(\"".INFUSIONS."fusionboard4/includes/ajax/rateAjax.php\"+\"?post=\"+post+\"&from=\"+from+\"&to=\"+to+\"&type=\"+type+\"&sid=\"+Math.random());
}
</script>");
?>
<style type='text/css'>
.ratingbox {
	opacity:<?php echo $fb4['rating_opacity'] ?>;
}
</style>
<?php
function showRatings($post, $userfrom, $userto, $wrapper=true){
	global $ratingsExist;
	$types = dbquery("select * from ".DB_PREFIX."fb_rate_type");
	if(($userfrom !== $userto) && dbrows($types)){
		if($wrapper) echo "<div style='float:right;' id='rb_".$post."'>";
		echo "<span id='ratename$post' class='small'></span>&nbsp;\n";
		while($type = dbarray($types)){
			if(!dbrows(dbquery("select * from ".DB_PREFIX."fb_rate where rate_type='".$type['type_id']."' and rate_user='$userto' and rate_post='$post' and rate_by='$userfrom'"))){
				echo "<span onMouseOver='document.getElementById(\"ratename$post\").innerHTML=\"".stripslash($type['type_name'])."\";' onMouseOut='document.getElementById(\"ratename$post\").innerHTML=\" \"' onClick='giveRating($post, $userfrom, $userto, ".$type['type_id'].");'><img src='".INFUSIONS."fusionboard4/images/forum_icons/".$type['type_icon']."' alt='".stripslash($type['type_name'])."' title='".stripslash($type['type_name'])."' style='vertical-align:middle;cursor:pointer;' /></span>\n";
			}
		}
		if($wrapper) echo "</div>\n";
	}
}
function postRatings($post){
	global $locale, $fb4;
	$result = dbquery("select r.*, t.*, count(t.type_name) as total from ".DB_PREFIX."fb_rate r
	left join ".DB_PREFIX."fb_rate_type t on r.rate_type=t.type_id
	where r.rate_post='$post' group by r.rate_type");
	if(dbrows($result)){
		echo "<div style='float:left;vertical-align:middle;'>\n";
		while($data = dbarray($result)){
			$user_res = dbquery("select * from ".DB_PREFIX."fb_rate r
			left join ".DB_USERS." u on u.user_id=r.rate_by
			where r.rate_post='$post' and r.rate_type='".$data['rate_type']."'");
			$i = 0; $users = "<b>".$locale['fb862']."</b><br />";
			while($user_data = dbarray($user_res)){
				$users .= ($i !== 0 ? "<br />" : "").$user_data['user_name'];
				$i++;
			}
			echo "&nbsp;<span class='ratingbox small' onmouseover='this.style.opacity=\"1\"' onmouseout='this.style.opacity=\"".$fb4['rating_opacity']."\"' ".($fb4['boxover_ratings'] ? "title='header=[".$data['type_name']."] body=[$users]'" : "")." style='vertical-align:middle;'><img src='".INFUSIONS."fusionboard4/images/forum_icons/".$data['type_icon']."' style='vertical-align:middle;' /> x ".$data['total']."</span>";
		}
		echo "</div>\n";
	}
}
?>