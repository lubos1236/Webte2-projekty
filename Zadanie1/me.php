<?php
require_once("header.php");
require_once("restricted.php");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ME</title>
</head>
<body>
    <div class="container">
        <H1>Vitaj <?php echo $_SESSION['name']?></H1>
        <h2>Teraz si úspešne prihlásení.</h2>
        <form action="../Zadanie1/logout.php">
            <input type="submit" value="LogOut" />
        </form>
    </div>

    
    
</body>
</html>