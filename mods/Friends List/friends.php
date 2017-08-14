<?php
include_once("lib.php");

function friends($friendchar) { //this function is currently unused and untested as the link to this from the onlinechar page breaks the layout.
	
global $userrow;

$link = opendb();

if(isset($_POST['add'])) {
	
	$time = time();
	$query = doquery("INSERT INTO {{table}} SET id='', userchar='".$userrow['charname']."', friendchar='".$_POST['friendname']."', date='$time'", "friends");
	$page.="<center>This player has been added to your Friends List. 	
	<br /><br /><a href=\"index.php\">CONTINUE</a></center>";
	}
	
elseif(isset($_POST['no'])) {
$page.= <<<END
<meta http-equiv="refresh" content="2;URL=index.php">
END;
	$page.="<center>This player has not been added to your Friends List.</center>";
}

else {
$query = doquery($link, "SELECT * FROM {{table}} WHERE charname='$friendname' LIMIT 1", "users");
while ($row = mysqli_fetch_array($query)) {
	$friendname = $row["username"];
	$page .= "<form action=index.php?do=friends:$friendname method=post><br />";
	$page .= "
	<center> ADD »</b> into friends list?<br /><br /> ";
	$page .= "
	<select name='friendchar'>
	<option value='$friendname'>$friendname</option>
	</select>";

	$page .= "<input type=submit value=add name=add>
	&nbsp;&nbsp;<input type=submit value=No name=no></form><br />"; 
	} 
}
display($page, "Add Friend"); 
}

function remove_friends($friendchar) {
	
global $userrow;

$link = opendb();

if(isset($_POST['yes'])) {
	$time = time();
	$query = doquery($link, "DELETE FROM {{table}} WHERE friendchar='$friendchar' AND userchar='".$userrow['charname']."'", "friends");
	$page = "<center>This player has been removed from your Friends List.<br /><br /><a href=\"index.php\">Continue</a></center>";
}

if(isset($_POST['no'])) {

	header("location:index.php?do=friendslist");
}
	
$page = "Remove Friends";
$query = doquery($link, "SELECT * FROM {{table}} WHERE charname='$friendchar' LIMIT 1", "users");
while ($row = mysqli_fetch_array($query)) {
	$friendchar = $row["charname"];
	$page .= "<form action=index.php?do=remove_friends:$friendchar method=post><br />";
	$page .= "
	<center> DELETE » <b>$friendchar</b> from your friends list?<br /><br /> ";

	$page .= "<input type=submit value=YES name=yes>
	<input type=submit value=No name=no></form><br />"; 
	}

display($page, "Remove Friend");

}

