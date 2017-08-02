<?PHP

include_once('lib.php');
include('config.php');
$link = opendb();

if (isset($_POST['submit'])) {
	$_POST = protect($_POST['submit']);
 $sql = 'CREATE TABLE IF NOT EXISTS `'.$dbsettings['prefix'].'_mail` ('
        . ' `id` int(80) NOT NULL auto_increment, '
		. ' `UserTo` varchar(30) NOT NULL, '
        . ' `UserFrom` varchar(30)NOT NULL, '
        . ' `Subject` varchar(30) NOT NULL, '
        . ' `Message` text NOT NULL, '
        . ' `STATUS` text NOT NULL, '
        . ' `SentDate` datetime NOT NULL default "0000-00-00 00:00:00",'
		. ' UNIQUE KEY `id` (`id`) '
        . ' )Engine=InnoDB';
	mysqli_query($link, $sql) or die("The installer failed.<br />MySQL reported ".mysql_error()." <br />Contact me at official forum.");
	echo "The mail mod was installed sucessfully. DELETE install_mail from your game directory for security reasons!<br />
		You are now ready to <a href=\"index.php\">play the game</a><br>
		I hope this mod will be of some help, Blue Eyes";
} else {
	echo 'Welcome to the Mail installer.<br /><form action="install_mail.php" method="post"><input type="submit" name="submit" value="Install"></form>';
}
?>
