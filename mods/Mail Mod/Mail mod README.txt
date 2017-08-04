/**************************\
|  NEW Mail Mod v1.0       |
|  Developed by Blue Eyes  |
\**************************/

Updated for Dragon Knight 1.1.12 by Kesstryl.  Changed user id to charname for security purposes.
See the mail mod updates file to add an alert to the page if a user has unread messages.

Instalation instructions :
1. copy install_mail.php and mail.php to game directory and run install_mail.php 
2. open templates/leftnav.php and add somewhere that line:
<a href="mail.php">Mail</a> <br />

2. after you have done it, open lib.php in your game directory:
   find :  ?>
  
  add before : 

///////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Checking mail //////////////////////////////////////////
function checkmail(){

global $userrow;

$link = opendb();
$result = doquery($link, "SELECT * FROM {{table}} WHERE UserTo='$userrow[charname]' AND STATUS='unread'", "mail");
$num_rows = mysqli_num_rows($result);
if ( $num_rows > "0") {
	$userrow["check_mail"] = "<a href=\"mail.php\">Mail [".$num_rows."]</a><br />";
	}else{
	$userrow["check_mail"] = "<a href=\"mail.php\">Mail</a><br />";
	}
}
////////////////////////////// End Checking mail //////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

After that, open index.php

go to this part near top of page

// Login (or verify) if not logged in.
$userrow = checkcookies();

and after that insert this line 

$checkmail = checkmail();


Works perfect. For those users who have installed dk_mail you will need to change a few things for the mail part to work. FromUser=sender,ToUser=owner,Subject=title,SendDate=date.

Or something like that. Just look at your DB table fields and the code if you get an error and make them match and it will work.
   