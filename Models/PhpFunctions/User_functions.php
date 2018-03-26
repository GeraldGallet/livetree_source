<?php

	function connexion($email, $password)
	{
		try{
			$db = connectDB();
			$sql = 'SELECT * FROM user WHERE (email=\''.$email.'\' AND password=\''.$password.'\')';
			$query = $db->query($sql);
			$data = $query->fetch();
			$db = null;

			if (!empty($data))
			{
				$_SESSION['email'] = $data['email'];
				return true;
			}
			else{
				return false;
			}
		}
		catch (PDOException $e){
			echo ('Erreur: ' .$e->getMessage());
		}
	}

	function inscription($email, $password)
	{
		try{
			$db = connectDB();
			$sql = 'SELECT * FROM user WHERE email=\''.$email.'\'';
			$query = $db->query($sql);
			$data = $query->fetch();
			if (empty($data)){
				$insert = 'INSERT INTO users(email, password) VALUES (\''.$email.'\', \''.$password.'\')';
				$query = $db->exec($insert);
				$email = $db->lastInsertId();
				$db = null;
				$_SESSION['userid'] = $userid;
				return true;
			}
			else{
				$db = null;
				return false;
			}
		}
		catch (PDOException $e){
			echo ('<br><br><br><br>Erreur: ' .$e->getMessage());
		}
	}

	function infosUser($userid)
	{
		try{
			$db = connectDB();
			$sql = 'SELECT * FROM users WHERE userid=\''.$userid.'\'';
			$query = $db->query($sql);
			$data = $query->fetch();
			$username = $data['username'];
			return $username;
		}
		catch (PDOException $e){
			echo ('Erreur: ' .$e->getMessage());
		}
	}

?>