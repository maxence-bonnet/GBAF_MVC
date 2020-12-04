<?php $title = 'Contact' ; ?>

<?php
	ob_start();
?>

<div class="content"><h1>CONTACT</h1></div>

<?php

$content = ob_get_clean();

require('template.php');

?>