<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?>

En<?php echo $uarr['hname'] ?>

<?php break;case 'loginread': ?>

<li>Recommended browser: IE9+, Chrome, Firefox </li>
<li>First time, please see the [<a href='<?= surl('uio-help') ?>' target="_blank">help-page</a>] >>> </li>
<li>Account number >=2 bit, Password >=5 bit </li>
<li>authentication code , such as error, please refresh </li>
<li>Technical support: Peace(imcat.txjia.com).</li>

<?php break;case 'adminread': ?>

<li>Recommended browser: IE9+, Chrome, Firefox </li>
<li>First time, please see the [<a href='?help' target="_blank">help-page</a>] >>> </li>
<li>Account number >=2 bit, Password >=5 bit </li>
<li>authentication code , such as error, please refresh </li>
<li>Technical support: Peace(imcat.txjia.com).</li>

<?php break;case 'xxx': ?>  

<?php } ?>