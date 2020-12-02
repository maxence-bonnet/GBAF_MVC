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
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$actor = presentActor($actor_id);
	$actorname = actorname($actor_id);
	$existingUserComment = existUserComment($actor_id,$username);
	$like_number = countLikes($actor_id,'like');
	$dislike_number = countLikes($actor_id,'dislike');
	$like_list = listLikers($actor_id,'like');
	$dislike_list = listLikers($actor_id,'dislike');
	$like_state = checkLike($actor_id,$username);
	// affichage likes
	if(!$like_state)
	{
		$show = false;
	}
	elseif($like_state == 'like')
	{
		$show = 'Vous recommandez ce partenaire';
	}
	elseif($like_state == 'dislike')
	{
		$show = 'Vous déconseillez ce partenaire';	
	}
	// affichage des commentaires
	if(!existComment($actor_id)) // pas de commentaire posté pour cet acteur
	{
		$comments = false;
	}
	else // Il y a des commentaires
	{
		$comments = listComments($actor_id);
	}
	// affichage formulaire d'ajout de commentaire
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
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}

	$user = getUserId($username);
	$user_id = $user['id_user'];
	if(existUserComment($actor_id,$username)) // L'utilisateur a déjà commenté
	{
		session_start();
		$_SESSION['existing_comment'] = true;
		header('Location: index.php?action=acteur&act=' . $actor_id);
	}
	else // écriture
	{
		$work = newComment($user_id,$actor_id,$comment);
		if(!$work) // erreur pendant l'écriture
		{
			echo 'Erreur dans l\'ajout du commentaire';
		}
		else // bon déroulement
		{
			session_start();
			$_SESSION['posted'] = true;
			header('Location: index.php?action=acteur&act=' . $actor_id);
		}		
	}
}

function delComment($actor_id,$username)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$user = getUserId($username);
	$user_id = $user['id_user'];
	if(existUserComment($actor_id,$username)) // L'utilisateur a déjà commenté
	{
		$work = deleteComment($user_id,$actor_id);
		if(!$work) // erreur pendant l'écriture
		{
			echo 'Erreur dans la suppression du commentaire';
		}
		else // bon déroulement
		{
			session_start();
			$_SESSION['deleted_post'] = true;
			header('Location: index.php?action=acteur&act=' . $actor_id);
		}	
	}
	else // Pas de commentaire utilisateur existant -> retour (ne devrait pas arriver)
	{
		header('Location: index.php?action=acteur&act=' . $actor_id);
	}	
}

function likeManage($actor_id,$username,$like_request)
{
	$existingActor = existActor($actor_id);
	if(!$existingActor)
	{
		header('Location: index.php?action=accueil');
	}
	
	$user = getUserId($username);
	$user_id = $user['id_user'];
	$like_state = checkLike($actor_id,$username);
	if(!$like_state)
	{
		if($like_request == 1)
		{
			$like_request = 'like';
			$work = addMention($actor_id,$user_id,$like_request);
		}
		elseif($like_request == 2)
		{
			$like_request = 'dislike';
			$work = addMention($actor_id,$user_id,$like_request);
		}

		if(!$work)
		{
			echo 'Erreur pendant l\'ajout';
		}		
	}
	else
	{
		if($like_state == 'like' AND $like_request == 2)
		{
			// Upate de like à dislike
			$work = updateMention($actor_id,$user_id,$like_request);
		}
		elseif($like_state == 'dislike' AND $like_request == 1)
		{
			// Update de dislike à like
			$work = updateMention($actor_id,$user_id,$like_request);
		}
		elseif($like_request == 3)
		{
			// Supprimer la mention like ou dislike
			$work = deleteMention($actor_id,$user_id);
		}

		if(!$work)
		{
			echo 'Erreur pendant la mise à jour de la mention';
		}		
	}
	header('Location: index.php?action=acteur&act=' . $actor_id);
}