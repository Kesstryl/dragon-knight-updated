<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title"><img src="images/button_location.gif" alt="Location" title="Location" /></td></tr>
<tr><td>
<center>
Currently: {{currentaction}}<br />
<br />
<center>
<table width=100 height=100 style="background-image:url('images/map.gif'); background-position: {{brx}}px {{bry}}px; background-repeat: no-repeat; border-width: 1px; border-spacing: ; border-style: outset; border-color: black; border-collapse: collapse;">
<tr>
<td width=50 height=50 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
<td width=50 height=50 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
</tr>
<tr>
<td width=50 height=50 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
<td width=50 height=50 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
</td>
</tr>
</table>
<br />
<center>
Latitude: {{latitude}}<br />
Longitude: {{longitude}}<br />
<br />
<a href="javascript:openmappopup()">View Map</a><br /><br />
<form action="index.php?do=move" method="post">
<center>
<input name="north" type="submit" value="North" /><br />
<input name="west" type="submit" value="West" /><input name="east" type="submit" value="East" /><br />
<input name="south" type="submit" value="South" />
</center>
</form>
</td></tr>
</table><br />
<table width="100%">
<tr><td class="title"><img src="images/button_towns.gif" alt="Towns" title="Towns" /></td></tr>
<tr><td>
{{currenttown}}
Travel To:<br />
{{townslist}}
</td></tr>
</table><br />

<table width="100%">
<tr><td class="title"><img src="images/button_functions.gif" alt="Functions" title="Functions" /></td></tr>
<tr><td>
{{forumslink}}
{{adminlink}}
<a href="users.php?do=changepassword">Change Password</a><br />
<a href="login.php?do=logout">Log Out</a><br />
<a href="help.php">Help</a>
</td></tr>
</table><br />
THEVERYENDOFYOU;
?>