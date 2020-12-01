<?php

require('controller/controller.php');

// if(isset($_SESSION['username']) AND !empty($_SESSION['username'])) //si session active
// {
// 	$session = true;
	$username = 'bob';
	if(isset($_GET['action']) AND !empty($_GET['action'])) // requête d'une action
	{
		$call = htmlspecialchars($_GET['action']);
		if($call == 'connexion')
		{
			//
		}
		elseif($call == 'deconnexion')
		{
			//
		}				
		elseif($call == 'inscription')
		{
			// 
		}
		elseif($call == 'reinit')
		{
			//
		}
		elseif($call == 'accueil')
		{
			actorlist();
		}		
		elseif($call == 'acteur' AND isset($_GET['act']) AND !empty($_GET['act'])) // page acteur
		{
			actorfull($_GET['act'],'bob');
			if(isset($_GET['add']) AND $_GET['add'] == 1) // demande d'ajout de commentaire
			{
				//
			}
		}
		elseif($call == 'comment' AND isset($_GET['act']) AND !empty($_GET['act']))
		{
			if(isset($_GET['add']) AND $_GET['add'] == 1 AND isset($_POST['new_comment']))
			{
				$comment = htmlspecialchars($_POST['new_comment']);
				$actor_id = htmlspecialchars($_GET['act']);
				addComment($actor_id,$username,$comment);												
			}
		}		
		elseif($call == 'profil')
		{
			//
		}
		elseif($call == 'mentions-legales')
		{
			//
		}
		elseif($call == 'contact')
		{
			//
		}				
		else
		{
			// action invalide
		}
	}
	else
	{
		// action non saisie -> vers accueil
	}
// }
// else
// {
// 	$session = false;
// 	// vers formulaire de connexion
// }