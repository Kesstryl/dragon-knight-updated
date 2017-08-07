<?php

include_once("lib.php");
/**
 * @author Ando Roots
 * @copyright 2007
 * Klanni mode
 * updated by Kesstryl 
 */

function clan(){ //Clan main page

 	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];
	$message = $clanrow['message'];

if($userrow["clan"] == "0" || $userrow["clanjoin"] == 1) { header("Location: index.php?do=clanjoin"); die();	}
if($clanrow["logo"] != "0"){$logo="<img src=\"".$clanrow["logo"]."\"><br />";}else{$logo="";}
$page="$logo <font size=\"4\"> Headquarters of <b>$clanname</b></font><br />$message<br />";

$liikmed = doquery($link, "SELECT id FROM {{table}} WHERE clan='$clanid' ", "users");
    $liikmed = mysqli_num_rows($liikmed); $liitujad = doquery($link, "SELECT id FROM {{table}} WHERE clanjoin='$clanid' ", "users");
    $liitujad2 = mysqli_num_rows($liitujad); if($liitujad2 > "0"){$liitujad="<span class=\"higlight\">$liitujad2</span>";}
	else{$liitujad="0";}
    
$page .="<br /><table class=\"clan\" width=\"50%\"><tr><td class=\"nimekiri\" colspan=\"2\" align=\"center\">Clan statistics:</td></tr><tr><td>Leader:</td><td>".$clanrow["leader"]."</td></tr><tr><td>Members</td><td>$liikmed</td></tr><tr><td>Applications:</td><td>$liitujad</td></tr></table><br /><br /><table><tr class=\"nimekiri\"><td align=\"center\">Plot a plan here:</td></tr><tr><td><iframe src=\"index.php?do=clanbabble\" name=\"ssbox\" width=\"100%\" height=\"350\" frameborder=\"0\" id=\"bbbox\"></iframe></td></tr></table>";

    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clans");
}


function leader(){ //Clan leaders page
 
 	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname=$clanrow["name"]; $clanid=$clanrow["id"];

if($userrow["clan"] == "0") { header("Location: index.php?do=clanjoin"); die();	}
if($userrow["charname"] != $clanrow["leader"]) { header("Location: index.php?do=clan&teade=2"); die();	}


$page ="<br /><table><tr class=\"nimekiri\"><td>Welcome, leader of $clanname  !<br />There are two basic choices of this simple clan mod.<br /></td></tr><tr><td align=\"center\" class=\"nimekiri\"><a href=\"index.php?do=clannewbies\">Accept new members</a></td></tr><tr><td align=\"center\" class=\"nimekiri\"><a href=\"index.php?do=clankick\">Kick members</a></td></tr></table>";

    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan's");
}

function newmembers(){ //Accept new members
 
 	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];

if($userrow["clan"] == "0") { header("Location: index.php?do=clanjoin"); die();	}
if($userrow["charname"] != $clanrow["leader"]) { header("Location: index.php?do=clan"); die();	}

$token = formtoken();
$page="<br />Here is a list of all players, who wish to join this clan.<br /><br /><table width=\"50%\" class=\"title\"><tr><td>Name</td><td>Level</td><td>Gold</td><td>Accept?</td></tr>";
 $clanlistquery = doquery($link, "SELECT id,charname,level,gold FROM {{table}} WHERE clanjoin='$clanid' ", "users");
   while ($list = mysqli_fetch_array($clanlistquery)) {
	$page .="<tr class=\"nimekiri\"><td>".$list["charname"]."</td><td>".$list["level"]."</td><td>".$list["gold"]."</td><td><form action=\"index.php?do=clannewbies\" method=\"post\"><input type=\"submit\" name=\"jah\" value=\"Yes\"> / <input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"ei\" value=\"No\"><input type=\"hidden\" name=\"mangija\" value=\"".$list["id"]."\"></td></tr>";
}
if(isset($_POST["jah"])){
	$check = protectcsfr();
	$_POST = array_map('protectarray', $_POST);
    extract ($_POST, EXTR_SKIP);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	doquery($link, "UPDATE {{table}} SET clanjoin='0',clan='$clanid' WHERE id='$mangija' LIMIT 1", "users");
	unset($_SESSION['token']);
	$page ="<table><tr><td class=\"nimekiri\">You accepted a new member to your clan.</td></tr><tr><td>Return to <a href=\"index.php?do=clannewbies\">Clan Newbies Page</a></tr></td></table><br />";
		
}
if(isset($_POST["ei"])){
	$check = protectcsfr();
	$_POST = array_map('protectarray', $_POST);
    extract ($_POST, EXTR_SKIP);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	doquery($link, "UPDATE {{table}} SET clanjoin='0' WHERE id='$mangija' LIMIT 1", "users");
	 unset($_SESSION['token']);
	$page ="<table><tr><td class=\"nimekiri\">You threw the application into the recycle bin.</td></tr><tr><td>Return to <a href=\"index.php?do=clannewbies\">Clan Newbies Page</a></tr></td></table><br /></table><br />";
		
}

