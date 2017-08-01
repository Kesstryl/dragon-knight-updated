<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title">Quest Event:  <b>{{questname}}</b></td></tr>
<tr><td align="left">
{{questtext}}<br /> <br />
</td></tr>
<tr><td>
You are fighting a <b>{{monstername}}</b><br /><br />
{{monsterhp}}
{{yourturn}}
{{monsterturn}}
{{command}}
</td></tr>
</table>
THEVERYENDOFYOU;
?>