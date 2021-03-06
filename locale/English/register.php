<?php
$locale['400'] = "Register";
$locale['1400'] = "Register Forum Account";
$locale['4000'] = "Register Game Account";
$locale['401'] = "Activate Account";
// Registration Errors
$locale['402'] = "You must specify a user name, password & email address.";
$locale['1402'] = "You must specify a user name & password for your game account.";
$locale['403'] = "User name contains invalid characters.";
$locale['1403'] = "Game Account user name contains invalid characters.";
$locale['404'] = "Your two Passwords do not match.";
$locale['1404'] = "Your two Game Account Passwords do not match.";
$locale['405'] = "Invalid password, use alpha numeric characters only.<br />
Password must be a minimum of 6 characters long.";
$locale['1405'] = "Invalid Game Account password, use alpha numeric characters only.<br />
Password must be a minimum of 6 characters long.";
$locale['406'] = "Your email address does not appear to be valid.";
$locale['407'] = "Sorry, the user name ".(isset($_POST['username']) ? $_POST['username'] : "")." is in use.";
$locale['1407'] = "Sorry, the Game Account user name ".(isset($_POST['game_username']) ? $_POST['game_username'] : "")." is in use.";
$locale['408'] = "Sorry, the email address ".(isset($_POST['email']) ? $_POST['email'] : "")." is in use.";
$locale['409'] = "An inactive account has been registered with the email address.";
$locale['410'] = "Incorrect validation code.";
$locale['411'] = "Your email address or email domain is blacklisted.";
// Email Message
$locale['449'] = "Welcome to ".$settings['sitename'];
$locale['450'] = "Hello ".(isset($_POST['username']) ? $_POST['username'] : "").",\n
Welcome to ".$settings['sitename'].". Here are your login details:\n
Username: ".(isset($_POST['username']) ? $_POST['username'] : "")."
Password: ".(isset($_POST['password1']) ? $_POST['password1'] : "")."\n
Please activate your account via the following link:\n";
// Registration Success/Fail
$locale['451'] = "Registration complete";
$locale['452'] = "You can now log in.";
$locale['1452'] = "<font color=red size=3>You will be able to login within 3-5 mins. Set your realmlist to: ".Configuration::Get('realmlist')."</font>";
$locale['453'] = "An administrator will activate your account shortly.";
$locale['454'] = "Your registration is almost complete, you will receive an email containing your login details along with a link to verify your account.";
$locale['455'] = "Your account has been verified.";
$locale['1455'] = "Your game account has been created.";
$locale['456'] = "Registration Failed";
$locale['457'] = "Send mail failed, please contact the <a href='mailto:".$settings['siteemail']."'>Site Administrator</a>.";
$locale['458'] = "Registration failed for the following reason(s):";
$locale['459'] = "Please Try Again";
// Register Form
$locale['500'] = "";
$locale['501'] = "A verification email will be sent to your specified email address. ";
$locale['502'] = "";
$locale['503'] = " You can enter additional information by going to Edit Profile once you are logged in.";
$locale['504'] = "Validation Code:";
$locale['505'] = "Enter Validation Code:";
$locale['506'] = "Register";
$locale['1506'] = "Create Account";
$locale['507'] = "The registration system is currently disabled.";
$locale['508'] = "Terms of Agreement";
$locale['509'] = "I have read the <a href='".BASEDIR."print.php?type=T' target='_blank'>Terms of Agreement</a> and I agree with them.";
//Enhanced Registration Advantages
$locale['er_100'] = "Welcome";
$locale['er_101'] = "<img src='/images/register/signup.png' border='0' /><p>Become a Registered Member and take full advantage of this site.<p>";
$locale['er_102'] = "As a registered member, you have several advantages. <br />Below you can see the differences in the members and non members.<hr />";
$locale['er_103'] = "Opportunities:";
$locale['er_104'] = "Guest";
$locale['er_105'] = "Member";
$locale['er_106'] = "Access to the full site.";
$locale['er_107'] = "Access to support.";
$locale['er_108'] = "Personal Profile, and more.";
$locale['er_109'] = "Downloading Mods & Themes from our  Download Database.";
$locale['er_110'] = "Participate in our Forum.";
$locale['er_111'] = "Writing and read  our guestbook.";
$locale['er_112'] = "Meeting others with an interest in PHP-Fusion.";
$locale['er_113'] = "Help develop mods,infusions & themes.";
$locale['er_114'] = "The ability to test new mods and infusions.";
$locale['er_115'] = "Please enter your details below. Fields marked <span style='color:#ff0000;'>*</span> must be completed. Your user name and password is case-sensitive. <p>You Must Agree to the <a href='/print.php?type=T' target='_blank'>Terms Of Aggrement</a>, answer the security question correctly, and enter the correct Validation code before you can submit your registration.<p>If you have problems signing up! <br />Please contact Site management for help. ";
$locale['er_116'] = "HERE";
$locale['er_117'] = "Contact Management";
$locale['er_118'] = "Register";
$locale['er_119'] = "";
$locale['er_120'] = "";
$locale['er_121'] = "";
// Validation Errors
$locale['550'] = "Please specify a user name.";
$locale['551'] = "Please specify a password.";
$locale['552'] = "Please specify an email address.";
//Extras
$locale['5001'] = "The Game Account you entered is already binded to an account!";
$locale['5002'] = "The Game Account you entered does not exist!";
$locale['5003'] = "The Game Account password does not correspond to the username entered!";

?>