<?PHP
/*************************\
| Send mail modification  |
|      by Blue Eyes       |
|    v1.0 (25.12.2005) 	  |
|   updated by Kesstryl   |
|       (08/01/2017)      |
\*************************/
if (file_exists('install_mail.php')) { die("Please delete <b>install_mail.php</b> from your game directory before continuing."); }
include_once('lib.php');
include('cookies.php');
$link = opendb();

$userrow = checkcookies();
if ($userrow == false) { 
    header("Location: login.php?do=login"); die(); 
}

/*****************************************\
| Enough of checkups , let's start coding |
\*****************************************/
if (isset($_GET["do"])) {
	
	$check = protectcsfr();
	$_GET = array_map('protectarray', $_GET);
    $do = explode(":",$_GET["do"]);
	
	if ($do[0] == "inbox") { inbox(); }
    elseif ($do[0] == "reply") { reply($do[1]); }
    elseif ($do[0] == "read") { read_mail($do[1]); }
    elseif ($do[0] == "New") { write_mail(); }
    elseif ($do[0] == "Mass") { Mass_mail(); }
    elseif ($do[0] == "Delete") { Delete_mail($do[1]); }
} else { inbox(); }
/******************************************\
| Time to explain which each function does |
\******************************************/
function inbox() {
	
    global $userrow, $controlrow;
	
	$check = protectcsfr();
	$link = opendb();
	
    $query = doquery($link, "SELECT * FROM {{table}} WHERE UserTo='".$userrow['charname']."' ORDER BY id DESC LIMIT 50", "mail");

    $page = "<form method=\"POST\" action=\"mail.php?do=Delete\">
	        <table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\">
				<table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
					<tr>
						<th colspan=\"4\" style=\"background-color:#dddddd;\">
							<center>Your Inbox!...... <a href=\"index.php\">Return to Game</a>.</center>
						</th>
					</tr>
					<tr>
						<th width=\"40%\" style=\"background-color:#dddddd;\">
							Subject:
						</th>
						<th width=\"20%\"style=\"background-color:#dddddd;\">
							From:
						</th>
						<th width=\"30%\" style=\"background-color:#dddddd;\">
							Date:
						</th>
						<th style=\"background-color:#dddddd;\">
							Delete
						</th>
					</tr>\n";
    	
    if (mysqli_num_rows($query) == 0) 
		{ 
        	$page .= "<tr>
					  	<td style=\"background-color:#ffffff;\" colspan=\"4\">
					  		<b>You have no Messages</b>
					  	</td>
					  </tr>\n";
    	} 
	
	else 
		{ 
        	while ($row = mysqli_fetch_array($query)) 
				{
            		$query2 = doquery($link, "SELECT * FROM {{table}} WHERE charname='$row[UserFrom]'", "users");
            		$author = mysqli_fetch_array($query2);
					
					/*********************************************\
					| Daddy wants to highlight the unread msgs :D |
					\*********************************************/
					
       			 	if($row["STATUS"] == "read")
						{
							$page .= "<tr>
									  	<td style=\"background-color:#ffffff;\">
											<a href=\"mail.php?do=read:".$row["id"]."\">".$row["Subject"]."</a>
										</td>
										<td style=\"background-color:#ffffff;\">
											<a href=\"index.php?do=onlinechar:".$author["charname"]."\">
											".$author["charname"]."</a>
										</td>
										<td style=\"background-color:#ffffff;\">
											".$row["SentDate"]."
										</td>
										<td style=\"background-color:#ffffff;\">
										<center><input type=\"checkbox\" name=\"".$row["id"]."\" value=\"yes\" /></center>
										</td>
									 </tr>\n";
						}
				  else
				  		{
                			$page .= "<tr>
									 	<td style=\"background-color:#eeeeee;\">
											<a href=\"mail.php?do=read:".$row["id"]."\">".$row["Subject"]."</a>
										</td>
										<td style=\"background-color:#eeeeee;\">
											<a href=\"index.php?do=onlinechar:".$author["charname"]."\">
											 ".$author["charname"]."</a>
										</td>
										<td style=\"background-color:#eeeeee;\">
											".$row["SentDate"]."
										</td>
										<td style=\"background-color:#eeeeee;\">
											<center><input type=\"checkbox\" name=\"".$row["id"]."\" value=\"yes\" /></center>
										</td>
									 </tr>\n";
            			} //end of else loop
				} //end of while loop
		} //end of main else loop
		
    	$page .= "</table></td></tr></table>";
		$page .= "<table>
				<tr>
					<td><input type=\"submit\" name=\"do\" value=\"New\" /></td>
					<td><input type=\"submit\" name=\"do\" value=\"Delete\" /></td>";
    if ($userrow["authlevel"] == 1)
   		$page .= "<td><input type=\"submit\" name=\"do\" value=\"Mass\" /></td>";
    	$page .= "</tr></table></form>";
    	display($page, "Mail :: Inbox");

}//end of function inbox()

