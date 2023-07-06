<?php
ini_set("display_errors",1);
ini_set("display_startip_errors",1);
error_reporting(E_ALL);


require_once ("../Zadanie1/vendor/autoload.php");
require("../Zadanie1/header.php");
require_once("PHPGangsta/GoogleAuthenticator.php");
require_once("config.php");

$client = new Google\Client();
$client->setAuthConfig('../Zadanie1/client_secret.json');
$redirect_uri = "https://site238.webte.fei.stuba.sk/H5NS32KL12/Zadanie1/redirect.php";
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");
$auth_url = $client->createAuthUrl();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    try{
        $query="SELECT* FROM users WHERE email = :login";
        $stmt=$db->prepare($query);
        $stmt->bindParam(":login",$_POST['login'],PDO::PARAM_STR);
        if($stmt->execute()){
            if($stmt->rowCount()==1){
                $row=$stmt->fetch();
                if(password_verify($_POST['password'],$row['password'])){
                    $code2fa=new PHPGangsta_GoogleAuthenticator();
                    if($code2fa->verifyCode($row['2fa'],$_POST['2fa'],2)){
                        $_SESSION['logged']=true;
                        $_SESSION['user_id'] = $row['id'];
                        //$_SESSION['name']=$row['name'];
                        //$_SESSION['surname']=$row['surname'];
                        $_SESSION['email']=$row['email'];

                        $query = "INSERT INTO history (user_id,activity) VALUES (?,?)";
                        $stmt=$db->prepare($query);
                        $stmt->execute([$_SESSION['user_id'],"Logged in"]);
                        header('Location: me.php');


                        //header("location: index.php");
                    }
                }
                else if($row['password']==null){
                    header("Location: login.php?err=err");
                }
            }
        }

    }catch (PDOException $e) {
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
    <title>Login</title>
</head>
<body>
<?php
    if(isset($_GET['err']))
    echo "<H2>This email is already bound with another account!!</H2>"
    ?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <input type="text" name="login" placeholder="Email" required><br/>
    <input type="password" name="password" placeholder="Password" required><br/>
    <input type="number" name="2fa" maxlength="6" placeholder="2FA" required><br/>
    <button type="submit">Submit</button>
    <p>Don't have an account yet?<a href="register.php"> Register now!</a></p> 
    </form>

    <?php
    echo '<a role="button" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '">Google Account Login</a>';
    ?>
    
</body>
</html>