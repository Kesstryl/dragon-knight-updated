<?php // login.php :: Handles logins and cookies.

include('lib.php');
$check = protectcsfr();

if (isset($_GET["do"])) {
	$check = protectcsfr();
	$_GET = array_map('protectarray', $_GET);
    if ($_GET["do"] == "login") { login(); }
    elseif ($_GET["do"] == "logout") { logout(); }
}

function login() {
    
    include('config.php');

    if (isset($_POST["submit"])) {
		$check = protectcsfr();
		$link = opendb();
        $username = protect($_POST['username']);
        $pass = protect($_POST['password']);
		$token = protect($_POST['token']);
		
		if ($_SESSION['token'] != $token) { die("Invalid request");}
		$salt = $username;
		$password = hash('sha256', $salt.$pass);
        $query = doquery($link, "SELECT * FROM {{table}} WHERE username='$username' AND password='$password' LIMIT 1", "users") or die(mysqli_error($link));
        if (mysqli_num_rows($query) != 1) { die("Invalid username or password. Please go back and try again."); }
        $row = mysqli_fetch_array($query);
        if (isset($_POST["rememberme"])) { $expiretime = time()+604800; $rememberme = 1; } else { $expiretime = 0; $rememberme = 0; }
        $random = rand(100, 10000);
		$itsme = hash('sha256', $random);
		$query = doquery($link, "UPDATE {{table}} SET `random`='$itsme' WHERE `username`='$username' LIMIT 1", "users") or die(mysqli_error($link));		
		$hashme = md5($itsme);
		$cookie = ($row["id"]) . " " .$hashme. " " . $rememberme;
        setcookie("dkgame", $cookie, $expiretime, "/", "", 0, true);
		unset($_SESSION['token']);
		$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['me'] = sha1($itsme);
        header("Location: index.php");
        die();
        
    }
    
    $page = gettemplate("login");
    $title = "Log In";
    display($page, $title, false, false, false, false);
	 
}
    

function logout() {
    
    setcookie("dkgame", "", time()-100000, "/", "", 0, $httponly);
	setcookie ("dkgame", false);
	unset($_COOKIE["dkgame"]);
	session_destroy();
    header("Location: login.php?do=login");
    die();
    
}

?>