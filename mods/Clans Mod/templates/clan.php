<?php

/**
 * @author Ando Roots
 * @copyright 2007
 * Klanni mode
 */
 
$template = <<<THEVERYENDOFYOU
<br /><br /><center>
<table class="clanmenu" width="90%" height="5">
<tr><td align="center">
<a href="index.php?do=clan">Clan</a> | <a href="index.php?do=clanmembers"> Members </a>| <a href="index.php?do=clanleader"> Leader's Office</a>| <a href="index.php">Back to Town</a>
</td></tr></table>
<br />

<table class="clan" width="90%">
<tr><td height="520">
<center>
{{clancontent}}
<br /></center>
</td></tr>
</table>

THEVERYENDOFYOU;
?>