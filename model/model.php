<?php

// =============== Connexion à la base de données ===============

function dbConnect() // connexion à la base de données
{
	try
	{
		$db = new PDO('mysql:host=localhost;dbname=gbaf;charset=utf8', 'root', '');
	}
	catch (Exception $e)
	{
	    die('Erreur : ' . $e->getMessage());
	}
	return $db;
}

// =============== Connexion d'utilisateur ===============

function authenticateUser($username,$password) // traite une demande de connexion
{
	$db = dbConnect();
	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);
	$result = $db->prepare('SELECT username, password FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$content = $result->fetch();
	$testpass = password_verify($password,$content['password']);
	if($content AND $testpass)
	{
		return true;
	}
	else
	{
		return false;
	}
}

// =============== Inscription ===============

function testRegistration($last_name,$first_name,$username,$pass1,$pass2,$question,$answer)
{

}


function registerUser($last_name,$first_name,$username,$password,$question,$answer) // inscrit un utilisateur
{
	$db = dbConnect();
	$password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
	$answer = password_hash($_POST['question'],PASSWORD_DEFAULT);
	$query = $db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
	$work = $query->execute(array('nom' => $last_name, 'prenom' => $first_name, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
	return $work;
}

// =============== Session ===============

function isconnected() // vérifie si une connexion est active
{
	if(isset($_SESSION['username']) AND !empty($_SESSION['username']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// =============== Utilisateur ===============

function getUserId($username) // récupère l'identifiant utilisateur via username
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_user FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$user = $result->fetch();
	return $user;
}

function existUsername($username) // vérifie l'existance d'un nom d'utilisateur
{
	$db = dbConnect();
	$result = $db->prepare('SELECT username FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$existing = $result->fetch();
	return $existing;
}

// =============== Acteurs ===============

function listActors() // récupère toutes les informations de tous les acteurs
{
	$db = dbConnect();
	$actors_info = $db->query('SELECT * FROM actor');
	return $actors_info;
}

function existActor($actor_id) // vérifie l'existance de l'acteur
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_actor FROM actor WHERE id_actor = :actor');
	$result->execute(array(':actor' => $actor_id));
	$existingActor = $result->fetch();
	return $existingActor;
}

function actorname($actor_id) // récupère le nom de l'acteur en fonction de son id
{
	$db = dbConnect();
	$result = $db->prepare('SELECT actor FROM actor WHERE id_actor = :actor');
	$result->execute(array(':actor' => $actor_id));
	$actorname = $result->fetch();
	$actorname = $actorname['actor'];
	return $actorname;
}

function presentActor($actor_id) //récupère toutes les informations d'un acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT * FROM actor WHERE id_actor = :actor');
	$result->execute(array('actor' => $actor_id));
	$actor = $result->fetch();
	$result->closeCursor();
	return $actor;
}

// =============== Commentaires ===============

function existUserComment($actor_id,$username) // vérifie l'existance d'un commentaire de l'utilisateur connecté pour un acteur 
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, username, post.id_user, id_actor
							FROM account
							INNER JOIN post
							ON account.id_user = post.id_user
							WHERE username = :username
							AND id_actor = :actor');
	$result->execute(array('username' => $username, 'actor' => $actor_id));
	$existingUserComment = $result->fetch();
	$result->closeCursor();
	return $existingUserComment;
}

function existComment($actor_id) // vérifie l'existance d'au moins un commentaire pour l'acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT id_actor FROM post WHERE id_actor = :actor');
	$result->execute(array('actor' => $actor_id));
	$data = $result->fetch();
	$result->closeCursor();	
	if(!$data)
	{
		return false;
	}
	else
	{
		return true;
	}
}

function listComments($actor_id) // Dresse la liste des commentaires et leurs infos utilisateurs pour un acteur donné
{
	$db = dbConnect();
	$comments = $db->prepare('SELECT account.id_user, nom, prenom, photo, post.id_user, id_actor, date_add, post 
							FROM post
							INNER JOIN account
							ON account.id_user = post.id_user
							WHERE id_actor = :actor
							ORDER BY date_add');
	$comments->execute(array('actor' => $actor_id));
	return $comments;
}

function newComment($user_id,$actor_id,$comment) // ajoute un commentaire
{
	$db = dbConnect();
	$query = $db->prepare('INSERT INTO post(id_user, id_actor, post) VALUES(:id_user, :id_actor, :comment)');
	$work = $query->execute(array('id_user' => $user_id, 'id_actor' => $actor_id, 'comment' => $comment));
	$query->closeCursor();
	return $work;
}

function deleteComment($user_id,$actor_id) // supprime le commentaire existant
{	
	$db = dbConnect();
	$query = $db->prepare('DELETE FROM post WHERE id_user = :id_user AND id_actor = :id_actor');
	$work = $query->execute(array('id_user' => $user_id, 'id_actor' => $actor_id));
	$query->closeCursor();
	return $work;
}

// =============== Gestion des likes / dislikes ===============

function checkLike($actor_id,$username) // vérifie si l'utilisateur actuel a déjà ajouté une mention ("je recommande / déconseille")
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, username, vote.id_user, id_actor, vote 
							FROM account
							INNER JOIN vote
							ON account.id_user = vote.id_user
							WHERE username = :username
							AND id_actor = :actor');
	$result->execute(array('username' => $username, 'actor' => $actor_id));
	$data = $result->fetch();
	$result->closeCursor();
	if(!$data)
	{
		$like_state = false;
	}
	else
	{
		$like_state = $data['vote'];
	}
	return $like_state;
}

function countLikes($actor_id,$like_state) // Compteur de mention "je recommande" / "Je déconseille" (en fonction de $likestate)
{
	$db = dbConnect();
	$result = $db->prepare('SELECT COUNT(*) AS like_number FROM vote WHERE id_actor = :actor AND vote = :like_');
	$result->execute(array('actor' => $actor_id, 'like_' => $like_state));
	$data = $result->fetch();
	$result->closeCursor();
	$like_number = $data['like_number'];
	if(!$data)
	{
		$like_number = 0;
	}
	return $like_number;
}

function listLikers($actor_id,$like_state) // Dresse la liste des utilisateurs qui recommandent ou déconseillent (en fonction de $likestate) l'acteur donné
{
	$db = dbConnect();
	$result = $db->prepare('SELECT account.id_user, nom, prenom, vote.id_user, id_actor, vote 
							FROM vote
							INNER JOIN account
							ON account.id_user = vote.id_user
							WHERE id_actor = :actor
							AND vote = :like_');
	$result->execute(array('actor' => $actor_id, 'like_' => $like_state));
	while($data = $result->fetch())
	{
		$like_list[] = $data['nom'] . ' ' . $data['prenom'] ;
	}																								
	$result->closeCursor();
	return $like_list;
}

function addMention($actor_id,$user_id,$like_request) // Ajoute la mention (en fonction de $likestate)
{
	$db = dbConnect();
	$query = $db->prepare('INSERT INTO vote(id_user, id_actor, vote) VALUES(:id_user, :actor, :vote)');
	$work = $query->execute(array('id_user' => $user_id, 'actor' => $actor_id, 'vote' => $like_request));
	$query->closeCursor();
	return $work;
}

function updateMention($actor_id,$user_id,$like_request) // Met à jour la mention (en fonction de $likestate)
{
	$db = dbConnect();
	$query = $db->prepare('UPDATE vote SET vote = :vote WHERE id_user = :id_user AND id_actor = :actor');
	$work = $query->execute(array('vote' => $like_request, 'id_user' => $user_id, 'actor' => $actor_id));
	$query->closeCursor();
	return $work;	
}

function deleteMention($actor_id,$user_id) // Supprime la mention
{
	$db = dbConnect();
	$query = $db->prepare('DELETE FROM vote WHERE id_user = :id_user AND id_actor = :actor');
	$work = $query->execute(array('id_user' => $user_id, 'actor' => $actor_id));
	$query->closeCursor();	
	return $work;
}