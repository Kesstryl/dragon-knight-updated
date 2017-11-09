<?php
libxml_disable_entity_loader( true );
include('lib.php');
$link = opendb();
$homequery = doquery($link, "SELECT * FROM {{table}} WHERE id='1' LIMIT 1", "control");
$homerow = mysqli_fetch_array($homequery);
$gamename = $homerow['gamename'];
ob_start("ob_gzhandler");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width">
<META NAME="robots" CONTENT="noindex,nofollow">
<meta name="description" content="Dragon Lord is a free to play text based PBBG RPG where you are a dragon that must fight your way to the top and stop a terrible evil from destroying the world." />
<link rel="dns-prefetch" href="//fonts.googleapis.com/">
<link href='http://fonts.googleapis.com/css?family=Metamorphous' rel='stylesheet' type='text/css'>
<head>
<center><img class="logo" src="images/Banner.png" alt="{{dkgamename}}" title="{{dkgamename}}" border="0" /></center>
<center><a href="login.php?do=login"><img src="images/button_login.gif" alt="Log In" title="Log In" border="0" /></a> <a href="users.php?do=register"><img src="images/button_register.gif" alt="Register" title="Register" border="0" /></a> <a href="help.php"><img src="images/button_help.gif" alt="Help" title="Help" border="0" /></a></center></br>
<center><title><?php echo $homerow["gamename"]; ?> Welcome to {{dkgamename}}</title></center>
<style type="text/css">
body {
  background-image: url(images/background.jpg);
  color: black;
  font: 11px verdana;
}
pre {
 white-space: pre-line;
 word-break: keep-all;
 font: 12px verdana
}
table {
  border-style: none;
  padding: 0px;
  font: 11px verdana;
}
td {
  border-style: none;
  padding: 3px;
  vertical-align: top;
}
a {
    color: #663300;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    color: #330000;
}
.small {
  font: 10px verdana;
}
.highlight {
  color: red;
}
.light {
  color: #999999;
}
.title {
  border: solid 1px black;
  background-color: #eeeeee;
  font-weight: bold;
  padding: 5px;
  margin: 3px;
}
.copyright {
  border: solid 1px black;
  background-color: #eeeeee;
  font: 10px verdana;
}
</style>
</head>
<body>
<center><b>About This Game:<b></center></br>
<?php echo "<table width=\"99%\"><tr><td><pre>".$homerow['description']."</pre></td></tr></table></br>"; ?>
<center><b>Screenshots:</b></center></br>
<center><a href="images/screenshots/screenshot1.jpg" target="_blank">Screenshot 1</a></center>
<center><a href="images/screenshots/screenshot2.jpg" target="_blank">Screenshot 2</a></center></br></br>
<?php
if ($homerow["shownews"] == 1) { 
    $newsquery = doquery($link, "SELECT * FROM {{table}} ORDER BY id DESC LIMIT 1", "news");
    while($newsrow = mysqli_fetch_array($newsquery)){
        echo "<table width=\"99%\"><tr><td class=\"title\"><center>Latest News</center></td></tr><tr><td>\n
        <span class=\"light\">[".prettydate($newsrow['postdate'])."]</span><br /><pre>".nl2br($newsrow['content'])."</pre></td></tr></table>";
	}
}
?>
<table class="copyright" width="100%"><tr>
</td><td width="100%" align="center"> Powered by <a href="http://dragon.se7enet.com/dev.php" target="_new">Dragon Knight</a> &copy; 2003-2006 by renderse7en</td><td width="50%" align="center"></td>
</tr></table>
</body>
</html>