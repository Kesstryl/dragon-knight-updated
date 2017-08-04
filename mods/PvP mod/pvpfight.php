<?php
//made by doublet
//for more information: admin@maffia.pri.ee
//updated by Kesstryl 8/1/2017
include_once('lib.php');
function pvpfight() {
	
global $userrow, $numqueries;

$link = opendb();
if (isset($_POST['call'])) {
	
//	$_POST = array_map('protectarray', $_POST);
	$_POST['call'] = protect($_POST['call']);
	$_POST['enemy'] = protect($_POST['enemy']);
	$_POST['bet'] = protect($_POST['bet']);
	
	$query = doquery($link, "SELECT*FROM {{table}} WHERE charname='".$_POST['enemy']."' LIMIT 1", "users");
	$row = mysqli_fetch_array($query);
	$maxbet = $userrow["gold"];
	$sum = $row["gold"]; //use if not using bank mod
//    $sum = $row["gold"] + $row["bank"] / 2;  //include if using bank mod
	
if ($_POST['bet'] > $maxbet) { display("Your bet is to high.<p><a href=\"index.php?do=pvpfight\">Go Back</a>", "Error"); die(); }
//elseif ($_POST['bet'] > $userrow["gold"]) { display("You dont have that much money.<p><a href=\"index.php?do=bank\">Go to bank</a>", "ERROR"); die(); }  //include if using bank mod
elseif ($_POST['bet'] < "0") { display("ERROR. Bet has to be bigger than 0.<br /><a href=\"index.php?do=pvpfight\">Go Back</a>", "ERROR"); die(); }
elseif ($_POST['bet'] == "0") { display("ERROR. Bet has to be bigger than 0.<br /><a href=\"index.php?do=pvpfight\">Go back</a>", "ERROR"); die(); }
elseif ($_POST['enemy'] == "") { display("Wrong charactername<br><a href=\"index.php?do=pvpfight\">Go back</a>", "ERROR"); die(); }
elseif ($_POST['enemy'] == $userrow['charname']) { display("You can't challenge your self <br><a href=\"index.php?do=pvpfight\">Go back</a>", "ERROR"); die(); }

elseif ($_POST['bet'] > $sum) { display("Your Rival doesn't have enough money to match your bet!<br /><a href=\"index.php?do=pvpfight\">Go back</a>", "ERROR");


} else {
$newgold2 = $userrow["gold"] - $_POST['bet'];
$fightlvl = $userrow["strength"] + $userrow["dexterity"]; 
doquery($link, "INSERT INTO {{table}} SET id='', challenger='".$userrow["charname"]."', bet='".$_POST['bet']."', receiver='".$row["charname"]."', fightlvl='$fightlvl'", "pvp");

doquery($link, "UPDATE {{table}} SET gold='$newgold2' WHERE id='".$userrow["id"]."' LIMIT 1", "users"); 

$page = "Challenge sent!<a href=\"index.php\">Go to town.</a>"; } } 
else {
$maxbet = $userrow["gold"];
 			$page = "Rival = opponents charname.<br />Bet = Winner Takes All<strong></strong><br />";
			$page .= "<form action=index.php?do=pvpfight method=post><br />";
			$page .= "Rival: <input type=text name=enemy size=30><br />";
			$page .= "Maxbet is <strong>$$maxbet </strong><br />";
			$page .= "Bet:<input type=text name=bet size=10><br />";
			$page .= "<input type=submit value=Send Challenge name=call></form><br /><br />"; 
	$page .= "<a href=\"index.php\">Back to town</a>"; }
	
	
	display($page, "Challenge");
}

