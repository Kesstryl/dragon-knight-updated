<?php // quest.php :: Handles all questing action.
include_once('lib.php');
$link = opendb();

if (!isset($questrow)) {
    $questinfoquery = doquery($link, "SELECT * from {{table}} WHERE id = '".$userrow["currentquestid"]."' LIMIT 1", "quests");
    $questrow = mysqli_fetch_array($questinfoquery);
}
	

function quest() { // One big long function that determines the outcome of the quest.
    
    global $userrow, $controlrow, $questrow;
	
	$check = protectcsfr();
	$link = opendb();
    if ($userrow["currentaction"] != "Quest Event" || $userrow["currentquestid"] == 0) { display("Cheat attempt detected.<br /><br />Get a life, loser.", "Error"); }

    // Check to see if this is just a "find a certain area" quest...
    if ($questrow["quest_type"] == '0')
    {
	   $exp = $questrow["reward_exp"];
	   $gold = $questrow["reward_gold"];

	   if ($userrow["experience"] + $exp < 16777215) { $newexp = $userrow["experience"] + $exp; $warnexp = ""; } else { $newexp = $userrow["experience"]; $exp = 0; $warnexp = "You have maxed out your experience points."; }
    	   if ($userrow["gold"] + $gold < 16777215) { $newgold = $userrow["gold"] + $gold; $warngold = ""; } else { $newgold = $userrow["gold"]; $gold = 0; $warngold = "You have maxed out your gold."; }

         $levelquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".($userrow["level"]+1)."' LIMIT 1", "levels");
    	   if (mysqli_num_rows($levelquery) == 1) { $levelrow = mysqli_fetch_array($levelquery); }
    
	   $levelup = "false";
    	   if ($userrow["level"] < 100) {
         	if ($newexp >= $levelrow[$userrow["charclass"]."_exp"]) {
            	$newhp = $userrow["maxhp"] + $levelrow[$userrow["charclass"]."_hp"];
            	$newmp = $userrow["maxmp"] + $levelrow[$userrow["charclass"]."_mp"];
            	$newtp = $userrow["maxtp"] + $levelrow[$userrow["charclass"]."_tp"];
            	$newstrength = $userrow["strength"] + $levelrow[$userrow["charclass"]."_strength"];
            	$newdexterity = $userrow["dexterity"] + $levelrow[$userrow["charclass"]."_dexterity"];
            	$newattack = $userrow["attackpower"] + $levelrow[$userrow["charclass"]."_strength"];
            	$newdefense = $userrow["defensepower"] + $levelrow[$userrow["charclass"]."_dexterity"];
            	$newlevel = $levelrow["id"];
            
            	if ($levelrow[$userrow["charclass"]."_spells"] != 0) {
                		$userspells = $userrow["spells"] . ",".$levelrow[$userrow["charclass"]."_spells"];
                		$newspell = "spells='$userspells',";
                		$spelltext = "You have learned a new spell.<br />";
            	} else { $spelltext = ""; $newspell=""; }

			$levelup = "true";
	   	} else {
            	$newhp = $userrow["maxhp"];
            	$newmp = $userrow["maxmp"];
            	$newtp = $userrow["maxtp"];
            	$newstrength = $userrow["strength"];
            	$newdexterity = $userrow["dexterity"];
            	$newattack = $userrow["attackpower"];
            	$newdefense = $userrow["defensepower"];
            	$newlevel = $userrow["level"];
            	$newspell = "";
        	}		
		
		if ($levelup == "true") {
			$page = "<table width=\"100%\"><tr><td class=\"title\">" .$questrow["name"]. "</td></tr><tr><td align=\"left\">" . nl2br($questrow["end_text"])."</td></tr></table><br /><br />Congratulations. You have completed the following quest: <br /><b>".$questrow["name"]."</b><br /><br />You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold <br /><br /><b>You have gained a level!</b><br /><br />You gain ".$levelrow[$userrow["charclass"]."_hp"]." hit points.<br />You gain ".$levelrow[$userrow["charclass"]."_mp"]." magic points.<br />You gain ".$levelrow[$userrow["charclass"]."_tp"]." travel points.<br />You gain ".$levelrow[$userrow["charclass"]."_strength"]." strength.<br />You gain ".$levelrow[$userrow["charclass"]."_dexterity"]." dexterity.<br />$spelltext<br /><br />"; 
	  	} else {
			$page = "<table width=\"100%\"><tr><td class=\"title\">" .$questrow["name"]. "</td></tr><tr><td align=\"left\">" . nl2br($questrow["end_text"])."</td></tr></table><br /><br />Congratulations. You have completed the following quest: <br /><b>".$questrow["name"]."</b><br /><br />You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold<br /><br />"; 
	  	}

	  	if ($questrow["drop_id"] != 0)
	  	{
			$dropcode = "dropcode='".$questrow["drop_id"]."',";
			$page .= "You have earned a reward for completing this quest. <a href=\"index.php?do=questdrop\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";
	  	} else {
			$dropcode = "";
			$page .= "You can now continue <a href=\"index.php\">exploring</a>."; 
	  	}
    } else {
        $newhp = $userrow["maxhp"];
        $newmp = $userrow["maxmp"];
        $newtp = $userrow["maxtp"];
        $newstrength = $userrow["strength"];
        $newdexterity = $userrow["dexterity"];
        $newattack = $userrow["attackpower"];
        $newdefense = $userrow["defensepower"];
        $newlevel = $userrow["level"];
        $newspell = "";    
		$page = "Congratulations. You have defeated the ".$monsterrow["name"].",<br />
		and you have completed the following quest:<br /><b>".$questrow["name"]."</b><br /><br />
		You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold <img class=pet src=images/gold.gif /><br /><br /><br />"; 
		if ($questrow["drop_id"] != 0)
		{
		$dropcode = "dropcode='".$questrow["drop_id"]."',";
		$page .= "You have earned a Reward for completing this quest!  <img class=pet src=images/drop.gif /> <a href=\"index.php?do=questdrop\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";
		$page .= "<script type='text/javascript'>alert('You have a drop!');</script>";
		} else {
		$dropcode = "";
		$page .= "You can now continue <a href=\"index.php\">exploring</a>."; 
		}
	  
	}   
    	   $updatequestquery = doquery($link, "UPDATE {{table}} SET status='1' where quest_id='".$userrow["currentquestid"]."' AND user_id='".$userrow["id"]."' LIMIT 1", "questprogress");
    	   $updatequery = doquery($link, "UPDATE {{table}} SET currentaction='Exploring',level='$newlevel',maxhp='$newhp',maxmp='$newmp',maxtp='$newtp',strength='$newstrength',dexterity='$newdexterity',attackpower='$newattack',defensepower='$newdefense', $newspell currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',$dropcode experience='$newexp',gold='$newgold',currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    	   unset($questrow);
	   $title = "Quest Completed!";
    	   display($page, $title);
    	   die();
    }


    $pagearray = array();
    $playerisdead = 0;


    $pagearray["magiclist"] = "";
    $userspells = explode(",",$userrow["spells"]);
    $spellquery = doquery($link, "SELECT id,name FROM {{table}}", "spells");
    while ($spellrow = mysqli_fetch_array($spellquery)) {
        $spell = false;
        foreach ($userspells as $a => $b) {
            if ($b == $spellrow["id"]) { $spell = true; }
        }
        if ($spell == true) {
            $pagearray["magiclist"] .= "<option value=\"".$spellrow["id"]."\">".$spellrow["name"]."</option>\n";
        }
        unset($spell);
    }
    if ($pagearray["magiclist"] == "") { $pagearray["magiclist"] = "<option value=\"0\">None</option>\n"; }
    $magiclist = $pagearray["magiclist"];
	
    $chancetoswingfirst = 1;

    // First, check to see if we need to get a monster for the quest.
    if ($userrow["currentfight"] == 1 && $questrow["quest_type"] == '1') {       
        
        // This is a boss quest.  Get the boss.
        $monsterquery = doquery($link, "SELECT * FROM {{table}} WHERE id = '".$questrow["monster_id"]."' LIMIT 1", "monsters");
        $monsterrow = mysqli_fetch_array($monsterquery);
        $userrow["currentmonster"] = $monsterrow["id"];
        $userrow["currentmonsterhp"] = rand((($monsterrow["maxhp"]/5)*4),$monsterrow["maxhp"]);
        if ($userrow["difficulty"] == 2) { $userrow["currentmonsterhp"] = ceil($userrow["currentmonsterhp"] * $controlrow["diff2mod"]); }
        if ($userrow["difficulty"] == 3) { $userrow["currentmonsterhp"] = ceil($userrow["currentmonsterhp"] * $controlrow["diff3mod"]); }
        $userrow["currentmonstersleep"] = 0;
        $userrow["currentmonsterimmune"] = $monsterrow["immune"];
        
        $chancetoswingfirst = rand(1,10) + ceil(sqrt($userrow["dexterity"]));
        if ($chancetoswingfirst > (rand(1,7) + ceil(sqrt($monsterrow["maxdam"])))) { $chancetoswingfirst = 1; } else { $chancetoswingfirst = 0; }
        
        unset($monsterquery);
        unset($monsterrow);
        
    }
    
    // Next, get the monster statistics.
    $monsterquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
    $monsterrow = mysqli_fetch_array($monsterquery);
    $pagearray["monstername"] = $monsterrow["name"];
    
    // Do run stuff.
    if (isset($_POST["run"])) {
		
		$_POST = protect($_POST["run"]);
        $chancetorun = rand(4,10) + ceil(sqrt($userrow["dexterity"]));
        if ($chancetorun > (rand(1,5) + ceil(sqrt($monsterrow["maxdam"])))) { $chancetorun = 1; } else { $chancetorun = 0; }
        
        if ($chancetorun == 0) { 
            $pagearray["yourturn"] = "You tried to run away, but were blocked in front!<br /><br />";
            $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
            $pagearray["monsterturn"] = "";
            if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
                $chancetowake = rand(1,15);
                if ($chancetowake > $userrow["currentmonstersleep"]) {
                    $userrow["currentmonstersleep"] = 0;
                    $pagearray["monsterturn"] .= "The monster has woken up.<br />";
                } else {
                    $pagearray["monsterturn"] .= "The monster is still asleep.<br />";
                }
            }
            if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
                $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
                if ($userrow["difficulty"] == 2) { $tohit = ceil($tohit * $controlrow["diff2mod"]); }
                if ($userrow["difficulty"] == 3) { $tohit = ceil($tohit * $controlrow["diff3mod"]); }
                $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
                $tododge = rand(1,150);
                if ($tododge <= sqrt($userrow["dexterity"])) {
                    $tohit = 0; $pagearray["monsterturn"] .= "You dodge the monster's attack. No damage has been scored.<br />";
                    $persondamage = 0;
                } else {
                    $persondamage = $tohit - $toblock;
                    if ($persondamage < 1) { $persondamage = 1; }
                    if ($userrow["currentuberdefense"] != 0) {
                        $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
                    }
                    if ($persondamage < 1) { $persondamage = 1; }
                }
                $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
                $userrow["currenthp"] -= $persondamage;
                if ($userrow["currenthp"] <= 0) {
                    $newgold = ceil($userrow["gold"]/2);
                    $newhp = ceil($userrow["maxhp"]/4);
                    $updatequery = doquery("UPDATE {{table}} SET currenthp='$newhp',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold', curretnquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                    $playerisdead = 1;
                }
            }
        }
        unset($questinfoquery);
        unset($questrow);
        $updatequery = doquery($link, "UPDATE {{table}} SET currentaction='Exploring', currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
        header("Location: index.php");
        die();
        
    // Do fight stuff.
    } elseif (isset($_POST["fight"])) {
        
        // Your turn.
		$_POST = protect($_POST["fight"]);
        $pagearray["yourturn"] = "";
        $tohit = ceil(rand($userrow["attackpower"]*.75,$userrow["attackpower"])/3);
        $toexcellent = rand(1,150);
        if ($toexcellent <= sqrt($userrow["strength"])) { $tohit *= 2; $pagearray["yourturn"] .= "Excellent hit!<br />"; }
        $toblock = ceil(rand($monsterrow["armor"]*.75,$monsterrow["armor"])/3);        
        $tododge = rand(1,200);
        if ($tododge <= sqrt($monsterrow["armor"])) { 
            $tohit = 0; $pagearray["yourturn"] .= "The monster is dodging. No damage has been scored.<br />"; 
            $monsterdamage = 0;
        } else {
            $monsterdamage = $tohit - $toblock;
            if ($monsterdamage < 1) { $monsterdamage = 1; }
            if ($userrow["currentuberdamage"] != 0) {
                $monsterdamage += ceil($monsterdamage * ($userrow["currentuberdamage"]/100));
            }
        }
        $pagearray["yourturn"] .= "You attack the monster for $monsterdamage damage.<br /><br />";
        $userrow["currentmonsterhp"] -= $monsterdamage;
        $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
        if ($userrow["currentmonsterhp"] <= 0) {
            $updatequery = doquery($link, "UPDATE {{table}} SET currentmonsterhp='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            header("Location: index.php?do=questvictory");
            die();
        }
        
        // Monster's turn.
        $pagearray["monsterturn"] = "";
        if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
            $chancetowake = rand(1,15);
            if ($chancetowake > $userrow["currentmonstersleep"]) {
                $userrow["currentmonstersleep"] = 0;
                $pagearray["monsterturn"] .= "The monster has woken up.<br />";
            } else {
                $pagearray["monsterturn"] .= "The monster is still asleep.<br />";
            }
        }
        if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
            $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
            if ($userrow["difficulty"] == 2) { $tohit = ceil($tohit * $controlrow["diff2mod"]); }
            if ($userrow["difficulty"] == 3) { $tohit = ceil($tohit * $controlrow["diff3mod"]); }
            $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
            $tododge = rand(1,150);
            if ($tododge <= sqrt($userrow["dexterity"])) {
                $tohit = 0; $pagearray["monsterturn"] .= "You dodge the monster's attack. No damage has been scored.<br />";
                $persondamage = 0;
            } else {
                $persondamage = $tohit - $toblock;
                if ($persondamage < 1) { $persondamage = 1; }
                if ($userrow["currentuberdefense"] != 0) {
                    $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
                }
                if ($persondamage < 1) { $persondamage = 1; }
            }
            $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
            $userrow["currenthp"] -= $persondamage;
            if ($userrow["currenthp"] <= 0) {
                $newgold = ceil($userrow["gold"]/2);
                $newhp = ceil($userrow["maxhp"]/4);
                $updatequery = doquery($link, "UPDATE {{table}} SET currenthp='$newhp',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold',currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                $playerisdead = 1;
            }
        }
        
    // Do spell stuff.
    } elseif (isset($_POST["spell"])) {
        
        // Your turn.
		$_POST = protect($_POST['spell']);
        $pickedspell = protect($_POST["userspell"]);
        if ($pickedspell == 0) { display("You must select a spell first. Please go back and try again.", "Error"); die(); }
        
        $newspellquery = doquery($link, "SELECT * FROM {{table}} WHERE id='$pickedspell' LIMIT 1", "spells");
        $newspellrow = mysqli_fetch_array($newspellquery);
        $spell = false;
        foreach($userspells as $a => $b) {
            if ($b == $pickedspell) { $spell = true; }
        }
        if ($spell != true) { display("You have not yet learned this spell. Please go back and try again.", "Error"); die(); }
        if ($userrow["currentmp"] < $newspellrow["mp"]) { display("You do not have enough Magic Points to cast this spell. Please go back and try again.", "Error"); die(); }
        
        if ($newspellrow["type"] == 1) { // Heal spell.
            $newhp = $userrow["currenthp"] + $newspellrow["attribute"];
            if ($userrow["maxhp"] < $newhp) { $newspellrow["attribute"] = $userrow["maxhp"] - $userrow["currenthp"]; $newhp = $userrow["currenthp"] + $newspellrow["attribute"]; }
            $userrow["currenthp"] = $newhp;
            $userrow["currentmp"] -= $newspellrow["mp"];
            $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and gained ".$newspellrow["attribute"]." Hit Points.<br /><br />";
        } elseif ($newspellrow["type"] == 2) { // Hurt spell.
            if ($userrow["currentmonsterimmune"] == 0) {
                $monsterdamage = rand((($newspellrow["attribute"]/6)*5), $newspellrow["attribute"]);
                $userrow["currentmonsterhp"] -= $monsterdamage;
                $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell for $monsterdamage damage.<br /><br />";
            } else {
                $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, but the monster is immune to it.<br /><br />";
            }
            $userrow["currentmp"] -= $newspellrow["mp"];
        } elseif ($newspellrow["type"] == 3) { // Sleep spell.
            if ($userrow["currentmonsterimmune"] != 2) {
                $userrow["currentmonstersleep"] = $newspellrow["attribute"];
                $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell. The monster is asleep.<br /><br />";
            } else {
                $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, but the monster is immune to it.<br /><br />";
            }
            $userrow["currentmp"] -= $newspellrow["mp"];
        } elseif ($newspellrow["type"] == 4) { // +Damage spell.
            $userrow["currentuberdamage"] = $newspellrow["attribute"];
            $userrow["currentmp"] -= $newspellrow["mp"];
            $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and will gain ".$newspellrow["attribute"]."% damage until the end of this fight.<br /><br />";
        } elseif ($newspellrow["type"] == 5) { // +Defense spell.
            $userrow["currentuberdefense"] = $newspellrow["attribute"];
            $userrow["currentmp"] -= $newspellrow["mp"];
            $pagearray["yourturn"] = "You have cast the ".$newspellrow["name"]." spell, and will gain ".$newspellrow["attribute"]."% defense until the end of this fight.<br /><br />";            
        }
            
        $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
        if ($userrow["currentmonsterhp"] <= 0) {
            $updatequery = doquery($link, "UPDATE {{table}} SET currentmonsterhp='0',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            header("Location: index.php?do=questvictory");
            die();
        }
        
        // Monster's turn.
        $pagearray["monsterturn"] = "";
        if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
            $chancetowake = rand(1,15);
            if ($chancetowake > $userrow["currentmonstersleep"]) {
                $userrow["currentmonstersleep"] = 0;
                $pagearray["monsterturn"] .= "The monster has woken up.<br />";
            } else {
                $pagearray["monsterturn"] .= "The monster is still asleep.<br />";
            }
        }
        if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
            $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
            if ($userrow["difficulty"] == 2) { $tohit = ceil($tohit * $controlrow["diff2mod"]); }
            if ($userrow["difficulty"] == 3) { $tohit = ceil($tohit * $controlrow["diff3mod"]); }
            $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
            $tododge = rand(1,150);
            if ($tododge <= sqrt($userrow["dexterity"])) {
                $tohit = 0; $pagearray["monsterturn"] .= "You dodge the monster's attack. No damage has been scored.<br />";
                $persondamage = 0;
            } else {
                if ($tohit <= $toblock) { $tohit = $toblock + 1; }
                $persondamage = $tohit - $toblock;
                if ($userrow["currentuberdefense"] != 0) {
                    $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
                }
                if ($persondamage < 1) { $persondamage = 1; }
            }
            $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
            $userrow["currenthp"] -= $persondamage;
            if ($userrow["currenthp"] <= 0) {
                $newgold = ceil($userrow["gold"]/2);
                $newhp = ceil($userrow["maxhp"]/4);
                $updatequery = doquery($link, "UPDATE {{table}} SET currenthp='$newhp',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold',currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                $playerisdead = 1;
            }
        }
    
    // Do a monster's turn if person lost the chance to swing first. Serves him right!
    } elseif ( $chancetoswingfirst == 0 ) {
        $pagearray["yourturn"] = "The monster attacks before you are ready!<br /><br />";
        $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
        $pagearray["monsterturn"] = "";
        if ($userrow["currentmonstersleep"] != 0) { // Check to wake up.
            $chancetowake = rand(1,15);
            if ($chancetowake > $userrow["currentmonstersleep"]) {
                $userrow["currentmonstersleep"] = 0;
                $pagearray["monsterturn"] .= "The monster has woken up.<br />";
            } else {
                $pagearray["monsterturn"] .= "The monster is still asleep.<br />";
            }
        }
        if ($userrow["currentmonstersleep"] == 0) { // Only do this if the monster is awake.
            $tohit = ceil(rand($monsterrow["maxdam"]*.5,$monsterrow["maxdam"]));
            if ($userrow["difficulty"] == 2) { $tohit = ceil($tohit * $controlrow["diff2mod"]); }
            if ($userrow["difficulty"] == 3) { $tohit = ceil($tohit * $controlrow["diff3mod"]); }
            $toblock = ceil(rand($userrow["defensepower"]*.75,$userrow["defensepower"])/4);
            $tododge = rand(1,150);
            if ($tododge <= sqrt($userrow["dexterity"])) {
                $tohit = 0; $pagearray["monsterturn"] .= "You dodge the monster's attack. No damage has been scored.<br />";
                $persondamage = 0;
            } else {
                $persondamage = $tohit - $toblock;
                if ($persondamage < 1) { $persondamage = 1; }
                if ($userrow["currentuberdefense"] != 0) {
                    $persondamage -= ceil($persondamage * ($userrow["currentuberdefense"]/100));
                }
                if ($persondamage < 1) { $persondamage = 1; }
            }
            $pagearray["monsterturn"] .= "The monster attacks you for $persondamage damage.<br /><br />";
            $userrow["currenthp"] -= $persondamage;
            if ($userrow["currenthp"] <= 0) {
                $newgold = ceil($userrow["gold"]/2);
                $newhp = ceil($userrow["maxhp"]/4);
                $updatequery = doquery($link, "UPDATE {{table}} SET currenthp='$newhp',currentaction='In Town',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentfight='0',latitude='0',longitude='0',gold='$newgold',currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
                $playerisdead = 1;
            }
        }

    } else {
        $pagearray["yourturn"] = "";
        $pagearray["monsterhp"] = "Monster's HP: " . $userrow["currentmonsterhp"] . "<br /><br />";
        $pagearray["monsterturn"] = "";
    }
    
    $newmonster = $userrow["currentmonster"];

    $newmonsterhp = $userrow["currentmonsterhp"];
    $newmonstersleep = $userrow["currentmonstersleep"];
    $newmonsterimmune = $userrow["currentmonsterimmune"];
    $newuberdamage = $userrow["currentuberdamage"];
    $newuberdefense = $userrow["currentuberdefense"];
    $newfight = $userrow["currentfight"] + 1;
    $newhp = $userrow["currenthp"];
    $newmp = $userrow["currentmp"];    
    
if ($playerisdead != 1) { 
$check = protectcsfr();
$link = opendb();
$pagearray["command"] = <<<END
Command?<br /><br />
<form action="index.php?do=quest" method="post">
<input type="submit" name="fight" value="Fight" /><br /><br />
<select name="userspell"><option value="0">Choose One</option>$magiclist</select> <input type="submit" name="spell" value="Spell" /><br /><br />
<input type="submit" name="run" value="Run" /><br /><br />
</form>
END;
    $updatequery = doquery($link, "UPDATE {{table}} SET currentaction='Quest Event',currenthp='$newhp',currentmp='$newmp',currentfight='$newfight',currentmonster='$newmonster',currentmonsterhp='$newmonsterhp',currentmonstersleep='$newmonstersleep',currentmonsterimmune='$newmonsterimmune',currentuberdamage='$newuberdamage',currentuberdefense='$newuberdefense' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
} else {
    $pagearray["command"] = "<b>You have died.</b><br /><br />As a consequence, you've lost half of your gold. However, you have been given back a portion of your hit points to continue your journey.<br /><br />You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
}

    $pagearray["questtext"] = nl2br($questrow["end_text"]);
    $pagearray["questname"] = $questrow["name"];
    // Finalize page and display it.
    $template = gettemplate("quest");
    $page = parsetemplate($template,$pagearray);
    
    display($page, "Quest Event");
    
}

function questvictory() {
    
    global $userrow, $controlrow, $questrow;
    
	$check = protectcsfr();
	$link = opendb();
    if ($userrow["currentmonsterhp"] != 0) { header("Location: index.php?do=quest"); die(); }
    if ($userrow["currentfight"] == 0) { header("Location: index.php"); die(); }
    
    $monsterquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["currentmonster"]."' LIMIT 1", "monsters");
    $monsterrow = mysqli_fetch_array($monsterquery);
    
    $exp = rand((($monsterrow["maxexp"]/6)*5),$monsterrow["maxexp"]);
    if ($exp < 1) { $exp = 1; }
    if ($userrow["difficulty"] == 2) { $exp = ceil($exp * $controlrow["diff2mod"]); }
    if ($userrow["difficulty"] == 3) { $exp = ceil($exp * $controlrow["diff3mod"]); }
    if ($userrow["expbonus"] != 0) { $exp += ceil(($userrow["expbonus"]/100)*$exp); }
    $gold = rand((($monsterrow["maxgold"]/6)*5),$monsterrow["maxgold"]);
    if ($gold < 1) { $gold = 1; }
    if ($userrow["difficulty"] == 2) { $gold = ceil($gold * $controlrow["diff2mod"]); }
    if ($userrow["difficulty"] == 3) { $gold = ceil($gold * $controlrow["diff3mod"]); }
    if ($userrow["goldbonus"] != 0) { $gold += ceil(($userrow["goldbonus"]/100)*$exp); }
    
    $exp = $exp + $questrow["reward_exp"];
    $gold = $gold + $questrow["reward_gold"];

    if ($userrow["experience"] + $exp < 16777215) { $newexp = $userrow["experience"] + $exp; $warnexp = ""; } else { $newexp = $userrow["experience"]; $exp = 0; $warnexp = "You have maxed out your experience points."; }
    if ($userrow["gold"] + $gold < 16777215) { $newgold = $userrow["gold"] + $gold; $warngold = ""; } else { $newgold = $userrow["gold"]; $gold = 0; $warngold = "You have maxed out your gold."; }
    
    $levelquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".($userrow["level"]+1)."' LIMIT 1", "levels");
    if (mysqli_num_rows($levelquery) == 1) { $levelrow = mysqli_fetch_array($levelquery); }
    
	$levelup = "false";
    if ($userrow["level"] < 100) {
        if ($newexp >= $levelrow[$userrow["charclass"]."_exp"]) {
            $newhp = $userrow["maxhp"] + $levelrow[$userrow["charclass"]."_hp"];
            $newmp = $userrow["maxmp"] + $levelrow[$userrow["charclass"]."_mp"];
            $newtp = $userrow["maxtp"] + $levelrow[$userrow["charclass"]."_tp"];
            $newstrength = $userrow["strength"] + $levelrow[$userrow["charclass"]."_strength"];
            $newdexterity = $userrow["dexterity"] + $levelrow[$userrow["charclass"]."_dexterity"];
            $newattack = $userrow["attackpower"] + $levelrow[$userrow["charclass"]."_strength"];
            $newdefense = $userrow["defensepower"] + $levelrow[$userrow["charclass"]."_dexterity"];
            $newlevel = $levelrow["id"];
            
            if ($levelrow[$userrow["charclass"]."_spells"] != 0) {
                $userspells = $userrow["spells"] . ",".$levelrow[$userrow["charclass"]."_spells"];
                $newspell = "spells='$userspells',";
                $spelltext = "You have learned a new spell.<br />";
            } else { $spelltext = ""; $newspell=""; }

		$levelup = "true";
            
            $title = "Courage and Wit have served thee well!";
        } else {
            $newhp = $userrow["maxhp"];
            $newmp = $userrow["maxmp"];
            $newtp = $userrow["maxtp"];
            $newstrength = $userrow["strength"];
            $newdexterity = $userrow["dexterity"];
            $newattack = $userrow["attackpower"];
            $newdefense = $userrow["defensepower"];
            $newlevel = $userrow["level"];
            $newspell = "";
        }

        if ($levelup == "true") {
		$page = "Congratulations. You have defeated the ".$monsterrow["name"].",<br />and you have completed the following quest:<br /><b>".$questrow["name"]."</b><br /><br />You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold <br /><br /><b>You have gained a level!</b><br /><br />You gain ".$levelrow[$userrow["charclass"]."_hp"]." hit points.<br />You gain ".$levelrow[$userrow["charclass"]."_mp"]." magic points.<br />You gain ".$levelrow[$userrow["charclass"]."_tp"]." travel points.<br />You gain ".$levelrow[$userrow["charclass"]."_strength"]." strength.<br />You gain ".$levelrow[$userrow["charclass"]."_dexterity"]." dexterity.<br />$spelltext<br /><br />"; 
	  } else {
		$page = "Congratulations. You have defeated the ".$monsterrow["name"].",<br />and you have completed the following quest:<br /><b>".$questrow["name"]."</b><br /><br />You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold<br /><br /><br />"; 
	  }

	  if ($questrow["drop_id"] != 0)
	  {
		$dropcode = "dropcode='".$questrow["drop_id"]."',";
		$page .= "You have earned a reward for completing this quest. <a href=\"index.php?do=questdrop\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";
	  } else {
		$dropcode = "";
		$page .= "You can now continue <a href=\"index.php\">exploring</a>."; 
	  }
   } else {
        $newhp = $userrow["maxhp"];
        $newmp = $userrow["maxmp"];
        $newtp = $userrow["maxtp"];
        $newstrength = $userrow["strength"];
        $newdexterity = $userrow["dexterity"];
        $newattack = $userrow["attackpower"];
        $newdefense = $userrow["defensepower"];
        $newlevel = $userrow["level"];
        $newspell = "";    
		$page = "Congratulations. You have defeated the ".$monsterrow["name"].",<br />
		and you have completed the following quest:<br /><b>".$questrow["name"]."</b><br /><br />
		You gain $exp experience. $warnexp <br />You gain $gold gold. $warngold <img class=pet src=images/gold.gif /><br /><br /><br />"; 
		if ($questrow["drop_id"] != 0)
		{
		$dropcode = "dropcode='".$questrow["drop_id"]."',";
		$page .= "You have earned a Reward for completing this quest!  <img class=pet src=images/drop.gif /> <a href=\"index.php?do=questdrop\">Click here</a> to reveal and equip the item, or you may also move on and continue <a href=\"index.php\">exploring</a>.";
		$page .= "<script type='text/javascript'>alert('You have a drop!');</script>";
		} else {
		$dropcode = "";
		$page .= "You can now continue <a href=\"index.php\">exploring</a>."; 
		}
	  
	}
    $updatequestquery = doquery($link, "UPDATE {{table}} SET status='1' where quest_id='".$userrow["currentquestid"]."' AND user_id='".$userrow["id"]."' LIMIT 1", "questprogress");
    $updatequery = doquery($link, "UPDATE {{table}} SET currentaction='Exploring',level='$newlevel',maxhp='$newhp',maxmp='$newmp',maxtp='$newtp',strength='$newstrength',dexterity='$newdexterity',attackpower='$newattack',defensepower='$newdefense', $newspell currentfight='0',currentmonster='0',currentmonsterhp='0',currentmonstersleep='0',currentmonsterimmune='0',currentuberdamage='0',currentuberdefense='0',$dropcode experience='$newexp',gold='$newgold',currentquestid='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
    unset($questrow);    
    $title = "Quest Completed!";
    display($page, $title);
    
}

function questdrop() {
    
    global $userrow;
    
	$check = protectcsfr();
	$link = opendb();
    if ($userrow["dropcode"] == 0) { header("Location: index.php"); die(); }
    
    $dropquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["dropcode"]."' LIMIT 1", "drops");
    $droprow = mysqli_fetch_array($dropquery);
    
    if (isset($_POST["submit"])) {
        
		$_POST = protect($_POST['submit']);
        $slot = protect($_POST["slot"]);
        
        if ($slot == 0) { display("Please go back and select an inventory slot to continue.","Error"); }
        
        if ($userrow["slot".$slot."id"] != 0) {
            
            $slotquery = doquery($link, "SELECT * FROM {{table}} WHERE id='".$userrow["slot".$slot."id"]."' LIMIT 1", "drops");
            $slotrow = mysqli_fetch_array($slotquery);
            
            $old1 = explode(",",$slotrow["attribute1"]);
            if ($slotrow["attribute2"] != "X") { $old2 = explode(",",$slotrow["attribute2"]); } else { $old2 = array(0=>"maxhp",1=>0); }
            $new1 = explode(",",$droprow["attribute1"]);
            if ($droprow["attribute2"] != "X") { $new2 = explode(",",$droprow["attribute2"]); } else { $new2 = array(0=>"maxhp",1=>0); }
            
            $userrow[$old1[0]] -= $old1[1];
            $userrow[$old2[0]] -= $old2[1];
            if ($old1[0] == "strength") { $userrow["attackpower"] -= $old1[1]; }
            if ($old1[0] == "dexterity") { $userrow["defensepower"] -= $old1[1]; }
            if ($old2[0] == "strength") { $userrow["attackpower"] -= $old2[1]; }
            if ($old2[0] == "dexterity") { $userrow["defensepower"] -= $old2[1]; }
            
            $userrow[$new1[0]] += $new1[1];
            $userrow[$new2[0]] += $new2[1];
            if ($new1[0] == "strength") { $userrow["attackpower"] += $new1[1]; }
            if ($new1[0] == "dexterity") { $userrow["defensepower"] += $new1[1]; }
            if ($new2[0] == "strength") { $userrow["attackpower"] += $new2[1]; }
            if ($new2[0] == "dexterity") { $userrow["defensepower"] += $new2[1]; }
            
            if ($userrow["currenthp"] > $userrow["maxhp"]) { $userrow["currenthp"] = $userrow["maxhp"]; }
            if ($userrow["currentmp"] > $userrow["maxmp"]) { $userrow["currentmp"] = $userrow["maxmp"]; }
            if ($userrow["currenttp"] > $userrow["maxtp"]) { $userrow["currenttp"] = $userrow["maxtp"]; }
            
            $newname = addslashes($droprow["name"]);
            $query = doquery($link, "UPDATE {{table}} SET slot".$_POST["slot"]."name='$newname',slot".$_POST["slot"]."id='".$droprow["id"]."',$old1[0]='".$userrow[$old1[0]]."',$old2[0]='".$userrow[$old2[0]]."',$new1[0]='".$userrow[$new1[0]]."',$new2[0]='".$userrow[$new2[0]]."',attackpower='".$userrow["attackpower"]."',defensepower='".$userrow["defensepower"]."',currenthp='".$userrow["currenthp"]."',currentmp='".$userrow["currentmp"]."',currenttp='".$userrow["currenttp"]."',dropcode='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            
        } else {
            
            $new1 = explode(",",$droprow["attribute1"]);
            if ($droprow["attribute2"] != "X") { $new2 = explode(",",$droprow["attribute2"]); } else { $new2 = array(0=>"maxhp",1=>0); }
            
            $userrow[$new1[0]] += $new1[1];
            $userrow[$new2[0]] += $new2[1];
            if ($new1[0] == "strength") { $userrow["attackpower"] += $new1[1]; }
            if ($new1[0] == "dexterity") { $userrow["defensepower"] += $new1[1]; }
            if ($new2[0] == "strength") { $userrow["attackpower"] += $new2[1]; }
            if ($new2[0] == "dexterity") { $userrow["defensepower"] += $new2[1]; }
            
            $newname = protect($droprow["name"]);
            $query = doquery($link, "UPDATE {{table}} SET slot".$_POST["slot"]."name='$newname',slot".$_POST["slot"]."id='".$droprow["id"]."',$new1[0]='".$userrow[$new1[0]]."',$new2[0]='".$userrow[$new2[0]]."',attackpower='".$userrow["attackpower"]."',defensepower='".$userrow["defensepower"]."',dropcode='0' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
            
        }
        $page = "The item has been equipped. You can now continue <a href=\"index.php\">exploring</a>.";
        display($page, "Item Drop");
        
    }
    
    $attributearray = array("maxhp"=>"Max HP",
                            "maxmp"=>"Max MP",
                            "maxtp"=>"Max TP",
                            "defensepower"=>"Defense Power",
                            "attackpower"=>"Attack Power",
                            "strength"=>"Strength",
                            "dexterity"=>"Dexterity",
                            "expbonus"=>"Experience Bonus",
                            "goldbonus"=>"Gold Bonus");
    
    $page = "You have been rewarded the following item: <b>".$droprow["name"]."</b><br /><br />";
    $page .= "This item has the following attribute(s):<br />";
    
    $attribute1 = explode(",",$droprow["attribute1"]);
    $page .= $attributearray[$attribute1[0]];
    if ($attribute1[1] > 0) { $page .= " +" . $attribute1[1] . "<br />"; } else { $page .= $attribute1[1] . "<br />"; }
    
    if ($droprow["attribute2"] != "X") { 
        $attribute2 = explode(",",$droprow["attribute2"]);
        $page .= $attributearray[$attribute2[0]];
        if ($attribute2[1] > 0) { $page .= " +" . $attribute2[1] . "<br />"; } else { $page .= $attribute2[1] . "<br />"; }
    }
    
    $page .= "<br />Select an inventory slot from the list below to equip this item. If the inventory slot is already full, the old item will be discarded.";
    $page .= "<form action=\"index.php?do=drop\" method=\"post\"><select name=\"slot\"><option value=\"0\">Choose One</option><option value=\"1\">Slot 1: ".$userrow["slot1name"]."</option><option value=\"2\">Slot 2: ".$userrow["slot2name"]."</option><option value=\"3\">Slot 3: ".$userrow["slot3name"]."</option></select> <input type=\"submit\" name=\"submit\" value=\"Submit\" /></form>";
    $page .= "You may also choose to just continue <a href=\"index.php\">exploring</a> and give up this item.";
    
    display($page, "Item Drop");
    
}
    

function dead() {
    
    $page = "<b>You have died.</b><br /><br />As a consequence, you've lost half of your gold. However, you have been given back a portion of your hit points to continue your journey.<br /><br />You may now continue back to <a href=\"index.php\">town</a>, and we hope you fair better next time.";
        
}



?>