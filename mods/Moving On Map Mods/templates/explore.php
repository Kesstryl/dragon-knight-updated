<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title"><img src="images/title_exploring.gif" alt="Exploring" /></td></tr>
<tr><td>
You are exploring the map, and nothing has happened. Continue exploring using the direction buttons or the Travel To menus.
</td></tr>
</table>
<br />
<center>
<table width=400 height=400 style="background-image:url('images/map.gif'); background-position: {{brx}}px {{bry}}px; background-repeat: no-repeat; border-width: 1px; border-spacing: ; border-style: outset; border-color: black; border-collapse: collapse;">
<tr>
<td width=200 height=200 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
<td width=200 height=200 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
</tr>
<tr>
<td width=200 height=200 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
<td width=200 height=200 style="border-width: 1px; padding: 0px; border-style: dashed; border-color: blue;">
</td>
</td>
</tr>
</table>
<br />
<center>
Latitude: {{latitude}}<br />
Longitude: {{longitude}}<br />
<br />
THEVERYENDOFYOU;
?>