function pvpfight2() {
	 
global $userrow, $numqueries;

$link = opendb();
$page .= "<table width=500 class=title><tr><td>Challenge</td></tr></table>";
$page .= "<table width=500><tr class=title><td></td><td width=\"200\"><strong>Challenger</strong></td><td><strong>Bet</strong></td><td widh=150></td><td></td></tr>"; 
$query = doquery($link, "SELECT*FROM {{table}} WHERE receiver='".$userrow["charname"]."'", "pvp");
$rank = 1;
	while ($row = mysqli_fetch_array($query)) {
	    $page .= "<tr><td><b>$rank</b></td><td>".$row["charname"]."</td><td >$".$row["bet"]."</td><td><a href=\"index.php?do=pvpfight3:".$row["fightlvl"]."\">[Take challenge]</a></td><td><a href=\"index.php?do=pvpfight4:".$row["id"]."\">[Deny]</a></tr>\n"; 
        
    $rank++;
	}
    if (mysqli_num_rows($query) == 0) { $page .= "<tr><td width=\"100%\">You dont have any challenges.</td></tr>\n"; } // Which hopefully isnt the case.
    $page = "</table>";

$page .= "<a href=\"index.php\">Back to town</a>";
	display($page, "Challenge");
}
 

function pvpfight3($id) { 

global $userrow, $numqueries;

$link = opendb();
$query = doquery($link, "SELECT*FROM {{table}} WHERE id='$id'", "pvp");
$row = mysqli_fetch_array($query);

 $vastanequery = doquery($link, "SELECT*FROM {{table}} WHERE charname='".$row["challenger"]."'", "users"); 
$vastanerow = mysqli_fetch_array($vastanequery); // it is you rivals query...do not change it

if(isset($_POST['lose'])) {

//Sed letter to winner
$_POST = protect($_POST['lose']);
$auto = "Automessage PvP";
$win = $row["bet"] * 2;
$title = "Victory";
$message = "You won duel against ".$userrow["charname"].". You won $".$win."";
doquery($link, "INSERT INTO {{table}} SET UserFrom='".$userrow["charname"]."', UserTo='".$row["challenger"]."', Message='$message', Subject='$title', SentDate=NOW()", "mail");

//update tabel....winner
$newgold = $vastanerow["gold"] + $win;
$updatequery = doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE charname='".$row["challenger"]."'", "users");
//update table...looser
$newgold2 =$userrow["gold"] - $row["bet"];
$updatequery = doquery($link, "UPDATE {{table}} SET gold='$newgold2' WHERE id='".$userrow["id"]."'", "users");
//what displays
$page.= "You lost this challenge. You lost $".$row["bet"]."<p><a href=\"index.php\">back to town</a>";
//delete row from mysql
doquery($link, "DELETE FROM {{table}} WHERE id='$id'", "pvp");
}  
 
if(isset($_POST['draw'])) {

//Send letter draw
$_POST = protect($_POST['draw']);
$auto = "Automessage PvP";
$title = "Draw";
$message = "You had fight with ".$userrow["charname"]." and it was draw.";
doquery($link, "INSERT INTO {{table}} SET UserFrom='".$userrow["charname"]."', UserTo='".$row["challenger"]."', Message='$message', Subject='$title', SentDate=NOW()", "mail");
//delete row from mysql
doquery($link, "DELETE FROM {{table}} WHERE id='$id'", "pvp");
$page.= "<a href=\"index.php\">Back to Town</a>";
} 


elseif(isset($_POST['won'])) {
//send lewtter
$_POST = protect($_POST['won']);
$win = $row["bet"] * 2;
$title = "Challenge defeat";
$message = "You were defeated by ".$userrow["charname"]." and u lost  $".$row["bet"]."";
doquery($link, "INSERT INTO {{table}} SET UserFrom='".$userrow["charname"]."', UserTo='".$row["challenger"]."', Message='$message', Subject='$title', SentDate=NOW()", "mail");
//update table...looser
$newgold = $vastanerow["gold"] - $row["bet"];
$updatequery = doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE charname='".$row["challenger"]."' ", "users");
//update tabel....winner
$newgold2 = $userrow["gold"] + $row["bet"];
$updatequery = doquery($link, "UPDATE {{table}} SET gold='$newgold2' WHERE id='".$userrow["id"]."' ", "users");
//what displays
$page.= "You won! You got $".$row["bet"]."<p><a href=\"index.php\">Back to town</a>";
//delete row from mysql
doquery("DELETE FROM {{table}} WHERE id='$id'", "pvp");

}


elseif($row["fightlvl"] > $userrow["fightlvl"]) //you lose
 { $page.= "<table>";
 $page.= "<tr><td><img src=\"images/classes/".$userrow["charclass"].".jpg\"></td><td>V.S</td><td><img src=\"images/classes/".$row["challenger"].".jpg\"></td></tr><tr>".$userrow["charname"]."</td><td></td><td>".$vastanerow["charname"]."</td></tr><tr></tr><tr><td>What a Pitty, you lose!</td></tr><tr><td><form action=index.php?do=pvpfight3:$id method=post><input type=submit value='To main page' name=lose></td></tr></table>"; }

elseif($row["fightlvl"] == $userrow["fightlvl"]) //draw
 { $page.= "<table>";
 $page.= "<tr><td><img src=\"images/classes/".$userrow["charclass"].".jpg\"></td><td>V.S</td><td><img src=\"images/classes/".$row["challenger"].".jpg\"></td></tr><tr>".$userrow["charname"]."</td><td></td><td>".$vastanerow["charname"]."</td></tr><tr></tr><tr><td>Draw!</td></tr><tr><td><form action=index.php?do=pvpfight3:$id method=post><input type=submit value='To main page' name=draw></td></tr></table>";
///GIVE Challenger BACK HIS GOLD
$query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id'", "pvp");
$row = mysqli_fetch_array($query);
$bet = $row["bet"];
$challenger = $row["challenger"];
$query2= doquery($link, "SELECT gold FROM {{table}} WHERE charname='$challenger'", "users");
$qrow = mysqli_fetch_array($query2);
$bet2 = $qrow["gold"];
$newgold = $bet2 + $bet;
$query3 = doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE charname='$challenger' LIMIT 1", "users");
}

elseif($vastanerow["fightlvl"] == $userrow["fightlvl"]) //draw
 { $page.= "<table>";
 $page.= "<tr><td><img src=\"images/classes/".$userrow["charclass"].".jpg\"></td><td>V.S</td><td><img src=\"images/classes/".$row["challenger"].".jpg\"></td></tr><tr>".$userrow["charname"]."</td><td></td><td>".$vastanerow["charname"]."</td></tr><tr></tr><tr><td>Draw!</td></tr><tr><td><form action=index.php?do=pvpfight3:$id method=post><input type=submit value='To main page' name=draw></td></tr></table>"; }


elseif($vastanerow["fightlvl"] < $userrow["fightlvl"]) //you won
 { $page.= "<table>";
 $page.= "<tr><td><img src=\"images/classes/".$userrow["charclass"].".jpg\"></td><td>V.S</td><td><img src=\"images/classes/".$row["challenger"].".jpg\"></td></tr><tr>".$userrow["charname"]."</td><td></td><td>".$vastanerow["charname"]."</td></tr><tr></tr><tr><td>You have WON this challenge!!</td></tr><tr><td><form action=index.php?do=pvpfight3:$id method=post><input type=submit value='Back to main page' name=won></td></tr></table>"; }

else { $page.="ERROR"; }
display($page, "Challenge"); 
}



 function pvpfight4($id) { // 
    	
global $userrow, $numqueries, $vastanerow;

$link = opendb();
$query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id'", "pvp");
$row = mysqli_fetch_array($query);
$bet = $row["bet"];
$challenger = $row["challenger"];

 $query2= doquery($link, "SELECT gold FROM {{table}} WHERE charname='$challenger'", "users");
$qrow = mysqli_fetch_array($query2);
$bet2 = $qrow["gold"];

$newgold = $bet2 + $bet;
$query3 = doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE charname='$challenger' LIMIT 1", "users");
$query12 = doquery($link, "DELETE FROM {{table}} WHERE id='$id'  LIMIT 1", "pvp");
$page .= "<strong>Challenge deleted</strong>";
$page .= "<p><a href=\"index.php?do=pvpfight2\">Back to challenge area</a>";

display($page, "Delete challenge");
}

function mainfight() {
	
global $userrow;
$link = opendb();
$fightlvl = $userrow["strength"] + $userrow["dexterity"]; 
$update = doquery($link, "UPDATE {{table}} SET fightlvl='$fightlvl' WHERE id='".$userrow["id"]."' LIMIT 1","users"); // updates fighting level
$page = "<a href=\"index.php?do=pvpfight\">Invite to challenge</a><p><a href=\"index.php?do=pvpfight2\">Take challenge</a>";
display($page, "Main challenge hall"); 
}

?>