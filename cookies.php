<?php // cookies.php :: Handles cookies. (Mmm, tasty!)


function checkcookies() {
	
	$link = opendb();
    $row = false;
    
    if (isset($_COOKIE["dkgame"])) {
        
		if($_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']){
		header("Location: login.php?do=login"); die(); 
		}
        // COOKIE FORMAT:
        // {ID} {USERNAME} {PASSWORDHASH} {REMEMBERME}
        $theuser = explode(" ",$_COOKIE["dkgame"]);
		$link = opendb();
        $query = doquery($link, "SELECT * FROM {{table}} WHERE id='$theuser[0]'", "users");
        if (mysqli_num_rows($query) != 1) { die("Invalid cookie data (Error 1). Please clear cookies and log in again."); }
        $row = mysqli_fetch_array($query);
		if($_SESSION['me'] != sha1($row['random'])) { die("Invalid request.  Please log in again.");}
        if ($row["id"] != $theuser[0]) { die("Invalid cookie data (Error 2). Please clear cookies and log in again."); }
		if (md5($row["random"]) != $theuser[1]) { die("Invalid cookie data (Error 3). Please clear cookies and log in again."); }

        // If we've gotten this far, cookie should be valid, so write a new one.
        $newcookie = implode(" ",$theuser);
        if ($theuser[2] == 1) { $expiretime = time()+604800; } else { $expiretime = 0; }
        setcookie ("dkgame", $newcookie, $expiretime, "/", "", false, true);
        $onlinequery = doquery($link, "UPDATE {{table}} SET onlinetime=NOW() WHERE id='$theuser[0]' LIMIT 1", "users");
        
    }
        
    return $row;
    
}

?>