$page .="</table><br />";

    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan's");
}


function kick(){ //Kick members
 
 	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];
	$leader = $clanrow['leader'];

if($userrow["clan"] == "0") { header("Location: index.php?do=clanjoin"); die();	}
if($userrow["charname"] != $clanrow["leader"]) { header("Location: index.php?do=clan"); die();	}

$token = formtoken();
$page="<br />Choose your victims, who will be thrown out of the clan.<br /><br /><table width=\"50%\" class=\"title\"><tr><td>Name</td><td>Level</td><td>Gold</td><td>Kick member?</td></tr>";
 $clanlistquery = doquery($link, "SELECT id,charname,level,gold FROM {{table}} WHERE clan='$clanid' ", "users");
   while ($list = mysqli_fetch_array($clanlistquery)) {
	$page .="<tr class=\"nimekiri\"><td>".$list["charname"]."</td><td>".$list["level"]."</td><td>".$list["gold"]."</td><td><form action=\"index.php?do=clankick\" method=\"post\"><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"jah\" value=\"Kick\"> <input type=\"hidden\" name=\"mangija\" value=\"".$list["id"]."\"></td></tr>";
}
if(isset($_POST["jah"])){
	$check = protectcsfr();
	$_POST = array_map('protectarray', $_POST);
    extract ($_POST, EXTR_SKIP);
	$mangija = protect($_POST['mangija']);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	$check = doquery($link, "SELECT charname FROM {{table}} WHERE id= $mangija LIMIT 1", "users");
	$checked = mysqli_fetch_assoc($check);
	$charname = $checked['charname'];
	if($charname == $leader) {
		die ("You can't kick yourself out of a clan that you are the leader of.  <a href=\"index.php?do=clan\">Back to Clan's List.</a><br /><a href=\"index.php\">Back to Town</a>");
	}else{
	doquery($link, "UPDATE {{table}} SET clan='0' WHERE id='$mangija' LIMIT 1", "users");
	 $page ="<table><tr><td class=\"nimekiri\">You threw a member out of the clan.</td></tr></table><br />Return to <a href=\"index.php?do=clankick\">Clan Kick Page</a>";
	 unset($_SESSION['token']);
	}
}


$page .="</table><br />";

    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan's");
}


function clanjoin(){ //Join a clan
 
 	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];

if($userrow["clan"] != "0" && $userrow["clanjoin"] == "0") { header("Location: index.php?do=clan"); die();	}

