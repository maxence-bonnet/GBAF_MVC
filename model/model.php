<?php

// =============== Connexion à la base de données ===============

function dbConnect() // connexion à la base de données
{
	$db = new PDO('mysql:host=localhost;dbname=gbaf;charset=utf8', 'root', '');
	return $db;
}

// =============== Connexion d'utilisateur ===============

function authenticateUser($username,$password) // traite une demande de connexion
{
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
	$password = password_hash($_POST['pass1'],PASSWORD_DEFAULT);
	$answer = password_hash($_POST['question'],PASSWORD_DEFAULT);
	$query = $db->prepare('INSERT INTO account(nom, prenom, username, password, question, reponse) VALUES(:nom, :prenom, :username, :password, :question, :answer)');
	$work = $query->execute(array('nom' => $last_name, 'prenom' => $first_name, 'username' => $username, 'password' => $password, 'question' => $question, 'answer' => $answer));
	return $work;
}

// =============== ... ===============

function isconnected() // vérifie si une connexion est active
{
	if(isset(isset($_SESSION['username']) AND !empty($_SESSION['username'])))
	{
		return true;
	}
	else
	{
		return false;
	}
}

// =============== ... ===============

function getUserId($username) // récupère l'identifiant utilisateur via username
{
	$user_id = $db->prepare('SELECT id_user FROM account WHERE username = :username');
	$user_id->execute(array('username' => $username));
	return $user_id;
}

function listActor() // récupère toutes les informations de tous les acteurs
{
	$actors_info = $db->query('SELECT * FROM actor');
	return $actors_info
}

function existActor($actor_id) // vérifie l'existance de l'acteur
{
	$result = $db->prepare('SELECT id_actor FROM actor WHERE id_actor = :actor');
	$result->execute(array(':actor' => $actor_id));
	$existing = $result->fetch();
	return $existing;
}

function existUsername($username) // vérifie l'existance d'un nom d'utilisateur
{
	$result = $db->prepare('SELECT username FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$existing = $result->fetch();
	return $existing;
}

// =============== Commentaires ===============

function existComment($user_id,$actor_id) // vérifie l'existance d'un commentaire pour un acteur et un utilisateur donné
{
	$result = $db->prepare('SELECT id_post FROM post WHERE id_user = :id_user AND id_actor = :id_actor');
	$result->execute(array('id_user' => $user_id, 'id_actor' => $actor_id));
	$existing = $result->fetch();
	return $existing;
}

function addComment($user_id,$actor_id,$comment) // ajoute un commentaire
{
	$query = $db->prepare('INSERT INTO post(id_user, id_actor, post) VALUES(:id_user, :id_actor, :comment)');
	$work = $query->execute(array('id_user' => $user_id, 'id_actor' => $actor_id, 'comment' => $comment));
	return $work;
}

function delComment($user_id,$actor_id) // supprime le commentaire existant
{	
	$query = $db->prepare('DELETE FROM post WHERE id_user = :id_user AND id_actor = :id_actor');
	$work = $query->execute(array('id_user' => $id_user, 'id_actor' => $actor));
	return $work;
}

function 

function existUser($)
{
	
}
