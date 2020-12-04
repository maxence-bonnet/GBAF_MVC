<?php $title = 'Connexion' ; ?>

<?php
	ob_start();
?>

<div class="content accueil_content"> 
	<form class="connection_form" action="index.php?action=connexion&amp;try=1" method="post">
		<fieldset>
			<legend>Connexion</legend>
				<?php
					if(isset($_SESSION['passchanged']))
					{
						    echo '<p style=color:red;>La modification du mot de passe a bien été prise en compte.</p>';
						    unset($_SESSION['passchanged']);
					}
					if(isset($_SESSION['missing_field']))
					{
						    echo '<p style=color:red;>Certains champs n\'ont pas été remplis.</p>';
						    unset($_SESSION['missing_field']);
					}						
					if(isset($_SESSION['wrong']))
					{
						    echo '<p style=color:red;>Le mot de passe ou l\'identifiant est incorrect.</p>';
						    unset($_SESSION['wrong']);
					}
					if(isset($_SESSION['success']))
					{
						    echo '<p style=color:red;>Inscription réussie !</p>';
						    unset($_SESSION['success']);
					}														
				?>
				<label for="username">Indentifiant / nom d'utilisateur :</label><input type="text" name="username" id="username"/>

				<label for="password">Mot de passe :</label><input type="password" name="password" id="password"/>

				<div class="connection_link"><a href="index.php?action=reinit">Mot de passe oublié ?</a><p>  |  </p><a href="index.php?action=inscription">Je ne suis pas encore inscrit</a></div>

				<input type="submit" name="submit" value="Me connecter">
		</fieldset>			
	</form>
</div>

<?php

$content = ob_get_clean();

require('template.php');

?>