if(isset($_POST['Join'])){
	$_POST['Join'] = protect($_POST['Join']);
	$clanid = protect($_POST['clanid']);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	
	doquery($link, "UPDATE {{table}} SET clan='$clanid', clanjoin='1' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	 unset($_SESSION['token']);
	header('Location: index.php?do=clanjoin'); die();
}
if(isset($_POST['Cancel'])){
	$_POST['Cancel'] = protect($_POST['Cancel']);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	doquery($link, "UPDATE {{table}} SET clan='0',clanjoin='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page = "You left the clan!<br /><br /><a href=\"index.php?do=clan\">Back to Clans List.</a><br /><a href=\"index.php\">Back to Town</a><br />";
	unset($_SESSION['token']);
}

$page = "You are not yet a member of any of the clan's. Here is a list of all the clan's in the game. Please choose one to join.<br /><br /><table><tr class=\"title\"><td>Clan's name</td><td>Members</td><td>Message to joiners</td><td>Join</td></tr>";

	$clans = doquery($link, "SELECT * FROM {{table}}", "clan");
	while($list = mysqli_fetch_array($clans)) {
		$id = $list["id"];
		$name = $list['name'];
		$message = $list['message'];
		$membersquery= doquery($link, "SELECT id FROM {{table}} WHERE clan='$id' LIMIT 1", "users");
	
		$members= mysqli_num_rows($membersquery);
		if($userrow["clan"] == $list["id"]){
		$page = "Application pending";
		$page .="<br /><br /><form action=\"index.php?do=clanmembers\" method=\"post\"><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"Cancel\" value=\"Cancel Application\"></form><br />";
		}else{
	$token = formtoken();
	
	$page .="<tr class=\"nimekiri\"><td>$name</td><td>$members</td><td>$message</td>";
	$page .= "<td><form action=\"index.php?do=clanjoin\" method=\"post\"><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"Join\" value=\"Join\"> <input type=\"hidden\" name=\"clanid\" value=\"".$list["id"]."\></form></td></tr></br></br>";
		}
	
	}
		
$page .="<tr class=\"nimekiri\"><td colspan=\"4\" align=\"center\"><a href=\"index.php?do=clancreate\">Found a NEW clan!</a></td></tr></table><br /><br />";
$page .="<a href=\"index.php\">Back to Town</a><br />";


    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan's");
}


function members(){ //Clan main page
 
 	global $userrow;

	$check = protectcsfr();
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];
    
if($userrow["clan"] == "0") { header("Location: index.php?do=clanjoin"); die();	}

if(isset($_POST['Leave'])){
	$_POST['Leave'] = protect($_POST['Leave']);
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	if($clanrow['leader'] == $userrow['charname']){
		die ("You can't leave a clan you are the leader of. <a href=\"index.php?do=clan\">Back to Clan's List.</a><br /><a href=\"index.php\">Back to Town</a>"); 
		unset($_SESSION['token']);
		}else{
	 doquery($link, "UPDATE {{table}} SET clan='0',clanjoin='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	$page = "You left the clan!<br /><br /><a href=\"index.php?do=clan\">Back to Clan's List.</a><br /><a href=\"index.php\">Back to Town</a><br />";
	 unset($_SESSION['token']);
	 }
}

$page= "<br />Here is the list of all members of this clan.<br /><br /><table width=\"50%\" class=\"title\"><tr><td>Name</td><td>Level</td><td>Gold</td></tr>";
$clanlistquery = doquery($link, "SELECT id,charname,level,gold FROM {{table}} WHERE clan='$clanid' ", "users");
   while ($list = mysqli_fetch_array($clanlistquery)) {
	$page .="<tr class=\"nimekiri\"><td><a href=\"index.php?do=onlinechar:".$list["id"]."\">".$list["charname"]."</a></td><td>".$list["level"]."</td><td>".$list["gold"]."</td></tr>";
	}
	$token = formtoken();
	$page .="<br /><br /><tr><td><form action=\"index.php?do=clanmembers\" method=\"post\"><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"Leave\" value=\"Leave Clan\"></form></td></tr><br />";
	$page .="</table><br />";
	
    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan");
  
}

function create(){ //Make a new clan
 
 	global $userrow;
	
	$link = opendb();
    $clanquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["clan"]."' LIMIT 1", "clan");
    $clanrow = mysqli_fetch_array($clanquery);
    $clanname = $clanrow["name"]; 
	$clanid = $clanrow["id"];
	$_GET = array_map('protectarray', $_GET);
if($userrow["clan"] != "0") { header("Location: index.php?do=clan"); die();	}

$token = formtoken();
$page="You can make a new clan here. You must be at least level 10 and the building of the clan's headquarters costs 1000 gold.<br /><br /><form action=\"index.php?do=clancreate\" method=\"post\"><table class=\"nimekiri\" width=\"50%\"><tr><td colspan=\"2\" align=\"center\">Make a new clan</td></tr><tr><td>Name: </td><td><input type=\"text\" name=\"nimi\" size=\"20\" maxlength=\"50\"></td></tr><tr><td>Logo: </td><td><input type=\"text\" name=\"logo\" size=\"30\" maxlength=\"100\"></td></tr><tr><td>Message to joiners:</td><td><input type=\"text\" name=\"teade\" size=\"30\" maxlength=\"50\"></td></tr><tr><td><input type=\"hidden\" name=\"token\" value=\"$token\" /></td></tr><tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"asuta\" value=\"Build your clan\"></td></tr></table></form><br /><br />";
if(isset($_POST["asuta"])){
	$check = protectcsfr();
	$_POST = array_map('protectarray', $_POST);
    extract ($_POST, EXTR_SKIP); 
	$token = protect($_POST['token']);
	
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	$viga="0";
	$namequery = doquery($link, "SELECT name FROM {{table}} ", "clan");
    while ($namerow = mysqli_fetch_array($namequery)){
	if($nimi == $namerow["name"]){$viga++; $kirjeldus .="A clan with that name allready exists!<br />";}	}
	if($nimi == ""){$viga++; $kirjeldus .="Please choose a name to your clan!<br />";}
	if($teade == ""){$viga++; $kirjeldus .="Please enter a message to new joiners<br />";}
	if($logo == ""){$logo="0";}
//	if($userrow["gold"] < "1000"){$viga++; $kirjeldus .="You must have at least 1000 gold!<br />";}
//	if($level < "10"){$viga++; $kirjeldus .="You must be at least level 10!<br />";}
	

if($viga == "0"){
	 doquery($link, "INSERT INTO {{table}} SET id='',name='$nimi',logo='$logo',message='$teade',leader='".$userrow["charname"]."'", "clan");
	 $idq = doquery($link, "SELECT id FROM {{table}} WHERE name='$nimi' LIMIT 1", "clan");
    $idq2 = mysqli_fetch_array($idq); $identify=$idq2["id"];
	 $uusraha=$userrow["gold"];
	 doquery($link, "UPDATE {{table}} SET clan='$identify',gold='$uusraha' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
	 unset($_SESSION['token']);
	header("Location: index.php?do=clan&teade=1"); die();
}else{
	unset($_SESSION['token']);
	$page="Errors:<br />$kirjeldus<br />";}
}

$page .="<a href=\"index.php\">Back to Town</a><br />";


    $page2["clancontent"] = $page;
	$display = gettemplate("clan");
    $display = parsetemplate($display, $page2);
	display($display,"Clan's");
}



function clanbabble() {
    
    global $userrow;
    
	$link = opendb();
    if (isset($_POST["babble"])) {
		$check = protectcsfr();
        $safecontent = makesafe($_POST["babble"]);
		$safecontent = protect($_POST["babble"]);
		$token = protect($_POST['token']);
	
		if ($_SESSION['token'] != $token) { die("Invalid request");}
        if ($safecontent == "" || $safecontent == " ") { //blank post. do nothing.
        } else { 
		$insert = doquery($link, "INSERT INTO {{table}} SET id='',posttime=NOW(),clan='".$userrow["clan"]."',author='".$userrow["charname"]."',babble='$safecontent'", "clanbabble"); }
        unset($_SESSION['token']);
		header("Location: index.php?do=clanbabble");
        die();
    }
    $babblebox = array("content"=>"");
    $bg = 1;
    $babblequery = doquery($link, "SELECT * FROM {{table}} WHERE clan='".$userrow["clan"]."' ORDER BY id DESC LIMIT 20 ", "clanbabble");
    while ($babblerow = mysqli_fetch_array($babblequery)) {
        if ($bg == 1) { $new = "<div style=\"width:98%; background-color:#eeeeee;\">[<b>".$babblerow["author"]."</b>] ".$babblerow["babble"]."</div>\n"; $bg = 2; }
        else { $new = "<div style=\"width:98%; background-color:#ffffff;\">[<b>".$babblerow["author"]."</b>] ".($babblerow["babble"])."</div>\n"; $bg = 1; } 
        $babblebox["content"] = $new . $babblebox["content"];
    }
	$token = formtoken();
    $babblebox["content"] .= "<center><form action=\"index.php?do=clanbabble\" method=\"post\"><input type=\"text\" name=\"babble\" size=\"40\" maxlength=\"220\" /><br /><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"submit\" value=\"Talk\" /></form></center>";
    
    // Make page tags for XHTML validation.
    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n"
    . "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
	$xml = libxml_disable_entity_loader( true );
    $page = $xml . gettemplate("babblebox");
    echo parsetemplate($page, $babblebox);
    die();

}

?>