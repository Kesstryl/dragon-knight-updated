<?php // forum.php :: Internal forums script for the game.
include('lib.php');
include('cookies.php');
$check = protectcsfr();
$link = opendb();
$userrow = checkcookies();
if ($userrow == false) { display("The forum is for registered players only.", "Forum"); die(); }
$controlquery = doquery($link, "SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$controlrow = mysqli_fetch_array($controlquery);
// Close game.
if ($controlrow["gameopen"] == 0) { display("The game is currently closed for maintanence. Please check back later.","Game Closed"); die(); }
// Force verify if the user isn't verified yet.
if ($controlrow["verifyemail"] == 1 && $userrow["verify"] != 1) { header("Location: users.php?do=verify"); die(); }
// Block user if he/she has been banned.
if ($userrow["authlevel"] == 2) { die("Your account has been blocked. Please try back later."); }

if (isset($_GET["do"])) {

	$_GET = array_map('protectarray', $_GET);
	$do = explode(":",$_GET["do"]);
	
	if ($do[0] == "thread") { showthread($do[1], $do[2]); }
	elseif ($do[0] == "new") { newthread(); }
	elseif ($do[0] == "reply") { reply(); }
	elseif ($do[0] == "edit") { edit($do[1]); } 
	elseif ($do[0] == "modedit") { modedit(); } 
	elseif ($do[0] == "updated") { updated(); }
	elseif ($do[0] == "list") { donothing($do[1]); }
	
} else { donothing(0); }

function donothing($start=0) {
	
	$check = protectcsfr();
	$link = opendb();
    $query = doquery($link, "SELECT * FROM {{table}} WHERE parent='0' ORDER BY newpostdate && sticky DESC LIMIT 100", "forum");
    $page = "<table class=title width=100%><tr><td><center>Forum</center></td></tr></table>
			<center>(*):Sticky  (C):Clan  (G):Guide  (?):Help  (RP):Role Play</center></br>
			<table width=\"100%\"><tr><td style=\"padding:1px; background-color:#996622;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><th colspan=\"3\" style=\"background-color:#dddddd;\"><center><a href=\"forum.php?do=new\">New Thread</a>.......<a href=\"forummain.php\">Return To Forum</a></center></th></tr>
			<tr><th width=\"50%\" style=\"background-color:#dddddd;\">Thread</th><th width=\"10%\" style=\"background-color:#dddddd;\">Replies</th><th style=\"background-color:#dddddd;\">Last Post</th></tr>\n";
    $count = 1;
    if (mysqli_num_rows($query) == 0) { 
        $page .= "<tr><td style=\"background-color:#ffffff;\" colspan=\"3\"><b>No threads in forum.</b></td></tr>\n";
    } else { 
        while ($row = mysqli_fetch_array($query)) {
			$title = decode($row["title"]);
			if($row['sticky'] == "1"){
				$sticky = "<b>(*)</b>" ;
			}else{
				$sticky = "" ;
			}
        	if ($count == 1) {
            	$page .= "<tr><td style=\"background-color:#ffffff;\">$sticky<a href=\"forum.php?do=thread:".$row["id"].":0\">$title</a></td><td style=\"background-color:#ffffff;\">".$row["replies"]."</td><td style=\"background-color:#ffffff;\">".$row["newpostdate"]."</td></tr>\n";
            	$count = 2;
            } else {
                $page .= "<tr><td style=\"background-color:#eeeeee;\">$sticky<a href=\"forum.php?do=thread:".$row["id"].":0\">$title</a></td><td style=\"background-color:#eeeeee;\">".$row["replies"]."</td><td style=\"background-color:#eeeeee;\">".$row["newpostdate"]."</td></tr>\n";
                $count = 1;
            }
			
        }
    }
    $page .= "</table></td></tr></table>";
    
    display($page, "Forum");
    
}

function showthread($id, $start) {
	
	global $userrow;

	$check = protectcsfr();
	$link = opendb();
    $query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id' OR parent='$id' ORDER BY id LIMIT $start,50", "forum");
    $query2 = doquery($link, "SELECT id,title,locked,author FROM {{table}} WHERE id='$id' LIMIT 1", "forum");
    $row2 = mysqli_fetch_array($query2);
	$id = $row2['id'];
	$title = decode($row2['title']);
    $page = "<table width=\"100%\"><tr><td style=\"padding:1px; background-color:black;\"><table width=\"100%\" style=\"margins:0px;\" cellspacing=\"1\" cellpadding=\"3\"><tr><td colspan=\"2\" style=\"background-color:#dddddd;\"><b><a href=\"forum.php\">Forum</a> :: $title</b></td></tr>\n";
	$count = 1;
    while ($row = mysqli_fetch_array($query)) {
		$cont = ($row['content']);
		$content = decode($cont);
        if ($count == 1) {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#ffffff; vertical-align:top;\"><span class=\"small\"><a href=\"index.php?do=onlinechar:".$row["author"]."\"><b>".$row["author"]."</b></a><br /><br />".prettyforumdate($row["postdate"]).", #".$row['id']."</td><td style=\"background-color:#ffffff; vertical-align:top;\">$content</td></tr>\n";
            $count = 2;
        } else {
            $page .= "<tr><td width=\"25%\" style=\"background-color:#eeeeee; vertical-align:top;\"><span class=\"small\"><a href=\"index.php?do=onlinechar:".$row["author"]."\"><b>".$row["author"]."</b></a><br /><br />".prettyforumdate($row["postdate"]).", #".$row['id']."</td><td style=\"background-color:#eeeeee; vertical-align:top;\">$content</td></tr>\n";
            $count = 1;
        }
    }
	$token = formtoken();
    $page .= "</table></td></tr></table><br />";
	if($row2["locked"] == "0"){
	$page .= "<table width=\"100%\"><tr><td><b>Reply To This Thread:</b><br /><form action=\"forum.php?do=reply\" method=\"post\"><input type=\"hidden\" name=\"parent\" value=\"$id\" /><input type=\"hidden\" name=\"title\" value=\"Re: ".$row2["title"]."\" /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /><input type=\"hidden\" name=\"token\" value=\"$token\" /></form></td></tr></table>";
    }else{
	$page .=" This thread is locked so you can not reply.";
	}
	if ($row2['author'] == $userrow['charname']) {
		$page .= "<a href=\"forum.php?do=edit:$id\"><b>[Edit Your Thread]</b></a>";
	}
	if ($userrow['authlevel'] == 1 || $userrow['authlevel'] == 2){
		$start = 0;
		$page .= "</br>[Moderate This Thread]<form action=\"forum.php?do=modedit\" method=\"post\">Id of Post: <input type=\"text\" name=\"id\" size=10 maxlength=10 autocomplete=\"off\"/><input type=\"hidden\" name=\"token\" value=\"$token\" /><input type=\"submit\" name=\"mod\" value=\"mod\" />";
	}
    display($page, "Forum");
    
}

function reply() {

    global $userrow;
	$check = protectcsfr();
	$link = opendb();
	$content = protect($_POST['content']);
	$parent = protect($_POST['parent']);
	$title = protect($_POST['title']);
	$token = protect($_POST['token']);
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	
	$textsmilies = array(":p", ":)", ":(", ";)", "-.-");
    $imagesmilies = array("<img class=smile src=\"images/smilies/tongue.gif\">", "<img class=smile src=\"images/smilies/smile.gif\">", "<img class=smile src=\"images/smilies/frown.gif\">", "<img class=smile src=\"images/smilies/wink.gif\">", "<img class=smile src=\"images/smilies/mm.gif\">");
    $content = str_ireplace($textsmilies, $imagesmilies, $content);
		
	$query = doquery($link, "INSERT INTO {{table}} SET postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='$parent',replies='0',title='$title',content='$content'", "forum");
	$query2 = doquery($link, "UPDATE {{table}} SET newpostdate=NOW(),replies=replies+1 WHERE id='$parent' LIMIT 1", "forum");
	unset($_SESSION['token']);
	header("Location: forum.php?do=thread:$parent:0");
	die();
	
}

function newthread() {

    global $userrow;
	$check = protectcsfr();
    $link = opendb();
    if (isset($_POST["submit"])) {
		$titlebody = protect($_POST['title']);
		$content = protect($_POST['content']);
		$type = protect($_POST['type']);
		$token = protect($_POST['token']);
		if ($_SESSION['token'] != $token) { die("Invalid request");}
		
		$textsmilies = array(":p", ":)", ":(", ";)", "-.-");
        $imagesmilies = array("<img class=smile src=\"images/smilies/tongue.gif\">", "<img class=smile =\"images/smilies/smile.gif\">", "<img class=smile =\"images/smilies/frown.gif\">", "<img class=smile =\"images/smilies/wink.gif\">", "<img class=smile src=\"images/smilies/mm.gif\">");
        $content = str_ireplace($textsmilies, $imagesmilies, $content);
		$title = $type.$titlebody;
		
        $query = doquery($link, "INSERT INTO {{table}} SET postdate=NOW(),newpostdate=NOW(),author='".$userrow["charname"]."',parent='0',replies='0',title='$title',content='$content'", "forum");
        unset($_SESSION['token']);
		header("Location: forum.php");
        die();
    }
	
    $token = formtoken();
    $page = "<table width=\"100%\"><tr><td><b>Make A New Post:</b><br />
			Using thread types will help others know what kind of forum thread this is.<br/ ></br>
			<form action=\"forum.php?do=new\" method=\"post\">Title:<br />
			<input type=\"text\" name=\"title\" size=\"50\" maxlength=\"50\" /><br /><br />Message:<br /><textarea name=\"content\" rows=\"7\" cols=\"40\"></textarea></br></br>
			Thread Type: <select name=\"type\"><option value=\"\" >General</option><option value=\"(RP): \">Role Play</option><option value=\"(?): \">Help</option><option value=\"(G): \">Game Guide</option><option value=\"(C): \">Clan Recruitment</option></select>
			<br /><br /><input type=\"submit\" name=\"submit\" value=\"Submit\" /> <input type=\"reset\" name=\"reset\" value=\"Reset\" /><input type=\"hidden\" name=\"token\" value=\"{$token}\" /></form></td></tr></table>";
    display($page, "Forum");
    
}

function edit($id) {
	
	global $userrow;
	
	$link = opendb();
	
if (isset($_POST["submit"])) { 
	
	$check = protectcsfr();
	$content = protect($_POST["content"]);
	$token = protect($_POST['token']);
	if ($_SESSION['token'] != $token) { die("Invalid request");}
		
	$textsmilies = array(":p", ":)", ":(", ";)", "-.-");
    $imagesmilies = array("<img class=smile src=\"images/smilies/tongue.gif\">", "<img class=smile =\"images/smilies/smile.gif\">", "<img class=smile =\"images/smilies/frown.gif\">", "<img class=smile =\"images/smilies/wink.gif\">", "<img class=smile src=\"images/smilies/mm.gif\">");
    $content = str_ireplace($textsmilies, $imagesmilies, $content);

	if ($content == "" || $content == " ") { } // blank post. do nothing.
	$query = doquery($link, "UPDATE {{table}} SET newpostdate=NOW(),content='$content' WHERE id='$id' LIMIT 1", "forum");
	$page = "Post has been updated.  You may now <a href=forum.php?do=thread:$id:0>view your edited post</a>";
	unset($_SESSION['token']);
	display($page,"Update Post"); 
	}
$query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id' AND author='".$userrow['charname']."' LIMIT 1", "forum");
$row = mysqli_fetch_array($query);
$content = decode($row['content']);
$parent = decode($row['parent']);
$replies = decode($row['replies']);

if ($row['parent'] == 0) { $parent = "<strong>Parent Post</strong>"; } else { $parent = "$row[title]"; }
$token = formtoken();
$page = <<<END
<table width=100% align=center><tr><td class=title colspan=2 align=center border=1>
<b><u>Edit Post</u></b></td></tr><tr><td align=center>
<table width=100% align=center><tr><td class=title width=33% align=center>
<a href=forum.php title='Return to the Main Forum Index'>Forum</a>
<tr><td>Post ID#: {$id}</td><td >Parent ID#: [ {$parent} ]</td><td >$parent 
</td></tr></table>
<table align=center width="100%"><tr><td >
Replie's: {$replies} </td>
</td></tr></table>

<form action="forum.php?do=edit:$id" method="post">
<table align=center width="100%"><tr><td align=center>
</td></tr><tr><td align=center>
<strong>Content:</strong><br/><textarea cols="55" rows="20" name="content" wrap="physical">{$content}</textarea>
</td></tr><tr><td align=center>
<input type="hidden" name="token" value="{$token}" /><input type="submit" name="submit" value="Submit">
</td></tr></table>
</form>
</td></tr></table>
END;

$page = parsetemplate($page, $row);
display($page, "Edit Forum"); 
}

function modedit() {
	
	global $userrow;
	
	$link = opendb();
	
if (isset($_POST["mod"])){
	
	$check = protectcsfr();
	if (!($userrow['authlevel'] == '1' || $userrow['authlevel'] == '2')) {
		header("location: forum.php");
	}
	$id = protect($_POST["id"]);
	$token = protect($_POST['token']);
	if ($_SESSION['token'] != $token) { die("Invalid request");}
		
	$query = doquery($link, "SELECT * FROM {{table}} WHERE id='$id' LIMIT 1", "forum");
	$row = mysqli_fetch_array($query);
	$id = $row['id'];
	$cont = $row['content'];
	$parent = $row['parent'];
	$replies = $row['replies'];
	$content = decode($cont);

if ($row['parent'] == 0) { $parent = "<strong>Parent Post</strong>"; } else { $parent = "$row[title]"; }
$token = formtoken();
$page = <<<END
<table width=100% align=center><tr><td class=title colspan=2 align=center border=1>
<b><u>Edit Post</u></b></td></tr><tr><td align=center>
<table width=100% align=center><tr><td class=title width=33% align=center>
<a href=forum.php title='Return to the Main Forum Index'>Forum</a>
<tr><td>Post ID#: {$id}</td><td >Parent ID#: [ {$parent} ]</td><td >$parent 
</td></tr></table>
<table align=center width="100%"><tr><td >
Replie's: {$replies} </td>
</td></tr></table>

<form action="forum.php?do=updated" method="post">
<table align=center width="100%"><tr><td align=center>
</td></tr><tr><td align=center><input type="hidden" name="id" value="$id" />
<strong>Content:</strong><br/><textarea cols="55" rows="20" name="content" wrap="physical">{$content}</textarea>
</td></tr><tr><td>Mod Reason:<select name="type"><option value="--Moderated for corrections." >Corrections</option><option value="--Moderated for foul dragon mouth! ">Bad Language</option><option value="--Moderated for spam(dragons don't eat spam!)">Spam</option><option value="--Moderated for trolling (we are dragons, not trolls!)">Trolling</option>
<option value="--Moderated for flaming (save it for the dragon arena!)">Flaming</option></select><option value="--Moderated for rule violation (read the forum rules!)" >Violation</option></select></td><tr>
<tr><td align=center>
<input type="hidden" name="token" value="{$token}" /><input type="submit" name="submit" value="submit">
</td></tr></table>
</form>
</td></tr></table>
END;
}

$page = parsetemplate($page, $row);
display($page, "Edit Forum"); 
	
}

function updated(){
	global $userrow;
	if (!($userrow['authlevel'] == '1' || $userrow['authlevel'] == '2')) {
		header("location: forum.php");
	}
if (isset($_POST["submit"])) { 
	$check = protectcsfr();
	$id = protect($_POST["id"]);
	$cont = protect($_POST["content"]);
	$type = protect($_POST["type"]);
	$link = opendb();
	$token = protect($_POST['token']);
	if ($_SESSION['token'] != $token) { die("Invalid request");}
	
	$textsmilies = array(":p", ":)", ":(", ";)", "-.-");
    $imagesmilies = array("<img class=smile src=\"images/smilies/tongue.gif\">", "<img class=smile =\"images/smilies/smile.gif\">", "<img class=smile =\"images/smilies/frown.gif\">", "<img class=smile =\"images/smilies/wink.gif\">", "<img class=smile src=\"images/smilies/mm.gif\">");
    $cont = str_ireplace($textsmilies, $imagesmilies, $cont);
	$content = $cont.$type;

	if ($content == "" || $content == " ") { } // blank post. do nothing.
	$query2 = doquery($link, "UPDATE {{table}} SET newpostdate=NOW(),content='$content' WHERE id='$id' LIMIT 1", "forum");
	$page = "Post has been updated.  You may now <a href=forum.php>Return to the Forum</a>";
	unset($_SESSION['token']);
	display($page,"Update Post"); 
	}
}
	
?>