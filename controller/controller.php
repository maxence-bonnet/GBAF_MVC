<?php

require('model/model.php');

function actorlist()
{
	$actors_info = listActors();
	require('view/accueilView.php');
}

function actorfull($actor_id,$username)
{
	$existingActor = existActor($actor_id);
	if($existingActor)
	{
		$actor = presentActor($actor_id);
		$actorname = actorname($actor_id);
		$existingUserComment = existUserComment($actor_id,$username);
		$like_number = countLikes($actor_id);
		$dislike_number = countDislikes($actor_id);
		$like_list = listLikers($actor_id);
		$dislike_list = listDislikers($actor_id);
		$show = checkLike($actor_id,$username);
		if(!existComment($actor_id)) // pas de commentaire posté pour cet acteur
		{
			$comments = false;
			echo 'comments = false';
		}
		else // Il y a des commentaires
		{
			$comments = listComments($actor_id);
		}
	}
	if(isset($_GET['add']) AND $_GET['add'] == 1) 
	{
		$showform = true;
	}
	else
	{
		$showform = false;
	}
	require('view/acteurView.php');
}

function addComment($actor_id,$username,$comment)
{
	$existingActor = existActor($actor_id);
	if($existingActor)
	{
		$user_id = getUserId($username);
		if(existUserComment($actor_id,$username)) // L'utilisateur a déjà commenté
		{
			$_SESSION['existing_comment'] = true;
			require('index.php?action=acteur&act=' . $actor_id);
		}
		else // écriture
		{
			newComment($user_id,$actor_id,$comment);
			if($work == false) // erreur pendant l'écriture
			{
				die('Erreur dans l\'ajout du commentaire');
			}
			else // bon déroulement
			{
				$_SESSION['posted'] = true;
				require('index.php?action=acteur&act=' . $actor_id);
			}		
		}
	}
	else
	{
		actorlist();
	}
}