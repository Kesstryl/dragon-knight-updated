<?php
include_once('lib.php');
include('config.php');
$link = opendb();

if (isset($_POST['submit'])) {
	$_POST = protect($_POST['submit']);
	
$prefix = $dbsettings["prefix"];
$users = $prefix . "_users";
$pvp = $prefix . "_pvp";

$query = <<<END
CREATE TABLE IF NOT EXISTS `$pvp` (
`id` smallint(6) NOT NULL auto_increment,
  `challenger` varchar(30) NOT NULL default '',
  `receiver` varchar(30) NOT NULL default '',
  `bet` mediumint(9) NOT NULL default '0',
  `fightlvl` mediumint(9) NOT NULL default '0',
   PRIMARY KEY  (`id`)
)ENGINE=InnoDB;
END;

mysqli_query($link, $query) or die("Installer failed");
	echo "The pvp table was installed sucessfully. DELETE installpvp.php from your game directory for security reasons!<br />";
unset($query);

$sql = "ALTER TABLE `$users` ADD `fightlvl` mediumint(9) DEFAULT '1' NOT NULL";
if(mysqli_query($link, $sql)) { echo "Fighting level row added to dk_users.  You are now ready to <a href=\"index.php\">play the game</a><br>"; } else { echo"Error adding to users table."; }

unset($sql);
}

echo 'Welcome to the pvp fight table installer.<br /><form action="installpvp.php" method="post"><input type="submit" name="submit" value="Install"></form>';
	
?>
