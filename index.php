<?php
session_start();
require('controller/controller.php');

if(isset($_GET['action']) AND !empty($_GET['action'])) // requête d'une action
{
	$call = htmlspecialchars($_GET['action']);
	if(isset($_SESSION['username']) AND !empty($_SESSION['username'])) //si session active
	{
		$username = htmlspecialchars($_SESSION['username']);
		if($call == 'deconnexion')
		{
			deconnection();
		}				
		elseif($call == 'inscription')
		{
			inscription();
		}
		elseif($call == 'reinit')
		{
			if(isset($_GET['fgt']))
			{
				$step=$_GET['fgt'];
				reinit($step);
			}
			else
			{
				reinit(1);
			}		
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
	elseif($call == 'connexion' AND isset($_GET['try']) AND $_GET['try'] == 1)
	{
		connectionRequest();
	}
	else
	{
		connection();
	}
}
else
{
	connection();
}