function read_mail($id) {
    
	global $userrow, $controlrow;
	
	$check = protectcsfr();
	$link = opendb();
	
    $query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id'", "mail");
	$update_query=doquery($link, "UPDATE {{table}} SET STATUS='read' WHERE UserTo='$userrow[charname]' AND id='$id'", "mail");
    $row = mysqli_fetch_array($query);
    $replyuser = $row['UserFrom'];
	$Message = $row['Message'];
	if (!$row)
        display("No such Message!<br /><a href=\"javascript: history.go(-1)\">back</a>", "Mail :: Error");
    if ($row['UserTo'] != $userrow['charname'])
        die("Hack attempt. This has been sent to the administrator");
		
    $query2 = doquery($link, "SELECT * FROM {{table}} WHERE charname='$replyuser'", "users");
    $author = mysqli_fetch_array($query2);

/******************************\
| Don't touch this bitch, it's |
|   for my future reference   |
| when i make quote system and |
|     maybe even BBC code .    |
\******************************/
   
	$page = "<table width=\"100%\">
			 <tr>
			 	<td style=\"padding:1px; background-color:black;\">
					<table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
						<tr>
							<td colspan=\"2\" style=\"background-color:#dddddd;\">
								<b><a href=\"mail.php\">Inbox</a> :: ".$row["Subject"]."</b>
							</td>
						</tr>
					</table>
    				<table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
							<td width=\"50%\" style=\"background-color:#ffffff;\">
								<b>From: <a href=\"index.php?do=onlinechar:".$author["charname"]."\">
								".$author["charname"]."</a></b>
							</td>
			 		</table>
					<table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
						<tr>
							<td style=\"background-color:#FFFFFF; vertical-align:center;\">
								<b>Send date:</b> ".$row["SentDate"]."
							</td>
						</tr>	
					</table>
					<table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
						<tr>
							<td style=\"background-color:#FFFFFF; vertical-align:center;\">
								<b>Subject:</b> ".$row["Subject"]."
							</td>
						</tr>
					</table>
					<table width=\"100%\" height=\"200\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\">
						<tr>
							<td style=\"background-color:#FFFFFF;\">
								".nl2br($Message)."
							</td>
						</tr>
					</table>
			  	</td>
			  </tr>
			  </table><br />";
	
	$page .= "<table width=\"100%\">
			<tr>
				<td style=\"background-color:#FFFFFF; vertical-align:center;\">
					<b>Reply to message:</b>
				</td>
			</tr>
			<tr>
				<td style=\"background-color:#FFFFFF; vertical-align:center;\">
								<form action=\"mail.php?do=reply:".$row["id"]."\" method=\"post\">
								<b>Subject: </b>
								<input type=\"text\" name=\"Subject\" value=\"RE: $row[Subject]\" size=\"20\" maxlength=\"20\" />
				</td>
			</tr>
			</table>
			<table width=\"100%\">	          
			<tr>
				<td>
					<center><textarea name=\"Message\" rows=\"8\" cols=\"44\"></textarea><br /><br /></center>
					<center><input type=\"submit\" name=\"submit\" value=\"Submit\" /> 
					<input type=\"reset\" name=\"reset\" value=\"Reset\" /></center>
								</form>
				</td>
			 </tr>
			 </table>";
    
    display($page, "Mail :: Reading Mail");

}

