<?php
//by Kesstryl
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title"><center><img src="images/welcome_{{gamename}}.gif" alt="Welcome to {{gamename}}" title="Welcome to {{gamename}}" /></center></td></tr>
<tr><td>
<center><b>About This Game:</b></br>
{{description}}
<center></td></tr>
<tr><td><center>
<br /><b>Screenshots:</b></center></br>
<center><a href="images/screenshots/screenshot1.jpg" target="_blank">Buying Equipment Screenshot</a></center>
<center><a href="images/screenshots/screenshot2.jpg" target="_blank">Monster Fighting Screenshot</a></center>
</td></tr>

<tr>
<div style="height:50px overflow:auto;"><center>
<td>
{{news}}
</div>
<br />
</td></tr>
</table>
THEVERYENDOFYOU;
?>