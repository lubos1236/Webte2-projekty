<?php
require_once "../Zadanie1/vendor/autoload.php";
require_once("config.php");
session_start();

$client=new Google\Client();
$client->setAuthConfig('../Zadanie1/client_secret.json');
$client->setRedirectUri("https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie1/redirect.php");
$client->addScope("email");
$client->addScope("profile");

function userExist($db, $email){
    $tmp_email=trim($email);

    $query="SELECT * FROM users
    WHERE email = :email";
    $stmt=$db->prepare($query);
    $stmt->bindParam(":email", $tmp_email, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        $data=$stmt->fetch(PDO::FETCH_ASSOC);
        if($data['password']===null)
        {
            unset($stmt);
            $_SESSION['user_id'] = $data['id'];
            return true;
        }
        else{
            header("Location: login.php?err=err");
        }
    }
    return false;
}




if(isset($_GET['code'])){
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    $oauth = new Google\Service\Oauth2($client);
    $account_info = $oauth->userinfo->get();
    
    $g_fullname = $account_info->name;
    $g_id = $account_info->id;
    $g_email = $account_info->email;
    $g_name = $account_info->givenName;
    $g_surname = $account_info->familyName;

    $_SESSION['access_token'] = $token['access_token'];
    $_SESSION['email'] = $g_email;
    //$_SESSION['id'] = $g_id;
    //$_SESSION['fullname'] = $g_fullname;
    $_SESSION['name'] = $g_name;
    $_SESSION['surname'] = $g_surname;
    $_SESSION['logged']=true;
    
    
    
    
    if(userExist($db,$g_email) === false){
        $query = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";
        
        $stmt=$db->prepare($query);
        $stmt->bindParam(":name", $g_name,PDO::PARAM_STR);
        $stmt->bindParam(":surname", $g_surname,PDO::PARAM_STR);
        $stmt->bindParam(":email", $g_email,PDO::PARAM_STR);
        
        $stmt->execute();

        $query = "SELECT id FROM users
        WHERE email = ?";
        $stmt=$db->prepare($query);
        $stmt->execute([$g_email]);
        $data=$stmt->fetch(PDO::FETCH_ASSOC);
    
        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
        $stmt=$db->prepare($query);
        $stmt->execute([$data['id'],"Accont Created"]);
        header('Location: me.php');
    }

    
    $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
    $stmt=$db->prepare($query);
    $stmt->execute([$_SESSION['user_id'],"Logged in with a google account "]);
    header('Location: me.php');
    
    
}





?>