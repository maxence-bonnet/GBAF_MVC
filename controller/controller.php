<?php

require('model/model.php');

function connection()
{
	require('view/connexionView.php');
}

function connectionRequest()
{
	if(isset($_POST['username']) AND isset($_POST['password']) AND !empty($_POST['username']) AND !empty($_POST['password']))
	{
		$username = htmlspecialchars($_POST['username']);
		$password = htmlspecialchars($_POST['password']);
		$connection = testConnectionRequest($username,$password);
		if($connection)
		{
			header('Location: index.php?action=accueil');
		}
		else
		{
			$_SESSION['wrong'] = 1;
			require('view/connexionView.php');
		}
	}
	else
	{
		$_SESSION['missing_field'] = 1 ;
		require('view/connexionView.php');
	}
}

function deconnection()
{
	session_destroy();
	header('Location: index.php?action=accueil');	
}

function inscription()
{
	require('view/inscriptionView.php');
}

function reinit($step)
{
	if($step == 1)
	{
		$content = getReinitContent(1);
		require('view/reinitView.php');
	}
	elseif($step == 3)
	{
		if(isset($_POST['answer']) AND isset($_POST['pass1']) AND isset($_POST['pass2']) AND isset($_SESSION['usertemp']))
		{
			$username = $_SESSION['usertemp'];
			$answer = htmlspecialchars($_POST['answer']);		
			$test = testReinitAns($username,$answer);
			if(!$test)
			{
				$_SESSION['invalid_answer'] = 1 ;
				header('Location: index.php?action=reinit&fgt=2');
				// mauvaise réponse à la question secrète
			}
			else
			{
				$pass1 = htmlspecialchars($_POST['pass1']);
				$pass2 = htmlspecialchars($_POST['pass2']);
				$test = testReinitPass($pass1,$pass2);
				if(!$test)
				{
					$_SESSION['invalid_pass_format'] = 1 ;
					header('Location: index.php?action=reinit&fgt=2');
					// mauvais format de mot de passe (mais bonne réponse)
				}
				else
				{
					$work = reinitPass($username,$pass1);
					if(!$work)
					{
						$_SESSION['update_error'] = 1 ;
						header('Location: index.php?action=reinit&fgt=2');
						// erreur pendant l'écriture
					}
					else
					{
						unset($_SESSION['usertemp']);
						$_SESSION['passchanged'] = 1 ;
						header('Location: index.php?action=connexion');
						// succès dans la réinitialisation -> retour à la page de connexion
					}
				}
			}
		}
		else
		{
			$_SESSION['missing_field'] = 1 ;
			header('Location: index.php?action=reinit&amp;fgt=2');
			// manque certains champs
		}
	}
	elseif($step == 2 AND isset($_POST['username']) OR isset($_SESSION['usertemp']))
	{
		if(isset($_POST['username']))
		{
			$username = htmlspecialchars($_POST['username']);	
		}
		else // cas où il y a eu un précédent retour d'erreur
		{
			$username = htmlspecialchars($_SESSION['usertemp']);
		}

		$existing = existUsername($username);

		if(!$existing)
		{
			$_SESSION['invalid_user'] = 1 ;
			header('Location: index.php?action=reinit&amp;fgt=1');
			// utilisateur inexistant
		}
		else
		{
			$_SESSION['usertemp'] = $username;
			$question = getQuestion($username);
			$content = getReinitContent(2,$question);
			require('view/reinitView.php');
		}			
	}		
	else
	{
		$step = 1;
		require('view/reinitView.php');
	}
}

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
			echo ' /!\ Erreur pendant la mise à jour de la mention /!\ ';
		}		
	}
	header('Location: index.php?action=acteur&act=' . $actor_id);
}

function myProfile($username)
{
	require('view/profilView.php');
}

function profileUpdateUsername($new_username)// changement d'identifiant
{
	$new_username = htmlspecialchars($new_username);
	$work = updateUsername($new_username);
	if(!$work)
	{
		$_SESSION['exist'] = 1 ;// erreur dans l'écriture
	}
	else
	{
		$_SESSION['username'] = $new_username;
		$_SESSION['usernamechanged'] = 1 ; // écriture effectuée						
	}
	return 
}

function profileUpdatePassword($username,$actual_pass,$pass1,$pass2) // changement mot de passe
{
	$testpass = testPassword($username,$actual_pass);
	if($testpass)
	{
		$test = testReinitPass($pass1,$pass2);
		if($test) // écriture
		{			
			$work = reinitPass($username,$pass1);
			if(!$work)
			{
				$_SESSION['unknown'] = 1 ;// erreur dans l'écriture
			}
			else
			{
				$_SESSION['passchanged'] = 1 ; // écriture effectuée						
			}
		}
		else
		{
			$_SESSION['invalidpass'] = 1 ;// mauvais format mdp
		}
	}
	else
	{
		$_SESSION['wrongpass'] = 1 ; // mauvais mdp
	}
}

function profileUpdatePhoto($photo)// changement de photo
{
	////////////////////////////////////////////////////////////////////////
}

function mentions()
{
	require('view/mentions-legalesView.php');
}

function contact()
{
	require('view/contactView.php');
}