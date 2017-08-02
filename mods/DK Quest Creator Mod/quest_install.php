<?PHP
include('lib.php');
include('config.php');
$link = opendb();
if (isset($_POST['submit'])) {

$_POST = protect($_POST['submit']);
$sql = 'ALTER TABLE `'.$dbsettings['prefix'].'_users` ADD `currentquestid` SMALLINT( 5  ) DEFAULT \'0\' NOT NULL';
mysqli_query($link, $sql) or die("Error connecting to DB.");
echo "Users table successfully altered!<br />";

$sql2 = "CREATE TABLE IF NOT EXISTS `".$dbsettings['prefix']."_quests` (";
$sql2 .= <<<END
`id` smallint(5) NOT NULL auto_increment,
`name` varchar(50) NOT NULL,
`town_id` tinyint(3) NOT NULL,
`min_level` smallint(5) NOT NULL,
`max_level` smallint(5) NOT NULL,
`quest_type` enum('0','1') NOT NULL default '0',
`monster_id` smallint(5) NOT NULL default '0',
`pre_id` smallint(5) NOT NULL default '0',
`begin_text` text NOT NULL,
`end_text` text NOT NULL,
`objective_lat` smallint(6) NOT NULL,
`objective_long` smallint(6) NOT NULL,
`reward_exp` mediumint(8) NOT NULL default '0',
`reward_gold` mediumint(8) NOT NULL default '0',
`drop_id` mediumint(8) NOT NULL default '0',
PRIMARY KEY  (`id`)
) ENGINE=InnoDB;  
END;

mysqli_query($link, $sql2) or die("Error connecting to DB.");
echo "Quests table successfully created!<br />";


$sql3 = "INSERT INTO `".$dbsettings['prefix']."_quests` (";
$sql3 .= <<<END
`id`, `name`, `town_id`, `min_level`, `max_level`, `quest_type`, `monster_id`, `pre_id`, `begin_text`, `end_text`, `objective_lat`, `objective_long`, `reward_exp`, `reward_gold`, `drop_id`) VALUES
(1, 'Fetch Quest Example', 1, 1, 100, '0', 0, 0, 'Well, lookie here!  Your very first quest!  It looks like Trav is popping your DK quest cherry.  :)  OK, enough blabbering.  Let''s get to it.\r\n\r\nThis is an example of a ''fetch'' quest.  A fetch quest means you just have to find a certain area to complete the quest.  Prett easy, eh?  Well, there are lots of coordinates on the map, so it isn''t as easy as it may seem.\r\n\r\nLet''s make this real simple, though.  I''ll even give you the exact coordinates to go to!  Go to latitude 3, longitude 2 to finish this quest.  I''ll see you there.  :)\r\n\r\nOnce you accept this quest, click on the Quest Log link found on the right side of the page.  It will help you remember just where you need to go to finish quests.', 'Woo hoo!  You found the super top secret hidden area!  However did you find it?!?!?!  Oh, yeah.  I told you exactly where to go.  :P\r\n\r\nOk, notice that since this was just a fetch quest, all you had to do was find the right coordinates and you get the quest rewards.  Piece o'' cake.  Now head on back to town and you''ll get your next quest...a kill quest.  Scary!', 3, 2, 2, 2, 0),
(2, 'Scary Kill Quest Example', 1, 1, 100, '1', 1, 1, 'Good job on the fetch quest!  This one will be a bit tougher.  \r\n\r\nI want you to go kill a slime.  I know, you are thinking, &quot;Poor slime!  What did the slime do?&quot;  Well, let''s just say my girlfriend apparently has a thing for slimes...\r\n\r\nAnyway, since these are just silly example quests, I''ll be nice and give you the coordinates again.\r\n\r\nHead out to latitude 2, longitude -2 and take care of the slime who was taking care of my girl!', 'There the bastard slime is!  Look at that smile on his face.  Kill him and reap your quest rewards!  \r\n\r\nBy the way, after you kill the monster you might notice that you are rewarded more experience and gold than the quest said you would receive.  This is because you also receive experience and gold from the monster you kill.  It all gets added together.\r\n\r\nOnce you have sent him to slime hell, head back to town for your final example quest.  I''ll be waiting for you.', 2, -2, 3, 3, 0),
(3, 'Final Quest Example - Chains and Drops', 1, 1, 100, '1', 2, 2, 'Well, you killed the damn slime.  (If it wasn''t a slime you killed, you edited DK''s default monsters and killed something else.  By default the monster with an id of 1 is a Blue Slime.)  Now, let''s take care of my girlfriend.  You didn''t think I was going to let her off the hook, now did you?\r\n\r\nFirst, let''s talk business.  Do you see how quests are only showing up after you complete a previous quest?  That is because I''m chaining them together.  To do this, just set the &quot;Previous Quest ID&quot; field to be the id of the previous quest in the chain.  The first quest in the chain should always have a &quot;Previous Quest ID&quot; of 0.  You can then chain as many quests together as you like.\r\n\r\nAlright, back to the quest.  Last night I wined and dined my sweet ol'' girlfriend.  I ''wined'' her with a litle concoction that made her turn into a red slime!  If she likes slimes so much, she can join ''em!  Now head on out to latitude -1, longitude 3 and put her out of her misery.', 'There she is!  Finish her off.  After you do, you will receive your rewards.\r\n\r\nThis is the last example quest, so I added a drop reward this time.  To do this, just enter a &quot;Drop ID&quot; that matches the id of an item in the &quot;drop&quot; table that you would like to hand out as a quest reward.  It''s as simple as that.\r\n\r\nI''m going to leave you alone to start creating your own quests now.  Have fun!  Don''t forget to thank old Trav here for putting in all his time and effort to provide you with this neat little feature.  If you have any problems or suggestions, feel free to drop me a line at travman75@hotmail.com.\r\n\r\nNow kill that biotch!!!  Adios!', -1, 3, 4, 4, 1);
END;

mysqli_query($link, $sql3) or die("Error connecting to DB.");
echo "Quests table successfully populated!<br />";

$sql4 = "CREATE TABLE IF NOT EXISTS `".$dbsettings['prefix']."_questprogress` (";
$sql4 .= <<<END
`id` mediumint(8) NOT NULL auto_increment,
`user_id` smallint(5) NOT NULL,
`quest_id` smallint(5) NOT NULL,
`status` enum('0','1') NOT NULL,
`latitude` smallint(6) NOT NULL,
`longitude` smallint(6) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;
END;

mysqli_query($link, $sql4) or die("Error connecting to DB.");
echo "Quest progress table successfully created!<br />Installation complete.  DEKETE the installation script now for security reasons.<br />You are now ready to <a href="index.php">play the game</a><br>Have fun!  -- Trav";

} else {
	echo 'Welcome to the DK Quest Creator installer!<br /><form action="quest_install.php" method="post"><input type="submit" name="submit" value="Install"></form>';
}
?>