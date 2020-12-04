<?php $title = 'Mention Légales' ; ?>

<?php
	ob_start();
?>

<div class="content"><h1>MENTIONS LÉGALES</div>

<?php

$content = ob_get_clean();

require('template.php');

?>