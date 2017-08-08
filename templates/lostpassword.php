<?php

//this code protects users from csfr attacks on this page, do not remove unless you can implement better csfr protection.
if (!isset($_SESSION['token'])) {
    $token = sha1(uniqid(rand(), TRUE));
    $_SESSION['token'] = $token;
	}else
	{
    $token = $_SESSION['token'];
	}

$token = $_SESSION['token'];

$template = <<<THEVERYENDOFYOU
<form action="users.php?do=lostpassword" method="post">
<table width="80%">
<tr><td colspan="2">If you've lost your password, enter your email address below and you will be sent a new one.</td></tr>
<tr><td width="20%">Email Address:</td><td><input type="text" name="email" size="30" maxlength="100" /></td></tr>
<tr><td>Verification:</td><td><img src="auth.php" alt="Image Verification" /><br /><br />Copy the text from the above image into the box below. Can't read it? <a href="users.php?do=lostpassword">Refresh</a>.<br /><input id="imagever" name="imagever" type="text" /></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" /></td></tr>
<tr><td><input type="hidden" name="token" value="{$token}" />   </td></tr>
</table>
</form>
THEVERYENDOFYOU;
?>