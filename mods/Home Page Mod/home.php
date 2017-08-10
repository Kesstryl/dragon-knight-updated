<?php
//by Kesstryl
include('lib.php');

if (isset($_GET["do"])) {
	$check = protectcsfr();
	$_GET = array_map('protectarray', $_GET);
    if ($_GET["do"] == "homepage") { homepage(); }
}

function homepage(){
	
$link = opendb();
$homequery = doquery($link, "SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$homerow = mysqli_fetch_array($homequery);
$gamename = $homerow['gamename'];
$description = $homerow['description'];

if ($homerow["shownews"] == 1) { 
    $newsquery = doquery($link, "SELECT * FROM {{table}} ORDER BY postdate DESC LIMIT 5", "news");
    while($newsrow = mysqli_fetch_array($newsquery)){
        $homerow["news"] = "<table width=\"95%\"><tr><td class=\"title\">Latest News</td></tr><tr><td>\n";
        $homerow["news"] .= "<span class=\"light\">[".prettydate($newsrow["postdate"])."]</span><br />".nl2br($newsrow["content"]."<br/>");
        $homerow["news"] .= "</td></tr></table>\n";
	}
}
$page = gettemplate("home");
$page = parsetemplate($page, $homerow);
$title = "{$gamename}";
    
display($page, $title, false, false, false, false);
    
}
?>