function friendslist() {

global $userrow ;

$link = opendb();

if(isset($_POST['add'])) {
	
	$friendname = protect($_POST['friendname']);
	$time = time();
	$check = doquery($link, "SELECT * FROM {{table}} WHERE userchar='".$userrow['charname']."'","friends");
	$friendrow = mysqli_fetch_array($check);
	$exist = doquery($link, "SELECT charname FROM {{table}} WHERE charname='$friendname' LIMIT 1","users");
	$exists = mysqli_fetch_assoc($exist);
	if($friendrow['friendchar'] == $friendname){
		die ("You already have a friend with that name. Return to <a href=index.php?do=friendslist>Friends List</a>");
	}elseif($friendname == "") {
		die ("You need to add a name to add a friend. Return to <a href=index.php?do=friendslist>Friends List</a>");
	}elseif($friendname != $exists['charname']){
		die ("No user with that name exists. Return to <a href=index.php?do=friendslist>Friends List</a>");
	}else{
	$query = doquery($link, "INSERT INTO {{table}} SET id='', userchar='".$userrow['charname']."', friendchar='$friendname', date='$time'", "friends");
	$page ="<center>This player has been added to your Friends List. 	
	<br /><br /><a href=\"index.php\">CONTINUE</a></center>";
		}
	
}

$query = doquery($link, "SELECT date,friendchar FROM {{table}} WHERE userchar='".$userrow['charname']."'", "friends");
$page = "<center><br /><br />
Friends List  ||  Return to <a href='index.php'>the Game</a>
<br /><br /></center>
<div>
<table><tr>
<th>[ NAME</th>
<th>| MAIL</th>
<th>| DELETE</th>";
$count = 1;
while ($row = mysqli_fetch_array($query)) {
	$friendchar = $row['friendchar'];
	$friendsearch = doquery($link, "SELECT id FROM {{table}} WHERE charname='".$row["friendchar"]."' LIMIT 1","users");
	$friend = mysqli_fetch_assoc($friendsearch);
	$id = $friend['id'];
	
if ($count == 1) { 
$page .= "
<tr><td><a href='index.php?do=onlinechar:$id' title='PROFILE'>".$row["friendchar"]."</a></td>
<td>
<a href='index.php?do=mailfriend:$friendchar' title='Mail'>MAIL</a></td>
<td><a href='index.php?do=remove_friends:".$row["friendchar"]."' title='DELETE'>DELETE</a></td>
</tr>"; $count = 2; 
}
else { 
$page .= " <tr><td><a href='index.php?do=onlinechar:$id' title='PROFILE'>".$row["friendchar"]."</a></td>
<td>
<a href='index.php?do=mailfriend:$friendchar' title='Mail'>MAIL</a></td>
<td><a href='index.php?do=remove_friends:".$row["friendchar"]."' title='DELETE'>DELETE</a></td>
</tr>"; 
$count = 1; 
	}
}
if (mysqli_num_rows($query) == 0) { $page .= "	
You do not have any friends in your Friends List."; }
$page .= "</table>
</div>";
$userquery = doquery($link, "SELECT charname FROM {{table}}","users");
$page .="Add a friend:</br>
		<form action=index.php?do=friendslist method=post><br />
		<input type=text name=friendname value=''></br>
		<input type=submit value=add name=add></form>";
		
display($page, "Friends List");

}

function mailfriend($friendchar) {
	
    global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
	$page = "Mail Friend";
	
if (isset($_POST["submit"])) {	
			
		$Message = protect($_POST['Message']);
		$Subject = protect($_POST['Subject']);
		$receiver = protect($_POST['name']);
		$token = protect($_POST['token']);
		
		if ($_SESSION['token'] != $token) { die("Invalid request");}
		/**********************************\
		| Making sure that message cannot  |
		| be sent if there is no message.  |
		\**********************************/
		if ($Message == ""){
				display("Why would you like to send empty mail?<br />
		     		    Please go <a href=\"javascript: history.go(-1)\">back</a> and try again.", "Mail :: Error");
		}
		/**********************************\
		| Making sure that even message    |
		| without subject get's some name  |
		\**********************************/			
		if ($Subject == ""){
			$Subject="None";
		}			
		/***************************\
		| Let's enable sending mail |
		| over user's charname            |
		\***************************/				
		if($receiver == "") { //if there is no charname entered we won't allow sending mail
			display("You have to fill out the <b>charname</b> field.<br>.
				Please go <a href=\"javascript: history.go(-1)\">back</a> and try again.", "Mail :: Error");
		}
		$oquery = doquery($link, "SELECT charname FROM {{table}} WHERE charname='$friendchar' LIMIT 1", "users") or die("error connecting to the DB");
    	$row= mysqli_fetch_assoc($oquery);
		$receiving = $row['charname']; 
			if($receiver != $receiving) {
				display("you filled out the character name incorrectly<br>
					Please go <a href=\"javascript: history.go(-1)\">back</a> and try again.", "Mail :: Error");
				}
				
		$oquery2 = doquery($link, "SELECT charname FROM {{table}} WHERE charname='".$userrow['charname']."' LIMIT 1", "users")or die("error connecting to the DB");
		while($row2 = mysqli_fetch_assoc($oquery2)){
			$sender = $row2['charname'];  // now we have picked the charname of the guy sending mail
				if ($receiver == $sender) {   //if name of reciver = name of sender we won't allow sending mail to ourslf
				echo "What is the use of sending mail to yourself?";
				}  //if all is well and if nothing returns error then we can start sending
			$mail = doquery($link, "INSERT INTO {{table}} SET id='',UserTo='$receiver',UserFrom='$sender',Subject='$Subject',Message='$Message',SentDate=NOW(), STATUS='unread'", "mail") or die(mysqli_error($link));
				 unset($_SESSION['token']);
				display("Mail sent!  You may now <a href=\"index.php\">play the game</a>.","Mail :: Sent");			
		}
}//end of if (isset($_POST["submit"]))
	
   $token = formtoken();
  
   $page .= "<form action=\"index.php?do=mailfriend:$friendchar\" method=\"post\">
			<table width=\"100%\">
			<tr>
				<td>
					<b>Friend:</b><br /><input type=\"text\" name=\"name\" value=\"$friendchar\" />$friendchar<br />
					<b>Subject:</b><br /><input type=\"text\" name=\"Subject\" size=\"20\" maxlength=\"20\" /><br /><br />
					<b>Message:</b><br /><textarea name=\"Message\" rows=\"8\" cols=\"44\"></textarea><br /><br /><br />
					<input type=\"hidden\" name=\"token\" value=\"$token\" />
					<input type=\"submit\" name=\"submit\" value=\"Submit\" /> 
					<input type=\"reset\" name=\"reset\" value=\"Reset\" />
				</td>
			</tr>
			</table></form>";
			
    display($page, "Write Friend");

}

?>