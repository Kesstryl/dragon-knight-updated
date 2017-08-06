//this will allow your players to fight random "Bosses" while exploring.. it's simple and easy to get running.
//Bosses are created from random monsters picked from the monster table and buffing stats.
////--Everything works for me just fine, just edit it how you want.

////First Open your explore.php file and find this line



$chancetofight = rand(1,5);



////Replace ALL of that (until the end "}") with these lines



 $chancetofight = rand(1,10);
    if ($chancetofight == 1) { 
        $action = "currentaction='Fighting', currentfight='1',";
    } else {
    if ($chancetofight == 3) { 
        $action = "currentaction='Fighting', currentfight='1',";
    } else {
    if ($chancetofight == 7) { 
        $action = "currentaction='Fighting', currentfight='1',";
    } else {
 if ($chancetofight == 5) { 
        $action = "currentaction='FightingBoss', currentfight='2',";
} else {
 if ($chancetofight == 8) { 
        $action = "currentaction='FightingBoss', currentfight='2',";
} else {
        $action = "currentaction='Exploring',";
    }
   }
  }
 }
}

Alternatively, if you don't want that much fighting and you want to keep the game more vanilla,
find this: 

	$chancetofight = rand(1,15);
    if ($chancetofight == 1) { 
        $action = "currentaction='Fighting', currentfight='1',";
		
and after that add this

} elseif ($chancetofight == 5) { 
    $action = "currentaction='FightingBoss', currentfight='2',";
	
make sure this code is right after (this was already in the script)
} else {
        $action = "currentaction='Exploring',";
    }

////Next still in explore.php find this line



 if ($userrow["currentaction"] == "Fighting") { header("Location: index.php?do=fight"); die(); }


////Under that line ^ place this line of code


   if ($userrow["currentaction"] == "Fightingboss") { header("Location: index.php?do=fightboss"); die(); }



////save explore.php and exit.





//open index.php

////in the town functions find thise


//Fighting functions.
    elseif ($do[0] == "fight") { include('fight.php'); fight(); }
    elseif ($do[0] == "victory") { include('fight.php'); victory(); }
    elseif ($do[0] == "drop") { include('fight.php'); drop(); }
    elseif ($do[0] == "dead") { include('fight.php'); dead(); }


////Add this underneith it



   //boss functions
    elseif ($do[0] == "fightboss") { include('fightboss.php'); fightboss(); }
    elseif ($do[0] == "victoryboss") { include('fightboss.php'); victoryboss(); }
    elseif ($do[0] == "dropboss") { include('fightboss.php'); dropboss(); }
    elseif ($do[0] == "deadboss") { include('fightboss.php'); deadboss(); }



////find these lines of code in the donothing function:


} elseif ($userrow["currentaction"] == "Fighting")  {
        $page = dofight();
        $title = "Fighting";


////Under that ^ place this


   } elseif ($userrow["currentaction"] == "FightingBoss")  {
        $page = dofightboss();
        $title = "Fighting Boss";


////(remember at the end of all the else if's to close this off with a "}" )






////IF your game has POTIONS find this in index.php


  if($userrow["currentaction"] == "Fighting")
    {
      $page = "The Monster Snatches The Potion From You Before You Can Drink It, Back To The <a href='index.php'>Fight</a>";
      $die = true;
      doquery($link, "UPDATE {{table}} SET `$type` = `$type` - '1' WHERE `id` = '".$userrow["id"]."'", "users");
    }



////add this line of code undernieth it



        if($userrow["currentaction"] == "FightingBoss")
    {
      $page = "The Boss Snatches The Potion From You Before You Can Drink It, Back To The <a href='index.php'>Fight</a>";
      $die = true;
      doquery($link, "UPDATE {{table}} SET `$type` = `$type` - '1' WHERE `id` = '".$userrow["id"]."'", "users");
    }





////still in index.php find these lines of code


function dofight() { // Redirect to fighting.
    
    header("Location: index.php?do=fight");
    
}



////Underneith this place these lines of code



function dofightboss() { // Redirect to fighting.
    
    header("Location: index.php?do=fightboss");
    
}

////save index.php and exit



Place the fightboss.php file included into your directory (with index,admin,users,lib,the_help_files,town ect..)

=]

done!!!



EDIT THE VALUES HOWEVER YOU WISH I SPENT 3HRS FIXING BUGS AND WHAT NOT.. IT SEEMS BALANCED AND READY FOR USE TO ME



--The bosses are HARD. You will take a beating from them with my stats.

AND you WONT deal normal damage to them. HEALING IS A MUST 

wheee


ENJOY!

~JUSTIN


EMAIL: Deadthcomestnow8@aim.com
AIM: Deathcomestnow 
or
trumpetplaya1


MSN: trumpet_playa_1@hotmail.com


For any questions

[update] 

I have re uploaded as I found a major problem with enemy gold / exp

also damage has been reduced slightly.

(Mod udated by Kesstryl to use with the Dragon Knight Updated engine)