function reply($reply) {
	
    global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
	
    $query = doquery($link, "SELECT * FROM {{table}} WHERE id=$reply", "mail");
    $mail = mysqli_fetch_assoc($query);
	$from = $mail['UserFrom'];
    $query2 = doquery($link, "SELECT charname FROM {{table}} WHERE charname='$from'", "users");
    $mailer = mysqli_fetch_assoc($query2);
    
	if(isset($_POST['submit'])){
		$Subject = protect($_POST["Subject"]);
		$Message = protect($_POST['Message']);
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

    $sendreply = doquery($link, "INSERT INTO {{table}} SET id='',UserTo='$from',UserFrom='".$userrow['charname']."',Subject='$Subject',Message='$Message',SentDate=NOW(), STATUS='unread'", "mail");
    display("Mail sent!  You may now <a href=\"index.php\">play the game</a>.","Reply :: Sent");	
	}

}

function Mass_mail() {
	
    global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
	
    if ($userrow["authlevel"] != 1)
        header("Location: mail.php");
    if (isset($_POST['submit'])) {
		
		$Message = protect($_POST['Message']); 
		$Subject = protect($_POST['Subject']);
		
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
		$i = 0;		
        $oquery = doquery($link, "SELECT * FROM {{table}}", "users");
        while ($row = mysqli_fetch_assoc($oquery)){
			$row_array[$i]['charname'] = $row['charname'];
			
				$sendall = doquery($link, "INSERT INTO {{table}} SET id='',UserTo='".$row['charname']."',UserFrom='".$userrow['charname']."',Subject='$Subject',Message='$Message',SentDate=NOW(), STATUS='unread'", "mail");
				$i++;
		}	
	}

    $page = "<form action=\"mail.php?do=Mass\" method=\"post\">
	         <table width=\"100%\">
			 <tr>
			 	<td>
					Mass email Message (<b>Sending to all users in game</b>).  When you are done you can <a href=\"index.php\">play the game</a>.<br /><br/ >
					Title:<br />
					<input type=\"text\" name=\"Subject\" size=\"20\" maxlength=\"20\" /><br /><br />
					Message:<br /><textarea name=\"Message\" rows=\"7\" cols=\"40\"></textarea><br /><br />
					<input type=\"submit\" name=\"submit\" value=\"Submit\" /> 
					<input type=\"reset\" name=\"reset\" value=\"Reset\" />
				</td>
			</tr>
			</table></form>";
    display($page, "Mail :: Mass Emailer");

}

function write_mail() {
	
    global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
	if (isset($_POST["submit"])) {	
			
		$Message = protect($_POST['Message']);
		$Subject = protect($_POST['Subject']);
		$receiver = protect($_POST['name']);
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
		$oquery = doquery($link, "SELECT charname FROM {{table}} WHERE charname='".$_POST['name']."' LIMIT 1", "users") or die("error connecting to the DB");
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
				display("Mail sent!  You may now <a href=\"index.php\">play the game</a>.","Mail :: Sent");			
		}
	}//end of if (isset($_POST["submit"]))

   
    $page = "<form action=\"mail.php?do=New\" method=\"post\">
			<table width=\"100%\">
			<tr>
				<td>
					<b>charname:</b><br /><input type=\"text\" name=\"name\" size=\"30\" maxlength=\"30\" /><br />
					<b>Subject:</b><br /><input type=\"text\" name=\"Subject\" size=\"20\" maxlength=\"20\" /><br /><br />
					<b>Message:</b><br /><textarea name=\"Message\" rows=\"8\" cols=\"44\"></textarea><br /><br /><br />
					<input type=\"submit\" name=\"submit\" value=\"Submit\" /> 
					<input type=\"reset\" name=\"reset\" value=\"Reset\" />
				</td>
			</tr>
			</table></form>";
			
    display($page, "Write Mail");

}

function Delete_mail($id) {
    global $userrow;
	
	$check = protectcsfr();
	$_POST = array_map('protectarray', $_POST);
    if ($_POST['do'] == 'New') {
        header("Location: mail.php?do=New");
        die();
    }
    if ($_POST['do'] == 'Mass') {
        header("Location: mail.php?do=Mass");
        die();
    }
    foreach($_POST as $a => $b) {
		$link = opendb();
        if ($a != "do")
            doquery($link, "DELETE FROM {{table}}  WHERE id={$a}", "mail");
    }
    header("Location: mail.php");
    die();
}
