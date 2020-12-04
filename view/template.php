<!DOCTYPE html>
<html>
    <head>
    	<title><?= $title ?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <link href="public/css/style.css" rel="stylesheet" />
        <link rel="icon" type="image/png" href="public/images/logos/gbaf_ico.png" />
    </head>       
    <body>
    	<!-- HEADER -->

    	<div class="header_content">
			<div class="logo_gbaf">
				<a href="index.php?action=accueil"><img src="public/images/logos/gbaf.png" title="GBAF"alt="GBAF logo"/></a>
			</div>
			<?php
				if(isset($_SESSION['username']) AND !empty($_SESSION['username'])) // session active
				{ 
					?>
					<div class="user_ref">
						<div class="user_photo">
							<a href="profil.php"><img src="public/images/uploads/<?= $_SESSION['photo'] ?>" alt="Ma photo de profil" title="Voir mon profil"/></a>
						</div>
						<div class="user_name">
							<a href="profil.php" title="Voir mon profil"><p><?= $_SESSION['prenom'] . ' ' . $_SESSION['nom'] ; ?></p></a>
						</div>
						<form class="deconnection_form" action="index.php?action=deconnexion" method="post"><input type="submit" value="deconnexion"/></form>				
					</div>
					<?php
				}
				else // pas de session
				{
					?>
					<div class="inscription_link">
						<a href="index.php?action=inscription">S'inscrire</a><p>/</p><a href="index.php?action=connexion">Se connecter</a>
					</div>
					<?php
				}
			?>
		</div>

		<!-- CONTENT -->

        <?= $content ?>


        <!-- FOOTER -->

        <div class="footer_content">
        	<p><a href="../index.php?action=mentions-legales.php"> Mentions légales </a> | <a href="../index.php?action=contact.php">Contact</a></p>
        </div>

    </body>
</html>