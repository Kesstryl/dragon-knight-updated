<?php // login.php :: Handles logins and cookies.
include('lib.php');

if (isset($_GET["do"])) {
	$check = protectcsfr();
	$_GET = array_map('protectarray', $_GET);
    if ($_GET["do"] == "login") { login(); }
	elseif ($_GET["do"] == "nobrute") { nobrute(); }
    elseif ($_GET["do"] == "logout") { logout(); }
}

function login() {
    include('config.php');
	@$_SESSION['loginCount'] += 0;
    if (isset($_POST["submit"])) {
		$check = protectcsfr();
		$link = opendb();
        $username = protect($_POST['username']);
        $pass = protect($_POST['password']);
		$token = protect($_POST['token']);
		if ($_SESSION['token'] != $token) { die("Invalid request, try <a href=login.php?do=login>logging in</a> again.");}
		if($_SESSION['loginCount']>=1){
			header("Location: login.php?do=nobrute");
			die();}
		$salt = $username;
		$password = hash('sha256', $salt.$pass);
        $query = doquery($link, "SELECT * FROM {{table}} WHERE username='$username' AND password='$password' LIMIT 1", "users") or die(mysqli_error($link));
        if (mysqli_num_rows($query) != 1) { 
            $_SESSION['loginCount'] = $_SESSION['loginCount']+1;
		die("Invalid username or password. Please <a href=\"login.php?do=login\">go back </a>and try again."); }
        $row = mysqli_fetch_array($query);
        if (isset($_POST["rememberme"])) { $expiretime = time()+604800; $rememberme = 1; } else { $expiretime = 0; $rememberme = 0; }
        $random = rand(100, 10000);
		$itsme = hash('sha256', $random);
		$query = doquery($link, "UPDATE {{table}} SET `random`='$itsme' WHERE `username`='$username' LIMIT 1", "users") or die(mysqli_error($link));		
		$hashme = md5($itsme);
		$cookie = ($row["id"]) . " " .$hashme. " " . $rememberme;
        setcookie("dkgame", $cookie, $expiretime, "/", "", false, true);
		unset($_SESSION['token']);
		unset($_SESSION['loginCount']);
		session_regenerate_id();
		$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['me'] = sha1($itsme);
        header("Location: index.php");
        die();
    }
    $page = gettemplate("login");
    $title = "Log In";
    display($page, $title, false, false, false, false);
}

function nobrute(){
	if(isset($_POST['verify'])){
        // Process image verification.
			$number = protect($_POST['imagever']);
			if (md5($number) != $_SESSION['image_random_value']) { die("Image verification failed.<br />"); }
			unset($_SESSION['loginCount']);	
			header("Location: login.php?do=login");
			die();
	}
	$page = "Verify that you are human</br></br><form action=\"login.php?do=nobrute\" method=\"post\">Verification:<img src=\"auth.php\" alt=\"Image Verification\" /><br /><br />Copy the text from the above image into the box below. Can't read it? <a href=\"login.php?do=nobrute\">Refresh</a>.<br /><input id=\"imagever\" name=\"imagever\" type=\"text\" /><input type=\"submit\" name=\"verify\" value=\"Submit\" /></form>";
	$title = "Verify Yourself";
	display($page, $title, false, false, false, false);
}
	
function logout() {
    setcookie("dkgame", "", time()-100000, "/", "", false, $httponly);
	setcookie ("dkgame", false);
	unset($_COOKIE["dkgame"]);
	session_destroy();
    header("Location: login.php?do=login");
    die();
}
?>