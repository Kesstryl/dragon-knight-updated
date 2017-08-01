<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title">Quest Info</td></tr>
<tr><td>
<table width="100%">
<tr>
<td align="center"><b>{{questname}}</b></td>
</tr>
<tr>
<td align="left">{{questtext}}<br/></br></td>
</tr>
</table>
<table>
<tr><td colspan="2"><b>Rewards</b></td></tr>
<tr>
<td align="right" width="50%">Exp:</td>
<td align="left" width="50%">{{rewardexp}}</td>
</tr>
<tr>
<td align="right" width="50%">Gold:</td>
<td align="left" width="50%">{{rewardgold}}</td>
</tr>
<tr>
<td align="right" width="50%">Item:</td>
<td align="left" width="50%">{{dropinfo}}</td>
</tr>
</table>
<br />
<a href="index.php?do=acceptquest&id={{questid}}">Accept Quest</a> | <a href="index.php?do=getquests">Back to Quest List</a> | <a href="index.php">Back to Town</a>
</td></tr>
</table>
THEVERYENDOFYOU;
?>