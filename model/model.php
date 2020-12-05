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

function testConnectionRequest($username,$password) // traite une demande de connexion
{
	$db = dbConnect();
	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);
	$result = $db->prepare('SELECT username, password, prenom, nom, photo FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$content = $result->fetch();
	if($content)
	{
		$testpass = password_verify($password,$content['password']);
		if($testpass)
		{
			$connection = true;
			$_SESSION['username'] = $username;
			$_SESSION['prenom'] = htmlspecialchars($content['prenom']);
			$_SESSION['nom'] = htmlspecialchars($content['nom']);
			$_SESSION['photo'] = htmlspecialchars($content['photo']);
		}
		else
		{
			$connection = false;
		}
	}
	else
	{
		$connection = false;
	}
	return $connection;
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
	$data = $result->fetch();
	$user = $data['id_user'];
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
	$work = $result->execute(array('actor' => $actor_id, 'like_' => $like_state));
	if(!$work)
	{
		$like_list[] = '';
	}
	else
	{
		$like_list[] = '';
		while($data = $result->fetch())
		{
			$like_list[] = $data['nom'] . ' ' . $data['prenom'] ;
		}	
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

// =============== Gestion réinitialisation ===============

function getReinitContent($step,$question = 0)
{
	if($step == 1)
	{
		if(isset($_SESSION['invalid_user']))
		{
			$error_message = '<p style=color:red;text-align:center;>L\'identifiant saisi n\'existe pas.</p>';
			unset($_SESSION['invalid_user']);
		}
		else
		{
			$error_message = '';
		}
		$content = '<div class="content reinit_content">
						<form class="connection_form" action="index.php?action=reinit&amp;fgt=2" method="post">
							<fieldset>
							<legend class="long_legend">Réinitialiser le mot de passe :</legend>

							<label for="username">Veuillez saisir votre nom d\'utilisateur :</label><input type="text" name="username" id="username"/>

							' . $error_message . '

							<input type="submit" name="submit" value="Valider">					
							</fieldset>			
						</form>
					</div>
					';
	}
	if($step == 2)
	{
		if(isset($_SESSION['missing_field']))
		{
			$error_message = '<p style=color:red;text-align:center;>Certains champs n\'ont pas été remplis';
			unset($_SESSION['missing_field']);
		}		
		elseif(isset($_SESSION['invalid_answer']))
		{
			$error_message = '<p style=color:red;text-align:center;>Mauvaise Réponse</p>';
			unset($_SESSION['invalid_answer']);
		}
		elseif(isset($_SESSION['invalid_pass_format']))
		{
			$error_message = '<p style=color:red;text-align:center;>Bonne réponse mais le format des mots de passe de convient pas, veuillez recommencer.</p>';
			unset($_SESSION['invalid_pass_format']);
		}
		else
		{
			$error_message = '';
		}	
		$content = '<div class="content reinit_content">
						<form class="connection_form" action="index.php?action=reinit&amp;fgt=3" method="post">
							<fieldset>
								<legend class="long_legend">Réinitialiser le mot de passe :</legend>
									' . $error_message . '

									<label for="answer">' . $question . '</label><input type="text" name="answer" id="answer" required/>

									<label for="pass1">Nouveau mot de passe <span class="lower_italic">(8 caractères, une majuscule, un chiffre et un caractère spécial au minimum)</span> :</label><input type="password" name="pass1" id="pass1" required/>

									<label for="pass2">Confirmation du mot de passe :</label><input type="password" name="pass2" id="pass2" required/>

									<input type="submit" name="submit" value="Changer le mot de passe">
							</fieldset>			
						</form>
					</div>
					';
	}
	return $content;
}

function getQuestion($username) // Récupère la question secrète de l'utilisateur actuel
{
	$db = dbConnect();
	$result = $db->prepare('SELECT question FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$data = $result->fetch();
	$result->closeCursor();
	if(!$data) // ne devrait pas arriver
	{
		$question = '[...]';
	}
	else
	{
		$question = preg_replace("#(\?)#"," ",htmlspecialchars($data['question']));
		$question = 'Votre question secrète : ' . $question . ' ?';
	}
	return $question;
}

function testReinitAns($username,$answer) // Teste la validité de la réponse à la question secrète
{
	$db = dbConnect();
	$result = $db->prepare('SELECT reponse FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$data = $result->fetch();
	$result->closeCursor();	
	if(!$data) // ne devrait pas arriver
	{
		$test = false;
	}
	else
	{
		$user_answer = htmlspecialchars($data['reponse']);
		$test = password_verify($answer,$user_answer );
	}	
	return $test;
}

function testReinitPass($pass1,$pass2) // Vérifie le format et la correspondance des mots de passe	
{
	if(preg_match("#(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\d)(?=.*[^A-Za-z\d])#",$pass1) AND $pass1=$pass2) 
	{
		$test =  true;
	}
	else
	{
		$test = false;
	}
	return $test;
}

function reinitPass($username,$pass1)
{
	$db = dbConnect();
	$pass = password_hash($pass1, PASSWORD_DEFAULT);
	$query = $db->prepare('UPDATE account SET password = :pass WHERE username = :username');
	$work = $query->execute(array('pass' => $pass,'username' => $username));
	$query->closeCursor();
	return $work;
}

// =============== Gestion changement de profil ===============

function testPassword($username,$password) // Vérifie le mot de passe actuel
{
	$db = dbConnect();
	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);
	$result = $db->prepare('SELECT username, password FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$content = $result->fetch();
	if($content)
	{
		$actual_password = htmlspecialchars($content['password']);
		$testpass = password_verify($password,$actual_password);
	}
	else // ne devrait pas arriver
	{
		$testpass = false;
	}
	return $testpass;
}

function updateUsername($new_username) // Change le nom d'utilisateur
{
	$user = getUserId($_SESSION['username']);
	$existing = existUsername($new_username);
	if($existing)
	{
		$work = false ;
	}
	else
	{
		$db = dbConnect();
		$query = $db->prepare('UPDATE account SET username = :username WHERE id_user = :user');
		$work = $query->execute(array('username' => $new_username, 'user' => $user));
		$query->closeCursor();
	}
	return $work;
}

function testFile($upload_ext,$size) // Vérifie les caractéristiques du fichier reçu
{
	$allowed_extensions = array('jpg', 'jpeg', 'png');
	if($size <= 2000000 AND in_array($upload_ext,$allowed_extensions))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function addPhoto($username,$photo,$upload_ext) // Ajoute la photo
{
	$db = dbConnect();
	delPhoto($username);
	$uploaddir = 'public/images/uploads/';
	$filename = basename($photo['name']);
	$filename = rand(0,99999999999) . preg_replace("#\s#","_",$filename);
	$uploadfile = $uploaddir . $filename;
	$work = move_uploaded_file($photo['tmp_name'], $uploadfile);
	echo $photo['tmp_name'] . '<br/>' ;
	echo $filename . '<br/>' ;
	echo $uploadfile . '<br/>' ;
	if(!$work)
	{
		$work = false;
	}
	else
	{
		if($upload_ext == 'jpeg' OR $upload_ext == 'jpg')
		{
			jpegtoMini($filename);
			$work = updateUserAccount($username,$filename);
		}
		elseif($upload_ext == 'png' )
		{
			pngtoMini($filename);
			$work = updateUserAccount($username,$filename);
		}
		else // ne devrait pas arriver
		{
			$work = false;
		}
	}
	return array($work, $filename);
}

function delPhoto($username) // Supprime la photo précédente (sauf si c'est celle par défaut)
{
	$db = dbConnect();
	$result = $db->prepare('SELECT photo FROM account WHERE username = :username');
	$result->execute(array('username' => $username));
	$data = $result->fetch();
	$result->closeCursor();
	$actual_filename = htmlspecialchars($data['photo']);
	echo $actual_filename ;
	if($actual_filename != 'default.png')
	{
		unlink(realpath('C:/xampp/htdocs/GBAF_MVC/public/images/uploads/' . $actual_filename));
	}
}

function jpegtoMini($filename) // jpeg vers miniature
{
	$source = imagecreatefromjpeg('public/images/uploads/' . $filename);
	$target = imagecreatetruecolor(150, 150);
	$source_width= imagesx($source);
	$source_height = imagesy($source);
	$target_width = imagesx($target);
	$target_height = imagesy($target);
	imagecopyresampled($target, $source, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
	imagejpeg($target,'public/images/uploads/' . $filename);	
}

function pngtoMini($filename) // png vers miniature
{
	$source = imagecreatefrompng('public/images/uploads/' . $filename);
	$target = imagecreatetruecolor(150, 150);
	$source_width= imagesx($source);
	$source_height = imagesy($source);
	$target_width = imagesx($target);
	$target_height = imagesy($target);
	imagecopyresampled($target, $source, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
	imagepng($target,'public/images/uploads/' . $filename);
}

function updateUserAccount($username,$filename) // Met à jour le lien de l'image de l'utilisateur
{
	$db = dbConnect();
	$query = $db->prepare('UPDATE account SET photo = :filename WHERE username = :username');
	$work = $query->execute(array(':filename' => $filename,'username' => $username));
	$query->closeCursor();
	if(!$work)
	{
		$work = false;
	}
	else
	{
		$work = true;
	}
	return $work;
}

