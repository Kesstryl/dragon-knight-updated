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
<form action="users.php?do=changepassword" method="post">
<table width="100%">
<tr><td colspan="2">Use the form below to change your password. All fields are required. New passwords must be 10 alphanumeric characters or less.</td></tr>
<tr><td width="20%">Username:</td><td><input type="text" name="username" size="30" maxlength="30" /></td></tr>
<tr><td>Old Password:</td><td><input type="password" name="oldpass" size="20" /></td></tr>
<tr><td>New Password:</td><td><input type="password" name="newpass1" size="20" maxlength="10" /></td></tr>
<tr><td>Verify New Password:</td><td><input type="password" name="newpass2" size="20" maxlength="10" /><br /><br /><br /></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="Submit" onclick="return confirm('Confirm action');" /> <input type="reset" name="reset" value="Reset" /></td></tr>
<tr><td><input type="hidden" name="token" value="{$token}" />   </td></tr>
</table>
</form>
THEVERYENDOFYOU;
?>