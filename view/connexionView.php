<?php $title = 'Connexion' ; ?>

<?php
	session_start();
	ob_start();
?>

<?php

$content = ob_get_clean();

require('template.php');

?>