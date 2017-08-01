<?php  // available quests script
if (file_exists('quest_install.php')) { die("Please delete <b>quest_install.php</b> from your Dragon Knight directory before continuing."); }

include_once('lib.php');
$link = opendb();

function displayQuests()
{
	global $userrow;
	
	$check = protectcsfr();
	$link = opendb();
	$townquery = doquery($link, "SELECT id FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysqli_num_rows($townquery) != 1) { display("Cheat attempt detected.<br /><br />Get a life, loser.", "ERROR"); }
	$townrow = mysqli_fetch_array($townquery);

	$questsquery = doquery2($link, "SELECT DISTINCT id, name, min_level, max_level, begin_text, reward_exp, reward_gold, drop_id
	FROM {{table1}} WHERE ".$userrow["level"]." >= min_level 
	AND ".$userrow["level"]." <= max_level AND town_id = '".$townrow["id"]."' 
	AND id NOT IN (SELECT quest_id FROM {{table2}} WHERE user_id = '".$userrow["id"]."') 
	AND (pre_id = 0 OR pre_id IN (SELECT quest_id FROM {{table2}} WHERE quest_id = pre_id 
	AND user_id = '".$userrow["id"]."' AND STATUS = '1'))","quests", "questprogress");
	
	$page = "<table width=\"100%\"><tr><td class=\"title\">Available Quests</td></tr>\n";
	$page .= "<tr><td><table width=\"500\"><tr><td>";
	if (mysqli_num_rows($questsquery) == 0)
	{
		$page .= "There are no available quests at the moment.  Try back later.<br /><br />";
		//$page .= "<a href=\"index.php\">Back to Town</a>";
	}
	else
	{

		$page .= "<ul>";

		while ($questrow = mysqli_fetch_assoc($questsquery)) {
			$id = $questrow["id"];
			$name = $questrow["name"];
			$page .= "<li><a href=\"index.php?do=viewquest&id=".$id."\">".$name."</a></li>";
			}
		$page .= "</ul>";
	}
	$page .= "</td></tr></table><br /><a href='index.php'>Back to Town</a></td></tr></table>";
	$title = "Quests";
	display($page,$title);
}

function viewQuest()
{
	global $userrow;
	
	// make sure an id was passed in
	if (!isset($_GET["id"])) {
		display("<table width=\"100%\"><tr><td>No id passed in!</td.</tr></table>","ERROR");
		die();
	}
	$check = protectcsfr();
	$link = opendb();
//	$_GET = array_map('protectarray', $_GET);
	$questid = explode(":",$_GET["id"]);
	$id = $questid[0];

	// make sure id passed in is valid
	if (isNaN($id))
	{
		display("<table width=\"100%\"><tr><td>Invalid quest id!</td></tr></table>","ERROR");
		die();
	}
	
	// make sure player is in town and get town info to use later
	$townquery = doquery($link, "SELECT id FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysqli_num_rows($townquery) != 1) { 
		display("<table width=\"100%\"><tr><td>Cheat attempt detected.<br /><br />Get a life, loser.</td></tr></table>", "ERROR");
		die();
	}
	$townrow = mysqli_fetch_array($townquery);

	// retrieve quest info.  only retrieve it if the player is in the town where the quest is available and the is eligible for the quest.
	$result = doquery2($link, "SELECT DISTINCT id, name, min_level, max_level, begin_text, reward_exp, reward_gold, drop_id
	FROM {{table1}} WHERE id = " . $id . " AND " . $userrow["level"] . " >= min_level 
	AND " . $userrow["level"] . " <= max_level AND town_id = '" . $townrow["id"] . "' 
	AND id NOT IN (SELECT quest_id FROM {{table2}} WHERE user_id = '" .$userrow["id"] ."') 
	AND (pre_id = 0 OR pre_id IN (SELECT quest_id FROM {{table2}} WHERE quest_id = pre_id 
	AND user_id = '" . $userrow["id"] . "' AND STATUS = '1'))","quests", "questprogress");
	
	$rows = mysqli_num_rows($result);
	if (mysqli_num_rows($result) != 1) { 
		display("<table width=\"100%\"><tr><td>Quest info not found!</td></tr></table>","Error");
		die();
	}

	$title = "Quest Details";
	while ($resultrow = mysqli_fetch_assoc($result)) {
	$name = $resultrow["name"];
	$text = $resultrow["begin_text"];
	$rewardexp = $resultrow["reward_exp"];
	$rewardgold = $resultrow["reward_gold"];
	$dropid = $resultrow["drop_id"];
	$dropname = "";
	$dropbonus1 = "";
	$dropbonus2 = "";
	$dropinfo = "";
	if ($dropid != 0)
	{
		$dropquery = doquery($link, "SELECT * FROM {{table}} WHERE id = '" . $dropid . "'","drops");
		$droprow = mysqli_fetch_array($dropquery);
		
		$attributearray = array("maxhp"=>"Max HP",
                            "maxmp"=>"Max MP",
                            "maxtp"=>"Max TP",
                            "defensepower"=>"Defense Power",
                            "attackpower"=>"Attack Power",
                            "strength"=>"Strength",
                            "dexterity"=>"Dexterity",
                            "expbonus"=>"Experience Bonus",
                            "goldbonus"=>"Gold Bonus");


		$attribute1 = explode(",",$droprow["attribute1"]);
    		$dropbonus1 = $attributearray[$attribute1[0]];
   		if ($attribute1[1] > 0) { $dropbonus1 .= " +" . $attribute1[1]; } else { $dropbonus1 .= $attribute1[1]; }
		if ($droprow["attribute2"] != "X") { 
        		$attribute2 = explode(",",$droprow["attribute2"]);
        		$dropbonus2 = $attributearray[$attribute2[0]];
        		if ($attribute2[1] > 0) { $dropbonus2 .= " +" . $attribute2[1]; } else { $dropbonus2 .= $attribute2[1]; }
    		}		
		$dropname = $droprow["name"];

		$dropinfo = $droprow["name"] . ": " . $dropbonus1;
		if ($dropbonus2 != "")
			{
			$dropinfo .= ", ".$dropbonus2;
			}
		}
	}

	$pagearray = array();
	$pagearray["questname"] = $name;
	$pagearray["questtext"] = nl2br($text);
	$pagearray["rewardexp"] = $rewardexp;
	$pagearray["rewardgold"] = $rewardgold;
	if ($dropinfo == "") { $dropinfo = "None"; }
	$pagearray["dropinfo"] = $dropinfo;
	$pagearray["questid"] = $id;

	// Finalize page and display it.
   	$template = gettemplate("viewquest");
    	$page = parsetemplate($template,$pagearray);
		

	display($page,$title);


}

function acceptQuest()
{

	global $userrow;

	// make sure an id was passed in
	if (!isset($_GET["id"])) {
		display("<table width=\"100%\"><tr><td>No id passed in!</td.</tr></table>","ERROR");
		die();
	}
	$check = protectcsfr();
	$link = opendb();
	$_GET = array_map('protectarray', $_GET);
	$questid = explode(":",$_GET["id"]);
	$id = $questid[0];

	// make sure id passed in is valid
	if (isNaN($id))
	{
		display("<table width=\"100%\"><tr><td>Invalid quest id!</td></tr></table>","ERROR");
		die();
	}
	
	// make sure player is in town and get town info to use later
	$townquery = doquery($link, "SELECT id FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysqli_num_rows($townquery) != 1) { 
		display("<table width=\"100%\"><tr><td>Cheat attempt detected.<br /><br />Get a life, loser.</td></tr></table>", "ERROR");
		die();
	}
	$townrow = mysqli_fetch_array($townquery);

	// retrieve quest info.  only retrieve it if the player is in the town where the quest is available and the is eligible for the quest.
	//$questquery = doquery("SELECT * from {{table}} where id = " . $id,"quests");
	$result = doquery2($link, "SELECT DISTINCT id, objective_lat, objective_long 
	FROM {{table1}} WHERE id = " . $id . " AND " . $userrow["level"] . " >= min_level 
	AND " . $userrow["level"] . " <= max_level AND town_id = '" . $townrow["id"] . "' 
	AND id NOT IN (SELECT quest_id FROM {{table2}} WHERE user_id = '" .$userrow["id"] ."') 
	AND (pre_id = 0 OR pre_id IN (SELECT quest_id FROM {{table2}} WHERE quest_id = pre_id 
	AND user_id = '" . $userrow["id"] . "' AND STATUS = '1'))","quests", "questprogress");
	
	$rows = mysqli_num_rows($result);
	if (mysqli_num_rows($result) != 1) { 
		display("<table width=\"100%\"><tr><td>You can not accept this quest right now.</td></tr></table>","Error");
		die();
	}

	// everything appears valid, insert the record
	while($row = mysqli_fetch_assoc($result)) {
	$user_id = $userrow["id"];
	$quest_id = $id;
	$quest_lat = $row["objective_lat"];
	$quest_long = $row["objective_long"];
	

	$query = doquery($link, "INSERT INTO {{table}} SET id='',user_id='".$user_id."',quest_id='".$quest_id."',status='0',latitude='".$quest_lat."',longitude='".$quest_long."'", "questprogress") or die(mysql_error());

	$page = "<table width=\"100%\"><tr><td class=\"title\">Available Quests</td></tr></table>";
	$page .= "<table width=\"100%\"><tr><td>";
	$page .= "You have accepted the quest, and it has been added to your quest log.<br /><br />";
	$page .= "You may now <a href=\"index.php\">return to town</a> or go back to the <a href=\"index.php?do=getquests\">quest list</a>";
	$page .= "</td></tr></table>";
	display($page,"Quest Accepted");
	}
}


?>
