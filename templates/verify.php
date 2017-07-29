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
<form action="users.php?do=verify" method="post">
<table width="80%">
<tr><td colspan="2">Thank you for registering a character. Please enter your username, email address, and the verification code
that was emailed to you to unlock your character.</td></tr>
<tr><td width="20%">Username:</td><td><input type="text" name="username" size="30" maxlength="30" /></td></tr>
<tr><td>Email Address:</td><td><input type="text" name="email" size="30" maxlength="100" /></td></tr>
<tr><td>Verification Code:</td><td><input type="text" name="verify" size="10" maxlength="8" /><br /><br /><br /></td></tr>
<tr><td colspan="2"><input type="hidden" name="birthday" value=""><input type="submit" name="submit" value="Submit" onclick="return confirm('Confirm action');" /> <input type="reset" name="reset" value="Reset" /></td></tr>
<tr><td><input type="hidden" name="token" value="{$token}" />   </td></tr>
</table>
</form>
THEVERYENDOFYOU;
?>