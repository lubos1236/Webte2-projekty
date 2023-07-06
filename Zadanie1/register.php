<?php
require("../Zadanie1/header.php");
require_once("config.php");
require_once("PHPGangsta/GoogleAuthenticator.php");

ini_set("display_errors", 1);
ini_set("display_startip_errors", 1);
error_reporting(E_ALL);

function userExist($db, $email){
    $tmp_email=trim($email);

    $query="SELECT id FROM users
    WHERE email = :email";
    $stmt=$db->prepare($query);
    $stmt->bindParam(":email", $tmp_email, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()==1){
        unset($stmt);
        return true;
    }
    return false;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $err='';
    try {
            
        if(userExist($db,$_POST['email']) === false){
            $query = "INSERT INTO users (name, surname, email, password,2fa) VALUES (:name, :surname, :email, :password, :2fa)";
            $code2fa=new PHPGangsta_GoogleAuthenticator();
            $user_secret = $code2fa->createSecret();
            $qrCode=$code2fa->getQRCodeGoogleUrl("Olympic Games", $user_secret);

            $hashPassword=password_hash($_POST['password'],PASSWORD_ARGON2ID);

            $stmt=$db->prepare($query);
            $stmt->bindParam(":name", $_POST['name'],PDO::PARAM_STR);
            $stmt->bindParam(":surname", $_POST['surname'],PDO::PARAM_STR);
            $stmt->bindParam(":email", $_POST['email'],PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashPassword,PDO::PARAM_STR);
            $stmt->bindParam(":2fa", $user_secret,PDO::PARAM_STR);

            $stmt->execute();
            unset($stmt);

            
            $query = "SELECT id FROM users
            WHERE email = ?";
            $stmt=$db->prepare($query);
            $stmt->execute([$_POST['email']]);
            $data=$stmt->fetch(PDO::FETCH_ASSOC);


            $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
            $stmt=$db->prepare($query);
            $stmt->execute([$data['id'],"Accont Created"]);
        }
        else
            $err="Uesr already exist";


    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="text" name="name" placeholder="Name" required><br/>
        <input type="text" name="surname" placeholder="Surname" required><br/>
        <input type="email" name="email" placeholder="Email" required><br/>
        <input type="password" name="password" placeholder="Password" required><br/>
        <button type="submit">Submit</button>

        <?php
        if(!empty($err))
            echo $err;
        if(isset($qrCode))
        {
            echo "<br><img src='".$qrCode."'>";
            echo "<br>Now you can <a href='login.php'>Login</a>";
        }
        ?>
    </form>
    
</body>
</html>