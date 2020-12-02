<?php

require('controller/controller.php');

// if(isset($_SESSION['username']) AND !empty($_SESSION['username'])) //si session active
// {
// 	$session = true;
	$username = 'Maitre-Verreux';
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
			$actor_id = htmlspecialchars($_GET['act']);
			if(isset($_GET['like']))
			{
				$like_request = $_GET['like'];			
				$right_values = array(1,2,3);
				if(in_array($like_request,$right_values))
				{
					likeManage($actor_id,$username,$like_request);
				}
			}
			actorfull($_GET['act'],$username);
		}
		elseif($call == 'comment' AND isset($_GET['act']) AND !empty($_GET['act']))
		{
			$actor_id = htmlspecialchars($_GET['act']);
			if(isset($_GET['add']) AND $_GET['add'] == 1 AND isset($_POST['new_comment']))
			{
				$comment = htmlspecialchars($_POST['new_comment']);
				addComment($actor_id,$username,$comment);												
			}
			elseif(isset($_GET['delete']) AND $_GET['delete'] == 1)
			{
				delComment($actor_id,$username);
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
			header('Location: index.php?action=accueil');
		}
	}
	else
	{
		header('Location: index.php?action=accueil');
	}
// }
// else
// {
// 	$session = false;
// 	// vers formulaire de connexion
// }