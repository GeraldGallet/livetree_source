<?php

require dirname(__FILE__) . ('/../PhpObjects/DatabaseProvider.php');
require dirname(__FILE__) . ('/../../Controlers/includes/initSession.php');

$msgErreurConnexion = "";
$msgErreurInscription = "";

function connexion($email, $password) {
    try {
        $db = connectDB();
        $sql = 'SELECT * FROM user WHERE (email=\'' . $email . '\' AND password=\'' . $password . '\')';
        $query = $db->query($sql);
        $data = $query->fetch();
        $db = null;

        if (!empty($data)) {
            $_SESSION['email'] = $data['email'];
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo ('Erreur: ' . $e->getMessage());
    }
}

function inscription($email, $password) {
    try {
        $db = connectDB();
        $sql = 'SELECT * FROM user WHERE email=\'' . $email . '\'';
        $query = $db->query($sql);
        $data = $query->fetch();
        if (empty($data)) {
            $insert = 'INSERT INTO user(email, password) VALUES (\'' . $email . '\', \'' . $password . '\')';
            $query = $db->exec($insert);
            $email = $db->lastInsertId();
            $db = null;
            $_SESSION['email'] = $email;
            return true;
        } else {
            $db = null;
            return false;
        }
    } catch (PDOException $e) {
        echo ('<br><br><br><br>Erreur: ' . $e->getMessage());
    }
}

function infosUser($email) {
    try {
        $db = connectDB();
        $sql = 'SELECT * FROM user WHERE email=\'' . $email . '\'';
        $query = $db->query($sql);
        $data = $query->fetch();
        $email = $data['email'];
        return $email;
    } catch (PDOException $e) {
        echo ('Erreur: ' . $e->getMessage());
    }
}

/**
 * 
 * @return type
 */
function checIfUserExistAndConnexionWhenSubmitConnexion() {
    if (!empty($_POST['sign in'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!empty($email && !empty($password))) {
            $connexionBool = connexion($email, $password);
            if ($connexionBool) {
//                header("Location: ../page_home/index.php");
//           SUCCESS!                 
            } else {
                return $msgErreurConnexion = "Nom d'utilisateur ou mot de passe erroné.";
            }
        } else {
            return $msgErreurConnexion = "Veuillez remplir les champs requis.";
        }
    }
}

function checIfUserExistAndConnexionWhenSubmitSinscrire() {

    if( isset( $_GET['sign_up'])) {
        if (!empty($_GET['sign_up'])) {
            $email = $_GET['email'];
            $password = $_GET['password'];
            echo $email;
            echo $password;
            if (!empty($email && !empty($password))) {
                $inscriptionBool = inscription($email, $password);
                if ($inscriptionBool) {
                    header("Location: ../page_home/index.php");
                } else {
                    return $msgErreurInscription = "Ce mail est déjà utilisé.";
                }
            } else {
                return $msgErreurInscription = "Veuillez remplir les champs requis.";
            }
        }
    }
}

//$path='"../"'. (string) dirname(__FILE__);
?>