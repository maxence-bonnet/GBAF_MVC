<?php $title = 'Accueil'; ?>

<?php ob_start(); ?>

<div class="presentation_section">
	<h1>Groupement Banque-Assurance Français</h1>
	<p>Le GBAF représente les professions bancaires et de l'assurance sur tous les axes de la réglementation financière française. Sa mission est de promouvoir l'activité bancaire à l'échelle nationale. Il est également un interlocuteur privilégié des pourvoirs publics. Il est le fruit de l'association de 6 grands groupes français : BNP Paribas, BPCE, Crédit Agricole, Crédit Mutuel-CIC, Société Générale, La Banque Postale.</p>
	<div class="illustration">
		<div class="illustration_logos_container">
			<a href="#"><img src="public/images/logos/banques/BP.jpg" alt="banque_postale"/></a>
			<a href="#"><img src="public/images/logos/banques/CA.png" alt="credit_agricole"/></a>
			<a href="#"><img src="public/images/logos/banques/SG.png" alt="societe_generale"/></a>
			<a href="#"><img src="public/images/logos/banques/CIC.png" alt="cic"/></a>						
			<a href="#"><img src="public/images/logos/banques/BPCE.png" alt="bpce"/></a>
			<a href="#"><img src="public/images/logos/banques/CM.png" alt="credit_mutuel"/></a>	
			<a href="#"><img src="public/images/logos/banques/BNP.png" alt="bnp_paribas"/></a>
		</div>
	</div>
	</div>
	<div class="actors_list_section">
	<div class="actors_list_intro">
		<h2>Les acteurs et partenaires</h2>
			<p>Une liste complète des différents partenaires avec qui nous sommes susceptibles de collaborer. Vous pourrez ici vous renseigner sur chacun d'entre eux, consulter les avis de confrères, ou y laisser votre propre commentaire afin d'échanger des appréciations constructives et de distinguer les qualités et compétences de chacun de ces partenaires.</p>
	</div>					
		<?php // Récupération des infos et extraits de tous les partenaires
			while($actor = $actors_info->fetch())
			{
				$content = htmlspecialchars($actor['description']);
				$extract = explode(" ",$content);
				?>								
				    <div class="actor">
				    	<div class="actor_logo_n_desc">
				    		<div class="actor_logo"><img src="public/images/logos/<?= $actor['logo']; ?>" alt="logo <?= $actor['actor']; ?>"></div>
				    			<div class="actor_description">
					    			<h3><?= $actor['actor']; ?></h3>									    			
					    			<p><?php /* boucle pour écrire les 25 premiers mots */
					    			$i = 0;
					    			while($i < 25)
					    			{
					    				echo $extract[$i] . ' ';
					    				$i++;
					    			}
					    			?>... </p><p>Vers le site de <a class="actor_external_link" href="#"><?= $actor['actor']?></a></p>
					    		</div>
					    	</div>
					    	<a class="actor_read_more" href="index.php?action=acteur&amp;act=<?= $actor['id_actor']; ?>">Lire la suite</a>
				    </div>
				<?php
			}
			$actors_info->closeCursor();
		?>
	</div>

<?php

$content = ob_get_clean();

require('template.php');

?>