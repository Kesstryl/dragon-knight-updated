<?php
include_once("lib.php");

function bank() {
	global $userrow, $numqueries;
	
	$check = protectcsfr();
	$link = opendb();

	$townquery = doquery($link, "SELECT name,innprice FROM {{table}} WHERE latitude='".$userrow["latitude"]."' AND longitude='".$userrow["longitude"]."' LIMIT 1", "towns");
	if (mysqli_num_rows($townquery) != 1) { display("Cheat attempt detected.<br /><br />Get a life, loser.", "Error"); }

			if (isset($_POST['withdraw'])) {
				$gold = protect($_POST['bankgold']);
				$token = protect($_POST['token']);
				if ($_SESSION['token'] != $token) { die("Invalid request");}
				if ($gold <= 0) {
					$page = "You must enter an amount above 0!";
				}elseif(!is_numeric($gold)) {
                            $page = "You have invalid characters in your withdraw field. Head back to the <a href=index.php?do=bank>Bank</a>."; 
				}elseif ($gold > $userrow['bank']){
					 unset($_SESSION['token']);
					$page = "You dont have that much gold in the bank!";
				}else {
					$newgold = $userrow['gold'] + intval($gold);
					$newbank = $userrow['bank'] - intval($gold);
					doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
					doquery($link, "UPDATE {{table}} SET bank='$newbank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
					$page = "You withdrew $gold gold!";
					$page .= "<br />You may go back to the <a href=index.php?do=bank>bank</a> or the <a href=index.php>town</a>";
					 unset($_SESSION['token']);
				}

			} 
			if (isset($_POST['deposit'])) {
				$gold = protect($_POST['gold']);
				$token = protect($_POST['token']);
				if ($_SESSION['token'] != $token) { die("Invalid request");}
				if ($gold <= 0) {
					$page = "You must enter an amount above 0!";
                }elseif(!is_numeric($gold)) {
                           $page = "You have invalid characters in your deposit field. <br /> Head back to the <a href=index.php?do=bank>Bank</a>."; 
				}elseif ($gold > $userrow['gold']){
					$page = "You dont have that much gold!";
					 unset($_SESSION['token']);
				}else {
					$newgold = $userrow['gold'] - intval($gold);
					$newbank = $userrow['bank'] + intval($gold);
					doquery($link, "UPDATE {{table}} SET gold='$newgold' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
					doquery($link, "UPDATE {{table}} SET bank='$newbank' WHERE id='".$userrow["id"]."' LIMIT 1", "users");
					$page = "You deposited $gold gold!";
					$page .= "<br />You may go back to the <a href=index.php?do=bank>bank</a> or the <a href=index.php>town</a>";
					 unset($_SESSION['token']);
				}
			}
		
			$token = formtoken();
			$title = "Bank Transactions";
			$page = "<center><br />The Bank<center><br />";
			$page .= "Your bank account currently holds $userrow[bank] gold. You have the following options:<br />";
			$page .= "<form action=index.php?do=bank method=post><br />";
			$page .= "Gold <input type=text  value=$userrow[gold] name=gold><input type=hidden name=token value=$token><input type=submit value=Deposit name=deposit></form><br />";
			$page .= "<form action=index.php?do=bank method=post><br />";
			$page .= "Bank <input type=text value=$userrow[bank] name=bankgold><input type=hidden name=token value=$token><input type=submit value=Withdraw name=withdraw></form><br />";
			$page .= "<br />If you changed your mind, go back to the <a href=index.php>town</a>, or use the direction buttons on the left to start exploring.</a>";
		
            
	display($page, $title);
    
}
?>