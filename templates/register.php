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
<form action="users.php?do=register" method="post">
<table width="80%">
<tr><td width="20%">Username:</td><td><input type="text" name="username" size="30" maxlength="30" /><br />Usernames must be 30 alphanumeric characters or less.<br /><br /><br /></td></tr>
<tr><td>Password:</td><td><input type="password" name="password1" size="30" maxlength="120" /></td></tr>
<tr><td>Verify Password:</td><td><input type="password" name="password2" size="30" maxlength="120" /><br />Passwords should be greater than 8 characters.<br /><br /><br /></td></tr>
<tr><td>Email Address:</td><td><input type="text" name="email1" size="30" maxlength="100" /></td></tr>
<tr><td>Verify Email:</td><td><input type="text" name="email2" size="30" maxlength="100" />{{verifytext}}<br /><br /><br /></td></tr>
<tr><td>Character Name:</td><td><input type="text" name="charname" size="30" maxlength="30" /></td></tr>
<tr><td>Character Class:</td><td><select name="charclass"><option value="1">{{class1name}}</option><option value="2">{{class2name}}</option><option value="3">{{class3name}}</option></select></td></tr>
<tr><td>Difficulty:</td><td><select name="difficulty"><option value="1">{{diff1name}}</option><option value="2">{{diff2name}}</option><option value="3">{{diff3name}}</option></select></td></tr>
<tr><td colspan="2">See <a href="help.php">Help</a> for more information about character classes and difficulty levels.<br /><br /></td></tr>
<tr><td>Verification:</td><td><img src="auth.php" alt="Image Verification" /><br /><br />Copy the text from the above image into the box below. Can't read it? <a href="users.php?do=register">Refresh</a>.<br /><input id="imagever" name="imagever" type="text" /></td></tr>
<tr><td colspan="2"><input type="hidden" name="birthday" value=""><input type="submit" name="submit" value="Submit" /> <input type="reset" name="reset" value="Reset" /></td></tr>
<tr><td><input type="hidden" name="token" value="{$token}" />   </td></tr>
</table>
</form>
THEVERYENDOFYOU;
?>