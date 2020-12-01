<?php

require('../controller/controller.php');

if(isset($_SESSION['username']) AND !empty($_SESSION['username'])) //si session active
{
	$session = true;
	if(isset($_GET['action']) AND !empty($_GET['action'])) // requête d'une action
	{
		if($_GET['action'] == 'connexion')
		{
			//
		}
		elseif($_GET['action'] == 'inscription')
		{
			// 
		}
		elseif($_GET['action'] == 'reinit')
		{
			//
		}
		elseif($_GET['action'] == 'acteur' AND isset($_GET['act']) AND !empty($_GET['act'])) // page acteur
		{
			if(isset($_GET['add']) AND $_GET['add'] == 1) // demande d'ajout de commentaire
			{
				//
			}
		}
		elseif($_GET['action'] == 'profil')
		{
			//
		}
		elseif($_GET['action'] == 'mentions-legales')
		{
			//
		}
		elseif($_GET['action'] == 'contact')
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
}
else
{
	$session = false;
	// vers formulaire de connexion
}